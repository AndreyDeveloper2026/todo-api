<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleTaskCreated implements ShouldQueue
{

    public int $tries = 5;
    public int $backoff = 10;

    public function handle(TaskCreated $event): void
    {
        logger()->info('START TaskCreated', [
            'task_id' => $event->task->id,
        ]);

        sleep(3);

        logger()->info('END TaskCreated', [
            'task_id' => $event->task->id,
        ]);
    }
}
