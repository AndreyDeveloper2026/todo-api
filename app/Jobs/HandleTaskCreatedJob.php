<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleTaskCreatedJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public int $backoff = 10;
    public int $timeout = 30;

    public function __construct(
        public Task $task
    ) {}


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('START TaskCreated', [
            'task_id' => $this->task->id,
        ]);

        sleep(3);

        logger()->info('END TaskCreated', [
            'task_id' => $this->task->id,
        ]);
    }
}
