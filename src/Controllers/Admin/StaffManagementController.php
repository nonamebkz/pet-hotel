<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\StaffRole;
use App\Enums\StatusAkun;
use App\Repositories\StaffRepository;
use App\Services\AuthService;
use App\Services\StaffManagementService;

final class StaffManagementController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly StaffRepository $staffRepo = new StaffRepository(),
        private readonly StaffManagementService $staffService = new StaffManagementService(),
    ) {}

    public function index(Request $request): Response
    {
        return $this->adminView('admin/staff/index', 'Manajemen Staff', [
            'staffList' => $this->staffRepo->findAllStaff(),
            'statusLabels' => StatusAkun::labels(),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->adminView('admin/staff/form', 'Tambah Staff', [
            'staff' => null,
            'action' => '/admin/staff/tambah',
            'submitLabel' => 'Simpan Staff',
            'statusLabels' => StatusAkun::labels(),
            'errors' => [],
        ]);
    }

    public function store(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/staff/tambah');
        }

        $result = $this->staffService->create($request->all());

        if (!$result['success']) {
            return $this->formWithErrors(null, '/admin/staff/tambah', 'Simpan Staff', $request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Akun staff berhasil ditambahkan.');

        return Response::redirect('/admin/staff');
    }

    public function edit(Request $request): Response
    {
        $id = (string) $request->input('id', '');
        $staff = $this->findManageableStaff($id);

        if (!$staff) {
            Session::flash('error', 'Akun staff tidak ditemukan.');

            return Response::redirect('/admin/staff');
        }

        return $this->adminView('admin/staff/form', 'Edit Staff', [
            'staff' => $staff,
            'action' => '/admin/staff/edit?id=' . urlencode($id),
            'submitLabel' => 'Simpan Perubahan',
            'statusLabels' => StatusAkun::labels(),
            'errors' => [],
        ]);
    }

    public function update(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/staff');
        }

        $id = (string) $request->input('id', '');
        $staff = $this->findManageableStaff($id);

        if (!$staff) {
            Session::flash('error', 'Akun staff tidak ditemukan.');

            return Response::redirect('/admin/staff');
        }

        $result = $this->staffService->update($id, $request->all());

        if (!$result['success']) {
            return $this->formWithErrors($staff, '/admin/staff/edit?id=' . urlencode($id), 'Simpan Perubahan', $request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Data staff berhasil diperbarui.');

        return Response::redirect('/admin/staff');
    }

    public function resetPasswordForm(Request $request): Response
    {
        $id = (string) $request->input('id', '');
        $staff = $this->findManageableStaff($id);

        if (!$staff) {
            Session::flash('error', 'Akun staff tidak ditemukan.');

            return Response::redirect('/admin/staff');
        }

        return $this->adminView('admin/staff/reset-password', 'Reset Password Staff', [
            'staff' => $staff,
            'action' => '/admin/staff/reset-password?id=' . urlencode($id),
            'errors' => [],
        ]);
    }

    public function resetPassword(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/staff');
        }

        $id = (string) $request->input('id', '');
        $staff = $this->findManageableStaff($id);

        if (!$staff) {
            Session::flash('error', 'Akun staff tidak ditemukan.');

            return Response::redirect('/admin/staff');
        }

        $result = $this->staffService->resetPassword($id, $request->all());

        if (!$result['success']) {
            Session::pullOld($request->all());

            return $this->adminView('admin/staff/reset-password', 'Reset Password Staff', [
                'staff' => $staff,
                'action' => '/admin/staff/reset-password?id=' . urlencode($id),
                'errors' => $result['errors'] ?? [],
            ]);
        }

        Session::flash('success', 'Password staff berhasil direset.');

        return Response::redirect('/admin/staff');
    }

    public function toggleStatus(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/staff');
        }

        $id = (string) $request->input('id', '');
        $result = $this->staffService->toggleStatus($id);

        Session::flash(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Status akun staff diperbarui.' : ($result['error'] ?? 'Gagal memperbarui status.'),
        );

        return Response::redirect('/admin/staff');
    }

    /** @return array<string, mixed>|null */
    private function findManageableStaff(string $id): ?array
    {
        if ($id === '') {
            return null;
        }

        $staff = $this->staffRepo->findById($id);

        if (!$staff || ($staff['role'] ?? '') !== StaffRole::STAFF->value) {
            return null;
        }

        unset($staff['password_hash']);

        return $staff;
    }

    /**
     * @param array<string, mixed>|null $staff
     * @param array<string, string> $errors
     */
    private function formWithErrors(
        ?array $staff,
        string $action,
        string $submitLabel,
        Request $request,
        array $errors,
    ): Response {
        Session::pullOld($request->all());

        $merged = $staff ?? [];
        $merged['nama'] = $request->input('nama', $merged['nama'] ?? '');
        $merged['email'] = $request->input('email', $merged['email'] ?? '');
        $merged['username'] = $request->input('username', $merged['username'] ?? '');
        $merged['status'] = $request->input('status', $merged['status'] ?? StatusAkun::AKTIF->value);

        return $this->adminView('admin/staff/form', $staff ? 'Edit Staff' : 'Tambah Staff', [
            'staff' => $merged ?: null,
            'action' => $action,
            'submitLabel' => $submitLabel,
            'statusLabels' => StatusAkun::labels(),
            'errors' => $errors,
        ]);
    }

    /** @param array<string, mixed> $data */
    private function adminView(string $view, string $title, array $data = []): Response
    {
        $role = $this->auth->currentStaffRole() ?? StaffRole::STAFF;

        $html = View::render($view, array_merge([
            'title' => $title,
            'layout' => 'admin',
            'nama' => Session::get('auth.staff_nama', 'Staff'),
            'role' => $role,
            'roleLabel' => $role->label(),
        ], $data));

        return Response::html($html);
    }
}
