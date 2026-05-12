<?php

namespace App\Console\Commands;

use App\Consumers\Analytics\TaskCreatedConsumer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisEventWorker extends Command
{
    protected $signature = 'events:consume';

    protected $description = 'Consume Redis events';

    public function handle(): void
    {
        $stream = 'events';
        $group = 'default-group';
        $consumer = 'consumer-1';

        try {
            Redis::xgroup('CREATE', $stream, $group, '$', true);
        } catch (\Throwable $e) {
        }

        while (true) {

            try {
                $messages = Redis::xreadgroup(
                    $group,
                    $consumer,
                    [$stream => '>'],
                    10,
                    1000
                );

                if (empty($messages)) {
                    continue;
                }

                foreach ($messages as $streamName => $entries) {

                    foreach ($entries as $id => $fields) {

                        try {
                            $event = is_array($fields) ? $fields : [];

                            logger()->info('EVENT RECEIVED', [
                                'event' => $event,
                                'id' => $id,
                            ]);

                            match ($event['type'] ?? null) {
                                'TaskCreated' => app(TaskCreatedConsumer::class)->handle($event),
                                default => null,
                            };

                            Redis::xack($stream, $group, [$id]);

                        } catch (\Throwable $e) {

                            logger()->error('EVENT HANDLE ERROR', [
                                'error' => $e->getMessage(),
                                'event' => $fields,
                                'id' => $id,
                            ]);
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
}
