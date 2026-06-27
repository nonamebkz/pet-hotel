<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\JenisGroomingRepository;
use function uuid;

final class JenisGroomingService
{
    public function __construct(
        private readonly JenisGroomingRepository $jenisRepo = new JenisGroomingRepository(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, jenisId?: string}
     */
    public function create(array $input): array
    {
        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $id = uuid();

        $this->jenisRepo->create(
            $id,
            $validated['nama'],
            $validated['deskripsi'],
            $validated['harga'],
            $validated['aktif'],
        );

        return ['success' => true, 'jenisId' => $id];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>}
     */
    public function update(string $id, array $input): array
    {
        $existing = $this->jenisRepo->findById($id);

        if (!$existing) {
            return ['success' => false, 'errors' => ['general' => 'Jenis grooming tidak ditemukan.']];
        }

        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $this->jenisRepo->update(
            $id,
            $validated['nama'],
            $validated['deskripsi'],
            $validated['harga'],
            $validated['aktif'],
        );

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function delete(string $id): array
    {
        if (!$this->jenisRepo->findById($id)) {
            return ['success' => false, 'error' => 'Jenis grooming tidak ditemukan.'];
        }

        if (!$this->jenisRepo->delete($id)) {
            return ['success' => false, 'error' => 'Gagal menghapus jenis grooming.'];
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
     *   aktif: bool
     * }
     */
    private function validateInput(array $input): array
    {
        $errors = [];
        $nama = trim((string) ($input['nama'] ?? ''));
        $deskripsi = trim((string) ($input['deskripsi'] ?? '')) ?: null;
        $hargaRaw = trim((string) ($input['harga'] ?? ''));
        $aktif = filter_var($input['aktif'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($nama === '') {
            $errors['nama'] = 'Nama jenis grooming wajib diisi.';
        } elseif (mb_strlen($nama) > 100) {
            $errors['nama'] = 'Nama maksimal 100 karakter.';
        }

        if ($hargaRaw === '' || !is_numeric($hargaRaw) || (float) $hargaRaw < 0) {
            $errors['harga'] = 'Harga harus angka ≥ 0.';
        }

        return [
            'errors' => $errors,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga' => (float) ($hargaRaw ?: 0),
            'aktif' => $aktif,
        ];
    }
}
