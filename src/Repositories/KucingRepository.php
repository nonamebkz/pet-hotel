<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class KucingRepository
{
    /** @return list<array<string, mixed>> */
    public function findAllByPelanggan(string $pelangganId): array
    {
        if (!$this->tableExists('kucing')) {
            return [];
        }

        $stmt = Database::connection()->prepare(
            'SELECT * FROM kucing WHERE pelanggan_id = :pelanggan_id ORDER BY nama ASC'
        );
        $stmt->execute(['pelanggan_id' => $pelangganId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByPelanggan(string $pelangganId): int
    {
        if (!$this->tableExists('kucing')) {
            return 0;
        }

        $stmt = Database::connection()->prepare(
            'SELECT COUNT(*) FROM kucing WHERE pelanggan_id = :pelanggan_id'
        );
        $stmt->execute(['pelanggan_id' => $pelangganId]);

        return (int) $stmt->fetchColumn();
    }

    public function findById(string $id): ?array
    {
        if (!$this->tableExists('kucing')) {
            return null;
        }

        $stmt = Database::connection()->prepare(
            'SELECT * FROM kucing WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByIdAndPelanggan(string $id, string $pelangganId): ?array
    {
        if (!$this->tableExists('kucing')) {
            return null;
        }

        $stmt = Database::connection()->prepare(
            'SELECT * FROM kucing WHERE id = :id AND pelanggan_id = :pelanggan_id LIMIT 1'
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(
        string $id,
        string $pelangganId,
        string $nama,
        string $jenisKelamin,
        ?string $ras,
        ?string $tanggalLahir,
        ?float $beratBadan,
        ?string $fotoUrl,
        ?string $catatanKesehatan,
    ): void {
        $stmt = Database::connection()->prepare(
            'INSERT INTO kucing (
                id, pelanggan_id, nama, jenis_kelamin, ras, tanggal_lahir,
                berat_badan, foto_url, catatan_kesehatan
             ) VALUES (
                :id, :pelanggan_id, :nama, :jenis_kelamin, :ras, :tanggal_lahir,
                :berat_badan, :foto_url, :catatan_kesehatan
             )'
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
            'nama' => $nama,
            'jenis_kelamin' => $jenisKelamin,
            'ras' => $ras,
            'tanggal_lahir' => $tanggalLahir,
            'berat_badan' => $beratBadan,
            'foto_url' => $fotoUrl,
            'catatan_kesehatan' => $catatanKesehatan,
        ]);
    }

    public function update(
        string $id,
        string $pelangganId,
        string $nama,
        string $jenisKelamin,
        ?string $ras,
        ?string $tanggalLahir,
        ?float $beratBadan,
        ?string $fotoUrl,
        ?string $catatanKesehatan,
    ): void {
        $stmt = Database::connection()->prepare(
            'UPDATE kucing SET
                nama = :nama,
                jenis_kelamin = :jenis_kelamin,
                ras = :ras,
                tanggal_lahir = :tanggal_lahir,
                berat_badan = :berat_badan,
                foto_url = :foto_url,
                catatan_kesehatan = :catatan_kesehatan
             WHERE id = :id AND pelanggan_id = :pelanggan_id'
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
            'nama' => $nama,
            'jenis_kelamin' => $jenisKelamin,
            'ras' => $ras,
            'tanggal_lahir' => $tanggalLahir,
            'berat_badan' => $beratBadan,
            'foto_url' => $fotoUrl,
            'catatan_kesehatan' => $catatanKesehatan,
        ]);
    }

    public function delete(string $id, string $pelangganId): bool
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM kucing WHERE id = :id AND pelanggan_id = :pelanggan_id'
        );
        $stmt->execute([
            'id' => $id,
            'pelanggan_id' => $pelangganId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function tableExists(string $tableName): bool
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

    public function hasActiveBooking(string $kucingId): bool
    {
        $checks = [];

        if ($this->tableExists('booking_grooming')) {
            $checks[] = "EXISTS (
                SELECT 1 FROM booking_grooming
                WHERE kucing_id = :kucing_id
                  AND status NOT IN ('DIBATALKAN', 'SELESAI')
            )";
        }

        if ($this->tableExists('booking_penitipan')) {
            $checks[] = "EXISTS (
                SELECT 1 FROM booking_penitipan
                WHERE kucing_id = :kucing_id
                  AND status NOT IN ('DIBATALKAN', 'CHECK_OUT')
            )";
        }

        if ($this->tableExists('booking_pet_care')) {
            $checks[] = "EXISTS (
                SELECT 1 FROM booking_pet_care
                WHERE kucing_id = :kucing_id
                  AND status NOT IN ('DIBATALKAN', 'SELESAI')
            )";
        }

        if ($checks === []) {
            return false;
        }

        $sql = 'SELECT (' . implode(' OR ', $checks) . ') AS has_active';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['kucing_id' => $kucingId]);

        return (bool) $stmt->fetchColumn();
    }
}
