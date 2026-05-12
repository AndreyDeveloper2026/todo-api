<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TrackTaskCreatedAnalyticsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $taskId
    ) {}

    public function handle(): void
    {
        logger()->info('ANALYTICS', [
            'task_id' => $this->taskId,
        ]);
    }
}
