<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Enums\JenisLayanan;
use App\Enums\JenisNotifikasi;
use App\Enums\StatusBookingGrooming;
use App\Enums\StatusPenitipan;
use App\Enums\StatusPerpanjanganPenitipan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusVerifikasi;
use App\Repositories\BookingGroomingRepository;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\BuktiTransferRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PelangganRepository;
use App\Repositories\PerpanjanganPenitipanRepository;
use App\Repositories\TransaksiRepository;
use function uuid;

final class PembayaranService
{
    public function __construct(
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly BuktiTransferRepository $buktiRepo = new BuktiTransferRepository(),
        private readonly BookingGroomingRepository $groomingBookingRepo = new BookingGroomingRepository(),
        private readonly BookingPenitipanRepository $penitipanBookingRepo = new BookingPenitipanRepository(),
        private readonly PerpanjanganPenitipanRepository $perpanjanganRepo = new PerpanjanganPenitipanRepository(),
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly InvoiceRepository $invoiceRepo = new InvoiceRepository(),
        private readonly FileUploadService $fileUpload = new FileUploadService(),
        private readonly PerpanjanganPenitipanService $perpanjanganService = new PerpanjanganPenitipanService(),
        private readonly NotifikasiService $notifikasiService = new NotifikasiService(),
    ) {}

    /**
     * @param array<string, mixed>|null $file
     * @return array{success: bool, error?: string, errors?: array<string, string>}
     */
    public function uploadBuktiTransfer(string $transaksiId, string $pelangganId, ?array $file): array
    {
        $transaksi = $this->transaksiRepo->findByIdAndPelanggan($transaksiId, $pelangganId);

        if (!$transaksi) {
            return ['success' => false, 'error' => 'Transaksi tidak ditemukan.'];
        }

        if ((string) $transaksi['status_pembayaran'] !== StatusPembayaran::MENUNGGU_PEMBAYARAN->value) {
            return ['success' => false, 'error' => 'Transaksi tidak dalam status menunggu pembayaran.'];
        }

        $jenis = (string) $transaksi['jenis_layanan'];

        if ($jenis === JenisLayanan::GROOMING->value) {
            $valid = $this->validateGroomingUpload($transaksi);
        } elseif ($jenis === JenisLayanan::PENITIPAN->value) {
            $valid = $this->validatePenitipanUpload($transaksi);
        } else {
            return ['success' => false, 'error' => 'Transaksi tidak valid.'];
        }

        if (!$valid['success']) {
            return $valid;
        }

        $upload = $this->fileUpload->upload($file ?? [], 'bukti_transfer');

        if (!$upload['success']) {
            return [
                'success' => false,
                'errors' => ['bukti' => $upload['error'] ?? 'Bukti transfer wajib diupload.'],
            ];
        }

        $existingBukti = $this->buktiRepo->findByTransaksiId($transaksiId);
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            if ($existingBukti) {
                $this->fileUpload->deletePublicPath($existingBukti['file_url'] ?? null);
                $this->buktiRepo->replace($transaksiId, (string) $upload['path'], $pdo);
            } else {
                $this->buktiRepo->create(uuid(), $transaksiId, (string) $upload['path'], $pdo);
            }

            $this->transaksiRepo->updateStatusPembayaran(
                $transaksiId,
                StatusPembayaran::MENUNGGU_VERIFIKASI->value,
                $pdo,
            );

            if ($jenis === JenisLayanan::GROOMING->value) {
                $this->groomingBookingRepo->updateStatus(
                    (string) $transaksi['booking_id'],
                    StatusBookingGrooming::MENUNGGU_VERIFIKASI_BUKTI->value,
                    $pdo,
                );
            } elseif ($transaksi['perpanjangan_penitipan_id']) {
                $this->perpanjanganRepo->updateStatus(
                    (string) $transaksi['perpanjangan_penitipan_id'],
                    StatusPerpanjanganPenitipan::MENUNGGU_VERIFIKASI_BUKTI->value,
                    $pdo,
                );
            } else {
                $this->penitipanBookingRepo->updateStatus(
                    (string) $transaksi['booking_id'],
                    StatusPenitipan::MENUNGGU_VERIFIKASI_BUKTI->value,
                    $pdo,
                );
            }

            $pdo->commit();

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $this->fileUpload->deletePublicPath($upload['path'] ?? null);

            return ['success' => false, 'error' => 'Gagal mengupload bukti transfer.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function setujuiBukti(string $buktiId, string $staffId): array
    {
        $bukti = $this->buktiRepo->findById($buktiId);

        if (!$bukti || (string) $bukti['status_verifikasi'] !== StatusVerifikasi::MENUNGGU->value) {
            return ['success' => false, 'error' => 'Bukti transfer tidak ditemukan atau sudah diverifikasi.'];
        }

        $transaksi = $this->transaksiRepo->findById((string) $bukti['transaksi_id']);

        if (!$transaksi
            || (string) $transaksi['status_pembayaran'] !== StatusPembayaran::MENUNGGU_VERIFIKASI->value) {
            return ['success' => false, 'error' => 'Transaksi tidak valid untuk verifikasi.'];
        }

        $jenis = (string) $transaksi['jenis_layanan'];
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $this->buktiRepo->setujui($buktiId, $staffId, $pdo);
            $this->transaksiRepo->markLunas((string) $transaksi['id'], $pdo);

            if ($jenis === JenisLayanan::GROOMING->value) {
                $booking = $this->groomingBookingRepo->findById((string) $transaksi['booking_id']);

                if (!$booking
                    || (string) $booking['status'] !== StatusBookingGrooming::MENUNGGU_VERIFIKASI_BUKTI->value) {
                    throw new \RuntimeException('Status booking grooming tidak valid.');
                }

                $this->groomingBookingRepo->updateStatus(
                    (string) $transaksi['booking_id'],
                    StatusBookingGrooming::TERKONFIRMASI->value,
                    $pdo,
                );
            } elseif ($jenis === JenisLayanan::PENITIPAN->value) {
                if ($transaksi['perpanjangan_penitipan_id']) {
                    $perpanjangan = $this->perpanjanganRepo->findById((string) $transaksi['perpanjangan_penitipan_id']);

                    if (!$perpanjangan
                        || (string) $perpanjangan['status'] !== StatusPerpanjanganPenitipan::MENUNGGU_VERIFIKASI_BUKTI->value) {
                        throw new \RuntimeException('Status perpanjangan tidak valid.');
                    }

                    $this->perpanjanganRepo->updateStatus(
                        (string) $transaksi['perpanjangan_penitipan_id'],
                        StatusPerpanjanganPenitipan::DISETUJUI->value,
                        $pdo,
                    );

                    $this->penitipanBookingRepo->extendStay(
                        (string) $transaksi['booking_id'],
                        (string) $perpanjangan['check_out_baru'],
                        (int) $perpanjangan['tambah_hari'],
                        (float) $perpanjangan['subtotal_tambahan'],
                        $pdo,
                    );
                } else {
                    $booking = $this->penitipanBookingRepo->findById((string) $transaksi['booking_id']);

                    if (!$booking
                        || (string) $booking['status'] !== StatusPenitipan::MENUNGGU_VERIFIKASI_BUKTI->value) {
                        throw new \RuntimeException('Status booking penitipan tidak valid.');
                    }

                    if ((bool) ($booking['promo_dipakai'] ?? false)) {
                        $this->pelangganRepo->markPromoPenitipanUsed((string) $booking['pelanggan_id'], $pdo);
                    }
                }
            }

            if (!$this->invoiceRepo->findByTransaksiId((string) $transaksi['id'])) {
                $this->invoiceRepo->create(
                    uuid(),
                    (string) $transaksi['id'],
                    $this->invoiceRepo->generateNomor(),
                    $pdo,
                );
            }

            $pdo->commit();

            $pelangganId = (string) $transaksi['pelanggan_id'];
            $total = number_format((float) $transaksi['total_bayar'], 0, ',', '.');

            if ($jenis === JenisLayanan::PENITIPAN->value && !empty($transaksi['perpanjangan_penitipan_id'])) {
                $this->notifikasiService->notifyPelanggan(
                    $pelangganId,
                    JenisNotifikasi::PERPANJANGAN_PENITIPAN_DISETUJUI,
                    'Perpanjangan penitipan aktif',
                    "Pembayaran perpanjangan sebesar Rp {$total} telah diverifikasi. Masa penitipan diperpanjang.",
                    (string) $transaksi['perpanjangan_penitipan_id'],
                    'perpanjangan_penitipan',
                );
            } else {
                $this->notifikasiService->notifyPelanggan(
                    $pelangganId,
                    JenisNotifikasi::BOOKING_DISETUJUI,
                    'Pembayaran diverifikasi',
                    "Pembayaran sebesar Rp {$total} telah diverifikasi. Anda dapat mengunduh invoice.",
                    (string) $transaksi['id'],
                    'transaksi',
                );
            }

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal menyetujui bukti transfer.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function tolakBukti(string $buktiId, string $staffId, ?string $catatan): array
    {
        $bukti = $this->buktiRepo->findById($buktiId);

        if (!$bukti || (string) $bukti['status_verifikasi'] !== StatusVerifikasi::MENUNGGU->value) {
            return ['success' => false, 'error' => 'Bukti transfer tidak ditemukan atau sudah diverifikasi.'];
        }

        $transaksi = $this->transaksiRepo->findById((string) $bukti['transaksi_id']);

        if (!$transaksi) {
            return ['success' => false, 'error' => 'Transaksi tidak ditemukan.'];
        }

        $jenis = (string) $transaksi['jenis_layanan'];
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $this->buktiRepo->tolak($buktiId, $staffId, $catatan, $pdo);
            $this->transaksiRepo->updateStatusPembayaran(
                (string) $transaksi['id'],
                StatusPembayaran::MENUNGGU_PEMBAYARAN->value,
                $pdo,
            );

            if ($jenis === JenisLayanan::GROOMING->value) {
                $this->groomingBookingRepo->updateStatus(
                    (string) $transaksi['booking_id'],
                    StatusBookingGrooming::MENUNGGU_PEMBAYARAN->value,
                    $pdo,
                );
            } elseif ($transaksi['perpanjangan_penitipan_id']) {
                $this->perpanjanganRepo->updateStatus(
                    (string) $transaksi['perpanjangan_penitipan_id'],
                    StatusPerpanjanganPenitipan::MENUNGGU_PEMBAYARAN->value,
                    $pdo,
                );
            } else {
                $this->penitipanBookingRepo->updateStatus(
                    (string) $transaksi['booking_id'],
                    StatusPenitipan::MENUNGGU_PEMBAYARAN->value,
                    $pdo,
                );
            }

            $pdo->commit();

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal menolak bukti transfer.'];
        }
    }

    /** @param array<string, mixed> $transaksi */
    /** @return array{success: bool, error?: string} */
    private function validateGroomingUpload(array $transaksi): array
    {
        if ((string) $transaksi['jenis_layanan'] !== JenisLayanan::GROOMING->value) {
            return ['success' => false, 'error' => 'Transaksi tidak valid.'];
        }

        $booking = $this->groomingBookingRepo->findById((string) $transaksi['booking_id']);

        if (!$booking || (string) $booking['status'] !== StatusBookingGrooming::MENUNGGU_PEMBAYARAN->value) {
            return ['success' => false, 'error' => 'Booking tidak siap untuk upload bukti.'];
        }

        return ['success' => true];
    }

    /** @param array<string, mixed> $transaksi */
    /** @return array{success: bool, error?: string} */
    private function validatePenitipanUpload(array $transaksi): array
    {
        if ($transaksi['perpanjangan_penitipan_id']) {
            $perpanjangan = $this->perpanjanganRepo->findById((string) $transaksi['perpanjangan_penitipan_id']);

            if (!$perpanjangan
                || (string) $perpanjangan['status'] !== StatusPerpanjanganPenitipan::MENUNGGU_PEMBAYARAN->value) {
                return ['success' => false, 'error' => 'Perpanjangan tidak siap untuk upload bukti.'];
            }

            return ['success' => true];
        }

        $booking = $this->penitipanBookingRepo->findById((string) $transaksi['booking_id']);

        if (!$booking || (string) $booking['status'] !== StatusPenitipan::MENUNGGU_PEMBAYARAN->value) {
            return ['success' => false, 'error' => 'Booking tidak siap untuk upload bukti.'];
        }

        return ['success' => true];
    }
}
