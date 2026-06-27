<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Enums\JenisLayanan;
use App\Enums\JenisNotifikasi;
use App\Enums\StatusPenitipan;
use App\Enums\StatusPerpanjanganPenitipan;
use App\Enums\StatusPembayaran;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\PaketPenitipanRepository;
use App\Repositories\PerpanjanganPenitipanRepository;
use App\Repositories\TransaksiRepository;
use function uuid;

final class PerpanjanganPenitipanService
{
    public function __construct(
        private readonly PerpanjanganPenitipanRepository $perpanjanganRepo = new PerpanjanganPenitipanRepository(),
        private readonly BookingPenitipanRepository $bookingRepo = new BookingPenitipanRepository(),
        private readonly PaketPenitipanRepository $paketRepo = new PaketPenitipanRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly PenitipanBookingService $bookingService = new PenitipanBookingService(),
        private readonly AppSettingsService $settings = new AppSettingsService(),
        private readonly NotifikasiService $notifikasiService = new NotifikasiService(),
    ) {}

    /**
     * @return array{success: bool, errors?: array<string, string>, data?: array<string, mixed>}
     */
    public function estimasi(string $bookingId, string $pelangganId, string $checkOutBaru): array
    {
        $booking = $this->bookingRepo->findByIdAndPelanggan($bookingId, $pelangganId);

        if (!$booking) {
            return ['success' => false, 'errors' => ['general' => 'Booking tidak ditemukan.']];
        }

        $validation = $this->validatePerpanjanganRequest($booking, $checkOutBaru);

        if (!$validation['success']) {
            return $validation;
        }

        $data = $validation['data'];

        return [
            'success' => true,
            'data' => [
                'check_out_sebelum' => $data['check_out_sebelum'],
                'check_out_baru' => $checkOutBaru,
                'tambah_hari' => $data['tambah_hari'],
                'subtotal_tambahan' => $data['subtotal_tambahan'],
                'total_bayar' => $data['subtotal_tambahan'],
            ],
        ];
    }

    /**
     * @return array{success: bool, errors?: array<string, string>, perpanjanganId?: string}
     */
    public function ajukan(string $bookingId, string $pelangganId, string $checkOutBaru): array
    {
        $booking = $this->bookingRepo->findByIdAndPelanggan($bookingId, $pelangganId);

        if (!$booking) {
            return ['success' => false, 'errors' => ['general' => 'Booking tidak ditemukan.']];
        }

        $validation = $this->validatePerpanjanganRequest($booking, $checkOutBaru);

        if (!$validation['success']) {
            return $validation;
        }

        $data = $validation['data'];
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $kamarId = (string) $booking['kamar_penitipan_id'];
            $checkOutSebelum = (string) $data['check_out_sebelum'];

            if (!$this->isKuotaAvailable($kamarId, $checkOutSebelum, $checkOutBaru, $pdo)) {
                $pdo->rollBack();

                return ['success' => false, 'errors' => ['check_out_baru' => 'Kuota penuh untuk hari tambahan.']];
            }

            $perpanjanganId = uuid();

            $this->perpanjanganRepo->create(
                $perpanjanganId,
                $bookingId,
                $checkOutSebelum,
                $checkOutBaru,
                (int) $data['tambah_hari'],
                (float) $data['subtotal_tambahan'],
                $pdo,
            );

            $pdo->commit();

            $tambahHari = (int) $data['tambah_hari'];
            $checkOutFormatted = date('d/m/Y', strtotime($checkOutBaru));

            $this->notifikasiService->notifyAllActiveStaff(
                JenisNotifikasi::PERPANJANGAN_PENITIPAN_MENUNGGU_KONFIRMASI,
                'Permintaan perpanjangan penitipan',
                "Pelanggan mengajukan perpanjangan check-out ke {$checkOutFormatted} (+{$tambahHari} hari).",
                $perpanjanganId,
                'perpanjangan_penitipan',
            );

            return ['success' => true, 'perpanjanganId' => $perpanjanganId];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'errors' => ['general' => 'Gagal mengajukan perpanjangan.']];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function confirmByStaff(string $perpanjanganId, string $staffId): array
    {
        $perpanjangan = $this->perpanjanganRepo->findById($perpanjanganId);

        if (!$perpanjangan) {
            return ['success' => false, 'error' => 'Permintaan perpanjangan tidak ditemukan.'];
        }

        if ((string) $perpanjangan['status'] !== StatusPerpanjanganPenitipan::MENUNGGU_KONFIRMASI->value) {
            return ['success' => false, 'error' => 'Permintaan tidak dalam status menunggu konfirmasi.'];
        }

        $booking = $this->bookingRepo->findById((string) $perpanjangan['booking_penitipan_id']);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $pdo = Database::connection();
        $checkOutSebelum = (string) $perpanjangan['check_out_sebelum'];
        $checkOutBaru = (string) $perpanjangan['check_out_baru'];
        $kamarId = (string) $booking['kamar_penitipan_id'];

        try {
            $pdo->beginTransaction();

            if (!$this->isKuotaAvailable($kamarId, $checkOutSebelum, $checkOutBaru, $pdo)) {
                $pdo->rollBack();

                return ['success' => false, 'error' => 'Kuota penuh untuk hari tambahan.'];
            }

            $this->bookingService->incrementKuotaRange($kamarId, $checkOutSebelum, $checkOutBaru, $pdo);
            $this->perpanjanganRepo->confirm($perpanjanganId, $staffId, $pdo);

            $transaksiId = uuid();
            $subtotal = (float) $perpanjangan['subtotal_tambahan'];

            $this->transaksiRepo->create(
                $transaksiId,
                (string) $booking['pelanggan_id'],
                JenisLayanan::PENITIPAN->value,
                (string) $booking['id'],
                $subtotal,
                0.0,
                $subtotal,
                $pdo,
                0.0,
                $perpanjanganId,
            );

            $deadlineHours = (int) $this->settings->get('payment_deadline_hours');
            $batasWaktu = date('Y-m-d H:i:s', strtotime("+{$deadlineHours} hours"));
            $this->transaksiRepo->setBatasWaktuBayar($transaksiId, $batasWaktu, $pdo);

            $pdo->commit();

            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::PERPANJANGAN_PENITIPAN_MENUNGGU_PEMBAYARAN,
                'Tagihan perpanjangan penitipan',
                'Permintaan perpanjangan disetujui. Silakan lakukan pembayaran sebelum batas waktu.',
                $perpanjanganId,
                'perpanjangan_penitipan',
            );

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal mengkonfirmasi perpanjangan.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function rejectByStaff(string $perpanjanganId, string $staffId, ?string $catatan): array
    {
        $perpanjangan = $this->perpanjanganRepo->findById($perpanjanganId);

        if (!$perpanjangan) {
            return ['success' => false, 'error' => 'Permintaan perpanjangan tidak ditemukan.'];
        }

        if ((string) $perpanjangan['status'] !== StatusPerpanjanganPenitipan::MENUNGGU_KONFIRMASI->value) {
            return ['success' => false, 'error' => 'Permintaan tidak dapat ditolak.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();
            $this->perpanjanganRepo->reject($perpanjanganId, $staffId, $catatan, $pdo);
            $pdo->commit();

            $booking = $this->bookingRepo->findById((string) $perpanjangan['booking_penitipan_id']);

            if ($booking) {
                $this->notifikasiService->notifyPelanggan(
                    (string) $booking['pelanggan_id'],
                    JenisNotifikasi::PERPANJANGAN_PENITIPAN_DITOLAK,
                    'Perpanjangan penitipan ditolak',
                    'Permintaan perpanjangan penitipan Anda ditolak oleh staff petshop.',
                    $perpanjanganId,
                    'perpanjangan_penitipan',
                );
            }

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal menolak perpanjangan.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function applyAfterPayment(string $perpanjanganId): array
    {
        $perpanjangan = $this->perpanjanganRepo->findById($perpanjanganId);

        if (!$perpanjangan) {
            return ['success' => false, 'error' => 'Perpanjangan tidak ditemukan.'];
        }

        $booking = $this->bookingRepo->findById((string) $perpanjangan['booking_penitipan_id']);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $this->perpanjanganRepo->updateStatus(
                $perpanjanganId,
                StatusPerpanjanganPenitipan::DISETUJUI->value,
                $pdo,
            );

            $this->bookingRepo->extendStay(
                (string) $booking['id'],
                (string) $perpanjangan['check_out_baru'],
                (int) $perpanjangan['tambah_hari'],
                (float) $perpanjangan['subtotal_tambahan'],
                $pdo,
            );

            $pdo->commit();

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal menerapkan perpanjangan.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function cancelExpired(string $perpanjanganId): array
    {
        $perpanjangan = $this->perpanjanganRepo->findById($perpanjanganId);

        if (!$perpanjangan) {
            return ['success' => false, 'error' => 'Perpanjangan tidak ditemukan.'];
        }

        if ((string) $perpanjangan['status'] !== StatusPerpanjanganPenitipan::MENUNGGU_PEMBAYARAN->value) {
            return ['success' => false, 'error' => 'Status perpanjangan tidak valid.'];
        }

        $booking = $this->bookingRepo->findById((string) $perpanjangan['booking_penitipan_id']);
        $transaksi = $this->transaksiRepo->findByPerpanjanganId($perpanjanganId);
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            if ($booking) {
                $this->bookingService->decrementKuotaRange(
                    (string) $booking['kamar_penitipan_id'],
                    (string) $perpanjangan['check_out_sebelum'],
                    (string) $perpanjangan['check_out_baru'],
                    $pdo,
                );
            }

            $this->perpanjanganRepo->cancel($perpanjanganId, $pdo);

            if ($transaksi) {
                $this->transaksiRepo->updateStatusPembayaran(
                    (string) $transaksi['id'],
                    StatusPembayaran::KEDALUWARSA->value,
                    $pdo,
                );
            }

            $pdo->commit();

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal membatalkan perpanjangan kedaluwarsa.'];
        }
    }

    /**
     * @param array<string, mixed> $booking
     * @return array{success: bool, errors?: array<string, string>, data?: array<string, mixed>}
     */
    private function validatePerpanjanganRequest(array $booking, string $checkOutBaru): array
    {
        $transaksi = $this->transaksiRepo->findByPenitipanBooking((string) $booking['id']);
        $transaksiLunas = $transaksi
            && (string) $transaksi['status_pembayaran'] === StatusPembayaran::LUNAS->value;
        $status = StatusPenitipan::tryFrom((string) $booking['status']);

        if (!$status || !$status->canRequestPerpanjangan($transaksiLunas)) {
            return [
                'success' => false,
                'errors' => ['general' => 'Perpanjangan hanya bisa diajukan saat kucing sedang dititipkan.'],
            ];
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkOutBaru)) {
            return ['success' => false, 'errors' => ['check_out_baru' => 'Tanggal check-out baru tidak valid.']];
        }

        $checkOutSebelum = (string) $booking['check_out'];

        if ($checkOutBaru <= $checkOutSebelum) {
            return [
                'success' => false,
                'errors' => ['check_out_baru' => 'Check-out baru harus setelah check-out saat ini.'],
            ];
        }

        $paket = $this->paketRepo->findById((string) $booking['paket_penitipan_id']);

        if (!$paket) {
            return ['success' => false, 'errors' => ['general' => 'Paket penitipan tidak ditemukan.']];
        }

        $tambahHari = count((new \App\Repositories\KuotaPenitipanRepository())->datesInRange(
            $checkOutSebelum,
            $checkOutBaru,
        ));
        $subtotalTambahan = round((float) $paket['harga_per_hari'] * $tambahHari, 2);

        return [
            'success' => true,
            'data' => [
                'check_out_sebelum' => $checkOutSebelum,
                'tambah_hari' => $tambahHari,
                'subtotal_tambahan' => $subtotalTambahan,
            ],
        ];
    }

    private function isKuotaAvailable(
        string $kamarId,
        string $checkIn,
        string $checkOut,
        \PDO $pdo,
    ): bool {
        $kuotaRepo = new \App\Repositories\KuotaPenitipanRepository();

        foreach ($kuotaRepo->datesInRange($checkIn, $checkOut) as $tanggal) {
            $kuota = $kuotaRepo->findByKamarAndDateForUpdate($kamarId, $tanggal, $pdo);

            if (!$kuota || (int) $kuota['slot_terisi'] >= (int) $kuota['slot_maksimal']) {
                return false;
            }
        }

        return true;
    }
}
