<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusBookingGrooming: string
{
    case MENUNGGU_KONFIRMASI = 'MENUNGGU_KONFIRMASI';
    case MENUNGGU_PEMBAYARAN = 'MENUNGGU_PEMBAYARAN';
    case MENUNGGU_VERIFIKASI_BUKTI = 'MENUNGGU_VERIFIKASI_BUKTI';
    case TERKONFIRMASI = 'TERKONFIRMASI';
    case SEDANG_PROSES = 'SEDANG_PROSES';
    case SELESAI = 'SELESAI';
    case DIBATALKAN = 'DIBATALKAN';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::MENUNGGU_KONFIRMASI->value => 'Menunggu Konfirmasi Jam',
            self::MENUNGGU_PEMBAYARAN->value => 'Menunggu Pembayaran',
            self::MENUNGGU_VERIFIKASI_BUKTI->value => 'Menunggu Verifikasi Bukti',
            self::TERKONFIRMASI->value => 'Terkonfirmasi',
            self::SEDANG_PROSES->value => 'Sedang Proses',
            self::SELESAI->value => 'Selesai',
            self::DIBATALKAN->value => 'Dibatalkan',
        ];
    }

    public function canCancelByPelanggan(): bool
    {
        return in_array($this, [
            self::MENUNGGU_KONFIRMASI,
            self::MENUNGGU_PEMBAYARAN,
            self::MENUNGGU_VERIFIKASI_BUKTI,
        ], true);
    }

    public function canCancelByStaffWithRefund(): bool
    {
        return in_array($this, [
            self::MENUNGGU_VERIFIKASI_BUKTI,
            self::TERKONFIRMASI,
            self::SEDANG_PROSES,
        ], true);
    }

    public function nextOperationalStatus(): ?self
    {
        return match ($this) {
            self::TERKONFIRMASI => self::SEDANG_PROSES,
            self::SEDANG_PROSES => self::SELESAI,
            default => null,
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::MENUNGGU_KONFIRMASI => 'bg-gray-100 text-gray-700',
            self::MENUNGGU_PEMBAYARAN => 'bg-amber-100 text-amber-800',
            self::MENUNGGU_VERIFIKASI_BUKTI => 'bg-purple-100 text-purple-800',
            self::TERKONFIRMASI => 'bg-blue-100 text-blue-800',
            self::SEDANG_PROSES => 'bg-yellow-100 text-yellow-800',
            self::SELESAI => 'bg-green-100 text-green-800',
            self::DIBATALKAN => 'bg-gray-100 text-gray-500',
        };
    }
}
