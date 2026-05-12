<?php

namespace App\Console\Commands;

use App\Consumers\Analytics\TaskCreatedConsumer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisEventWorker extends Command
{
    protected $signature = 'events:consume';

    protected $description = 'Consume Redis events';

    public function handle(): void
    {
        $this->info('Redis event worker started...');

        while (true) {
            try {
                Redis::subscribe(['events'], function ($message) {

                    try {
                        $data = json_decode($message, true);

                        if (!is_array($data)) {
                            logger()->error('INVALID EVENT PAYLOAD', [
                                'payload' => $message,
                            ]);
                            return;
                        }

                        app(TaskCreatedConsumer::class)->handle($data);

                    } catch (\Throwable $e) {
                        logger()->error('EVENT HANDLING ERROR', [
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'payload' => $message,
                        ]);
                    }
                });

            } catch (\Throwable $e) {
                logger()->error('REDIS SUBSCRIBE CRASHED', [
                    'message' => $e->getMessage(),
                ]);

                $this->warn('Redis connection lost. Reconnecting in 2s...');

                sleep(2);
            }
        }
    }
}
