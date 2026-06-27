<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusLayanan;
use App\Repositories\LayananPetCareRepository;
use function uuid;

final class LayananPetCareService
{
    public function __construct(
        private readonly LayananPetCareRepository $layananRepo = new LayananPetCareRepository(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, layananId?: string}
     */
    public function create(array $input): array
    {
        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $id = uuid();

        $this->layananRepo->create(
            $id,
            $validated['nama'],
            $validated['deskripsi'],
            $validated['harga'],
            $validated['estimasi_durasi_menit'],
            $validated['status'],
        );

        return ['success' => true, 'layananId' => $id];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>}
     */
    public function update(string $id, array $input): array
    {
        $existing = $this->layananRepo->findById($id);

        if (!$existing || $existing['deleted_at'] !== null) {
            return ['success' => false, 'errors' => ['general' => 'Layanan tidak ditemukan.']];
        }

        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $this->layananRepo->update(
            $id,
            $validated['nama'],
            $validated['deskripsi'],
            $validated['harga'],
            $validated['estimasi_durasi_menit'],
            $validated['status'],
        );

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function delete(string $id): array
    {
        $existing = $this->layananRepo->findById($id);

        if (!$existing || $existing['deleted_at'] !== null) {
            return ['success' => false, 'error' => 'Layanan tidak ditemukan.'];
        }

        if (!$this->layananRepo->softDelete($id)) {
            return ['success' => false, 'error' => 'Gagal menghapus layanan.'];
        }

        return ['success' => true];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   errors: array<string, string>,
     *   nama: string,
     *   deskripsi: ?string,
     *   harga: float,
     *   estimasi_durasi_menit: int,
     *   status: string
     * }
     */
    private function validateInput(array $input): array
    {
        $errors = [];
        $nama = trim((string) ($input['nama'] ?? ''));
        $deskripsi = trim((string) ($input['deskripsi'] ?? '')) ?: null;
        $hargaRaw = trim((string) ($input['harga'] ?? ''));
        $durasiRaw = trim((string) ($input['estimasi_durasi_menit'] ?? ''));
        $status = trim((string) ($input['status'] ?? StatusLayanan::AKTIF->value));

        if ($nama === '') {
            $errors['nama'] = 'Nama layanan wajib diisi.';
        } elseif (mb_strlen($nama) > 150) {
            $errors['nama'] = 'Nama layanan maksimal 150 karakter.';
        }

        if ($hargaRaw === '' || !is_numeric($hargaRaw) || (float) $hargaRaw < 0) {
            $errors['harga'] = 'Harga harus angka ≥ 0.';
        }

        if ($durasiRaw === '' || !ctype_digit($durasiRaw) || (int) $durasiRaw <= 0) {
            $errors['estimasi_durasi_menit'] = 'Estimasi durasi harus bilangan bulat positif (menit).';
        }

        if (!in_array($status, [StatusLayanan::AKTIF->value, StatusLayanan::NONAKTIF->value], true)) {
            $errors['status'] = 'Status tidak valid.';
        }

        return [
            'errors' => $errors,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga' => (float) ($hargaRaw ?: 0),
            'estimasi_durasi_menit' => (int) ($durasiRaw ?: 0),
            'status' => $status,
        ];
    }
}
