<?php

namespace App\Contracts;

use App\Events\Contracts\EventContract;
use Illuminate\Support\Facades\Redis;

class RedisEventBus implements EventBus
{
    public function publish(EventContract $event): void
    {
        logger()->info('4 EVENT BUS CALLED', [
            'type' => $event->type(),
        ]);

        logger()->info('REDIS CONNECTION DEBUG', [
            'default_connection' => config('database.redis.default'),
            'host' => config('database.redis.connections.default.host'),
            'db' => config('database.redis.connections.default.database'),
        ]);

        $id = Redis::xadd('events', '*', $event->toArray());

        logger()->info('5 EVENT PUSHED TO REDIS', [
            'redis_id' => $id,
        ]);
    }
}
