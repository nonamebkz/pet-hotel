<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\KamarPenitipanRepository;
use function uuid;

final class KamarPenitipanService
{
    public function __construct(
        private readonly KamarPenitipanRepository $kamarRepo = new KamarPenitipanRepository(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, kamarId?: string}
     */
    public function create(array $input): array
    {
        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $id = uuid();
        $this->kamarRepo->create($id, $validated['nama_kamar'], $validated['kapasitas'], $validated['aktif']);

        return ['success' => true, 'kamarId' => $id];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>}
     */
    public function update(string $id, array $input): array
    {
        if (!$this->kamarRepo->findById($id)) {
            return ['success' => false, 'errors' => ['general' => 'Kamar tidak ditemukan.']];
        }

        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $this->kamarRepo->update($id, $validated['nama_kamar'], $validated['kapasitas'], $validated['aktif']);

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function delete(string $id): array
    {
        if (!$this->kamarRepo->findById($id)) {
            return ['success' => false, 'error' => 'Kamar tidak ditemukan.'];
        }

        if (!$this->kamarRepo->delete($id)) {
            return ['success' => false, 'error' => 'Gagal menghapus kamar.'];
        }

        return ['success' => true];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{errors: array<string, string>, nama_kamar: string, kapasitas: int, aktif: bool}
     */
    private function validateInput(array $input): array
    {
        $errors = [];
        $nama = trim((string) ($input['nama_kamar'] ?? ''));
        $kapasitasRaw = trim((string) ($input['kapasitas'] ?? ''));
        $aktif = filter_var($input['aktif'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($nama === '') {
            $errors['nama_kamar'] = 'Nama kamar wajib diisi.';
        }

        if ($kapasitasRaw === '' || !ctype_digit($kapasitasRaw) || (int) $kapasitasRaw <= 0) {
            $errors['kapasitas'] = 'Kapasitas harus bilangan bulat positif.';
        }

        return [
            'errors' => $errors,
            'nama_kamar' => $nama,
            'kapasitas' => (int) ($kapasitasRaw ?: 0),
            'aktif' => $aktif,
        ];
    }
}
