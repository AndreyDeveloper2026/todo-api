<?php

namespace App\Domains\Task\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrackTaskCreatedAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $taskId
    ) {
    }

    public function handle(): void
    {
        logger()->info('ANALYTICS TRACKED', [
            'task_id' => $this->taskId,
        ]);
    }
}
