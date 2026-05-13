<?php

namespace App\Infrastructure\Observability;

class EventMetrics
{
    public static function incProcessed(string $type): void
    {
        logger()->info('METRIC event_processed', ['type' => $type]);
    }

    public static function incFailed(string $type): void
    {
        logger()->info('METRIC event_failed', ['type' => $type]);
    }

    public static function incRetry(string $type): void
    {
        logger()->info('METRIC event_retry', ['type' => $type]);
    }
}
