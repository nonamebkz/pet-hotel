<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Enums\StaffRole;
use App\Enums\UserType;
use App\Repositories\PelangganRepository;
use App\Repositories\StaffRepository;
use function uuid;

final class AuthService
{
    public function __construct(
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly StaffRepository $staffRepo = new StaffRepository(),
        private readonly LoginThrottleService $throttle = new LoginThrottleService(),
    ) {}

    /** @return array{success: bool, errors?: array<string, string>, user?: array} */
    public function register(string $nama, string $email, string $password, string $passwordConfirm): array
    {
        $errors = $this->validateRegistration($nama, $email, $password, $passwordConfirm);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $id = uuid();
        $this->pelangganRepo->create($id, $nama, $email, password_hash($password, PASSWORD_DEFAULT));

        return [
            'success' => true,
            'user' => $this->pelangganRepo->findById($id),
        ];
    }

    /** @return array{success: bool, error?: string, user?: array} */
    public function loginPelanggan(string $email, string $password, string $ip): array
    {
        $key = "pelanggan:$email:$ip";

        if ($this->throttle->isLocked($key)) {
            return ['success' => false, 'error' => 'Terlalu banyak percobaan login. Coba lagi dalam beberapa menit.'];
        }

        $user = $this->pelangganRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->throttle->recordFailure($key);

            return ['success' => false, 'error' => 'Email atau password salah.'];
        }

        $this->throttle->clear($key);
        $this->loginPelangganSession($user);

        return ['success' => true, 'user' => $user];
    }

    /** @return array{success: bool, error?: string, user?: array} */
    public function loginStaff(string $identifier, string $password, string $ip): array
    {
        $key = "staff:$identifier:$ip";

        if ($this->throttle->isLocked($key)) {
            return ['success' => false, 'error' => 'Terlalu banyak percobaan login. Coba lagi dalam beberapa menit.'];
        }

        $user = $this->staffRepo->findByEmailOrUsername($identifier);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->throttle->recordFailure($key);

            return ['success' => false, 'error' => 'Email/username atau password salah.'];
        }

        if ($user['status'] !== 'AKTIF') {
            return ['success' => false, 'error' => 'Akun Anda dinonaktifkan. Hubungi owner.'];
        }

        $this->throttle->clear($key);
        $this->loginStaffSession($user);

        return ['success' => true, 'user' => $user];
    }

    public function logoutPelanggan(): void
    {
        Session::forget('auth.pelanggan_id');
        Session::forget('auth.pelanggan_nama');
        Session::forget('auth.type');
    }

    public function logoutStaff(): void
    {
        Session::forget('auth.staff_id');
        Session::forget('auth.staff_nama');
        Session::forget('auth.role');
        Session::forget('auth.type');
    }

    public function logoutAll(): void
    {
        $this->logoutPelanggan();
        $this->logoutStaff();
    }

    /** @return array{success: bool, error?: string} */
    public function changePasswordPelanggan(string $id, string $currentPassword, string $newPassword, string $confirmPassword): array
    {
        $errors = $this->validatePasswordChange($newPassword, $confirmPassword);

        if ($errors !== []) {
            return ['success' => false, 'error' => $errors['password'] ?? 'Validasi gagal.'];
        }

        $user = $this->pelangganRepo->findById($id);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Password lama tidak benar.'];
        }

        $this->pelangganRepo->updatePassword($id, password_hash($newPassword, PASSWORD_DEFAULT));

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function changePasswordStaff(string $id, string $currentPassword, string $newPassword, string $confirmPassword): array
    {
        $errors = $this->validatePasswordChange($newPassword, $confirmPassword);

        if ($errors !== []) {
            return ['success' => false, 'error' => $errors['password'] ?? 'Validasi gagal.'];
        }

        $user = $this->staffRepo->findById($id);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Password lama tidak benar.'];
        }

        $this->staffRepo->updatePassword($id, password_hash($newPassword, PASSWORD_DEFAULT));

        return ['success' => true];
    }

    public function isPelangganLoggedIn(): bool
    {
        return Session::get('auth.type') === UserType::PELANGGAN->value
            && Session::get('auth.pelanggan_id') !== null;
    }

    public function isStaffLoggedIn(): bool
    {
        return Session::get('auth.type') === UserType::STAFF->value
            && Session::get('auth.staff_id') !== null;
    }

    public function currentPelangganId(): ?string
    {
        return $this->isPelangganLoggedIn() ? (string) Session::get('auth.pelanggan_id') : null;
    }

    public function currentStaffId(): ?string
    {
        return $this->isStaffLoggedIn() ? (string) Session::get('auth.staff_id') : null;
    }

    public function currentStaffRole(): ?StaffRole
    {
        if (!$this->isStaffLoggedIn()) {
            return null;
        }

        return StaffRole::from((string) Session::get('auth.role'));
    }

    public function isOwner(): bool
    {
        return $this->currentStaffRole() === StaffRole::OWNER;
    }

    /** @param array<string, mixed> $user */
    private function loginPelangganSession(array $user): void
    {
        $this->logoutAll();
        Session::regenerate();
        Session::set('auth.type', UserType::PELANGGAN->value);
        Session::set('auth.pelanggan_id', $user['id']);
        Session::set('auth.pelanggan_nama', $user['nama']);
    }

    /** @param array<string, mixed> $user */
    private function loginStaffSession(array $user): void
    {
        $this->logoutAll();
        Session::regenerate();
        Session::set('auth.type', UserType::STAFF->value);
        Session::set('auth.staff_id', $user['id']);
        Session::set('auth.staff_nama', $user['nama']);
        Session::set('auth.role', $user['role']);
    }

    /** @return array<string, string> */
    private function validateRegistration(string $nama, string $email, string $password, string $passwordConfirm): array
    {
        $errors = [];

        if (trim($nama) === '') {
            $errors['nama'] = 'Nama wajib diisi.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        } elseif ($this->pelangganRepo->findByEmail($email)) {
            $errors['email'] = 'Email sudah terdaftar.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'Password minimal 8 karakter.';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirmation'] = 'Konfirmasi password tidak cocok.';
        }

        return $errors;
    }

    /** @return array<string, string> */
    private function validatePasswordChange(string $newPassword, string $confirmPassword): array
    {
        $errors = [];

        if (strlen($newPassword) < 8) {
            $errors['password'] = 'Password baru minimal 8 karakter.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors['password_confirmation'] = 'Konfirmasi password tidak cocok.';
        }

        return $errors;
    }
}
