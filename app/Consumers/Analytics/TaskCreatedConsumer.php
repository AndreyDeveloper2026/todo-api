<?php

namespace App\Consumers\Analytics;

use App\Jobs\TrackTaskCreatedAnalyticsJob;

class TaskCreatedConsumer
{
    public function handle(array $event): void
    {
        TrackTaskCreatedAnalyticsJob::dispatch(
            $event['taskId']
        )->onQueue('analytics');
    }
}
