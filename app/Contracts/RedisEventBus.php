<?php

namespace App\Contracts;

use Illuminate\Support\Facades\Redis;

class RedisEventBus implements EventBus
{
    public function publish(string $stream, array $event): void
    {
        Redis::xadd(
            $stream,
            '*',
            $event
        );
    }
}
