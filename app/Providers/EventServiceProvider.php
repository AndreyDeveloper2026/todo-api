<?php

namespace App\Providers;

use App\Contracts\EventBus;
use App\Contracts\RedisEventBus;
use App\Events\TaskCreated;
use App\Jobs\SendTaskCreatedNotificationJob;
use App\Jobs\TrackTaskCreatedAnalyticsJob;
use App\Listeners\HandleTaskCreated;
use App\Listeners\SendTaskCreatedNotification;
use App\Listeners\TrackTaskCreatedAnalytics;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected array $listen = [
        TaskCreated::class => [
            SendTaskCreatedNotification::class,
            TrackTaskCreatedAnalytics::class,
            HandleTaskCreated::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            EventBus::class,
            RedisEventBus::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
