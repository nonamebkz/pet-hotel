<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\StaffRole;
use App\Services\AuthService;
use App\Services\TransaksiRiwayatService;

final class TransaksiController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly TransaksiRiwayatService $riwayatService = new TransaksiRiwayatService(),
    ) {}

    public function index(Request $request): Response
    {
        $data = $this->riwayatService->getAdminRiwayat($request->all());

        return $this->adminView('admin/transaksi/index', 'Riwayat Transaksi', $data);
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
