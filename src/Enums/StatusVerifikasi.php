<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusVerifikasi: string
{
    case MENUNGGU = 'MENUNGGU';
    case DISETUJUI = 'DISETUJUI';
    case DITOLAK = 'DITOLAK';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::MENUNGGU->value => 'Menunggu',
            self::DISETUJUI->value => 'Disetujui',
            self::DITOLAK->value => 'Ditolak',
        ];
    }
}
