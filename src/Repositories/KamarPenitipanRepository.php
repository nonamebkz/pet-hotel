<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class KamarPenitipanRepository
{
    /** @return list<array<string, mixed>> */
    public function findAllActive(): array
    {
        $stmt = Database::connection()->query(
            'SELECT * FROM kamar_penitipan WHERE aktif = 1 ORDER BY nama_kamar ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function findAll(): array
    {
        $stmt = Database::connection()->query(
            'SELECT * FROM kamar_penitipan ORDER BY nama_kamar ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM kamar_penitipan WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(
        string $id,
        string $namaKamar,
        int $kapasitas,
        bool $aktif,
    ): void {
        $stmt = Database::connection()->prepare(
            'INSERT INTO kamar_penitipan (id, nama_kamar, kapasitas, aktif)
             VALUES (:id, :nama_kamar, :kapasitas, :aktif)'
        );
        $stmt->execute([
            'id' => $id,
            'nama_kamar' => $namaKamar,
            'kapasitas' => $kapasitas,
            'aktif' => $aktif ? 1 : 0,
        ]);
    }

    public function update(
        string $id,
        string $namaKamar,
        int $kapasitas,
        bool $aktif,
    ): bool {
        $stmt = Database::connection()->prepare(
            'UPDATE kamar_penitipan SET
                nama_kamar = :nama_kamar,
                kapasitas = :kapasitas,
                aktif = :aktif
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'nama_kamar' => $namaKamar,
            'kapasitas' => $kapasitas,
            'aktif' => $aktif ? 1 : 0,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(string $id): bool
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM kamar_penitipan WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
