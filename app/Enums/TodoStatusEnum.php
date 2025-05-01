<?php

namespace App\Enums;

enum TodoStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public static function getValues(): array
    {
        return array_map(fn ($enum) => $enum->value, self::cases());
    }
}
