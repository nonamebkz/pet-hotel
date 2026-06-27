<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusRefund: string
{
    case TIDAK_ADA = 'TIDAK_ADA';
    case PENDING_REFUND = 'PENDING_REFUND';
    case REFUNDED = 'REFUNDED';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::TIDAK_ADA->value => 'Tidak Ada Refund',
            self::PENDING_REFUND->value => 'Refund Sedang Diproses',
            self::REFUNDED->value => 'Refund Selesai',
        ];
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::TIDAK_ADA => 'bg-gray-100 text-gray-600',
            self::PENDING_REFUND => 'bg-amber-100 text-amber-800',
            self::REFUNDED => 'bg-green-100 text-green-800',
        };
    }
}
