<?php

namespace App\Infrastructure\Event;

use App\Domains\Task\Consumers\TaskCreatedConsumer;

class ConsumerRouter
{
    public function handle(array $event): void
    {
        match ($event['type'] ?? null) {

            'TaskCreated' =>
            app(TaskCreatedConsumer::class)->handle($event),

            default =>
            logger()->warning('UNKNOWN EVENT TYPE', $event),
        };
    }
}
