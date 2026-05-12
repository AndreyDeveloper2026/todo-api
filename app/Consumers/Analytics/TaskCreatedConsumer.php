<?php

namespace App\Consumers\Analytics;

use App\Jobs\SendTaskCreatedNotificationJob;
use App\Jobs\TrackTaskCreatedAnalyticsJob;

class TaskCreatedConsumer
{
    public function handle(array $event): void
    {
        TrackTaskCreatedAnalyticsJob::dispatch(
            $event['taskId']
        )->onQueue('analytics');

        SendTaskCreatedNotificationJob::dispatch(
            $event['taskId']
        )->onQueue('notifications');
    }
}
