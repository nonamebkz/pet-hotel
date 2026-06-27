<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class PaketPenitipanRepository
{
    /** @return list<array<string, mixed>> */
    public function findAllActive(): array
    {
        $stmt = Database::connection()->query(
            'SELECT * FROM paket_penitipan WHERE aktif = 1 ORDER BY harga_per_hari ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function findAll(): array
    {
        $stmt = Database::connection()->query(
            'SELECT * FROM paket_penitipan ORDER BY nama ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM paket_penitipan WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findActiveById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM paket_penitipan WHERE id = :id AND aktif = 1 LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(
        string $id,
        string $nama,
        float $hargaPerHari,
        ?string $deskripsi,
        bool $aktif,
    ): void {
        $stmt = Database::connection()->prepare(
            'INSERT INTO paket_penitipan (id, nama, harga_per_hari, deskripsi, aktif)
             VALUES (:id, :nama, :harga_per_hari, :deskripsi, :aktif)'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'harga_per_hari' => $hargaPerHari,
            'deskripsi' => $deskripsi,
            'aktif' => $aktif ? 1 : 0,
        ]);
    }

    public function update(
        string $id,
        string $nama,
        float $hargaPerHari,
        ?string $deskripsi,
        bool $aktif,
    ): bool {
        $stmt = Database::connection()->prepare(
            'UPDATE paket_penitipan SET
                nama = :nama,
                harga_per_hari = :harga_per_hari,
                deskripsi = :deskripsi,
                aktif = :aktif
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'harga_per_hari' => $hargaPerHari,
            'deskripsi' => $deskripsi,
            'aktif' => $aktif ? 1 : 0,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(string $id): bool
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM paket_penitipan WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
