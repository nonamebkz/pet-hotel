<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPembayaran: string
{
    case MENUNGGU_PEMBAYARAN = 'MENUNGGU_PEMBAYARAN';
    case MENUNGGU_VERIFIKASI = 'MENUNGGU_VERIFIKASI';
    case LUNAS = 'LUNAS';
    case DIBATALKAN = 'DIBATALKAN';
    case KEDALUWARSA = 'KEDALUWARSA';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::MENUNGGU_PEMBAYARAN->value => 'Menunggu Pembayaran',
            self::MENUNGGU_VERIFIKASI->value => 'Menunggu Verifikasi',
            self::LUNAS->value => 'Lunas',
            self::DIBATALKAN->value => 'Dibatalkan',
            self::KEDALUWARSA->value => 'Kedaluwarsa',
        ];
    }
}
