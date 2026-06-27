<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PaketPenitipanRepository;
use function uuid;

final class PaketPenitipanService
{
    public function __construct(
        private readonly PaketPenitipanRepository $paketRepo = new PaketPenitipanRepository(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, paketId?: string}
     */
    public function create(array $input): array
    {
        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $id = uuid();
        $this->paketRepo->create(
            $id,
            $validated['nama'],
            $validated['harga_per_hari'],
            $validated['deskripsi'],
            $validated['aktif'],
        );

        return ['success' => true, 'paketId' => $id];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>}
     */
    public function update(string $id, array $input): array
    {
        if (!$this->paketRepo->findById($id)) {
            return ['success' => false, 'errors' => ['general' => 'Paket tidak ditemukan.']];
        }

        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $this->paketRepo->update(
            $id,
            $validated['nama'],
            $validated['harga_per_hari'],
            $validated['deskripsi'],
            $validated['aktif'],
        );

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function delete(string $id): array
    {
        if (!$this->paketRepo->findById($id)) {
            return ['success' => false, 'error' => 'Paket tidak ditemukan.'];
        }

        if (!$this->paketRepo->delete($id)) {
            return ['success' => false, 'error' => 'Gagal menghapus paket.'];
        }

        return ['success' => true];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   errors: array<string, string>,
     *   nama: string,
     *   deskripsi: ?string,
     *   harga_per_hari: float,
     *   aktif: bool
     * }
     */
    private function validateInput(array $input): array
    {
        $errors = [];
        $nama = trim((string) ($input['nama'] ?? ''));
        $deskripsi = trim((string) ($input['deskripsi'] ?? '')) ?: null;
        $hargaRaw = trim((string) ($input['harga_per_hari'] ?? ''));
        $aktif = filter_var($input['aktif'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($nama === '') {
            $errors['nama'] = 'Nama paket wajib diisi.';
        }

        if ($hargaRaw === '' || !is_numeric($hargaRaw) || (float) $hargaRaw < 0) {
            $errors['harga_per_hari'] = 'Harga per hari harus angka ≥ 0.';
        }

        return [
            'errors' => $errors,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga_per_hari' => (float) ($hargaRaw ?: 0),
            'aktif' => $aktif,
        ];
    }
}
