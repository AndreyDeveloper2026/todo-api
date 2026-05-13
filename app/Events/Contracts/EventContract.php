<?php

namespace App\Events\Contracts;

interface EventContract
{
    public function type(): string;

    public function toArray(): array;
}
