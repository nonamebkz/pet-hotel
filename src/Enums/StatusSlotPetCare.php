<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusSlotPetCare: string
{
    case TERSEDIA = 'TERSEDIA';
    case DITUTUP = 'DITUTUP';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::TERSEDIA->value => 'Tersedia',
            self::DITUTUP->value => 'Ditutup',
        ];
    }
}
