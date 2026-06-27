<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class JenisGroomingRepository
{
    /** @return list<array<string, mixed>> */
    public function findAllActive(): array
    {
        $stmt = Database::connection()->query(
            'SELECT * FROM jenis_grooming WHERE aktif = 1 ORDER BY nama ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function findAll(): array
    {
        $stmt = Database::connection()->query(
            'SELECT * FROM jenis_grooming ORDER BY nama ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM jenis_grooming WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findActiveById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM jenis_grooming WHERE id = :id AND aktif = 1 LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(
        string $id,
        string $nama,
        ?string $deskripsi,
        float $harga,
        bool $aktif,
    ): void {
        $stmt = Database::connection()->prepare(
            'INSERT INTO jenis_grooming (id, nama, deskripsi, harga, aktif)
             VALUES (:id, :nama, :deskripsi, :harga, :aktif)'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga' => $harga,
            'aktif' => $aktif ? 1 : 0,
        ]);
    }

    public function update(
        string $id,
        string $nama,
        ?string $deskripsi,
        float $harga,
        bool $aktif,
    ): bool {
        $stmt = Database::connection()->prepare(
            'UPDATE jenis_grooming SET
                nama = :nama,
                deskripsi = :deskripsi,
                harga = :harga,
                aktif = :aktif
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga' => $harga,
            'aktif' => $aktif ? 1 : 0,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(string $id): bool
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM jenis_grooming WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
