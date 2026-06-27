<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Enums\TipePenerima;
use PDO;

final class NotifikasiRepository
{
    public function create(
        string $id,
        string $penerimaId,
        string $tipePenerima,
        string $jenis,
        string $judul,
        string $pesan,
        ?string $referensiId = null,
        ?string $referensiTipe = null,
        ?PDO $pdo = null,
    ): void {
        $connection = $pdo ?? Database::connection();
        $stmt = $connection->prepare(
            'INSERT INTO notifikasi (
                id, penerima_id, tipe_penerima, jenis, judul, pesan,
                referensi_id, referensi_tipe
             ) VALUES (
                :id, :penerima_id, :tipe_penerima, :jenis, :judul, :pesan,
                :referensi_id, :referensi_tipe
             )'
        );
        $stmt->execute([
            'id' => $id,
            'penerima_id' => $penerimaId,
            'tipe_penerima' => $tipePenerima,
            'jenis' => $jenis,
            'judul' => $judul,
            'pesan' => $pesan,
            'referensi_id' => $referensiId,
            'referensi_tipe' => $referensiTipe,
        ]);
    }

    /** @return list<array<string, mixed>> */
    public function findRecentByPelanggan(string $pelangganId, int $limit = 5): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM notifikasi
             WHERE penerima_id = :penerima_id
               AND tipe_penerima = :tipe
             ORDER BY created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue('penerima_id', $pelangganId);
        $stmt->bindValue('tipe', TipePenerima::PELANGGAN->value);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUnreadByPelanggan(string $pelangganId): int
    {
        $stmt = Database::connection()->prepare(
            "SELECT COUNT(*) FROM notifikasi
             WHERE penerima_id = :penerima_id
               AND tipe_penerima = :tipe
               AND sudah_dibaca = 0"
        );
        $stmt->execute([
            'penerima_id' => $pelangganId,
            'tipe' => TipePenerima::PELANGGAN->value,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /** @return list<array<string, mixed>> */
    public function findAllByPelanggan(string $pelangganId): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM notifikasi
             WHERE penerima_id = :penerima_id
               AND tipe_penerima = :tipe
             ORDER BY created_at DESC"
        );
        $stmt->execute([
            'penerima_id' => $pelangganId,
            'tipe' => TipePenerima::PELANGGAN->value,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead(string $id, string $pelangganId): bool
    {
        $stmt = Database::connection()->prepare(
            "UPDATE notifikasi SET sudah_dibaca = 1
             WHERE id = :id
               AND penerima_id = :penerima_id
               AND tipe_penerima = :tipe"
        );
        $stmt->execute([
            'id' => $id,
            'penerima_id' => $pelangganId,
            'tipe' => TipePenerima::PELANGGAN->value,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function markAllAsReadByPelanggan(string $pelangganId): void
    {
        $stmt = Database::connection()->prepare(
            "UPDATE notifikasi SET sudah_dibaca = 1
             WHERE penerima_id = :penerima_id
               AND tipe_penerima = :tipe
               AND sudah_dibaca = 0"
        );
        $stmt->execute([
            'penerima_id' => $pelangganId,
            'tipe' => TipePenerima::PELANGGAN->value,
        ]);
    }
}
