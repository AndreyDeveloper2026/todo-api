<?php

namespace App\Contracts;

interface EventBus
{
    public function publish(object $event): void;
}
