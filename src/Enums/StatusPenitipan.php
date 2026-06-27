<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPenitipan: string
{
    case MENUNGGU_KONFIRMASI = 'MENUNGGU_KONFIRMASI';
    case MENUNGGU_PEMBAYARAN = 'MENUNGGU_PEMBAYARAN';
    case MENUNGGU_VERIFIKASI_BUKTI = 'MENUNGGU_VERIFIKASI_BUKTI';
    case CHECK_IN = 'CHECK_IN';
    case SEDANG_DITITIPKAN = 'SEDANG_DITITIPKAN';
    case CHECK_OUT = 'CHECK_OUT';
    case DIBATALKAN = 'DIBATALKAN';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::MENUNGGU_KONFIRMASI->value => 'Menunggu Konfirmasi',
            self::MENUNGGU_PEMBAYARAN->value => 'Menunggu Pembayaran',
            self::MENUNGGU_VERIFIKASI_BUKTI->value => 'Menunggu Verifikasi Bukti',
            self::CHECK_IN->value => 'Check-in',
            self::SEDANG_DITITIPKAN->value => 'Sedang Dititipkan',
            self::CHECK_OUT->value => 'Check-out',
            self::DIBATALKAN->value => 'Dibatalkan',
        ];
    }

    public function displayLabel(bool $transaksiLunas = false): string
    {
        if ($this === self::MENUNGGU_VERIFIKASI_BUKTI && $transaksiLunas) {
            return 'Pembayaran Lunas — Menunggu Check-in';
        }

        return self::labels()[$this->value];
    }

    public function canCancelByPelanggan(bool $transaksiLunas = false): bool
    {
        if ($transaksiLunas) {
            return false;
        }

        return in_array($this, [
            self::MENUNGGU_KONFIRMASI,
            self::MENUNGGU_PEMBAYARAN,
            self::MENUNGGU_VERIFIKASI_BUKTI,
        ], true);
    }

    public function canCancelByStaffWithRefund(bool $transaksiLunas = false): bool
    {
        if (!$transaksiLunas) {
            return false;
        }

        return in_array($this, [
            self::MENUNGGU_VERIFIKASI_BUKTI,
            self::CHECK_IN,
            self::SEDANG_DITITIPKAN,
        ], true);
    }

    public function canRequestPerpanjangan(bool $transaksiLunas = false): bool
    {
        if (!$transaksiLunas) {
            return false;
        }

        return in_array($this, [self::CHECK_IN, self::SEDANG_DITITIPKAN], true);
    }

    public function canCheckIn(bool $transaksiLunas = false): bool
    {
        return $this === self::MENUNGGU_VERIFIKASI_BUKTI && $transaksiLunas;
    }

    public function nextOperationalStatus(): ?self
    {
        return match ($this) {
            self::CHECK_IN => self::SEDANG_DITITIPKAN,
            self::SEDANG_DITITIPKAN => self::CHECK_OUT,
            default => null,
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::MENUNGGU_KONFIRMASI => 'bg-yellow-100 text-yellow-800',
            self::MENUNGGU_PEMBAYARAN => 'bg-orange-100 text-orange-800',
            self::MENUNGGU_VERIFIKASI_BUKTI => 'bg-blue-100 text-blue-800',
            self::CHECK_IN => 'bg-indigo-100 text-indigo-800',
            self::SEDANG_DITITIPKAN => 'bg-purple-100 text-purple-800',
            self::CHECK_OUT => 'bg-green-100 text-green-800',
            self::DIBATALKAN => 'bg-red-100 text-red-800',
        };
    }
}
