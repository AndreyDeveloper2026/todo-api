<?php

namespace App\Contracts;

use Illuminate\Support\Facades\Redis;

class RedisEventBus implements EventBus
{
    public function publish(object $event): void
    {
        Redis::publish(
            get_class($event),
            json_encode($event)
        );
    }
}
