<?php

namespace App\Utils;

use Exception;

class StatusConverter
{
    public const TODO_STATUS = 'todo';
    public const DONE_STATUS = 'done';
    public const STATUSES = [self::TODO_STATUS => false, self::DONE_STATUS => true];

    public static function toString(bool $status): string
    {
        return $status ? self::DONE_STATUS : self::TODO_STATUS;
    }

    public static function toBoolean(string $status): bool
    {
        return self::STATUSES[$status] ?? false;
    }
}
