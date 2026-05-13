<?php

namespace App\Infrastructure\Observability;

class EventLogger
{
    public function received(array $event, string $stream, string $id): void
    {
        logger()->info('EVENT RECEIVED', [
            'event_id' => $event['event_id'] ?? null,
            'type' => $event['type'] ?? null,
            'stream' => $stream,
            'redis_id' => $id,
            'attempts' => $event['attempts'] ?? 0,
        ]);
    }

    public function processed(array $event): void
    {
        logger()->info('EVENT PROCESSED', [
            'event_id' => $event['event_id'] ?? null,
            'type' => $event['type'] ?? null,
        ]);
    }

    public function failed(array $event, string $error, string $stream, string $id): void
    {
        logger()->error('EVENT FAILED', [
            'event_id' => $event['event_id'] ?? null,
            'type' => $event['type'] ?? null,
            'stream' => $stream,
            'redis_id' => $id,
            'error' => $error,
            'attempts' => $event['attempts'] ?? 0,
        ]);
    }

    public function retry(array $event): void
    {
        logger()->warning('EVENT MOVED TO RETRY', [
            'event_id' => $event['event_id'] ?? null,
            'attempts' => $event['attempts'] ?? 0,
            'next_attempt_at' => $event['next_attempt_at'] ?? null,
        ]);
    }

    public function dlq(array $event): void
    {
        logger()->error('EVENT MOVED TO DLQ', [
            'event_id' => $event['event_id'] ?? null,
            'attempts' => $event['attempts'] ?? 0,
        ]);
    }
}
