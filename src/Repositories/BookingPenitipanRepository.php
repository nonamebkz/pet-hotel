<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class BookingPenitipanRepository
{
    public function create(
        string $id,
        string $pelangganId,
        string $kucingId,
        string $paketId,
        string $kamarId,
        string $checkIn,
        string $checkOut,
        int $lamaHari,
        bool $promoDipakai,
        float $subtotalPenitipan,
        float $potonganPromo,
        string $opsiPengantaran,
        ?float $jarakKm,
        float $biayaAntarJemput,
        ?string $catatanMakan,
        PDO $pdo,
    ): void {
        $stmt = $pdo->prepare(
            "INSERT INTO booking_penitipan (
                id, pelanggan_id, kucing_id, paket_penitipan_id, kamar_penitipan_id,
                check_in, check_out, lama_hari, promo_dipakai,
                subtotal_penitipan, potongan_promo, opsi_pengantaran,
                jarak_km, biaya_antar_jemput, status, catatan_makan
             ) VALUES (
                :id, :pelanggan_id, :kucing_id, :paket_id, :kamar_id,
                :check_in, :check_out, :lama_hari, :promo_dipakai,
                :subtotal_penitipan, :potongan_promo, :opsi_pengantaran,
                :jarak_km, :biaya_antar_jemput, 'MENUNGGU_KONFIRMASI', :catatan_makan
             )"
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
            'kucing_id' => $kucingId,
            'paket_id' => $paketId,
            'kamar_id' => $kamarId,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'lama_hari' => $lamaHari,
            'promo_dipakai' => $promoDipakai ? 1 : 0,
            'subtotal_penitipan' => $subtotalPenitipan,
            'potongan_promo' => $potonganPromo,
            'opsi_pengantaran' => $opsiPengantaran,
            'jarak_km' => $jarakKm,
            'biaya_antar_jemput' => $biayaAntarJemput,
            'catatan_makan' => $catatanMakan,
        ]);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM booking_penitipan WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByIdAndPelanggan(string $id, string $pelangganId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM booking_penitipan WHERE id = :id AND pelanggan_id = :pelanggan_id LIMIT 1'
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function findAllByPelanggan(string $pelangganId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT b.*,
                    p.nama AS paket_nama,
                    k.nama AS kucing_nama,
                    km.nama_kamar
             FROM booking_penitipan b
             INNER JOIN paket_penitipan p ON p.id = b.paket_penitipan_id
             INNER JOIN kucing k ON k.id = b.kucing_id
             INNER JOIN kamar_penitipan km ON km.id = b.kamar_penitipan_id
             WHERE b.pelanggan_id = :pelanggan_id
             ORDER BY b.check_in DESC, b.created_at DESC'
        );
        $stmt->execute(['pelanggan_id' => $pelangganId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findDetailByIdAndPelanggan(string $id, string $pelangganId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT b.*,
                    p.nama AS paket_nama,
                    p.harga_per_hari,
                    k.nama AS kucing_nama,
                    km.nama_kamar,
                    pl.nama AS pelanggan_nama
             FROM booking_penitipan b
             INNER JOIN paket_penitipan p ON p.id = b.paket_penitipan_id
             INNER JOIN kucing k ON k.id = b.kucing_id
             INNER JOIN kamar_penitipan km ON km.id = b.kamar_penitipan_id
             INNER JOIN pelanggan pl ON pl.id = b.pelanggan_id
             WHERE b.id = :id AND b.pelanggan_id = :pelanggan_id
             LIMIT 1'
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findDetailById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT b.*,
                    p.nama AS paket_nama,
                    p.harga_per_hari,
                    k.nama AS kucing_nama,
                    km.nama_kamar,
                    pl.nama AS pelanggan_nama,
                    pl.email AS pelanggan_email,
                    pl.no_telepon AS pelanggan_telepon,
                    pl.alamat_lengkap AS pelanggan_alamat
             FROM booking_penitipan b
             INNER JOIN paket_penitipan p ON p.id = b.paket_penitipan_id
             INNER JOIN kucing k ON k.id = b.kucing_id
             INNER JOIN kamar_penitipan km ON km.id = b.kamar_penitipan_id
             INNER JOIN pelanggan pl ON pl.id = b.pelanggan_id
             WHERE b.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @param array{status?: string, check_in?: string} $filters
     * @return list<array<string, mixed>>
     */
    public function findAllForAdmin(array $filters = []): array
    {
        $sql = 'SELECT b.*,
                       p.nama AS paket_nama,
                       k.nama AS kucing_nama,
                       km.nama_kamar,
                       pl.nama AS pelanggan_nama,
                       pl.email AS pelanggan_email
                FROM booking_penitipan b
                INNER JOIN paket_penitipan p ON p.id = b.paket_penitipan_id
                INNER JOIN kucing k ON k.id = b.kucing_id
                INNER JOIN kamar_penitipan km ON km.id = b.kamar_penitipan_id
                INNER JOIN pelanggan pl ON pl.id = b.pelanggan_id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND b.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['check_in'])) {
            $sql .= ' AND b.check_in = :check_in';
            $params['check_in'] = $filters['check_in'];
        }

        $sql .= ' ORDER BY b.check_in DESC, b.created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(string $id, string $status, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'UPDATE booking_penitipan SET status = :status WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function confirm(string $id, string $staffId, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            "UPDATE booking_penitipan SET
                status = 'MENUNGGU_PEMBAYARAN',
                dikonfirmasi_oleh_staff_id = :staff_id
             WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'staff_id' => $staffId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function cancel(string $id, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            "UPDATE booking_penitipan SET status = 'DIBATALKAN' WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    public function extendStay(
        string $id,
        string $newCheckOut,
        int $tambahHari,
        float $subtotalTambahan,
        PDO $pdo,
    ): bool {
        $stmt = $pdo->prepare(
            'UPDATE booking_penitipan SET
                check_out = :check_out,
                lama_hari = lama_hari + :tambah_hari,
                subtotal_penitipan = subtotal_penitipan + :subtotal_tambahan
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'check_out' => $newCheckOut,
            'tambah_hari' => $tambahHari,
            'subtotal_tambahan' => $subtotalTambahan,
        ]);

        return $stmt->rowCount() > 0;
    }
}
