<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PelangganRepository;

final class PelangganProfileService
{
    public function __construct(
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly FileUploadService $fileUpload = new FileUploadService(),
    ) {}

    /** @param array<string, mixed> $pelanggan */
    public function isAddressComplete(array $pelanggan): bool
    {
        $alamat = trim((string) ($pelanggan['alamat_lengkap'] ?? ''));

        return $alamat !== ''
            && $pelanggan['latitude'] !== null
            && $pelanggan['longitude'] !== null;
    }

    /**
     * @param array<string, mixed>|null $file
     * @return array{success: bool, errors?: array<string, string>, pelanggan?: array}
     */
    public function updateProfile(
        string $pelangganId,
        string $nama,
        string $noTelepon,
        string $alamatLengkap,
        ?float $latitude,
        ?float $longitude,
        ?array $file,
    ): array {
        $errors = [];

        if (trim($nama) === '') {
            $errors['nama'] = 'Nama wajib diisi.';
        }

        $pelanggan = $this->pelangganRepo->findById($pelangganId);

        if (!$pelanggan) {
            return ['success' => false, 'errors' => ['general' => 'Pelanggan tidak ditemukan.']];
        }

        $alamatTrimmed = trim($alamatLengkap);
        $finalLat = null;
        $finalLng = null;

        if ($alamatTrimmed !== '') {
            if ($latitude === null || $longitude === null) {
                $errors['location'] = 'Pilih lokasi pada peta OpenStreetMap.';
            } elseif (!$this->isValidCoordinate($latitude, $longitude)) {
                $errors['location'] = 'Koordinat lokasi tidak valid.';
            } else {
                $finalLat = $latitude;
                $finalLng = $longitude;
            }
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->pelangganRepo->updateProfile(
            $pelangganId,
            trim($nama),
            trim($noTelepon) !== '' ? trim($noTelepon) : null,
            $alamatTrimmed !== '' ? $alamatTrimmed : null,
            $finalLat,
            $finalLng,
        );

        if ($file !== null) {
            $upload = $this->fileUpload->upload($file, 'profil');

            if (!$upload['success']) {
                return [
                    'success' => false,
                    'errors' => ['foto_profil' => $upload['error'] ?? 'Gagal mengunggah foto.'],
                ];
            }

            $this->fileUpload->deletePublicPath($pelanggan['foto_profil_url'] ?? null);
            $this->pelangganRepo->updateFotoProfil($pelangganId, $upload['path']);
        }

        $updated = $this->pelangganRepo->findById($pelangganId);

        return [
            'success' => true,
            'pelanggan' => $updated,
        ];
    }

    private function isValidCoordinate(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180;
    }
}
