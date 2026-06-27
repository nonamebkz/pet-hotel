<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Enums\JenisLayanan;
use PDO;

final class TransaksiRepository
{
    public function create(
        string $id,
        string $pelangganId,
        string $jenisLayanan,
        string $bookingId,
        float $subtotalLayanan,
        float $biayaAntarJemput,
        float $totalBayar,
        PDO $pdo,
        float $potonganPromo = 0.0,
        ?string $perpanjanganPenitipanId = null,
    ): void {
        $stmt = $pdo->prepare(
            "INSERT INTO transaksi (
                id, pelanggan_id, jenis_layanan, booking_id, perpanjangan_penitipan_id,
                subtotal_layanan, potongan_promo, biaya_antar_jemput, total_bayar,
                status_pembayaran
             ) VALUES (
                :id, :pelanggan_id, :jenis_layanan, :booking_id, :perpanjangan_id,
                :subtotal_layanan, :potongan_promo, :biaya_antar_jemput, :total_bayar,
                'MENUNGGU_PEMBAYARAN'
             )"
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
            'jenis_layanan' => $jenisLayanan,
            'booking_id' => $bookingId,
            'perpanjangan_id' => $perpanjanganPenitipanId,
            'subtotal_layanan' => $subtotalLayanan,
            'potongan_promo' => $potonganPromo,
            'biaya_antar_jemput' => $biayaAntarJemput,
            'total_bayar' => $totalBayar,
        ]);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM transaksi WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByGroomingBooking(string $bookingId): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM transaksi
             WHERE jenis_layanan = :jenis AND booking_id = :booking_id
             LIMIT 1"
        );
        $stmt->execute([
            'jenis' => JenisLayanan::GROOMING->value,
            'booking_id' => $bookingId,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByPenitipanBooking(string $bookingId): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM transaksi
             WHERE jenis_layanan = :jenis
               AND booking_id = :booking_id
               AND perpanjangan_penitipan_id IS NULL
             LIMIT 1"
        );
        $stmt->execute([
            'jenis' => JenisLayanan::PENITIPAN->value,
            'booking_id' => $bookingId,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByPerpanjanganId(string $perpanjanganId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM transaksi WHERE perpanjangan_penitipan_id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $perpanjanganId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByIdAndPelanggan(string $id, string $pelangganId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM transaksi WHERE id = :id AND pelanggan_id = :pelanggan_id LIMIT 1'
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function setBatasWaktuBayar(string $id, string $batasWaktu, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'UPDATE transaksi SET batas_waktu_bayar = :batas_waktu WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'batas_waktu' => $batasWaktu,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function updateStatusPembayaran(string $id, string $status, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'UPDATE transaksi SET status_pembayaran = :status WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function markLunas(string $id, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            "UPDATE transaksi SET
                status_pembayaran = 'LUNAS',
                dibayar_at = NOW()
             WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /** @return list<array<string, mixed>> */
    public function findPendingVerification(): array
    {
        $stmt = Database::connection()->query(
            "SELECT t.*,
                    b.tanggal AS booking_tanggal,
                    b.jam_grooming,
                    j.nama AS jenis_nama,
                    p.nama AS pelanggan_nama,
                    bt.id AS bukti_id,
                    bt.file_url AS bukti_file_url,
                    bt.status_verifikasi,
                    bt.uploaded_at AS bukti_uploaded_at
             FROM transaksi t
             INNER JOIN booking_grooming b ON b.id = t.booking_id AND t.jenis_layanan = 'GROOMING'
             INNER JOIN jenis_grooming j ON j.id = b.jenis_grooming_id
             INNER JOIN pelanggan p ON p.id = t.pelanggan_id
             INNER JOIN bukti_transfer bt ON bt.transaksi_id = t.id
             WHERE t.status_pembayaran = 'MENUNGGU_VERIFIKASI'
               AND bt.status_verifikasi = 'MENUNGGU'
             ORDER BY bt.uploaded_at ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function findPendingVerificationPenitipan(): array
    {
        $stmt = Database::connection()->query(
            "SELECT t.*,
                    b.check_in,
                    b.check_out,
                    b.lama_hari,
                    p.nama AS paket_nama,
                    pl.nama AS pelanggan_nama,
                    bt.id AS bukti_id,
                    bt.file_url AS bukti_file_url,
                    bt.status_verifikasi,
                    bt.uploaded_at AS bukti_uploaded_at,
                    pp.check_out_baru AS perpanjangan_check_out_baru
             FROM transaksi t
             INNER JOIN booking_penitipan b ON b.id = t.booking_id AND t.jenis_layanan = 'PENITIPAN'
             INNER JOIN paket_penitipan p ON p.id = b.paket_penitipan_id
             INNER JOIN pelanggan pl ON pl.id = t.pelanggan_id
             LEFT JOIN perpanjangan_penitipan pp ON pp.id = t.perpanjangan_penitipan_id
             INNER JOIN bukti_transfer bt ON bt.transaksi_id = t.id
             WHERE t.status_pembayaran = 'MENUNGGU_VERIFIKASI'
               AND bt.status_verifikasi = 'MENUNGGU'
             ORDER BY bt.uploaded_at ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function findAllPendingVerification(): array
    {
        return array_merge(
            $this->findPendingVerification(),
            $this->findPendingVerificationPenitipan(),
        );
    }

    /**
     * @return array{grooming: int, penitipan: int, total: int}
     */
    public function countPendingVerification(): array
    {
        $pdo = Database::connection();

        $stmtGrooming = $pdo->query(
            "SELECT COUNT(*)
             FROM transaksi t
             INNER JOIN bukti_transfer bt ON bt.transaksi_id = t.id
             WHERE t.jenis_layanan = 'GROOMING'
               AND t.status_pembayaran = 'MENUNGGU_VERIFIKASI'
               AND bt.status_verifikasi = 'MENUNGGU'"
        );
        $grooming = (int) $stmtGrooming->fetchColumn();

        $stmtPenitipan = $pdo->query(
            "SELECT COUNT(*)
             FROM transaksi t
             INNER JOIN bukti_transfer bt ON bt.transaksi_id = t.id
             WHERE t.jenis_layanan = 'PENITIPAN'
               AND t.status_pembayaran = 'MENUNGGU_VERIFIKASI'
               AND bt.status_verifikasi = 'MENUNGGU'"
        );
        $penitipan = (int) $stmtPenitipan->fetchColumn();

        return [
            'grooming' => $grooming,
            'penitipan' => $penitipan,
            'total' => $grooming + $penitipan,
        ];
    }

    /** @return list<array<string, mixed>> */
    public function findPendingPaymentByPelanggan(string $pelangganId): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT t.*,
                    CASE
                        WHEN t.jenis_layanan = 'GROOMING' THEN jg.nama
                        WHEN t.perpanjangan_penitipan_id IS NOT NULL THEN CONCAT('Perpanjangan — ', ppak.nama)
                        ELSE ppak.nama
                    END AS layanan_label,
                    bg.tanggal AS grooming_tanggal,
                    bp.check_in AS penitipan_check_in,
                    bp.check_out AS penitipan_check_out,
                    pp.tambah_hari AS perpanjangan_tambah_hari
             FROM transaksi t
             LEFT JOIN booking_grooming bg ON bg.id = t.booking_id AND t.jenis_layanan = 'GROOMING'
             LEFT JOIN jenis_grooming jg ON jg.id = bg.jenis_grooming_id
             LEFT JOIN booking_penitipan bp ON bp.id = t.booking_id AND t.jenis_layanan = 'PENITIPAN'
             LEFT JOIN paket_penitipan ppak ON ppak.id = bp.paket_penitipan_id
             LEFT JOIN perpanjangan_penitipan pp ON pp.id = t.perpanjangan_penitipan_id
             WHERE t.pelanggan_id = :pelanggan_id
               AND t.status_pembayaran = 'MENUNGGU_PEMBAYARAN'
             ORDER BY t.batas_waktu_bayar IS NULL, t.batas_waktu_bayar ASC, t.created_at ASC"
        );
        $stmt->execute(['pelanggan_id' => $pelangganId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function (array $row): array {
            $jenis = (string) $row['jenis_layanan'];
            $isPerpanjangan = !empty($row['perpanjangan_penitipan_id']);

            if ($jenis === JenisLayanan::GROOMING->value) {
                $row['tagihan_jenis'] = 'Grooming';
                $row['payment_url'] = '/grooming/pembayaran?id=' . urlencode((string) $row['booking_id']);
            } elseif ($isPerpanjangan) {
                $row['tagihan_jenis'] = 'Perpanjangan Penitipan';
                $row['payment_url'] = '/penitipan/perpanjangan/pembayaran?id='
                    . urlencode((string) $row['perpanjangan_penitipan_id']);
            } else {
                $row['tagihan_jenis'] = 'Penitipan';
                $row['payment_url'] = '/penitipan/pembayaran?id=' . urlencode((string) $row['booking_id']);
            }

            return $row;
        }, $rows);
    }

    public function updateStatusRefund(string $id, string $status, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'UPDATE transaksi SET status_refund = :status WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function findLunasByGroomingBooking(string $bookingId): ?array
    {
        $transaksi = $this->findByGroomingBooking($bookingId);

        if (!$transaksi || (string) $transaksi['status_pembayaran'] !== 'LUNAS') {
            return null;
        }

        return $transaksi;
    }

    public function findLunasByPenitipanBooking(string $bookingId): ?array
    {
        $transaksi = $this->findByPenitipanBooking($bookingId);

        if (!$transaksi || (string) $transaksi['status_pembayaran'] !== 'LUNAS') {
            return null;
        }

        return $transaksi;
    }

    /**
     * @param array{status?: string|null} $filters
     * @return list<array<string, mixed>>
     */
    public function findRiwayatByPelanggan(string $pelangganId, array $filters = []): array
    {
        $params = ['pelanggan_id' => $pelangganId];
        $conditions = ['t.pelanggan_id = :pelanggan_id'];

        $status = trim((string) ($filters['status'] ?? ''));
        if ($status !== '') {
            $conditions[] = 't.status_pembayaran = :status';
            $params['status'] = $status;
        }

        $where = implode(' AND ', $conditions);

        $stmt = Database::connection()->prepare(
            "SELECT t.*,
                    CASE
                        WHEN t.jenis_layanan = 'GROOMING' THEN jg.nama
                        WHEN t.perpanjangan_penitipan_id IS NOT NULL THEN CONCAT('Perpanjangan — ', ppak.nama)
                        ELSE ppak.nama
                    END AS layanan_label,
                    bg.tanggal AS grooming_tanggal,
                    bp.check_in AS penitipan_check_in,
                    bp.check_out AS penitipan_check_out,
                    pp.tambah_hari AS perpanjangan_tambah_hari,
                    bt.status_verifikasi AS bukti_status_verifikasi,
                    bt.catatan_penolakan AS bukti_catatan_penolakan,
                    bt.file_url AS bukti_file_url,
                    inv.id AS invoice_id,
                    inv.nomor_invoice
             FROM transaksi t
             LEFT JOIN booking_grooming bg ON bg.id = t.booking_id AND t.jenis_layanan = 'GROOMING'
             LEFT JOIN jenis_grooming jg ON jg.id = bg.jenis_grooming_id
             LEFT JOIN booking_penitipan bp ON bp.id = t.booking_id AND t.jenis_layanan = 'PENITIPAN'
             LEFT JOIN paket_penitipan ppak ON ppak.id = bp.paket_penitipan_id
             LEFT JOIN perpanjangan_penitipan pp ON pp.id = t.perpanjangan_penitipan_id
             LEFT JOIN bukti_transfer bt ON bt.transaksi_id = t.id
             LEFT JOIN invoice inv ON inv.transaksi_id = t.id
             WHERE {$where}
             ORDER BY t.created_at DESC"
        );
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array{
     *     status?: string|null,
     *     jenis?: string|null,
     *     mulai?: string,
     *     akhir?: string,
     *     q?: string
     * } $filters
     * @return list<array<string, mixed>>
     */
    public function findRiwayatForAdmin(array $filters = []): array
    {
        $params = [];
        $conditions = ['1 = 1'];

        $status = trim((string) ($filters['status'] ?? ''));
        if ($status !== '') {
            $conditions[] = 't.status_pembayaran = :status';
            $params['status'] = $status;
        }

        $jenis = trim((string) ($filters['jenis'] ?? ''));
        if ($jenis !== '') {
            $conditions[] = 't.jenis_layanan = :jenis';
            $params['jenis'] = $jenis;
        }

        $mulai = trim((string) ($filters['mulai'] ?? ''));
        if ($mulai !== '') {
            $conditions[] = 'DATE(t.created_at) >= :mulai';
            $params['mulai'] = $mulai;
        }

        $akhir = trim((string) ($filters['akhir'] ?? ''));
        if ($akhir !== '') {
            $conditions[] = 'DATE(t.created_at) <= :akhir';
            $params['akhir'] = $akhir;
        }

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $conditions[] = 'pl.nama LIKE :q';
            $params['q'] = '%' . $q . '%';
        }

        $where = implode(' AND ', $conditions);

        $stmt = Database::connection()->prepare(
            "SELECT t.*,
                    pl.nama AS pelanggan_nama,
                    pl.no_telepon AS pelanggan_telepon,
                    CASE
                        WHEN t.jenis_layanan = 'GROOMING' THEN jg.nama
                        WHEN t.perpanjangan_penitipan_id IS NOT NULL THEN CONCAT('Perpanjangan — ', ppak.nama)
                        ELSE ppak.nama
                    END AS layanan_label,
                    bg.tanggal AS grooming_tanggal,
                    bp.check_in AS penitipan_check_in,
                    bp.check_out AS penitipan_check_out,
                    pp.tambah_hari AS perpanjangan_tambah_hari,
                    bt.status_verifikasi AS bukti_status_verifikasi,
                    bt.catatan_penolakan AS bukti_catatan_penolakan,
                    bt.file_url AS bukti_file_url,
                    inv.id AS invoice_id,
                    inv.nomor_invoice
             FROM transaksi t
             INNER JOIN pelanggan pl ON pl.id = t.pelanggan_id
             LEFT JOIN booking_grooming bg ON bg.id = t.booking_id AND t.jenis_layanan = 'GROOMING'
             LEFT JOIN jenis_grooming jg ON jg.id = bg.jenis_grooming_id
             LEFT JOIN booking_penitipan bp ON bp.id = t.booking_id AND t.jenis_layanan = 'PENITIPAN'
             LEFT JOIN paket_penitipan ppak ON ppak.id = bp.paket_penitipan_id
             LEFT JOIN perpanjangan_penitipan pp ON pp.id = t.perpanjangan_penitipan_id
             LEFT JOIN bukti_transfer bt ON bt.transaksi_id = t.id
             LEFT JOIN invoice inv ON inv.transaksi_id = t.id
             WHERE {$where}
             ORDER BY t.created_at DESC"
        );
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
