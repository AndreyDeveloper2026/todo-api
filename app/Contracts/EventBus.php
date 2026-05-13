<?php

namespace App\Contracts;

use App\Events\Contracts\EventContract;

interface EventBus
{
    public function publish(EventContract $event): void;
}
