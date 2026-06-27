<?php

declare(strict_types=1);

namespace App\Services;

final class PenitipanPromoService
{
    public function __construct(
        private readonly AppSettingsService $settings = new AppSettingsService(),
    ) {}

    /**
     * @param array<string, mixed> $pelanggan
     * @return array{promo_dipakai: bool, potongan_promo: float, eligible: bool}
     */
    public function hitung(array $pelanggan, int $lamaHari, float $subtotal): array
    {
        $minDays = (int) $this->settings->get('promo_min_days');
        $discountPercent = (int) $this->settings->get('promo_discount_percent');
        $sudahPakai = (bool) ($pelanggan['pernah_pakai_promo_penitipan'] ?? false);

        $eligible = $lamaHari > $minDays && !$sudahPakai;
        $potongan = $eligible ? round($subtotal * ($discountPercent / 100), 2) : 0.0;

        return [
            'promo_dipakai' => $eligible,
            'potongan_promo' => $potongan,
            'eligible' => $eligible,
        ];
    }

    public function isEligibleForDisplay(array $pelanggan): bool
    {
        return !(bool) ($pelanggan['pernah_pakai_promo_penitipan'] ?? false);
    }
}
