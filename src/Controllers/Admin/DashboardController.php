<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\StaffRole;
use App\Services\AuthService;
use App\Services\StaffDashboardService;

final class DashboardController
{
    public function __construct(
        private readonly StaffDashboardService $dashboardService = new StaffDashboardService(),
    ) {}

    public function index(Request $request): Response
    {
        $auth = new AuthService();
        $role = $auth->currentStaffRole() ?? StaffRole::STAFF;
        $summary = $this->dashboardService->getHomeSummary();

        $html = View::render('dashboard/admin', [
            'title' => 'Dashboard Internal',
            'layout' => 'admin',
            'nama' => Session::get('auth.staff_nama', 'Staff'),
            'role' => $role,
            'roleLabel' => $role->label(),
            'today' => $summary['today'],
            'bookingsToday' => $summary['bookingsToday'],
            'pendingVerification' => $summary['pendingVerification'],
            'penitipanAktif' => $summary['penitipanAktif'],
            'pendapatan' => $summary['pendapatan'],
            'pendingVerificationPreview' => $summary['pendingVerificationPreview'],
        ]);

        return Response::html($html);
    }
}
