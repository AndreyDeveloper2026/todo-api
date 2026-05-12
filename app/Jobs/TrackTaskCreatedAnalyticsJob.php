<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TrackTaskCreatedAnalyticsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Task $task
    ) {}

    public function handle(): void
    {
        logger()->info('ANALYTICS: task created', [
            'task_id' => $this->task->id,
            'user_id' => $this->task->user_id,
            'project_id' => $this->task->project_id,
        ]);
    }
}
