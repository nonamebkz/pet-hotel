<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Enums\StaffRole;
use PDO;

final class StaffRepository
{
    public function findByEmailOrUsername(string $identifier): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM staff WHERE email = :email OR username = :username LIMIT 1'
        );
        $stmt->execute(['email' => $identifier, 'username' => $identifier]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findById(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM staff WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /** @return list<array<string, mixed>> */
    public function findAllStaff(): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, nama, email, username, role, status, created_at
             FROM staff
             WHERE role = :role
             ORDER BY nama ASC'
        );
        $stmt->execute(['role' => StaffRole::STAFF->value]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(
        string $id,
        string $nama,
        string $email,
        ?string $username,
        string $passwordHash,
        string $status,
    ): void {
        $stmt = Database::connection()->prepare(
            'INSERT INTO staff (id, nama, email, username, password_hash, role, status)
             VALUES (:id, :nama, :email, :username, :password_hash, :role, :status)'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'email' => $email,
            'username' => $username,
            'password_hash' => $passwordHash,
            'role' => StaffRole::STAFF->value,
            'status' => $status,
        ]);
    }

    public function updateProfile(string $id, string $nama, string $email, ?string $username): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE staff SET nama = :nama, email = :email, username = :username WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'nama' => $nama,
            'email' => $email,
            'username' => $username,
        ]);
    }

    public function updatePassword(string $id, string $passwordHash): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE staff SET password_hash = :password_hash WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'password_hash' => $passwordHash,
        ]);
    }

    public function updateStatus(string $id, string $status): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE staff SET status = :status WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    public function emailExists(string $email, ?string $excludeId = null): bool
    {
        $sql = 'SELECT 1 FROM staff WHERE email = :email';
        $params = ['email' => $email];

        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $sql .= ' LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    public function usernameExists(string $username, ?string $excludeId = null): bool
    {
        $sql = 'SELECT 1 FROM staff WHERE username = :username';
        $params = ['username' => $username];

        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $sql .= ' LIMIT 1';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }
}
