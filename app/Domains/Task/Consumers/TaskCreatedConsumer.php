<?php

namespace App\Domains\Task\Consumers;

use App\Jobs\SendTaskCreatedNotificationJob;
use App\Jobs\TrackTaskCreatedAnalyticsJob;
use App\Models\ProcessedEvent;

class TaskCreatedConsumer
{
    public function handle(array $event): void
    {
        $processed = ProcessedEvent::firstOrCreate(
            ['event_id' => $event['event_id']],
            ['processed_at' => now()]
        );

        if (! $processed->wasRecentlyCreated) {
            logger()->info('EVENT SKIPPED (duplicate)', [
                'event_id' => $event['event_id'],
            ]);

            return;
        }

        logger()->info('PROCESSING TASK CREATED', [
            'event_id' => $event['event_id'],
        ]);

        TrackTaskCreatedAnalyticsJob::dispatch($event['taskId'])
            ->onQueue('analytics');

        SendTaskCreatedNotificationJob::dispatch($event['taskId'])
            ->onQueue('notifications');

        logger()->info('EVENT MARKED PROCESSED', [
            'event_id' => $event['event_id'],
        ]);
    }
}
