<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\StaffRole;
use App\Services\AppSettingsService;
use App\Services\AuthService;
use App\Services\PengaturanPetshopService;

final class PengaturanController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly AppSettingsService $settings = new AppSettingsService(),
        private readonly PengaturanPetshopService $pengaturanService = new PengaturanPetshopService(),
    ) {}

    public function index(Request $request): Response
    {
        return $this->adminView('admin/pengaturan/form', 'Pengaturan Bisnis', [
            'settings' => $this->settings->all(),
            'errors' => [],
        ]);
    }

    public function update(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/pengaturan');
        }

        $staffId = $this->auth->currentStaffId();

        if ($staffId === null) {
            return Response::redirect('/admin/login');
        }

        $result = $this->pengaturanService->update($request->all(), $staffId);

        if (!$result['success']) {
            return $this->formWithErrors($request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Pengaturan bisnis berhasil disimpan.');

        return Response::redirect('/admin/pengaturan');
    }

    /**
     * @param array<string, string> $errors
     */
    private function formWithErrors(Request $request, array $errors): Response
    {
        Session::pullOld($request->all());

        $merged = $this->settings->all();
        foreach ([
            'petshop_lat', 'petshop_lng', 'pickup_free_radius_km', 'pickup_extra_fee_per_km',
            'payment_deadline_hours', 'bank_name', 'bank_account_number', 'bank_account_name',
            'promo_min_days', 'promo_discount_percent', 'min_vaccination_count', 'petshop_whatsapp',
        ] as $field) {
            $value = $request->input($field);
            if ($value !== null && $value !== '') {
                $merged[$field] = $value;
            }
        }

        return $this->adminView('admin/pengaturan/form', 'Pengaturan Bisnis', [
            'settings' => $merged,
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
