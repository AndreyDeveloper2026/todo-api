<?php

namespace App\Domains\Task\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTaskCreatedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $taskId
    ) {
    }

    public function handle(): void
    {
        logger()->info('NOTIFICATION SENT', [
            'task_id' => $this->taskId,
        ]);

        // позже:
        // email
        // websocket
        // push
        // notification microservice
    }
}
