<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class BookingGroomingRepository
{
    public function create(
        string $id,
        string $pelangganId,
        string $kucingId,
        string $jenisGroomingId,
        string $kuotaGroomingId,
        string $tanggal,
        string $opsiPengantaran,
        ?float $jarakKm,
        float $biayaAntarJemput,
        float $hargaLayanan,
        ?string $catatan,
        PDO $pdo,
    ): void {
        $stmt = $pdo->prepare(
            "INSERT INTO booking_grooming (
                id, pelanggan_id, kucing_id, jenis_grooming_id, kuota_grooming_id,
                tanggal, opsi_pengantaran, jarak_km, biaya_antar_jemput, harga_layanan,
                status, catatan
             ) VALUES (
                :id, :pelanggan_id, :kucing_id, :jenis_grooming_id, :kuota_grooming_id,
                :tanggal, :opsi_pengantaran, :jarak_km, :biaya_antar_jemput, :harga_layanan,
                'MENUNGGU_KONFIRMASI', :catatan
             )"
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
            'kucing_id' => $kucingId,
            'jenis_grooming_id' => $jenisGroomingId,
            'kuota_grooming_id' => $kuotaGroomingId,
            'tanggal' => $tanggal,
            'opsi_pengantaran' => $opsiPengantaran,
            'jarak_km' => $jarakKm,
            'biaya_antar_jemput' => $biayaAntarJemput,
            'harga_layanan' => $hargaLayanan,
            'catatan' => $catatan,
        ]);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM booking_grooming WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByIdAndPelanggan(string $id, string $pelangganId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM booking_grooming WHERE id = :id AND pelanggan_id = :pelanggan_id LIMIT 1'
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
                    j.nama AS jenis_nama,
                    k.nama AS kucing_nama
             FROM booking_grooming b
             INNER JOIN jenis_grooming j ON j.id = b.jenis_grooming_id
             INNER JOIN kucing k ON k.id = b.kucing_id
             WHERE b.pelanggan_id = :pelanggan_id
             ORDER BY b.tanggal DESC, b.created_at DESC'
        );
        $stmt->execute(['pelanggan_id' => $pelangganId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findDetailByIdAndPelanggan(string $id, string $pelangganId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT b.*,
                    j.nama AS jenis_nama,
                    j.deskripsi AS jenis_deskripsi,
                    k.nama AS kucing_nama,
                    p.nama AS pelanggan_nama
             FROM booking_grooming b
             INNER JOIN jenis_grooming j ON j.id = b.jenis_grooming_id
             INNER JOIN kucing k ON k.id = b.kucing_id
             INNER JOIN pelanggan p ON p.id = b.pelanggan_id
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
                    j.nama AS jenis_nama,
                    k.nama AS kucing_nama,
                    p.nama AS pelanggan_nama,
                    p.email AS pelanggan_email,
                    p.no_telepon AS pelanggan_telepon,
                    p.alamat_lengkap AS pelanggan_alamat
             FROM booking_grooming b
             INNER JOIN jenis_grooming j ON j.id = b.jenis_grooming_id
             INNER JOIN kucing k ON k.id = b.kucing_id
             INNER JOIN pelanggan p ON p.id = b.pelanggan_id
             WHERE b.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @param array{status?: string, tanggal?: string} $filters
     * @return list<array<string, mixed>>
     */
    public function findAllForAdmin(array $filters = []): array
    {
        $sql = 'SELECT b.*,
                       j.nama AS jenis_nama,
                       k.nama AS kucing_nama,
                       p.nama AS pelanggan_nama,
                       p.email AS pelanggan_email
                FROM booking_grooming b
                INNER JOIN jenis_grooming j ON j.id = b.jenis_grooming_id
                INNER JOIN kucing k ON k.id = b.kucing_id
                INNER JOIN pelanggan p ON p.id = b.pelanggan_id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND b.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['tanggal'])) {
            $sql .= ' AND b.tanggal = :tanggal';
            $params['tanggal'] = $filters['tanggal'];
        }

        $sql .= ' ORDER BY b.tanggal DESC, b.created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(string $id, string $status, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'UPDATE booking_grooming SET status = :status WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function confirm(
        string $id,
        string $staffId,
        string $jamGrooming,
        PDO $pdo,
    ): bool {
        $stmt = $pdo->prepare(
            "UPDATE booking_grooming SET
                status = 'MENUNGGU_PEMBAYARAN',
                jam_grooming = :jam_grooming,
                dikonfirmasi_oleh_staff_id = :staff_id
             WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'jam_grooming' => $jamGrooming,
            'staff_id' => $staffId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function cancel(string $id, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            "UPDATE booking_grooming SET status = 'DIBATALKAN' WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
