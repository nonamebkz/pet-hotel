<?php

declare(strict_types=1);

namespace App\Controllers\Pelanggan;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\KucingRepository;
use App\Repositories\PelangganRepository;
use App\Services\AuthService;
use App\Services\PelangganProfileService;

final class DashboardController
{
    public function index(Request $request): Response
    {
        $auth = new AuthService();
        $pelangganId = $auth->currentPelangganId();
        $pelangganRepo = new PelangganRepository();
        $profileService = new PelangganProfileService();
        $kucingRepo = new KucingRepository();

        $pelanggan = $pelangganId ? $pelangganRepo->findById((string) $pelangganId) : null;
        $kucingCount = $pelangganId ? $kucingRepo->countByPelanggan((string) $pelangganId) : 0;

        $html = View::render('dashboard/pelanggan', [
            'title' => 'Dashboard Pelanggan',
            'layout' => 'pelanggan',
            'nama' => Session::get('auth.pelanggan_nama', 'Pelanggan'),
            'email' => $pelanggan['email'] ?? '',
            'addressComplete' => $pelanggan ? $profileService->isAddressComplete($pelanggan) : false,
            'kucingCount' => $kucingCount,
        ]);

        return Response::html($html);
    }
}
