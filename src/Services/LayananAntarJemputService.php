<?php

declare(strict_types=1);

namespace App\Services;

final class LayananAntarJemputService
{
    public function __construct(
        private readonly PelangganProfileService $profileService = new PelangganProfileService(),
        private readonly AppSettingsService $settings = new AppSettingsService(),
    ) {}

    public function hitungJarakKm(float $latPelanggan, float $lngPelanggan): float
    {
        $latPetshop = (float) $this->settings->get('petshop_lat');
        $lngPetshop = (float) $this->settings->get('petshop_lng');

        $earthRadiusKm = 6371.0;
        $latFrom = deg2rad($latPetshop);
        $latTo = deg2rad($latPelanggan);
        $latDelta = deg2rad($latPelanggan - $latPetshop);
        $lngDelta = deg2rad($lngPelanggan - $lngPetshop);

        $a = sin($latDelta / 2) ** 2
            + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c, 2);
    }

    public function hitungBiaya(float $jarakKm): float
    {
        $freeRadius = (float) $this->settings->get('pickup_free_radius_km');
        $feePerKm = (int) $this->settings->get('pickup_extra_fee_per_km');

        if ($jarakKm <= $freeRadius) {
            return 0.0;
        }

        $extraKm = (int) ceil($jarakKm - $freeRadius);

        return (float) ($extraKm * $feePerKm);
    }

    /**
     * @param array<string, mixed> $pelanggan
     * @return array{success: bool, error?: string, jarak_km?: float, biaya_antar_jemput?: float, gratis?: bool}
     */
    public function estimasiUntukPelanggan(array $pelanggan): array
    {
        if (!$this->profileService->isAddressComplete($pelanggan)) {
            return [
                'success' => false,
                'error' => 'Alamat profil belum lengkap. Lengkapi alamat dan pilih lokasi pada peta.',
            ];
        }

        $lat = (float) $pelanggan['latitude'];
        $lng = (float) $pelanggan['longitude'];
        $jarakKm = $this->hitungJarakKm($lat, $lng);
        $biaya = $this->hitungBiaya($jarakKm);
        $freeRadius = (float) $this->settings->get('pickup_free_radius_km');

        return [
            'success' => true,
            'jarak_km' => $jarakKm,
            'biaya_antar_jemput' => $biaya,
            'gratis' => $jarakKm <= $freeRadius,
        ];
    }

    /**
     * @param array<string, mixed> $pelanggan
     * @return array{jarak_km: ?float, biaya_antar_jemput: float}
     */
    public function hitungUntukBooking(string $opsiPengantaran, array $pelanggan): array
    {
        if ($opsiPengantaran !== 'ANTAR_JEMPUT') {
            return [
                'jarak_km' => null,
                'biaya_antar_jemput' => 0.0,
            ];
        }

        $estimasi = $this->estimasiUntukPelanggan($pelanggan);

        if (!$estimasi['success']) {
            throw new \InvalidArgumentException($estimasi['error'] ?? 'Alamat tidak valid.');
        }

        return [
            'jarak_km' => $estimasi['jarak_km'],
            'biaya_antar_jemput' => $estimasi['biaya_antar_jemput'],
        ];
    }
}
