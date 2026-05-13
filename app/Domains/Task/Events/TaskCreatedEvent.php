<?php

namespace App\Domains\Task\Events;

use App\Events\Contracts\EventContract;
use Illuminate\Support\Str;

class TaskCreatedEvent implements EventContract
{
    private string $eventId;

    public function __construct(
        public int $taskId,
        public int $userId,
        public int $projectId,
    ) {
        $this->eventId = (string) Str::uuid();
    }

    public function id(): string
    {
        return $this->eventId;
    }

    public function type(): string
    {
        return 'TaskCreated';
    }

    public function toArray(): array
    {
        return [
            'event_id' => $this->id(),
            'type' => $this->type(),
            'version' => 1,

            'payload' => [
                'taskId' => $this->taskId,
                'userId' => $this->userId,
                'projectId' => $this->projectId,
            ],

            'attempts' => 0,
            'next_attempt_at' => null,
        ];
    }
}
