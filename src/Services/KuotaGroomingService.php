<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\KuotaGroomingRepository;
use function uuid;

final class KuotaGroomingService
{
    public function __construct(
        private readonly KuotaGroomingRepository $kuotaRepo = new KuotaGroomingRepository(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, kuotaId?: string}
     */
    public function create(array $input): array
    {
        $tanggal = trim((string) ($input['tanggal'] ?? ''));
        $slotMaksimalRaw = trim((string) ($input['slot_maksimal'] ?? ''));
        $errors = [];

        if ($tanggal === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $errors['tanggal'] = 'Tanggal tidak valid.';
        } elseif ($tanggal < date('Y-m-d')) {
            $errors['tanggal'] = 'Tanggal tidak boleh di masa lalu.';
        }

        if ($slotMaksimalRaw === '' || !ctype_digit($slotMaksimalRaw) || (int) $slotMaksimalRaw <= 0) {
            $errors['slot_maksimal'] = 'Slot maksimal harus bilangan bulat positif.';
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        if ($this->kuotaRepo->existsByDate($tanggal)) {
            return ['success' => false, 'errors' => ['tanggal' => 'Kuota untuk tanggal ini sudah ada.']];
        }

        $id = uuid();
        $this->kuotaRepo->create($id, $tanggal, (int) $slotMaksimalRaw);

        return ['success' => true, 'kuotaId' => $id];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>}
     */
    public function update(string $id, array $input): array
    {
        $kuota = $this->kuotaRepo->findById($id);

        if (!$kuota) {
            return ['success' => false, 'errors' => ['general' => 'Kuota tidak ditemukan.']];
        }

        $slotMaksimalRaw = trim((string) ($input['slot_maksimal'] ?? ''));

        if ($slotMaksimalRaw === '' || !ctype_digit($slotMaksimalRaw) || (int) $slotMaksimalRaw <= 0) {
            return ['success' => false, 'errors' => ['slot_maksimal' => 'Slot maksimal harus bilangan bulat positif.']];
        }

        $slotMaksimal = (int) $slotMaksimalRaw;

        if ($slotMaksimal < (int) $kuota['slot_terisi']) {
            return [
                'success' => false,
                'errors' => ['slot_maksimal' => 'Slot maksimal tidak boleh kurang dari slot terisi.'],
            ];
        }

        $this->kuotaRepo->updateSlotMaksimal($id, $slotMaksimal);

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function delete(string $id): array
    {
        $kuota = $this->kuotaRepo->findById($id);

        if (!$kuota) {
            return ['success' => false, 'error' => 'Kuota tidak ditemukan.'];
        }

        if ((int) $kuota['slot_terisi'] > 0) {
            return ['success' => false, 'error' => 'Kuota masih memiliki booking aktif.'];
        }

        if (!$this->kuotaRepo->delete($id)) {
            return ['success' => false, 'error' => 'Gagal menghapus kuota.'];
        }

        return ['success' => true];
    }
}
