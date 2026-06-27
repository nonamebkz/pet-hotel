<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Enums\DibatalkanOleh;
use App\Enums\JenisNotifikasi;
use App\Enums\StatusBookingPetCare;
use App\Enums\StatusSlotPetCare;
use App\Repositories\BookingPetCareRepository;
use App\Repositories\KucingRepository;
use App\Repositories\KuotaPetCareRepository;
use App\Repositories\LayananPetCareRepository;
use function uuid;

final class PetCareBookingService
{
    public function __construct(
        private readonly BookingPetCareRepository $bookingRepo = new BookingPetCareRepository(),
        private readonly KuotaPetCareRepository $kuotaRepo = new KuotaPetCareRepository(),
        private readonly LayananPetCareRepository $layananRepo = new LayananPetCareRepository(),
        private readonly KucingRepository $kucingRepo = new KucingRepository(),
        private readonly NotifikasiService $notifikasiService = new NotifikasiService(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, bookingId?: string}
     */
    public function createBooking(string $pelangganId, array $input): array
    {
        if ($this->kucingRepo->countByPelanggan($pelangganId) < 1) {
            return [
                'success' => false,
                'errors' => ['kucing' => 'Minimal 1 kucing harus terdaftar sebelum booking.'],
            ];
        }

        $kuotaId = trim((string) ($input['kuota_pet_care_id'] ?? ''));
        $layananId = trim((string) ($input['layanan_pet_care_id'] ?? ''));
        $kucingId = trim((string) ($input['kucing_id'] ?? ''));
        $catatan = trim((string) ($input['catatan'] ?? '')) ?: null;

        $errors = [];

        if ($kuotaId === '') {
            $errors['kuota_pet_care_id'] = 'Pilih slot waktu.';
        }

        if ($layananId === '') {
            $errors['layanan_pet_care_id'] = 'Pilih layanan.';
        }

        if ($kucingId === '') {
            $errors['kucing_id'] = 'Pilih kucing.';
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $kucing = $this->kucingRepo->findByIdAndPelanggan($kucingId, $pelangganId);

        if (!$kucing) {
            return ['success' => false, 'errors' => ['kucing_id' => 'Kucing tidak ditemukan.']];
        }

        $layanan = $this->layananRepo->findActiveById($layananId);

        if (!$layanan) {
            return ['success' => false, 'errors' => ['layanan_pet_care_id' => 'Layanan tidak tersedia.']];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $kuota = $this->kuotaRepo->findByIdForUpdate($kuotaId, $pdo);

            if (!$kuota) {
                $pdo->rollBack();

                return ['success' => false, 'errors' => ['kuota_pet_care_id' => 'Slot tidak ditemukan.']];
            }

            $slotError = $this->validateSlot($kuota);

            if ($slotError !== null) {
                $pdo->rollBack();

                return ['success' => false, 'errors' => ['kuota_pet_care_id' => $slotError]];
            }

            $bookingId = uuid();

            $this->bookingRepo->create(
                $bookingId,
                $pelangganId,
                $kucingId,
                $layananId,
                $kuotaId,
                (string) $kuota['tanggal'],
                (string) $kuota['slot_waktu'],
                (float) $layanan['harga'],
                $catatan,
                $pdo,
            );

            $this->kuotaRepo->incrementTerisi($kuotaId, $pdo);

            $pdo->commit();

            $layanan = $this->layananRepo->findActiveById($layananId);
            $tanggalDisplay = date('d/m/Y', strtotime((string) $kuota['tanggal']));
            $slotDisplay = substr((string) $kuota['slot_waktu'], 0, 5);

            $this->notifikasiService->notifyPelanggan(
                $pelangganId,
                JenisNotifikasi::BOOKING_DISETUJUI,
                'Booking pet care terkonfirmasi',
                sprintf(
                    'Booking %s pada %s pukul %s WIB telah terkonfirmasi.',
                    (string) ($layanan['nama'] ?? 'pet care'),
                    $tanggalDisplay,
                    $slotDisplay,
                ),
                $bookingId,
                'booking_pet_care',
            );

            return ['success' => true, 'bookingId' => $bookingId];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'errors' => ['general' => 'Gagal membuat booking. Silakan coba lagi.']];
        }
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function cancelByPelanggan(string $bookingId, string $pelangganId): array
    {
        $booking = $this->bookingRepo->findByIdAndPelanggan($bookingId, $pelangganId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        return $this->cancelBooking($booking, DibatalkanOleh::PELANGGAN->value, null, null);
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function cancelByStaff(string $bookingId, string $staffId, ?string $alasan): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        return $this->cancelBooking($booking, DibatalkanOleh::STAFF->value, $staffId, $alasan);
    }

    /**
     * @return array{success: bool, error?: string}
     */
    public function updateStatus(string $bookingId, string $newStatus): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $current = StatusBookingPetCare::tryFrom((string) $booking['status']);
        $target = StatusBookingPetCare::tryFrom($newStatus);

        if (!$current || !$target) {
            return ['success' => false, 'error' => 'Status tidak valid.'];
        }

        if ($current->nextStatus() !== $target) {
            return ['success' => false, 'error' => 'Transisi status tidak diizinkan.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();
            $this->bookingRepo->updateStatus($bookingId, $target->value, $pdo);
            $pdo->commit();

            if ($target === StatusBookingPetCare::SELESAI) {
                $this->notifikasiService->notifyPelanggan(
                    (string) $booking['pelanggan_id'],
                    JenisNotifikasi::LAYANAN_SELESAI,
                    'Pet care selesai',
                    'Layanan pet care untuk kucing Anda telah selesai. Terima kasih!',
                    $bookingId,
                    'booking_pet_care',
                );
            }

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal memperbarui status.'];
        }
    }

    /** @param array<string, mixed> $kuota */
    private function validateSlot(array $kuota): ?string
    {
        if (($kuota['status_slot'] ?? '') !== StatusSlotPetCare::TERSEDIA->value) {
            return 'Slot tidak tersedia.';
        }

        if ((int) ($kuota['slot_terisi'] ?? 0) >= (int) ($kuota['slot_maksimal'] ?? 1)) {
            return 'Slot sudah penuh.';
        }

        $tanggal = (string) $kuota['tanggal'];
        $slotWaktu = (string) $kuota['slot_waktu'];
        $today = date('Y-m-d');

        if ($tanggal < $today) {
            return 'Slot sudah lewat.';
        }

        if ($tanggal === $today && $slotWaktu <= date('H:i:s')) {
            return 'Slot waktu sudah lewat.';
        }

        return null;
    }

    /** @param array<string, mixed> $booking */
    private function cancelBooking(array $booking, string $dibatalkanOleh, ?string $staffId, ?string $alasan): array
    {
        $status = StatusBookingPetCare::tryFrom((string) $booking['status']);

        if (!$status || !$status->canCancel()) {
            return ['success' => false, 'error' => 'Booking tidak dapat dibatalkan.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $this->bookingRepo->cancel(
                (string) $booking['id'],
                $dibatalkanOleh,
                $staffId,
                $alasan,
                $pdo,
            );

            $this->kuotaRepo->decrementTerisi((string) $booking['kuota_pet_care_id'], $pdo);

            $pdo->commit();

            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::BOOKING_DIBATALKAN,
                'Booking pet care dibatalkan',
                'Booking pet care Anda telah dibatalkan.',
                (string) $booking['id'],
                'booking_pet_care',
            );

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal membatalkan booking.'];
        }
    }
}
