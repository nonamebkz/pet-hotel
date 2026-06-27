<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Enums\UserType;
use App\Repositories\PasswordResetRepository;
use App\Repositories\PelangganRepository;
use App\Repositories\StaffRepository;
use function config;
use function url;
use function uuid;

final class PasswordResetService
{
    public function __construct(
        private readonly PasswordResetRepository $tokenRepo = new PasswordResetRepository(),
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly StaffRepository $staffRepo = new StaffRepository(),
    ) {}

    /** @return array{success: bool, message: string, reset_url?: string} */
    public function requestReset(string $email, UserType $userType): array
    {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid.'];
        }

        $userExists = match ($userType) {
            UserType::PELANGGAN => $this->pelangganRepo->findByEmail($email) !== null,
            UserType::STAFF => $this->staffRepo->findByEmailOrUsername($email) !== null,
        };

        $genericMessage = 'Jika email terdaftar, link reset password akan dikirim.';

        if (!$userExists) {
            return ['success' => true, 'message' => $genericMessage];
        }

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiryMinutes = config('app')['password_reset_expiry'];
        $expiresAt = date('Y-m-d H:i:s', time() + ($expiryMinutes * 60));

        $this->tokenRepo->create(uuid(), $email, $tokenHash, $userType, $expiresAt);

        $resetPath = match ($userType) {
            UserType::PELANGGAN => '/reset-password?token=' . $token,
            UserType::STAFF => '/admin/reset-password?token=' . $token,
        };

        $resetUrl = url($resetPath);

        if (config('app')['env'] === 'local') {
            return [
                'success' => true,
                'message' => $genericMessage . ' (Mode dev: link reset ditampilkan di bawah.)',
                'reset_url' => $resetUrl,
            ];
        }

        @mail($email, 'Reset Password Petshop', "Reset password: $resetUrl");

        return ['success' => true, 'message' => $genericMessage];
    }

    /** @return array{success: bool, error?: string} */
    public function resetPassword(string $token, string $password, string $passwordConfirm, UserType $userType): array
    {
        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Password minimal 8 karakter.'];
        }

        if ($password !== $passwordConfirm) {
            return ['success' => false, 'error' => 'Konfirmasi password tidak cocok.'];
        }

        $tokenHash = hash('sha256', $token);
        $record = $this->tokenRepo->findValidToken($tokenHash, $userType);

        if (!$record) {
            return ['success' => false, 'error' => 'Token reset tidak valid atau sudah kedaluwarsa.'];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        match ($userType) {
            UserType::PELANGGAN => $this->resetPelangganPassword($record['email'], $passwordHash),
            UserType::STAFF => $this->resetStaffPassword($record['email'], $passwordHash),
        };

        $this->tokenRepo->deleteById($record['id']);

        return ['success' => true];
    }

    private function resetPelangganPassword(string $email, string $passwordHash): void
    {
        $user = $this->pelangganRepo->findByEmail($email);

        if ($user) {
            $this->pelangganRepo->updatePassword($user['id'], $passwordHash);
        }
    }

    private function resetStaffPassword(string $email, string $passwordHash): void
    {
        $user = $this->staffRepo->findByEmailOrUsername($email);

        if ($user) {
            $this->staffRepo->updatePassword($user['id'], $passwordHash);
        }
    }
}
