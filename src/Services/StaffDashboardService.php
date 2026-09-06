<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JenisLayanan;
use App\Enums\StatusPenitipan;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\MonitoringPenitipanRepository;
use App\Repositories\StaffDashboardRepository;
use App\Repositories\TransaksiRepository;

final class StaffDashboardService
{
    private const PREVIEW_LIMIT = 5;

    public function __construct(
        private readonly StaffDashboardRepository $dashboardRepo = new StaffDashboardRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly BookingPenitipanRepository $bookingPenitipanRepo = new BookingPenitipanRepository(),
        private readonly MonitoringPenitipanRepository $monitoringRepo = new MonitoringPenitipanRepository(),
    ) {}

    /**
     * @return array{
     *   today: string,
     *   bookingsToday: array{grooming: int, penitipan: int, pet_care: int, total: int},
     *   pendingVerification: array{grooming: int, penitipan: int, total: int},
     *   pendingPenitipanConfirmation: int,
     *   pendingPenitipanConfirmationPreview: list<array<string, mixed>>,
     *   pendingMonitoringPenitipan: int,
     *   pendingMonitoringPenitipanPreview: list<array<string, mixed>>,
     *   penitipanAktif: int,
     *   pendapatan: array{harian: float, mingguan: float, mingguMulai: string, mingguAkhir: string},
     *   pendingVerificationPreview: list<array<string, mixed>>
     * }
     */
    public function getHomeSummary(): array
    {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime($today . ' +1 day'));
        $yesterday = date('Y-m-d', strtotime($today . ' -1 day'));
        $mingguMulai = date('Y-m-d', strtotime('monday this week'));

        $harianMulai = $today . ' 00:00:00';
        $kemarinMulai = $yesterday . ' 00:00:00';
        $kemarinAkhir = $today . ' 00:00:00';
        $mingguMulaiDt = $mingguMulai . ' 00:00:00';
        $akhirEksklusif = $tomorrow . ' 00:00:00';

        $bookingsToday = $this->dashboardRepo->countBookingsToday($today);
        $bookingsYesterday = $this->dashboardRepo->countBookingsToday($yesterday);
        $pendapatanHarian = $this->dashboardRepo->sumVerifiedRevenue($harianMulai, $akhirEksklusif);
        $pendapatanKemarin = $this->dashboardRepo->sumVerifiedRevenue($kemarinMulai, $kemarinAkhir);

        $pendingPenitipanConfirmation = $this->bookingPenitipanRepo->countByStatus(
            StatusPenitipan::MENUNGGU_KONFIRMASI->value,
        );

        return [
            'today' => $today,
            'bookingsToday' => $bookingsToday,
            'bookingsYesterday' => $bookingsYesterday,
            'pendingVerification' => $this->transaksiRepo->countPendingVerification(),
            'pendingPenitipanConfirmation' => $pendingPenitipanConfirmation,
            'pendingPenitipanConfirmationPreview' => $this->buildPendingPenitipanConfirmationPreview(),
            'pendingMonitoringPenitipan' => $this->monitoringRepo->countBookingsMissingTodayMonitoring(),
            'pendingMonitoringPenitipanPreview' => $this->buildPendingMonitoringPenitipanPreview(),
            'penitipanAktif' => $this->dashboardRepo->countPenitipanAktif(),
            'pendapatan' => [
                'harian' => $pendapatanHarian,
                'kemarin' => $pendapatanKemarin,
                'mingguan' => $this->dashboardRepo->sumVerifiedRevenue($mingguMulaiDt, $akhirEksklusif),
                'mingguMulai' => $mingguMulai,
                'mingguAkhir' => $today,
            ],
            'pendingVerificationPreview' => $this->buildPendingVerificationPreview(),
        ];
    }

    /** @return list<array<string, mixed>> */
    private function buildPendingVerificationPreview(): array
    {
        $items = $this->transaksiRepo->findAllPendingVerification();

        usort($items, static function (array $a, array $b): int {
            return strcmp((string) ($a['bukti_uploaded_at'] ?? ''), (string) ($b['bukti_uploaded_at'] ?? ''));
        });

        $preview = [];

        foreach (array_slice($items, 0, self::PREVIEW_LIMIT) as $item) {
            $jenisLayanan = (string) ($item['jenis_layanan'] ?? '');

            if ($jenisLayanan === JenisLayanan::GROOMING->value) {
                $preview[] = [
                    'pelanggan_nama' => (string) ($item['pelanggan_nama'] ?? ''),
                    'layanan_label' => 'Grooming — ' . (string) ($item['jenis_nama'] ?? ''),
                    'total_bayar' => (float) ($item['total_bayar'] ?? 0),
                    'uploaded_at' => (string) ($item['bukti_uploaded_at'] ?? ''),
                    'url' => '/admin/grooming/pembayaran',
                ];
                continue;
            }

            if ($jenisLayanan === JenisLayanan::PENITIPAN->value) {
                $isPerpanjangan = !empty($item['perpanjangan_penitipan_id']);
                $layananLabel = $isPerpanjangan
                    ? 'Perpanjangan Penitipan — ' . (string) ($item['paket_nama'] ?? '')
                    : 'Penitipan — ' . (string) ($item['paket_nama'] ?? '');

                $preview[] = [
                    'pelanggan_nama' => (string) ($item['pelanggan_nama'] ?? ''),
                    'layanan_label' => $layananLabel,
                    'total_bayar' => (float) ($item['total_bayar'] ?? 0),
                    'uploaded_at' => (string) ($item['bukti_uploaded_at'] ?? ''),
                    'url' => '/admin/penitipan/pembayaran#bukti-' . urlencode((string) ($item['bukti_id'] ?? '')),
                ];
            }
        }

        return $preview;
    }

    /** @return list<array<string, mixed>> */
    private function buildPendingPenitipanConfirmationPreview(): array
    {
        $preview = [];

        foreach ($this->bookingPenitipanRepo->findMenungguKonfirmasiPreview(self::PREVIEW_LIMIT) as $item) {
            $subtotal = (float) ($item['subtotal_penitipan'] ?? 0);
            $promo = (float) ($item['potongan_promo'] ?? 0);
            $antar = (float) ($item['biaya_antar_jemput'] ?? 0);

            $preview[] = [
                'pelanggan_nama' => (string) ($item['pelanggan_nama'] ?? ''),
                'kucing_nama' => (string) ($item['kucing_nama'] ?? ''),
                'check_in' => (string) ($item['check_in'] ?? ''),
                'lama_hari' => (int) ($item['lama_hari'] ?? 0),
                'total_bayar' => $subtotal - $promo + $antar,
                'url' => '/admin/penitipan/booking?status=' . urlencode(StatusPenitipan::MENUNGGU_KONFIRMASI->value),
            ];
        }

        return $preview;
    }

    /** @return list<array<string, mixed>> */
    private function buildPendingMonitoringPenitipanPreview(): array
    {
        $preview = [];

        foreach ($this->monitoringRepo->findBookingsMissingTodayMonitoringPreview(self::PREVIEW_LIMIT) as $item) {
            $bookingId = (string) ($item['booking_id'] ?? '');
            $preview[] = [
                'pelanggan_nama' => (string) ($item['pelanggan_nama'] ?? ''),
                'kucing_nama' => (string) ($item['kucing_nama'] ?? ''),
                'check_out' => (string) ($item['check_out'] ?? ''),
                'url' => '/admin/penitipan/monitoring/tambah?booking_id=' . urlencode($bookingId),
            ];
        }

        return $preview;
    }
}
