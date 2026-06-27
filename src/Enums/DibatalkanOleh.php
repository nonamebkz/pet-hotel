<?php

declare(strict_types=1);

namespace App\Enums;

enum DibatalkanOleh: string
{
    case PELANGGAN = 'PELANGGAN';
    case STAFF = 'STAFF';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::PELANGGAN->value => 'Pelanggan',
            self::STAFF->value => 'Staff',
        ];
    }
}
