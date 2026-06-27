<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class PelangganRepository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM pelanggan WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM pelanggan WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(string $id, string $nama, string $email, string $passwordHash): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO pelanggan (id, nama, email, password_hash) VALUES (:id, :nama, :email, :password_hash)'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);
    }

    public function updatePassword(string $id, string $passwordHash): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE pelanggan SET password_hash = :password_hash WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'password_hash' => $passwordHash,
        ]);
    }

    public function updateProfile(
        string $id,
        string $nama,
        ?string $noTelepon,
        ?string $alamatLengkap,
        ?float $latitude,
        ?float $longitude,
    ): void {
        $stmt = Database::connection()->prepare(
            'UPDATE pelanggan SET
                nama = :nama,
                no_telepon = :no_telepon,
                alamat_lengkap = :alamat_lengkap,
                latitude = :latitude,
                longitude = :longitude
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'no_telepon' => $noTelepon,
            'alamat_lengkap' => $alamatLengkap,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function updateFotoProfil(string $id, ?string $fotoProfilUrl): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE pelanggan SET foto_profil_url = :foto_profil_url WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'foto_profil_url' => $fotoProfilUrl,
        ]);
    }

    public function markPromoPenitipanUsed(string $id, PDO $pdo): void
    {
        $stmt = $pdo->prepare(
            'UPDATE pelanggan SET pernah_pakai_promo_penitipan = 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }
}
