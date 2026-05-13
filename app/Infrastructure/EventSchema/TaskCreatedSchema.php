<?php

namespace App\Infrastructure\EventSchema;

class TaskCreatedSchema
{
    public const VERSION = 1;

    public static function validate(array $event): bool
    {
        return isset($event['taskId'], $event['userId']);
    }
}
