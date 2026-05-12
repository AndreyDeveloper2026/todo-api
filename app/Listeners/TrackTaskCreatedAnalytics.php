<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Jobs\TrackTaskCreatedAnalyticsJob;

class TrackTaskCreatedAnalytics
{
    public function handle(TaskCreated $event): void
    {
        TrackTaskCreatedAnalyticsJob::dispatch($event->task)
            ->onQueue('analytics');
    }
}
