<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusBookingPetCare: string
{
    case TERKONFIRMASI = 'TERKONFIRMASI';
    case SEDANG_PROSES = 'SEDANG_PROSES';
    case SELESAI = 'SELESAI';
    case DIBATALKAN = 'DIBATALKAN';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::TERKONFIRMASI->value => 'Terkonfirmasi',
            self::SEDANG_PROSES->value => 'Sedang Proses',
            self::SELESAI->value => 'Selesai',
            self::DIBATALKAN->value => 'Dibatalkan',
        ];
    }

    public function isActive(): bool
    {
        return !in_array($this, [self::SELESAI, self::DIBATALKAN], true);
    }

    public function canCancel(): bool
    {
        return $this->isActive();
    }

    public function nextStatus(): ?self
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
            self::TERKONFIRMASI => 'bg-blue-100 text-blue-800',
            self::SEDANG_PROSES => 'bg-yellow-100 text-yellow-800',
            self::SELESAI => 'bg-green-100 text-green-800',
            self::DIBATALKAN => 'bg-gray-100 text-gray-600',
        };
    }
}
