<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\StaffRole;
use App\Services\AuthService;

final class DashboardController
{
    public function index(Request $request): Response
    {
        $auth = new AuthService();
        $role = $auth->currentStaffRole() ?? StaffRole::STAFF;

        $html = View::render('dashboard/admin', [
            'title' => 'Dashboard Internal',
            'layout' => 'admin',
            'nama' => Session::get('auth.staff_nama', 'Staff'),
            'role' => $role,
            'roleLabel' => $role->label(),
        ]);

        return Response::html($html);
    }

    public function staffPlaceholder(Request $request): Response
    {
        $auth = new AuthService();
        $role = $auth->currentStaffRole() ?? StaffRole::STAFF;

        $html = View::render('dashboard/staff-management', [
            'title' => 'Manajemen Staff',
            'layout' => 'admin',
            'nama' => Session::get('auth.staff_nama', 'Staff'),
            'role' => $role,
            'roleLabel' => $role->label(),
        ]);

        return Response::html($html);
    }
}
