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

    public function badgeClass(): string
    {
        return match ($this) {
            self::MENUNGGU_PEMBAYARAN => 'bg-amber-100 text-amber-800',
            self::MENUNGGU_VERIFIKASI => 'bg-blue-100 text-blue-800',
            self::LUNAS => 'bg-green-100 text-green-800',
            self::DIBATALKAN => 'bg-gray-100 text-gray-600',
            self::KEDALUWARSA => 'bg-red-100 text-red-800',
        };
    }
}
