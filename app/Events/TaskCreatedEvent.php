<?php

namespace App\Events;

use App\Events\Contracts\EventContract;

class TaskCreatedEvent implements EventContract
{
    public function __construct(
        public int $taskId,
        public int $userId,
        public int $projectId,
    ) {}

    public function type(): string
    {
        return 'TaskCreated';
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'taskId' => $this->taskId,
            'userId' => $this->userId,
            'projectId' => $this->projectId,
        ];
    }
}
