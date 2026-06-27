<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusSlotPetCare;
use App\Repositories\BookingPetCareRepository;
use App\Repositories\KuotaPetCareRepository;
use function uuid;

final class KuotaPetCareService
{
    public function __construct(
        private readonly KuotaPetCareRepository $kuotaRepo = new KuotaPetCareRepository(),
        private readonly BookingPetCareRepository $bookingRepo = new BookingPetCareRepository(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, kuotaId?: string}
     */
    public function create(array $input): array
    {
        $validated = $this->validateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        if ($this->kuotaRepo->existsByDateAndTime($validated['tanggal'], $validated['slot_waktu'])) {
            return ['success' => false, 'errors' => ['slot_waktu' => 'Jadwal bentrok — slot sudah ada untuk tanggal ini.']];
        }

        $id = uuid();

        try {
            $this->kuotaRepo->create($id, $validated['tanggal'], $validated['slot_waktu']);

            return ['success' => true, 'kuotaId' => $id];
        } catch (\Throwable) {
            return ['success' => false, 'errors' => ['general' => 'Gagal menambah slot.']];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function close(string $id): array
    {
        $kuota = $this->kuotaRepo->findById($id);

        if (!$kuota) {
            return ['success' => false, 'error' => 'Slot tidak ditemukan.'];
        }

        if (!$this->kuotaRepo->setStatus($id, StatusSlotPetCare::DITUTUP->value)) {
            return ['success' => false, 'error' => 'Gagal menutup slot.'];
        }

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function open(string $id): array
    {
        $kuota = $this->kuotaRepo->findById($id);

        if (!$kuota) {
            return ['success' => false, 'error' => 'Slot tidak ditemukan.'];
        }

        if (!$this->kuotaRepo->setStatus($id, StatusSlotPetCare::TERSEDIA->value)) {
            return ['success' => false, 'error' => 'Gagal membuka slot.'];
        }

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function delete(string $id): array
    {
        $kuota = $this->kuotaRepo->findById($id);

        if (!$kuota) {
            return ['success' => false, 'error' => 'Slot tidak ditemukan.'];
        }

        if ($this->bookingRepo->hasActiveBookingForSlot($id)) {
            return ['success' => false, 'error' => 'Slot masih memiliki booking aktif. Batalkan booking terlebih dahulu.'];
        }

        if (!$this->kuotaRepo->delete($id)) {
            return ['success' => false, 'error' => 'Gagal menghapus slot.'];
        }

        return ['success' => true];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{errors: array<string, string>, tanggal: string, slot_waktu: string}
     */
    private function validateInput(array $input): array
    {
        $errors = [];
        $tanggal = trim((string) ($input['tanggal'] ?? ''));
        $slotWaktu = trim((string) ($input['slot_waktu'] ?? ''));

        if ($tanggal === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $errors['tanggal'] = 'Tanggal tidak valid.';
        } elseif ($tanggal < date('Y-m-d')) {
            $errors['tanggal'] = 'Tanggal tidak boleh di masa lalu.';
        }

        if ($slotWaktu === '' || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $slotWaktu)) {
            $errors['slot_waktu'] = 'Waktu slot tidak valid.';
        } else {
            if (strlen($slotWaktu) === 5) {
                $slotWaktu .= ':00';
            }
        }

        return [
            'errors' => $errors,
            'tanggal' => $tanggal,
            'slot_waktu' => $slotWaktu,
        ];
    }
}
