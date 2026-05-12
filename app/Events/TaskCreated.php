<?php

namespace App\Events;


use Illuminate\Foundation\Events\Dispatchable;

class TaskCreated
{
    use Dispatchable;

    public function __construct(
        public int $taskId,
        public int $userId,
        public int $projectId,
    ) {}
}
