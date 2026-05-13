<?php

namespace App\Infrastructure\EventBus;

use App\Events\Contracts\EventContract;
use Illuminate\Support\Facades\Redis;

class RedisEventBus
{
    private string $stream = 'events';

    public function publish(EventContract $event): void
    {
        $payload = $event->toArray();

        Redis::xadd($this->stream, '*', $payload);

        logger()->info('EVENT PUBLISHED', [
            'stream' => $this->stream,
            'event_id' => $payload['event_id'] ?? null,
            'type' => $payload['type'] ?? null,
        ]);
    }
}
