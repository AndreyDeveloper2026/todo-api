<?php

namespace App\Events\Envelope;

interface EventEnvelope
{
    public function toArray(): array;
}
