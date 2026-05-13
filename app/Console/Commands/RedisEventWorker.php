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

                /**
                 * 2. RETRY STREAM (parking lot)
                 */
                $retryMessages = Redis::xreadgroup(
                    $this->group,
                    $this->consumer,
                    ['events:retry' => '>'],
                    10,
                    1000
                );

                $all = array_merge(
                    $messages ?? [],
                    $retryMessages ?? []
                );

                if (empty($all)) {
                    continue;
                }

                foreach ($all as $streamName => $entries) {
                    foreach ($entries as $id => $fields) {

                        $event = is_array($fields) ? $fields : [];

                        logger()->info('EVENT RECEIVED', [
                            'stream' => $streamName,
                            'id' => $id,
                            'event' => $event,
                        ]);

                        try {
                            if ($streamName === 'events:retry') {
                                $next = (int) ($event['next_attempt_at'] ?? 0);

                                if ($next > now()->timestamp) {
                                    continue;
                                }

                                logger()->info('RETRY EVENT READY', [
                                    'event_id' => $event['event_id'] ?? null,
                                    'attempts' => $event['attempts'] ?? null,
                                ]);
                            }

                            $this->handleEvent($event);

                            Redis::xack($streamName, $this->group, [$id]);

                            logger()->info('EVENT ACKED', [
                                'stream' => $streamName,
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

            } catch (\Throwable $e) {

                logger()->error('STREAM LOOP ERROR', [
                    'error' => $e->getMessage(),
                ]);

                sleep(2);
            }
        }
    }

    private function handleEvent(array $event): void
    {
        match ($event['type'] ?? null) {

            'TaskCreated' =>
            app(TaskCreatedConsumer::class)->handle($event),

            default =>
            logger()->warning('UNKNOWN EVENT TYPE', $event),
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

            Redis::xadd('events:dlq', '*', [
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

        Redis::xadd('events:retry', '*', $event);

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
            Redis::xgroup('CREATE', 'events:retry', $this->group, '$', true);
            Redis::xgroup('CREATE', 'events:dlq', $this->group, '$', true);
        } catch (\Throwable $e) {
        }
    }
}
