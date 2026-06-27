<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\JenisKelamin;
use App\Enums\StaffRole;
use App\Services\AdminPelangganService;
use App\Services\AuthService;

final class PelangganController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly AdminPelangganService $pelangganService = new AdminPelangganService(),
    ) {}

    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('q', ''));
        $data = $this->pelangganService->list($search !== '' ? $search : null);
        $data['search'] = $search;

        return $this->adminView('admin/pelanggan/index', 'Manajemen Pelanggan', $data);
    }

    public function show(Request $request): Response
    {
        $id = trim((string) $request->input('id', ''));

        if ($id === '') {
            Session::flash('error', 'Pelanggan tidak ditemukan.');

            return Response::redirect('/admin/pelanggan');
        }

        $data = $this->pelangganService->detail($id);

        if ($data === null) {
            Session::flash('error', 'Pelanggan tidak ditemukan.');

            return Response::redirect('/admin/pelanggan');
        }

        $data['jenisKelaminLabels'] = JenisKelamin::labels();

        return $this->adminView('admin/pelanggan/detail', 'Detail Pelanggan', $data);
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
