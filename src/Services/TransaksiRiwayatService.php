<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JenisLayanan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusRefund;
use App\Enums\StatusVerifikasi;
use App\Repositories\TransaksiRepository;

final class TransaksiRiwayatService
{
    public function __construct(
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
    ) {}

    /**
     * @param array{status?: string} $input
     * @return array{
     *     filterStatus: string,
     *     rows: list<array<string, mixed>>,
     *     statusLabels: array<string, string>,
     *     refundLabels: array<string, string>
     * }
     */
    public function getPelangganRiwayat(string $pelangganId, array $input): array
    {
        $filterStatus = $this->normalizePembayaranStatus(trim((string) ($input['status'] ?? '')));
        $filters = $filterStatus !== '' ? ['status' => $filterStatus] : [];

        $rows = array_map(
            fn (array $row): array => $this->enrichPelangganRow($row),
            $this->transaksiRepo->findRiwayatByPelanggan($pelangganId, $filters),
        );

        return [
            'filterStatus' => $filterStatus ?? '',
            'rows' => $rows,
            'statusLabels' => StatusPembayaran::labels(),
            'refundLabels' => StatusRefund::labels(),
        ];
    }

    /**
     * @param array{
     *     status?: string,
     *     jenis?: string,
     *     mulai?: string,
     *     akhir?: string,
     *     q?: string
     * } $input
     * @return array{
     *     mulai: string,
     *     akhir: string,
     *     filterStatus: string,
     *     filterJenis: string,
     *     filterQ: string,
     *     rows: list<array<string, mixed>>,
     *     statusLabels: array<string, string>,
     *     refundLabels: array<string, string>
     * }
     */
    public function getAdminRiwayat(array $input): array
    {
        $periode = $this->resolvePeriode($input);
        $filterStatus = $this->normalizePembayaranStatus(trim((string) ($input['status'] ?? '')));
        $filterJenis = $this->normalizeJenis(trim((string) ($input['jenis'] ?? '')));
        $filterQ = trim((string) ($input['q'] ?? ''));

        $filters = [
            'mulai' => $periode['mulai'],
            'akhir' => $periode['akhir'],
        ];

        if ($filterStatus !== null && $filterStatus !== '') {
            $filters['status'] = $filterStatus;
        }

        if ($filterJenis !== null && $filterJenis !== '') {
            $filters['jenis'] = $filterJenis;
        }

        if ($filterQ !== '') {
            $filters['q'] = $filterQ;
        }

        $rows = array_map(
            fn (array $row): array => $this->enrichAdminRow($row),
            $this->transaksiRepo->findRiwayatForAdmin($filters),
        );

        return [
            'mulai' => $periode['mulai'],
            'akhir' => $periode['akhir'],
            'filterStatus' => $filterStatus ?? '',
            'filterJenis' => $filterJenis ?? '',
            'filterQ' => $filterQ,
            'rows' => $rows,
            'statusLabels' => StatusPembayaran::labels(),
            'refundLabels' => StatusRefund::labels(),
        ];
    }

    /** @param array<string, mixed> $row */
    public function buktiDitolak(array $row): bool
    {
        return (string) ($row['bukti_status_verifikasi'] ?? '') === StatusVerifikasi::DITOLAK->value;
    }

    /**
     * @param array<string, mixed> $row
     * @param array<string, string> $statusLabels
     */
    public function displayStatusLabel(array $row, array $statusLabels): string
    {
        $status = (string) ($row['status_pembayaran'] ?? '');

        return $statusLabels[$status] ?? $status;
    }

    /** @param array<string, mixed> $row */
    private function enrichPelangganRow(array $row): array
    {
        $jenis = (string) $row['jenis_layanan'];
        $isPerpanjangan = !empty($row['perpanjangan_penitipan_id']);
        $bookingId = (string) $row['booking_id'];

        if ($jenis === JenisLayanan::GROOMING->value) {
            $row['tagihan_jenis'] = 'Grooming';
            $row['payment_url'] = '/grooming/pembayaran?id=' . urlencode($bookingId);
            $row['detail_url'] = '/grooming/detail?id=' . urlencode($bookingId);
            $row['invoice_url'] = '/grooming/invoice?id=' . urlencode($bookingId);
            $row['tanggal_display'] = !empty($row['grooming_tanggal'])
                ? date('d/m/Y', strtotime((string) $row['grooming_tanggal']))
                : '-';
        } elseif ($isPerpanjangan) {
            $row['tagihan_jenis'] = 'Perpanjangan Penitipan';
            $row['payment_url'] = '/penitipan/perpanjangan/pembayaran?id='
                . urlencode((string) $row['perpanjangan_penitipan_id']);
            $row['detail_url'] = '/penitipan/detail?id=' . urlencode($bookingId);
            $row['invoice_url'] = null;
            $row['tanggal_display'] = !empty($row['penitipan_check_in'])
                ? date('d/m/Y', strtotime((string) $row['penitipan_check_in']))
                    . ' — ' . date('d/m/Y', strtotime((string) ($row['penitipan_check_out'] ?? $row['penitipan_check_in'])))
                : '-';
        } else {
            $row['tagihan_jenis'] = 'Penitipan';
            $row['payment_url'] = '/penitipan/pembayaran?id=' . urlencode($bookingId);
            $row['detail_url'] = '/penitipan/detail?id=' . urlencode($bookingId);
            $row['invoice_url'] = '/penitipan/invoice?id=' . urlencode($bookingId);
            $checkIn = (string) ($row['penitipan_check_in'] ?? '');
            $checkOut = (string) ($row['penitipan_check_out'] ?? '');
            $row['tanggal_display'] = $checkIn !== ''
                ? date('d/m/Y', strtotime($checkIn)) . ' — ' . date('d/m/Y', strtotime($checkOut))
                : '-';
        }

        $row['bukti_ditolak'] = $this->buktiDitolak($row);
        $row['has_invoice'] = !empty($row['invoice_id']);

        return $row;
    }

    /** @param array<string, mixed> $row */
    private function enrichAdminRow(array $row): array
    {
        $jenis = (string) $row['jenis_layanan'];
        $isPerpanjangan = !empty($row['perpanjangan_penitipan_id']);
        $bookingId = (string) $row['booking_id'];

        if ($jenis === JenisLayanan::GROOMING->value) {
            $row['tagihan_jenis'] = 'Grooming';
            $row['admin_booking_url'] = '/admin/grooming/booking?tanggal='
                . urlencode((string) ($row['grooming_tanggal'] ?? date('Y-m-d')));
            $row['tanggal_display'] = !empty($row['grooming_tanggal'])
                ? date('d/m/Y', strtotime((string) $row['grooming_tanggal']))
                : '-';
        } elseif ($isPerpanjangan) {
            $row['tagihan_jenis'] = 'Perpanjangan Penitipan';
            $row['admin_booking_url'] = '/admin/penitipan/booking?check_in='
                . urlencode((string) ($row['penitipan_check_in'] ?? date('Y-m-d')));
            $row['tanggal_display'] = !empty($row['penitipan_check_in'])
                ? date('d/m/Y', strtotime((string) $row['penitipan_check_in']))
                    . ' — ' . date('d/m/Y', strtotime((string) ($row['penitipan_check_out'] ?? $row['penitipan_check_in'])))
                : '-';
        } else {
            $row['tagihan_jenis'] = 'Penitipan';
            $row['admin_booking_url'] = '/admin/penitipan/booking?check_in='
                . urlencode((string) ($row['penitipan_check_in'] ?? date('Y-m-d')));
            $checkIn = (string) ($row['penitipan_check_in'] ?? '');
            $checkOut = (string) ($row['penitipan_check_out'] ?? '');
            $row['tanggal_display'] = $checkIn !== ''
                ? date('d/m/Y', strtotime($checkIn)) . ' — ' . date('d/m/Y', strtotime($checkOut))
                : '-';
        }

        $row['bukti_ditolak'] = $this->buktiDitolak($row);
        $row['booking_id_short'] = substr($bookingId, 0, 8) . '…';

        return $row;
    }

    private function normalizePembayaranStatus(string $status): ?string
    {
        if ($status === '') {
            return null;
        }

        $labels = StatusPembayaran::labels();

        return array_key_exists($status, $labels) ? $status : null;
    }

    private function normalizeJenis(string $jenis): ?string
    {
        if ($jenis === '') {
            return null;
        }

        return in_array($jenis, [JenisLayanan::GROOMING->value, JenisLayanan::PENITIPAN->value], true)
            ? $jenis
            : null;
    }

    /**
     * @param array{mulai?: string, akhir?: string} $input
     * @return array{mulai: string, akhir: string}
     */
    private function resolvePeriode(array $input): array
    {
        $defaultMulai = date('Y-m-01');
        $defaultAkhir = date('Y-m-t');

        $mulai = trim((string) ($input['mulai'] ?? ''));
        $akhir = trim((string) ($input['akhir'] ?? ''));

        if ($mulai === '' || !$this->isValidDate($mulai)) {
            $mulai = $defaultMulai;
        }

        if ($akhir === '' || !$this->isValidDate($akhir)) {
            $akhir = $defaultAkhir;
        }

        if ($mulai > $akhir) {
            [$mulai, $akhir] = [$akhir, $mulai];
        }

        return [
            'mulai' => $mulai,
            'akhir' => $akhir,
        ];
    }

    private function isValidDate(string $date): bool
    {
        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }
}
