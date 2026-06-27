<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JenisLayanan;
use App\Repositories\StaffDashboardRepository;
use App\Repositories\TransaksiRepository;

final class StaffDashboardService
{
    private const PREVIEW_LIMIT = 5;

    public function __construct(
        private readonly StaffDashboardRepository $dashboardRepo = new StaffDashboardRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
    ) {}

    /**
     * @return array{
     *   today: string,
     *   bookingsToday: array{grooming: int, penitipan: int, pet_care: int, total: int},
     *   pendingVerification: array{grooming: int, penitipan: int, total: int},
     *   penitipanAktif: int,
     *   pendapatan: array{harian: float, mingguan: float, mingguMulai: string, mingguAkhir: string},
     *   pendingVerificationPreview: list<array<string, mixed>>
     * }
     */
    public function getHomeSummary(): array
    {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime($today . ' +1 day'));
        $mingguMulai = date('Y-m-d', strtotime('monday this week'));

        $harianMulai = $today . ' 00:00:00';
        $mingguMulaiDt = $mingguMulai . ' 00:00:00';
        $akhirEksklusif = $tomorrow . ' 00:00:00';

        return [
            'today' => $today,
            'bookingsToday' => $this->dashboardRepo->countBookingsToday($today),
            'pendingVerification' => $this->transaksiRepo->countPendingVerification(),
            'penitipanAktif' => $this->dashboardRepo->countPenitipanAktif(),
            'pendapatan' => [
                'harian' => $this->dashboardRepo->sumVerifiedRevenue($harianMulai, $akhirEksklusif),
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
                    'url' => '/admin/penitipan/pembayaran',
                ];
            }
        }

        return $preview;
    }
}
