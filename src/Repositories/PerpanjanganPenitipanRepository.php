<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class PerpanjanganPenitipanRepository
{
    public function create(
        string $id,
        string $bookingId,
        string $checkOutSebelum,
        string $checkOutBaru,
        int $tambahHari,
        float $subtotalTambahan,
        PDO $pdo,
    ): void {
        $stmt = $pdo->prepare(
            "INSERT INTO perpanjangan_penitipan (
                id, booking_penitipan_id, check_out_sebelum, check_out_baru,
                tambah_hari, subtotal_tambahan, status
             ) VALUES (
                :id, :booking_id, :check_out_sebelum, :check_out_baru,
                :tambah_hari, :subtotal_tambahan, 'MENUNGGU_KONFIRMASI'
             )"
        );
        $stmt->execute([
            'id' => $id,
            'booking_id' => $bookingId,
            'check_out_sebelum' => $checkOutSebelum,
            'check_out_baru' => $checkOutBaru,
            'tambah_hari' => $tambahHari,
            'subtotal_tambahan' => $subtotalTambahan,
        ]);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM perpanjangan_penitipan WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByIdAndPelanggan(string $id, string $pelangganId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT pp.*
             FROM perpanjangan_penitipan pp
             INNER JOIN booking_penitipan b ON b.id = pp.booking_penitipan_id
             WHERE pp.id = :id AND b.pelanggan_id = :pelanggan_id
             LIMIT 1'
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function findByBookingId(string $bookingId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM perpanjangan_penitipan
             WHERE booking_penitipan_id = :booking_id
             ORDER BY created_at DESC'
        );
        $stmt->execute(['booking_id' => $bookingId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array{status?: string} $filters
     * @return list<array<string, mixed>>
     */
    public function findAllForAdmin(array $filters = []): array
    {
        $sql = 'SELECT pp.*,
                       b.check_in,
                       b.check_out AS booking_check_out,
                       k.nama AS kucing_nama,
                       p.nama AS pelanggan_nama,
                       pk.nama AS paket_nama
                FROM perpanjangan_penitipan pp
                INNER JOIN booking_penitipan b ON b.id = pp.booking_penitipan_id
                INNER JOIN kucing k ON k.id = b.kucing_id
                INNER JOIN pelanggan p ON p.id = b.pelanggan_id
                INNER JOIN paket_penitipan pk ON pk.id = b.paket_penitipan_id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND pp.status = :status';
            $params['status'] = $filters['status'];
        }

        $sql .= ' ORDER BY pp.created_at DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(string $id, string $status, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            'UPDATE perpanjangan_penitipan SET status = :status WHERE id = :id'
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
            "UPDATE perpanjangan_penitipan SET
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

    public function reject(string $id, string $staffId, ?string $catatan, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            "UPDATE perpanjangan_penitipan SET
                status = 'DITOLAK',
                dikonfirmasi_oleh_staff_id = :staff_id,
                catatan_penolakan = :catatan
             WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'staff_id' => $staffId,
            'catatan' => $catatan,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function cancel(string $id, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            "UPDATE perpanjangan_penitipan SET status = 'DIBATALKAN' WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
