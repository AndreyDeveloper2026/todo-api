<?php

namespace App\Events\Contracts;

interface EventContract
{
    public function id(): string;

    public function type(): string;

    public function toArray(): array;
}
