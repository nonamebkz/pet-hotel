<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class LayananPetCareRepository
{
    /** @return list<array<string, mixed>> */
    public function findAllActive(): array
    {
        $stmt = Database::connection()->query(
            "SELECT * FROM layanan_pet_care
             WHERE status = 'AKTIF' AND deleted_at IS NULL
             ORDER BY nama ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return list<array<string, mixed>> */
    public function findAllIncludingDeleted(): array
    {
        $stmt = Database::connection()->query(
            'SELECT * FROM layanan_pet_care ORDER BY nama ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM layanan_pet_care WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findActiveById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            "SELECT * FROM layanan_pet_care
             WHERE id = :id AND status = 'AKTIF' AND deleted_at IS NULL
             LIMIT 1"
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
        int $estimasiDurasiMenit,
        string $status,
    ): void {
        $stmt = Database::connection()->prepare(
            'INSERT INTO layanan_pet_care (
                id, nama, deskripsi, harga, estimasi_durasi_menit, status
             ) VALUES (
                :id, :nama, :deskripsi, :harga, :estimasi_durasi_menit, :status
             )'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga' => $harga,
            'estimasi_durasi_menit' => $estimasiDurasiMenit,
            'status' => $status,
        ]);
    }

    public function update(
        string $id,
        string $nama,
        ?string $deskripsi,
        float $harga,
        int $estimasiDurasiMenit,
        string $status,
    ): void {
        $stmt = Database::connection()->prepare(
            'UPDATE layanan_pet_care SET
                nama = :nama,
                deskripsi = :deskripsi,
                harga = :harga,
                estimasi_durasi_menit = :estimasi_durasi_menit,
                status = :status
             WHERE id = :id AND deleted_at IS NULL'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga' => $harga,
            'estimasi_durasi_menit' => $estimasiDurasiMenit,
            'status' => $status,
        ]);
    }

    public function softDelete(string $id): bool
    {
        $stmt = Database::connection()->prepare(
            "UPDATE layanan_pet_care SET
                deleted_at = NOW(),
                status = 'NONAKTIF'
             WHERE id = :id AND deleted_at IS NULL"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
