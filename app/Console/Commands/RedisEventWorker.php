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
        Redis::subscribe(['App\\Events\\TaskCreated'], function ($message) {

            $data = json_decode($message, true);

            app(TaskCreatedConsumer::class)->handle($data);
        });
    }
}
