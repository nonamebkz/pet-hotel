<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class RiwayatVaksinRepository
{
    /** @return list<array<string, mixed>> */
    public function findByKucingId(string $kucingId): array
    {
        if (!$this->tableExists('riwayat_vaksin')) {
            return [];
        }

        $stmt = Database::connection()->prepare(
            'SELECT * FROM riwayat_vaksin WHERE kucing_id = :kucing_id ORDER BY tanggal_vaksin DESC'
        );
        $stmt->execute(['kucing_id' => $kucingId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countLengkapByKucingId(string $kucingId): int
    {
        if (!$this->tableExists('riwayat_vaksin')) {
            return 0;
        }

        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM riwayat_vaksin
             WHERE kucing_id = :kucing_id
               AND jenis_vaksin <> \'\'
               AND tanggal_vaksin IS NOT NULL'
        );
        $stmt->execute(['kucing_id' => $kucingId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param list<array{id?: string, jenis_vaksin: string, tanggal_vaksin: string, sertifikat_url?: string|null}> $entries
     */
    public function replaceForKucing(string $kucingId, array $entries): void
    {
        $pdo = Database::connection();

        $delete = $pdo->prepare('DELETE FROM riwayat_vaksin WHERE kucing_id = :kucing_id');
        $delete->execute(['kucing_id' => $kucingId]);

        if ($entries === []) {
            return;
        }

        $insert = $pdo->prepare(
            'INSERT INTO riwayat_vaksin (id, kucing_id, jenis_vaksin, tanggal_vaksin, sertifikat_url)
             VALUES (:id, :kucing_id, :jenis_vaksin, :tanggal_vaksin, :sertifikat_url)'
        );

        foreach ($entries as $entry) {
            $insert->execute([
                'id' => $entry['id'],
                'kucing_id' => $kucingId,
                'jenis_vaksin' => $entry['jenis_vaksin'],
                'tanggal_vaksin' => $entry['tanggal_vaksin'],
                'sertifikat_url' => $entry['sertifikat_url'] ?? null,
            ]);
        }
    }

    /** @return list<string> */
    public function findSertifikatUrlsByKucingId(string $kucingId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT sertifikat_url FROM riwayat_vaksin
             WHERE kucing_id = :kucing_id AND sertifikat_url IS NOT NULL'
        );
        $stmt->execute(['kucing_id' => $kucingId]);

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'sertifikat_url');
    }

    private function tableExists(string $tableName): bool
    {
        $config = config('database');
        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = :schema AND table_name = :table'
        );
        $stmt->execute([
            'schema' => $config['database'],
            'table' => $tableName,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
