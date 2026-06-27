<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Enums\JenisLayanan;
use App\Enums\JenisNotifikasi;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusBookingGrooming;
use App\Enums\StatusPembayaran;
use App\Repositories\BookingGroomingRepository;
use App\Repositories\JenisGroomingRepository;
use App\Repositories\KucingRepository;
use App\Repositories\KuotaGroomingRepository;
use App\Repositories\PelangganRepository;
use App\Repositories\TransaksiRepository;
use function uuid;

final class GroomingBookingService
{
    public function __construct(
        private readonly BookingGroomingRepository $bookingRepo = new BookingGroomingRepository(),
        private readonly KuotaGroomingRepository $kuotaRepo = new KuotaGroomingRepository(),
        private readonly JenisGroomingRepository $jenisRepo = new JenisGroomingRepository(),
        private readonly KucingRepository $kucingRepo = new KucingRepository(),
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly LayananAntarJemputService $pickupService = new LayananAntarJemputService(),
        private readonly AppSettingsService $settings = new AppSettingsService(),
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

        $kuotaId = trim((string) ($input['kuota_grooming_id'] ?? ''));
        $jenisId = trim((string) ($input['jenis_grooming_id'] ?? ''));
        $kucingId = trim((string) ($input['kucing_id'] ?? ''));
        $opsiPengantaran = trim((string) ($input['opsi_pengantaran'] ?? OpsiPengantaran::ANTAR_SENDIRI->value));
        $catatan = trim((string) ($input['catatan'] ?? '')) ?: null;

        $errors = [];

        if ($kuotaId === '') {
            $errors['kuota_grooming_id'] = 'Pilih tanggal grooming.';
        }

        if ($jenisId === '') {
            $errors['jenis_grooming_id'] = 'Pilih jenis grooming.';
        }

        if ($kucingId === '') {
            $errors['kucing_id'] = 'Pilih kucing.';
        }

        if (!in_array($opsiPengantaran, [
            OpsiPengantaran::ANTAR_JEMPUT->value,
            OpsiPengantaran::ANTAR_SENDIRI->value,
        ], true)) {
            $errors['opsi_pengantaran'] = 'Opsi pengantaran tidak valid.';
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $kucing = $this->kucingRepo->findByIdAndPelanggan($kucingId, $pelangganId);

        if (!$kucing) {
            return ['success' => false, 'errors' => ['kucing_id' => 'Kucing tidak ditemukan.']];
        }

        $jenis = $this->jenisRepo->findActiveById($jenisId);

        if (!$jenis) {
            return ['success' => false, 'errors' => ['jenis_grooming_id' => 'Jenis grooming tidak tersedia.']];
        }

        $pelanggan = $this->pelangganRepo->findById($pelangganId);

        if (!$pelanggan) {
            return ['success' => false, 'errors' => ['general' => 'Data pelanggan tidak ditemukan.']];
        }

        if ($opsiPengantaran === OpsiPengantaran::ANTAR_JEMPUT->value
            && !$this->pickupService->estimasiUntukPelanggan($pelanggan)['success']) {
            return [
                'success' => false,
                'errors' => ['opsi_pengantaran' => 'Alamat profil belum lengkap untuk antar-jemput.'],
            ];
        }

        try {
            $pickup = $this->pickupService->hitungUntukBooking($opsiPengantaran, $pelanggan);
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'errors' => ['opsi_pengantaran' => $e->getMessage()]];
        }

        $hargaLayanan = (float) $jenis['harga'];
        $biayaAntar = $pickup['biaya_antar_jemput'];
        $totalBayar = $hargaLayanan + $biayaAntar;

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $kuota = $this->kuotaRepo->findByIdForUpdate($kuotaId, $pdo);

            if (!$kuota) {
                $pdo->rollBack();

                return ['success' => false, 'errors' => ['kuota_grooming_id' => 'Kuota tidak ditemukan.']];
            }

            $slotError = $this->validateKuota($kuota);

            if ($slotError !== null) {
                $pdo->rollBack();

                return ['success' => false, 'errors' => ['kuota_grooming_id' => $slotError]];
            }

            $bookingId = uuid();
            $transaksiId = uuid();

            $this->bookingRepo->create(
                $bookingId,
                $pelangganId,
                $kucingId,
                $jenisId,
                $kuotaId,
                (string) $kuota['tanggal'],
                $opsiPengantaran,
                $pickup['jarak_km'],
                $biayaAntar,
                $hargaLayanan,
                $catatan,
                $pdo,
            );

            $this->transaksiRepo->create(
                $transaksiId,
                $pelangganId,
                JenisLayanan::GROOMING->value,
                $bookingId,
                $hargaLayanan,
                $biayaAntar,
                $totalBayar,
                $pdo,
            );

            $this->kuotaRepo->incrementTerisi($kuotaId, $pdo);

            $pdo->commit();

            return ['success' => true, 'bookingId' => $bookingId];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'errors' => ['general' => 'Gagal membuat booking. Silakan coba lagi.']];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function cancelByPelanggan(string $bookingId, string $pelangganId): array
    {
        $booking = $this->bookingRepo->findByIdAndPelanggan($bookingId, $pelangganId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $status = StatusBookingGrooming::tryFrom((string) $booking['status']);

        if (!$status || !$status->canCancelByPelanggan()) {
            return ['success' => false, 'error' => 'Booking tidak dapat dibatalkan.'];
        }

        $result = $this->cancelBooking($booking);

        if ($result['success']) {
            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::BOOKING_DIBATALKAN,
                'Booking grooming dibatalkan',
                'Booking grooming Anda telah dibatalkan.',
                (string) $booking['id'],
                'booking_grooming',
            );
        }

        return $result;
    }

    /** @return array{success: bool, error?: string} */
    public function confirmByStaff(string $bookingId, string $staffId, string $jamGrooming): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        if ((string) $booking['status'] !== StatusBookingGrooming::MENUNGGU_KONFIRMASI->value) {
            return ['success' => false, 'error' => 'Booking tidak dalam status menunggu konfirmasi.'];
        }

        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $jamGrooming)) {
            return ['success' => false, 'error' => 'Format jam grooming tidak valid.'];
        }

        if (strlen($jamGrooming) === 5) {
            $jamGrooming .= ':00';
        }

        $transaksi = $this->transaksiRepo->findByGroomingBooking($bookingId);

        if (!$transaksi) {
            return ['success' => false, 'error' => 'Data transaksi tidak ditemukan.'];
        }

        $deadlineHours = (int) $this->settings->get('payment_deadline_hours');
        $batasWaktu = date('Y-m-d H:i:s', strtotime("+{$deadlineHours} hours"));

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $this->bookingRepo->confirm($bookingId, $staffId, $jamGrooming, $pdo);
            $this->transaksiRepo->setBatasWaktuBayar((string) $transaksi['id'], $batasWaktu, $pdo);

            $pdo->commit();

            $pelangganId = (string) $booking['pelanggan_id'];
            $tanggal = date('d/m/Y', strtotime((string) $booking['tanggal']));
            $jamDisplay = substr($jamGrooming, 0, 5);

            $this->notifikasiService->notifyPelanggan(
                $pelangganId,
                JenisNotifikasi::JAM_GROOMING_DIUPDATE,
                'Jam grooming dikonfirmasi',
                "Booking grooming Anda pada {$tanggal} dijadwalkan pukul {$jamDisplay} WIB. Silakan lakukan pembayaran sebelum batas waktu.",
                $bookingId,
                'booking_grooming',
            );

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal mengkonfirmasi booking.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function rejectByStaff(string $bookingId): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        if ((string) $booking['status'] !== StatusBookingGrooming::MENUNGGU_KONFIRMASI->value) {
            return ['success' => false, 'error' => 'Booking tidak dapat ditolak.'];
        }

        $result = $this->cancelBooking($booking);

        if ($result['success']) {
            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::BOOKING_DITOLAK,
                'Booking grooming ditolak',
                'Permintaan booking grooming Anda ditolak oleh staff petshop.',
                (string) $booking['id'],
                'booking_grooming',
            );
        }

        return $result;
    }

    /** @return array{success: bool, error?: string} */
    public function updateOperationalStatus(string $bookingId, string $newStatus): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $current = StatusBookingGrooming::tryFrom((string) $booking['status']);
        $target = StatusBookingGrooming::tryFrom($newStatus);

        if (!$current || !$target || $current->nextOperationalStatus() !== $target) {
            return ['success' => false, 'error' => 'Transisi status tidak diizinkan.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();
            $this->bookingRepo->updateStatus($bookingId, $target->value, $pdo);
            $pdo->commit();

            if ($target === StatusBookingGrooming::SELESAI) {
                $this->notifikasiService->notifyPelanggan(
                    (string) $booking['pelanggan_id'],
                    JenisNotifikasi::LAYANAN_SELESAI,
                    'Grooming selesai',
                    'Layanan grooming untuk kucing Anda telah selesai. Terima kasih!',
                    $bookingId,
                    'booking_grooming',
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
    private function validateKuota(array $kuota): ?string
    {
        if ((int) ($kuota['slot_terisi'] ?? 0) >= (int) ($kuota['slot_maksimal'] ?? 0)) {
            return 'Kuota grooming sudah penuh untuk tanggal ini.';
        }

        $tanggal = (string) $kuota['tanggal'];

        if ($tanggal < date('Y-m-d')) {
            return 'Tanggal grooming sudah lewat.';
        }

        return null;
    }

    /** @param array<string, mixed> $booking */
    private function cancelBooking(array $booking): array
    {
        $transaksi = $this->transaksiRepo->findByGroomingBooking((string) $booking['id']);
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $this->bookingRepo->cancel((string) $booking['id'], $pdo);
            $this->kuotaRepo->decrementTerisi((string) $booking['kuota_grooming_id'], $pdo);

            if ($transaksi) {
                $this->transaksiRepo->updateStatusPembayaran(
                    (string) $transaksi['id'],
                    StatusPembayaran::DIBATALKAN->value,
                    $pdo,
                );
            }

            $pdo->commit();

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal membatalkan booking.'];
        }
    }
}
