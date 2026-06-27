<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
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
}
