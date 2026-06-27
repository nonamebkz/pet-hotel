<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusAkun: string
{
    case AKTIF = 'AKTIF';
    case NONAKTIF = 'NONAKTIF';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::AKTIF->value => 'Aktif',
            self::NONAKTIF->value => 'Nonaktif',
        ];
    }
}
