<?php

namespace App\Contracts;

interface EventBus
{
    public function publish(string $stream, array $event): void;
}
