<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JenisNotifikasi;
use App\Enums\StatusPenitipan;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\MonitoringPenitipanRepository;
use function uuid;

final class MonitoringPenitipanService
{
    public function __construct(
        private readonly MonitoringPenitipanRepository $monitoringRepo = new MonitoringPenitipanRepository(),
        private readonly BookingPenitipanRepository $bookingRepo = new BookingPenitipanRepository(),
        private readonly FileUploadService $fileUpload = new FileUploadService(),
        private readonly NotifikasiService $notifikasiService = new NotifikasiService(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed>|null $fotoFile
     * @return array{success: bool, errors?: array<string, string>, error?: string}
     */
    public function create(string $bookingId, string $staffId, array $input, ?array $fotoFile): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        if ((string) $booking['status'] !== StatusPenitipan::SEDANG_DITITIPKAN->value) {
            return ['success' => false, 'error' => 'Monitoring hanya bisa diinput saat kucing sedang dititipkan.'];
        }

        $tanggal = trim((string) ($input['tanggal'] ?? date('Y-m-d')));
        $catatanMakan = trim((string) ($input['catatan_makan'] ?? '')) ?: null;
        $kondisi = trim((string) ($input['kondisi'] ?? '')) ?: null;
        $aktivitas = trim((string) ($input['aktivitas_harian'] ?? '')) ?: null;
        $errors = [];

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $errors['tanggal'] = 'Tanggal tidak valid.';
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $fotoUrl = null;

        if ($fotoFile !== null && ($fotoFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $upload = $this->fileUpload->upload($fotoFile, 'monitoring');

            if (!$upload['success']) {
                return ['success' => false, 'errors' => ['foto' => $upload['error'] ?? 'Gagal mengunggah foto.']];
            }

            $fotoUrl = $upload['path'];
        }

        try {
            $this->monitoringRepo->create(
                uuid(),
                $bookingId,
                $staffId,
                $tanggal,
                $fotoUrl,
                $catatanMakan,
                $kondisi,
                $aktivitas,
            );

            $tanggalDisplay = date('d/m/Y', strtotime($tanggal));

            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::MONITORING_PENITIPAN,
                'Update monitoring penitipan',
                "Staff petshop telah menginput monitoring harian untuk tanggal {$tanggalDisplay}.",
                $bookingId,
                'booking_penitipan',
            );

            return ['success' => true];
        } catch (\Throwable) {
            $this->fileUpload->deletePublicPath($fotoUrl);

            return ['success' => false, 'error' => 'Gagal menyimpan monitoring.'];
        }
    }
}
