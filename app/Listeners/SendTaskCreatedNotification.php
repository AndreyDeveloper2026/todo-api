<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Jobs\SendTaskCreatedNotificationJob;

class SendTaskCreatedNotification
{
    public function handle(TaskCreated $event): void
    {
        SendTaskCreatedNotificationJob::dispatch($event->task)
            ->onQueue('notifications');
    }
}
