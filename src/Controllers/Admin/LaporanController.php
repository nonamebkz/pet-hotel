<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\OpsiPengantaran;
use App\Enums\StaffRole;
use App\Enums\StatusPembayaran;
use App\Services\AuthService;
use App\Services\LaporanService;

final class LaporanController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly LaporanService $laporanService = new LaporanService(),
    ) {}

    public function index(Request $request): Response
    {
        $data = $this->laporanService->getIndexReport($request->all());

        return $this->adminView('admin/laporan/index', 'Laporan', $data);
    }

    public function grooming(Request $request): Response
    {
        $data = $this->laporanService->getGroomingReport($request->all());
        $data['opsiLabels'] = OpsiPengantaran::labels();
        $data['activeTab'] = 'grooming';

        return $this->adminView('admin/laporan/grooming', 'Laporan Data Grooming', $data);
    }

    public function penitipan(Request $request): Response
    {
        $data = $this->laporanService->getPenitipanReport($request->all());
        $data['opsiLabels'] = OpsiPengantaran::labels();
        $data['activeTab'] = 'penitipan';

        return $this->adminView('admin/laporan/penitipan', 'Laporan Data Pet Hotel', $data);
    }

    public function petCare(Request $request): Response
    {
        $data = $this->laporanService->getPetCareReport($request->all());
        $data['activeTab'] = 'pet-care';

        return $this->adminView('admin/laporan/pet-care', 'Laporan Data Booking Pet Care', $data);
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
            'statusPembayaranLunas' => StatusPembayaran::LUNAS->value,
        ], $data));

        return Response::html($html);
    }
}
