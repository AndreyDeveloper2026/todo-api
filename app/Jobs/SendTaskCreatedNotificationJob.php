<?php

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendTaskCreatedNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $taskId
    ) {}

    public function handle(): void
    {
        $task = Task::with('user')->find($this->taskId);

        if (! $task?->user) {
            return;
        }

        $task->user->notify(
            new TaskCreatedNotification($task)
        );
    }
}
