<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StaffRole;
use App\Enums\StatusAkun;
use App\Repositories\StaffRepository;
use function uuid;

final class StaffManagementService
{
    public function __construct(
        private readonly StaffRepository $staffRepo = new StaffRepository(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, staffId?: string}
     */
    public function create(array $input): array
    {
        $validated = $this->validateCreateInput($input);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $id = uuid();

        $this->staffRepo->create(
            $id,
            $validated['nama'],
            $validated['email'],
            $validated['username'],
            password_hash($validated['password'], PASSWORD_DEFAULT),
            $validated['status'],
        );

        return ['success' => true, 'staffId' => $id];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>}
     */
    public function update(string $id, array $input): array
    {
        $existing = $this->staffRepo->findById($id);

        if (!$existing || !$this->isManageableStaff($existing)) {
            return ['success' => false, 'errors' => ['general' => 'Akun staff tidak ditemukan.']];
        }

        $validated = $this->validateProfileInput($input, $id);

        if ($validated['errors'] !== []) {
            return ['success' => false, 'errors' => $validated['errors']];
        }

        $this->staffRepo->updateProfile(
            $id,
            $validated['nama'],
            $validated['email'],
            $validated['username'],
        );

        return ['success' => true];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>}
     */
    public function resetPassword(string $id, array $input): array
    {
        $existing = $this->staffRepo->findById($id);

        if (!$existing || !$this->isManageableStaff($existing)) {
            return ['success' => false, 'errors' => ['general' => 'Akun staff tidak ditemukan.']];
        }

        $errors = $this->validatePasswordInput($input);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $password = (string) $input['password'];

        $this->staffRepo->updatePassword($id, password_hash($password, PASSWORD_DEFAULT));

        return ['success' => true];
    }

    /** @return array{success: bool, error?: string} */
    public function toggleStatus(string $id): array
    {
        $existing = $this->staffRepo->findById($id);

        if (!$existing || !$this->isManageableStaff($existing)) {
            return ['success' => false, 'error' => 'Akun staff tidak ditemukan atau tidak dapat dikelola.'];
        }

        $newStatus = $existing['status'] === StatusAkun::AKTIF->value
            ? StatusAkun::NONAKTIF->value
            : StatusAkun::AKTIF->value;

        $this->staffRepo->updateStatus($id, $newStatus);

        return ['success' => true];
    }

    /** @param array<string, mixed> $staff */
    private function isManageableStaff(array $staff): bool
    {
        return ($staff['role'] ?? '') === StaffRole::STAFF->value;
    }

    /**
     * @param array<string, mixed> $input
     * @return array{nama: string, email: string, username: ?string, password: string, status: string, errors: array<string, string>}
     */
    private function validateCreateInput(array $input): array
    {
        $profile = $this->validateProfileInput($input);
        $errors = $profile['errors'];

        $passwordErrors = $this->validatePasswordInput($input);
        $errors = array_merge($errors, $passwordErrors);

        $status = trim((string) ($input['status'] ?? StatusAkun::AKTIF->value));

        if (!in_array($status, [StatusAkun::AKTIF->value, StatusAkun::NONAKTIF->value], true)) {
            $errors['status'] = 'Status tidak valid.';
        }

        return [
            'nama' => $profile['nama'],
            'email' => $profile['email'],
            'username' => $profile['username'],
            'password' => (string) ($input['password'] ?? ''),
            'status' => $status,
            'errors' => $errors,
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{nama: string, email: string, username: ?string, errors: array<string, string>}
     */
    private function validateProfileInput(array $input, ?string $excludeId = null): array
    {
        $errors = [];

        $nama = trim((string) ($input['nama'] ?? ''));

        if ($nama === '') {
            $errors['nama'] = 'Nama wajib diisi.';
        }

        $email = trim((string) ($input['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        } elseif ($this->staffRepo->emailExists($email, $excludeId)) {
            $errors['email'] = 'Email sudah digunakan.';
        }

        $usernameRaw = trim((string) ($input['username'] ?? ''));
        $username = $usernameRaw !== '' ? $usernameRaw : null;

        if ($username !== null && strlen($username) < 3) {
            $errors['username'] = 'Username minimal 3 karakter.';
        } elseif ($username !== null && $this->staffRepo->usernameExists($username, $excludeId)) {
            $errors['username'] = 'Username sudah digunakan.';
        }

        return [
            'nama' => $nama,
            'email' => $email,
            'username' => $username,
            'errors' => $errors,
        ];
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, string>
     */
    private function validatePasswordInput(array $input): array
    {
        $errors = [];

        $password = (string) ($input['password'] ?? '');
        $passwordConfirm = (string) ($input['password_confirmation'] ?? '');

        if (strlen($password) < 8) {
            $errors['password'] = 'Password minimal 8 karakter.';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirmation'] = 'Konfirmasi password tidak cocok.';
        }

        return $errors;
    }
}
