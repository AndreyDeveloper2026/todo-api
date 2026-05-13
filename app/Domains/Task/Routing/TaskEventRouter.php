<?php

namespace App\Domains\Task\Routing;

use App\Domains\Task\Consumers\TaskCreatedConsumer;

class TaskEventRouter
{
    public function route(array $event): void
    {
        match ($event['type'] ?? null) {
            'TaskCreated' => $this->taskCreated($event),
            default => logger()->warning('UNKNOWN EVENT', $event),
        };
    }

    private function taskCreated(array $event): void
    {
        app(TaskCreatedConsumer::class)->handle($event);
    }
}
