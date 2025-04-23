<?php

namespace App\Enums;

enum GenderEnum: string
{
    case FEMALE = 'female';
    case MALE = 'male';
    case OTHERS = 'others';

    public static function getValues(): array
    {
        return array_map(fn ($enum) => $enum->value, self::cases());
    }
}
