<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Enums\JenisNotifikasi;
use App\Enums\StatusBookingGrooming;
use App\Enums\StatusPenitipan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusRefund;
use App\Repositories\BookingGroomingRepository;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\KuotaGroomingRepository;
use App\Repositories\TransaksiRepository;

final class PembatalanRefundService
{
    public function __construct(
        private readonly BookingGroomingRepository $groomingBookingRepo = new BookingGroomingRepository(),
        private readonly BookingPenitipanRepository $penitipanBookingRepo = new BookingPenitipanRepository(),
        private readonly KuotaGroomingRepository $kuotaGroomingRepo = new KuotaGroomingRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly PenitipanBookingService $penitipanBookingService = new PenitipanBookingService(),
        private readonly NotifikasiService $notifikasiService = new NotifikasiService(),
    ) {}

    /**
     * @param array<string, mixed> $booking
     * @param array<string, mixed>|null $transaksi
     */
    public function canStaffCancelGroomingWithRefund(array $booking, ?array $transaksi): bool
    {
        if (!$transaksi
            || (string) $transaksi['status_pembayaran'] !== StatusPembayaran::LUNAS->value
            || (string) ($transaksi['status_refund'] ?? StatusRefund::TIDAK_ADA->value) !== StatusRefund::TIDAK_ADA->value) {
            return false;
        }

        $status = StatusBookingGrooming::tryFrom((string) $booking['status']);

        return $status !== null && $status->canCancelByStaffWithRefund();
    }

    /**
     * @param array<string, mixed> $booking
     * @param array<string, mixed>|null $transaksi
     */
    public function canStaffCancelPenitipanWithRefund(array $booking, ?array $transaksi): bool
    {
        if (!$transaksi
            || (string) $transaksi['status_pembayaran'] !== StatusPembayaran::LUNAS->value
            || (string) ($transaksi['status_refund'] ?? StatusRefund::TIDAK_ADA->value) !== StatusRefund::TIDAK_ADA->value) {
            return false;
        }

        $status = StatusPenitipan::tryFrom((string) $booking['status']);

        return $status !== null && $status->canCancelByStaffWithRefund(true);
    }

    /** @return array{success: bool, error?: string} */
    public function cancelGroomingByStaff(string $bookingId, ?string $alasan = null): array
    {
        $booking = $this->groomingBookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $transaksi = $this->transaksiRepo->findLunasByGroomingBooking($bookingId);

        if (!$this->canStaffCancelGroomingWithRefund($booking, $transaksi)) {
            return ['success' => false, 'error' => 'Booking tidak dapat dibatalkan dengan refund.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $this->groomingBookingRepo->cancel($bookingId, $pdo);
            $this->kuotaGroomingRepo->decrementTerisi((string) $booking['kuota_grooming_id'], $pdo);
            $this->transaksiRepo->updateStatusRefund(
                (string) $transaksi['id'],
                StatusRefund::PENDING_REFUND->value,
                $pdo,
            );

            $pdo->commit();

            $pesan = 'Booking grooming Anda dibatalkan. Refund sedang diproses oleh petshop.';
            if ($alasan) {
                $pesan .= ' Alasan: ' . $alasan;
            }

            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::BOOKING_DIBATALKAN,
                'Booking grooming dibatalkan',
                $pesan,
                $bookingId,
                'booking_grooming',
            );

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal membatalkan booking grooming.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function cancelPenitipanByStaff(string $bookingId, ?string $alasan = null): array
    {
        $booking = $this->penitipanBookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $transaksi = $this->transaksiRepo->findLunasByPenitipanBooking($bookingId);

        if (!$this->canStaffCancelPenitipanWithRefund($booking, $transaksi)) {
            return ['success' => false, 'error' => 'Booking tidak dapat dibatalkan dengan refund.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $this->penitipanBookingRepo->cancel($bookingId, $pdo);
            $this->penitipanBookingService->decrementKuotaRange(
                (string) $booking['kamar_penitipan_id'],
                (string) $booking['check_in'],
                (string) $booking['check_out'],
                $pdo,
            );
            $this->transaksiRepo->updateStatusRefund(
                (string) $transaksi['id'],
                StatusRefund::PENDING_REFUND->value,
                $pdo,
            );

            $pdo->commit();

            $pesan = 'Booking penitipan Anda dibatalkan. Refund sedang diproses oleh petshop.';
            if ($alasan) {
                $pesan .= ' Alasan: ' . $alasan;
            }

            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::BOOKING_DIBATALKAN,
                'Booking penitipan dibatalkan',
                $pesan,
                $bookingId,
                'booking_penitipan',
            );

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal membatalkan booking penitipan.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function markRefundCompleted(string $transaksiId): array
    {
        $transaksi = $this->transaksiRepo->findById($transaksiId);

        if (!$transaksi) {
            return ['success' => false, 'error' => 'Transaksi tidak ditemukan.'];
        }

        if ((string) $transaksi['status_refund'] !== StatusRefund::PENDING_REFUND->value) {
            return ['success' => false, 'error' => 'Transaksi tidak dalam status refund pending.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();
            $this->transaksiRepo->updateStatusRefund(
                $transaksiId,
                StatusRefund::REFUNDED->value,
                $pdo,
            );
            $pdo->commit();

            $total = number_format((float) $transaksi['total_bayar'], 0, ',', '.');

            $this->notifikasiService->notifyPelanggan(
                (string) $transaksi['pelanggan_id'],
                JenisNotifikasi::STATUS_REFUND,
                'Refund selesai',
                "Refund sebesar Rp {$total} telah selesai diproses.",
                $transaksiId,
                'transaksi',
            );

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal menandai refund selesai.'];
        }
    }

    /**
     * @param array<string, mixed> $booking
     * @param array<string, mixed>|null $transaksi
     */
    public function canMarkRefundCompleted(?array $transaksi, array $booking): bool
    {
        if (!$transaksi
            || (string) ($transaksi['status_refund'] ?? '') !== StatusRefund::PENDING_REFUND->value) {
            return false;
        }

        return (string) ($booking['status'] ?? '') === StatusBookingGrooming::DIBATALKAN->value
            || (string) ($booking['status'] ?? '') === StatusPenitipan::DIBATALKAN->value;
    }
}
