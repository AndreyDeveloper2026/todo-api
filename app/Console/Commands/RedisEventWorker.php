<?php

namespace App\Console\Commands;

use App\Domains\Task\Consumers\TaskCreatedConsumer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisEventWorker extends Command
{
    protected $signature = 'events:consume';
    protected $description = 'Consume Redis events (production style)';

    private string $stream = 'events';
    private string $retryStream = 'events:retry';
    private string $dlqStream = 'events:dlq';

    private string $group = 'default-group';
    private string $consumer = 'consumer-1';

    public function handle(): void
    {
        $this->ensureGroupExists();

        while (true) {
            try {

                $messages = Redis::xreadgroup(
                    $this->group,
                    $this->consumer,
                    [$this->stream => '>'],
                    10,
                    1000
                );

                if (!empty($messages)) {
                    $this->processMessages($messages);
                }

                $retryMessages = Redis::xrange($this->retryStream, '-', '+', 10);

                if (!empty($retryMessages)) {
                    $this->processRetryMessages($retryMessages);
                }

            } catch (\Throwable $e) {

                logger()->error('STREAM LOOP ERROR', [
                    'error' => $e->getMessage(),
                ]);

                sleep(2);
            }
        }
    }

    private function processMessages(array $messages): void
    {
        foreach ($messages as $streamName => $entries) {
            foreach ($entries as $id => $fields) {

                $event = is_array($fields) ? $fields : [];

                logger()->info('EVENT RECEIVED', [
                    'stream' => $streamName,
                    'id' => $id,
                    'event' => $event,
                ]);

                try {
                    $this->handleEvent($event);

                    Redis::xack($streamName, $this->group, [$id]);

                    logger()->info('EVENT ACKED', [
                        'id' => $id,
                    ]);

                } catch (\Throwable $e) {

                    logger()->error('EVENT HANDLE FAILED', [
                        'error' => $e->getMessage(),
                        'stream' => $streamName,
                        'id' => $id,
                        'event' => $event,
                    ]);

                    $this->moveToRetryOrDlq($event, $id, $streamName, $e->getMessage());
                }
            }
        }
    }

    private function processRetryMessages(array $messages): void
    {
        foreach ($messages as $id => $event) {

            $nextAttempt = (int) ($event['next_attempt_at'] ?? 0);

            if ($nextAttempt > now()->timestamp) {
                continue;
            }

            try {
                logger()->info('RETRY EVENT PROCESSING', [
                    'event_id' => $event['event_id'] ?? null,
                    'attempts' => $event['attempts'] ?? null,
                ]);

                $this->handleEvent($event);

                Redis::xdel($this->retryStream, [$id]);

            } catch (\Throwable $e) {

                logger()->error('RETRY EVENT FAILED AGAIN', [
                    'error' => $e->getMessage(),
                    'id' => $id,
                    'event' => $event,
                ]);

                $this->moveToRetryOrDlq($event, $id, $this->retryStream, $e->getMessage());
            }
        }
    }

    private function handleEvent(array $event): void
    {
        match ($event['type'] ?? null) {
            'TaskCreated' => app(TaskCreatedConsumer::class)->handle($event),
            default => logger()->warning('UNKNOWN EVENT TYPE', $event),
        };
    }

    private function moveToRetryOrDlq(
        array $event,
        string $id,
        string $streamName,
        string $error
    ): void {

        $attempts = (int) ($event['attempts'] ?? 0) + 1;
        $event['attempts'] = $attempts;

        $delay = match ($attempts) {
            1 => 5,
            2 => 10,
            3 => 30,
            4 => 60,
            default => 120,
        };

        $event['next_attempt_at'] = now()
            ->addSeconds($delay)
            ->timestamp;

        if ($attempts >= 5) {

            Redis::xadd($this->dlqStream, '*', [
                ...$event,
                'failed_at' => now()->timestamp,
                'failed_reason' => $error,
            ]);

            Redis::xack($streamName, $this->group, [$id]);

            logger()->error('EVENT MOVED TO DLQ', [
                'event_id' => $event['event_id'] ?? null,
                'attempts' => $attempts,
            ]);

            return;
        }

        Redis::xadd($this->retryStream, '*', $event);

        Redis::xack($streamName, $this->group, [$id]);

        logger()->warning('EVENT MOVED TO RETRY', [
            'event_id' => $event['event_id'] ?? null,
            'attempts' => $attempts,
            'next_attempt_at' => $event['next_attempt_at'],
        ]);
    }

    private function ensureGroupExists(): void
    {
        try {
            Redis::xgroup('CREATE', $this->stream, $this->group, '$', true);
            Redis::xgroup('CREATE', $this->retryStream, $this->group, '$', true);
            Redis::xgroup('CREATE', $this->dlqStream, $this->group, '$', true);
        } catch (\Throwable $e) {
        }
    }
}
