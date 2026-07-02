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
     * @return array{success: bool, errors?: array<string, string>, created?: int, skipped?: int}
     */
    public function createFromInput(array $input): array
    {
        $mode = trim((string) ($input['mode'] ?? 'single'));

        if ($mode === 'range') {
            return $this->createRange($input);
        }

        if ($mode === 'recurring') {
            return $this->createRecurring($input);
        }

        return $this->create($input);
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, created?: int, skipped?: int}
     */
    public function createRange(array $input): array
    {
        $mulai = trim((string) ($input['tanggal_mulai'] ?? ''));
        $akhir = trim((string) ($input['tanggal_akhir'] ?? ''));
        $slotMaksimalRaw = trim((string) ($input['slot_maksimal'] ?? ''));
        $errors = [];

        if ($mulai === '' || $akhir === '' || $mulai > $akhir) {
            $errors['tanggal_mulai'] = 'Rentang tanggal tidak valid.';
        }

        if ($slotMaksimalRaw === '' || !ctype_digit($slotMaksimalRaw) || (int) $slotMaksimalRaw <= 0) {
            $errors['slot_maksimal'] = 'Slot maksimal harus bilangan bulat positif.';
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $created = 0;
        $skipped = 0;
        $current = $mulai;

        while ($current <= $akhir) {
            if ($current < date('Y-m-d')) {
                $current = date('Y-m-d', strtotime($current . ' +1 day'));
                continue;
            }

            if ($this->kuotaRepo->existsByDate($current)) {
                $skipped++;
            } else {
                $this->kuotaRepo->create(uuid(), $current, (int) $slotMaksimalRaw);
                $created++;
            }

            $current = date('Y-m-d', strtotime($current . ' +1 day'));
        }

        if ($created === 0) {
            return ['success' => false, 'errors' => ['general' => 'Tidak ada kuota baru dibuat. Semua tanggal sudah ada atau di masa lalu.']];
        }

        return ['success' => true, 'created' => $created, 'skipped' => $skipped];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, created?: int, skipped?: int}
     */
    public function createRecurring(array $input): array
    {
        $mulai = trim((string) ($input['tanggal_mulai'] ?? date('Y-m-d')));
        $weeks = max(1, min(12, (int) ($input['minggu'] ?? 4)));
        $days = $input['hari'] ?? [];
        $slotMaksimalRaw = trim((string) ($input['slot_maksimal'] ?? ''));

        if (!is_array($days) || $days === []) {
            return ['success' => false, 'errors' => ['hari' => 'Pilih minimal satu hari.']];
        }

        if ($slotMaksimalRaw === '' || !ctype_digit($slotMaksimalRaw) || (int) $slotMaksimalRaw <= 0) {
            return ['success' => false, 'errors' => ['slot_maksimal' => 'Slot maksimal harus bilangan bulat positif.']];
        }

        $allowedDays = array_map('intval', $days);
        $end = date('Y-m-d', strtotime($mulai . ' +' . ($weeks * 7) . ' days'));
        $created = 0;
        $skipped = 0;
        $current = $mulai;

        while ($current <= $end) {
            if ($current < date('Y-m-d')) {
                $current = date('Y-m-d', strtotime($current . ' +1 day'));
                continue;
            }

            $dayOfWeek = (int) date('N', strtotime($current));

            if (!in_array($dayOfWeek, $allowedDays, true)) {
                $current = date('Y-m-d', strtotime($current . ' +1 day'));
                continue;
            }

            if ($this->kuotaRepo->existsByDate($current)) {
                $skipped++;
            } else {
                $this->kuotaRepo->create(uuid(), $current, (int) $slotMaksimalRaw);
                $created++;
            }

            $current = date('Y-m-d', strtotime($current . ' +1 day'));
        }

        if ($created === 0) {
            return ['success' => false, 'errors' => ['general' => 'Tidak ada kuota baru dibuat untuk pola recurring.']];
        }

        return ['success' => true, 'created' => $created, 'skipped' => $skipped];
    }

    /**
     * @param list<string> $ids
     * @return array{success: bool, deleted?: int, error?: string}
     */
    public function bulkDelete(array $ids): array
    {
        $deleted = 0;

        foreach ($ids as $id) {
            $result = $this->delete((string) $id);

            if ($result['success']) {
                $deleted++;
            }
        }

        if ($deleted === 0) {
            return ['success' => false, 'error' => 'Tidak ada kuota yang dapat dihapus.'];
        }

        return ['success' => true, 'deleted' => $deleted];
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
