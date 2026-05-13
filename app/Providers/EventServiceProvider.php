<?php

namespace App\Providers;

use App\Contracts\EventBus;
use App\Infrastructure\EventBus\RedisEventBus;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
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
