<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Enums\UserType;
use PDO;

final class PasswordResetRepository
{
    public function create(string $id, string $email, string $tokenHash, UserType $userType, string $expiresAt): void
    {
        $this->deleteByEmail($email, $userType);

        $stmt = Database::connection()->prepare(
            'INSERT INTO password_reset_tokens (id, email, token_hash, user_type, expires_at)
             VALUES (:id, :email, :token_hash, :user_type, :expires_at)'
        );
        $stmt->execute([
            'id' => $id,
            'email' => $email,
            'token_hash' => $tokenHash,
            'user_type' => $userType->value,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findValidToken(string $tokenHash, UserType $userType): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM password_reset_tokens
             WHERE token_hash = :token_hash
               AND user_type = :user_type
               AND expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute([
            'token_hash' => $tokenHash,
            'user_type' => $userType->value,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function deleteByEmail(string $email, UserType $userType): void
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM password_reset_tokens WHERE email = :email AND user_type = :user_type'
        );
        $stmt->execute([
            'email' => $email,
            'user_type' => $userType->value,
        ]);
    }

    public function deleteById(string $id): void
    {
        $stmt = Database::connection()->prepare(
            'DELETE FROM password_reset_tokens WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }
}
