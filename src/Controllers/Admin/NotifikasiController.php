<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\StaffRole;
use App\Repositories\NotifikasiRepository;
use App\Services\AuthService;

final class NotifikasiController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly NotifikasiRepository $notifikasiRepo = new NotifikasiRepository(),
    ) {}

    public function index(Request $request): Response
    {
        $staffId = $this->auth->currentStaffId();

        if (!$staffId) {
            return Response::redirect('/admin/login');
        }

        $staffId = (string) $staffId;
        $this->notifikasiRepo->markAllAsReadByStaff($staffId);

        $role = $this->auth->currentStaffRole() ?? StaffRole::STAFF;

        $html = View::render('admin/notifikasi/index', [
            'title' => 'Notifikasi',
            'layout' => 'admin',
            'nama' => Session::get('auth.staff_nama', 'Staff'),
            'role' => $role,
            'roleLabel' => $role->label(),
            'notifikasiList' => $this->notifikasiRepo->findAllByStaff($staffId),
            'unreadNotificationCount' => 0,
        ]);

        return Response::html($html);
    }
}
