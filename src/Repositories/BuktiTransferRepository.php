<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class BuktiTransferRepository
{
    public function create(
        string $id,
        string $transaksiId,
        string $fileUrl,
        PDO $pdo,
    ): void {
        $stmt = $pdo->prepare(
            "INSERT INTO bukti_transfer (id, transaksi_id, file_url, status_verifikasi)
             VALUES (:id, :transaksi_id, :file_url, 'MENUNGGU')"
        );
        $stmt->execute([
            'id' => $id,
            'transaksi_id' => $transaksiId,
            'file_url' => $fileUrl,
        ]);
    }

    public function findByTransaksiId(string $transaksiId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM bukti_transfer WHERE transaksi_id = :transaksi_id LIMIT 1'
        );
        $stmt->execute(['transaksi_id' => $transaksiId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM bukti_transfer WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function replace(
        string $transaksiId,
        string $fileUrl,
        PDO $pdo,
    ): void {
        $stmt = $pdo->prepare(
            "UPDATE bukti_transfer SET
                file_url = :file_url,
                status_verifikasi = 'MENUNGGU',
                catatan_penolakan = NULL,
                diverifikasi_oleh_staff_id = NULL,
                diverifikasi_at = NULL,
                uploaded_at = NOW()
             WHERE transaksi_id = :transaksi_id"
        );
        $stmt->execute([
            'transaksi_id' => $transaksiId,
            'file_url' => $fileUrl,
        ]);
    }

    public function setujui(string $id, string $staffId, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            "UPDATE bukti_transfer SET
                status_verifikasi = 'DISETUJUI',
                diverifikasi_oleh_staff_id = :staff_id,
                diverifikasi_at = NOW(),
                catatan_penolakan = NULL
             WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'staff_id' => $staffId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function tolak(string $id, string $staffId, ?string $catatan, PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            "UPDATE bukti_transfer SET
                status_verifikasi = 'DITOLAK',
                diverifikasi_oleh_staff_id = :staff_id,
                diverifikasi_at = NOW(),
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
}
