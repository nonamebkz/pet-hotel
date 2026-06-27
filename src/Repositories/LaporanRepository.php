<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Enums\JenisLayanan;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusPembayaran;
use PDO;

final class LaporanRepository
{
    /**
     * @return array{grooming: int, penitipan: int, pet_care: int}
     */
    public function countRingkasan(string $mulai, string $akhir): array
    {
        $pdo = Database::connection();

        $stmtGrooming = $pdo->prepare(
            'SELECT COUNT(*) FROM booking_grooming
             WHERE tanggal BETWEEN :mulai AND :akhir'
        );
        $stmtGrooming->execute(['mulai' => $mulai, 'akhir' => $akhir]);

        $stmtPenitipan = $pdo->prepare(
            'SELECT COUNT(*) FROM booking_penitipan
             WHERE check_in BETWEEN :mulai AND :akhir'
        );
        $stmtPenitipan->execute(['mulai' => $mulai, 'akhir' => $akhir]);

        $stmtPetCare = $pdo->prepare(
            'SELECT COUNT(*) FROM booking_pet_care
             WHERE tanggal BETWEEN :mulai AND :akhir'
        );
        $stmtPetCare->execute(['mulai' => $mulai, 'akhir' => $akhir]);

        return [
            'grooming' => (int) $stmtGrooming->fetchColumn(),
            'penitipan' => (int) $stmtPenitipan->fetchColumn(),
            'pet_care' => (int) $stmtPetCare->fetchColumn(),
        ];
    }

    /**
     * @return array{
     *     jumlah_booking: int,
     *     total_pendapatan: float,
     *     breakdown_jenis: list<array{jenis_nama: string, jumlah: int, pendapatan: float}>,
     *     antar_jemput_jumlah: int,
     *     antar_jemput_pendapatan: float
     * }
     */
    public function aggregateGroomingMetrics(string $mulai, string $akhir, ?string $status): array
    {
        $where = 'b.tanggal BETWEEN :mulai AND :akhir';
        $params = ['mulai' => $mulai, 'akhir' => $akhir];

        if ($status !== null && $status !== '') {
            $where .= ' AND b.status = :status';
            $params['status'] = $status;
        }

        $pdo = Database::connection();

        $stmtCount = $pdo->prepare(
            "SELECT COUNT(*) FROM booking_grooming b WHERE {$where}"
        );
        $stmtCount->execute($params);
        $jumlahBooking = (int) $stmtCount->fetchColumn();

        $stmtPendapatan = $pdo->prepare(
            "SELECT COALESCE(SUM(t.total_bayar), 0)
             FROM booking_grooming b
             INNER JOIN transaksi t
                ON t.booking_id = b.id
               AND t.jenis_layanan = :jenis
               AND t.status_pembayaran = :lunas
             WHERE {$where}"
        );
        $stmtPendapatan->execute(array_merge($params, [
            'jenis' => JenisLayanan::GROOMING->value,
            'lunas' => StatusPembayaran::LUNAS->value,
        ]));
        $totalPendapatan = (float) $stmtPendapatan->fetchColumn();

        $stmtBreakdown = $pdo->prepare(
            "SELECT j.nama AS jenis_nama,
                    COUNT(*) AS jumlah,
                    COALESCE(SUM(
                        CASE WHEN t.status_pembayaran = :lunas THEN t.total_bayar ELSE 0 END
                    ), 0) AS pendapatan
             FROM booking_grooming b
             INNER JOIN jenis_grooming j ON j.id = b.jenis_grooming_id
             LEFT JOIN transaksi t
                ON t.booking_id = b.id
               AND t.jenis_layanan = :jenis
             WHERE {$where}
             GROUP BY j.id, j.nama
             ORDER BY jumlah DESC, j.nama ASC"
        );
        $stmtBreakdown->execute(array_merge($params, [
            'jenis' => JenisLayanan::GROOMING->value,
            'lunas' => StatusPembayaran::LUNAS->value,
        ]));
        $breakdownJenis = $stmtBreakdown->fetchAll(PDO::FETCH_ASSOC);

        $stmtAntar = $pdo->prepare(
            "SELECT COUNT(*) AS jumlah,
                    COALESCE(SUM(
                        CASE WHEN t.status_pembayaran = :lunas THEN t.biaya_antar_jemput ELSE 0 END
                    ), 0) AS pendapatan
             FROM booking_grooming b
             LEFT JOIN transaksi t
                ON t.booking_id = b.id
               AND t.jenis_layanan = :jenis
             WHERE {$where}
               AND b.opsi_pengantaran = :antar_jemput"
        );
        $stmtAntar->execute(array_merge($params, [
            'jenis' => JenisLayanan::GROOMING->value,
            'lunas' => StatusPembayaran::LUNAS->value,
            'antar_jemput' => OpsiPengantaran::ANTAR_JEMPUT->value,
        ]));
        $antarRow = $stmtAntar->fetch(PDO::FETCH_ASSOC) ?: ['jumlah' => 0, 'pendapatan' => 0];

        return [
            'jumlah_booking' => $jumlahBooking,
            'total_pendapatan' => $totalPendapatan,
            'breakdown_jenis' => array_map(static fn (array $row): array => [
                'jenis_nama' => (string) $row['jenis_nama'],
                'jumlah' => (int) $row['jumlah'],
                'pendapatan' => (float) $row['pendapatan'],
            ], $breakdownJenis),
            'antar_jemput_jumlah' => (int) $antarRow['jumlah'],
            'antar_jemput_pendapatan' => (float) $antarRow['pendapatan'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findGroomingRows(string $mulai, string $akhir, ?string $status): array
    {
        $where = 'b.tanggal BETWEEN :mulai AND :akhir';
        $params = ['mulai' => $mulai, 'akhir' => $akhir];

        if ($status !== null && $status !== '') {
            $where .= ' AND b.status = :status';
            $params['status'] = $status;
        }

        $sql = "SELECT b.*,
                       j.nama AS jenis_nama,
                       k.nama AS kucing_nama,
                       p.nama AS pelanggan_nama,
                       t.total_bayar,
                       t.status_pembayaran
                FROM booking_grooming b
                INNER JOIN jenis_grooming j ON j.id = b.jenis_grooming_id
                INNER JOIN kucing k ON k.id = b.kucing_id
                INNER JOIN pelanggan p ON p.id = b.pelanggan_id
                LEFT JOIN transaksi t
                    ON t.booking_id = b.id
                   AND t.jenis_layanan = :jenis
                WHERE {$where}
                ORDER BY b.tanggal DESC, b.created_at DESC";

        $params['jenis'] = JenisLayanan::GROOMING->value;

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array{
     *     jumlah_booking: int,
     *     total_hari: int,
     *     pendapatan_awal: float,
     *     pendapatan_perpanjangan: float,
     *     promo_jumlah: int,
     *     promo_total_potongan: float,
     *     antar_jemput_jumlah: int,
     *     antar_jemput_pendapatan: float
     * }
     */
    public function aggregatePenitipanMetrics(string $mulai, string $akhir, ?string $status): array
    {
        $where = 'b.check_in BETWEEN :mulai AND :akhir';
        $params = ['mulai' => $mulai, 'akhir' => $akhir];

        if ($status !== null && $status !== '') {
            $where .= ' AND b.status = :status';
            $params['status'] = $status;
        }

        $pdo = Database::connection();

        $stmtCount = $pdo->prepare(
            "SELECT COUNT(*), COALESCE(SUM(b.lama_hari), 0)
             FROM booking_penitipan b
             WHERE {$where}"
        );
        $stmtCount->execute($params);
        $countRow = $stmtCount->fetch(PDO::FETCH_NUM) ?: [0, 0];

        $stmtAwal = $pdo->prepare(
            "SELECT COALESCE(SUM(t.total_bayar), 0)
             FROM booking_penitipan b
             INNER JOIN transaksi t
                ON t.booking_id = b.id
               AND t.jenis_layanan = :jenis
               AND t.perpanjangan_penitipan_id IS NULL
               AND t.status_pembayaran = :lunas
             WHERE {$where}"
        );
        $stmtAwal->execute(array_merge($params, [
            'jenis' => JenisLayanan::PENITIPAN->value,
            'lunas' => StatusPembayaran::LUNAS->value,
        ]));
        $pendapatanAwal = (float) $stmtAwal->fetchColumn();

        $stmtPerpanjangan = $pdo->prepare(
            "SELECT COALESCE(SUM(t.total_bayar), 0)
             FROM booking_penitipan b
             INNER JOIN transaksi t
                ON t.booking_id = b.id
               AND t.jenis_layanan = :jenis
               AND t.perpanjangan_penitipan_id IS NOT NULL
               AND t.status_pembayaran = :lunas
             WHERE {$where}"
        );
        $stmtPerpanjangan->execute(array_merge($params, [
            'jenis' => JenisLayanan::PENITIPAN->value,
            'lunas' => StatusPembayaran::LUNAS->value,
        ]));
        $pendapatanPerpanjangan = (float) $stmtPerpanjangan->fetchColumn();

        $stmtPromo = $pdo->prepare(
            "SELECT COUNT(*) AS jumlah,
                    COALESCE(SUM(b.potongan_promo), 0) AS total_potongan
             FROM booking_penitipan b
             WHERE {$where}
               AND b.promo_dipakai = 1"
        );
        $stmtPromo->execute($params);
        $promoRow = $stmtPromo->fetch(PDO::FETCH_ASSOC) ?: ['jumlah' => 0, 'total_potongan' => 0];

        $stmtAntar = $pdo->prepare(
            "SELECT COUNT(*) AS jumlah,
                    COALESCE(SUM(
                        CASE WHEN t.status_pembayaran = :lunas THEN t.biaya_antar_jemput ELSE 0 END
                    ), 0) AS pendapatan
             FROM booking_penitipan b
             LEFT JOIN transaksi t
                ON t.booking_id = b.id
               AND t.jenis_layanan = :jenis
               AND t.perpanjangan_penitipan_id IS NULL
             WHERE {$where}
               AND b.opsi_pengantaran = :antar_jemput"
        );
        $stmtAntar->execute(array_merge($params, [
            'jenis' => JenisLayanan::PENITIPAN->value,
            'lunas' => StatusPembayaran::LUNAS->value,
            'antar_jemput' => OpsiPengantaran::ANTAR_JEMPUT->value,
        ]));
        $antarRow = $stmtAntar->fetch(PDO::FETCH_ASSOC) ?: ['jumlah' => 0, 'pendapatan' => 0];

        return [
            'jumlah_booking' => (int) $countRow[0],
            'total_hari' => (int) $countRow[1],
            'pendapatan_awal' => $pendapatanAwal,
            'pendapatan_perpanjangan' => $pendapatanPerpanjangan,
            'promo_jumlah' => (int) $promoRow['jumlah'],
            'promo_total_potongan' => (float) $promoRow['total_potongan'],
            'antar_jemput_jumlah' => (int) $antarRow['jumlah'],
            'antar_jemput_pendapatan' => (float) $antarRow['pendapatan'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findPenitipanRows(string $mulai, string $akhir, ?string $status): array
    {
        $where = 'b.check_in BETWEEN :mulai AND :akhir';
        $params = ['mulai' => $mulai, 'akhir' => $akhir];

        if ($status !== null && $status !== '') {
            $where .= ' AND b.status = :status';
            $params['status'] = $status;
        }

        $sql = "SELECT b.*,
                       pk.nama AS paket_nama,
                       k.nama AS kucing_nama,
                       pl.nama AS pelanggan_nama,
                       t.total_bayar AS total_bayar_awal,
                       t.status_pembayaran AS status_pembayaran_awal
                FROM booking_penitipan b
                INNER JOIN paket_penitipan pk ON pk.id = b.paket_penitipan_id
                INNER JOIN kucing k ON k.id = b.kucing_id
                INNER JOIN pelanggan pl ON pl.id = b.pelanggan_id
                LEFT JOIN transaksi t
                    ON t.booking_id = b.id
                   AND t.jenis_layanan = :jenis
                   AND t.perpanjangan_penitipan_id IS NULL
                WHERE {$where}
                ORDER BY b.check_in DESC, b.created_at DESC";

        $params['jenis'] = JenisLayanan::PENITIPAN->value;

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array{
     *     jumlah_booking: int,
     *     breakdown_layanan: list<array{layanan_nama: string, jumlah: int}>,
     *     breakdown_slot: list<array{tanggal: string, slot_waktu: string, jumlah: int}>
     * }
     */
    public function aggregatePetCareMetrics(string $mulai, string $akhir, ?string $status, ?string $layananId): array
    {
        $where = 'b.tanggal BETWEEN :mulai AND :akhir';
        $params = ['mulai' => $mulai, 'akhir' => $akhir];

        if ($status !== null && $status !== '') {
            $where .= ' AND b.status = :status';
            $params['status'] = $status;
        }

        if ($layananId !== null && $layananId !== '') {
            $where .= ' AND b.layanan_pet_care_id = :layanan_id';
            $params['layanan_id'] = $layananId;
        }

        $pdo = Database::connection();

        $stmtCount = $pdo->prepare(
            "SELECT COUNT(*) FROM booking_pet_care b WHERE {$where}"
        );
        $stmtCount->execute($params);
        $jumlahBooking = (int) $stmtCount->fetchColumn();

        $stmtLayanan = $pdo->prepare(
            "SELECT l.nama AS layanan_nama, COUNT(*) AS jumlah
             FROM booking_pet_care b
             INNER JOIN layanan_pet_care l ON l.id = b.layanan_pet_care_id
             WHERE {$where}
             GROUP BY l.id, l.nama
             ORDER BY jumlah DESC, l.nama ASC"
        );
        $stmtLayanan->execute($params);
        $breakdownLayanan = $stmtLayanan->fetchAll(PDO::FETCH_ASSOC);

        $stmtSlot = $pdo->prepare(
            "SELECT b.tanggal, b.slot_waktu, COUNT(*) AS jumlah
             FROM booking_pet_care b
             WHERE {$where}
             GROUP BY b.tanggal, b.slot_waktu
             ORDER BY b.tanggal DESC, b.slot_waktu ASC"
        );
        $stmtSlot->execute($params);
        $breakdownSlot = $stmtSlot->fetchAll(PDO::FETCH_ASSOC);

        return [
            'jumlah_booking' => $jumlahBooking,
            'breakdown_layanan' => array_map(static fn (array $row): array => [
                'layanan_nama' => (string) $row['layanan_nama'],
                'jumlah' => (int) $row['jumlah'],
            ], $breakdownLayanan),
            'breakdown_slot' => array_map(static fn (array $row): array => [
                'tanggal' => (string) $row['tanggal'],
                'slot_waktu' => (string) $row['slot_waktu'],
                'jumlah' => (int) $row['jumlah'],
            ], $breakdownSlot),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findPetCareRows(string $mulai, string $akhir, ?string $status, ?string $layananId): array
    {
        $where = 'b.tanggal BETWEEN :mulai AND :akhir';
        $params = ['mulai' => $mulai, 'akhir' => $akhir];

        if ($status !== null && $status !== '') {
            $where .= ' AND b.status = :status';
            $params['status'] = $status;
        }

        if ($layananId !== null && $layananId !== '') {
            $where .= ' AND b.layanan_pet_care_id = :layanan_id';
            $params['layanan_id'] = $layananId;
        }

        $sql = "SELECT b.*,
                       l.nama AS layanan_nama,
                       k.nama AS kucing_nama,
                       p.nama AS pelanggan_nama
                FROM booking_pet_care b
                INNER JOIN layanan_pet_care l ON l.id = b.layanan_pet_care_id
                INNER JOIN kucing k ON k.id = b.kucing_id
                INNER JOIN pelanggan p ON p.id = b.pelanggan_id
                WHERE {$where}
                ORDER BY b.tanggal DESC, b.slot_waktu DESC";

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
