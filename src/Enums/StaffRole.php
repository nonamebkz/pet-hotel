<?php

declare(strict_types=1);

namespace App\Enums;

enum StaffRole: string
{
    case STAFF = 'STAFF';
    case OWNER = 'OWNER';

    public function label(): string
    {
        return match ($this) {
            self::STAFF => 'Staff',
            self::OWNER => 'Owner',
        };
    }
}
