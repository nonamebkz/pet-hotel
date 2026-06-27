<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPerpanjanganPenitipan: string
{
    case MENUNGGU_KONFIRMASI = 'MENUNGGU_KONFIRMASI';
    case MENUNGGU_PEMBAYARAN = 'MENUNGGU_PEMBAYARAN';
    case MENUNGGU_VERIFIKASI_BUKTI = 'MENUNGGU_VERIFIKASI_BUKTI';
    case DISETUJUI = 'DISETUJUI';
    case DITOLAK = 'DITOLAK';
    case DIBATALKAN = 'DIBATALKAN';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::MENUNGGU_KONFIRMASI->value => 'Menunggu Konfirmasi',
            self::MENUNGGU_PEMBAYARAN->value => 'Menunggu Pembayaran',
            self::MENUNGGU_VERIFIKASI_BUKTI->value => 'Menunggu Verifikasi Bukti',
            self::DISETUJUI->value => 'Disetujui',
            self::DITOLAK->value => 'Ditolak',
            self::DIBATALKAN->value => 'Dibatalkan',
        ];
    }

    public function canCancelByPelanggan(): bool
    {
        return in_array($this, [
            self::MENUNGGU_KONFIRMASI,
            self::MENUNGGU_PEMBAYARAN,
        ], true);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::MENUNGGU_KONFIRMASI => 'bg-yellow-100 text-yellow-800',
            self::MENUNGGU_PEMBAYARAN => 'bg-orange-100 text-orange-800',
            self::MENUNGGU_VERIFIKASI_BUKTI => 'bg-blue-100 text-blue-800',
            self::DISETUJUI => 'bg-green-100 text-green-800',
            self::DITOLAK => 'bg-red-100 text-red-800',
            self::DIBATALKAN => 'bg-gray-100 text-gray-800',
        };
    }
}
