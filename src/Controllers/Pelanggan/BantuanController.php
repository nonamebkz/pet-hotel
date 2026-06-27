<?php

declare(strict_types=1);

namespace App\Controllers\Pelanggan;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Services\AuthService;

final class BantuanController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
    ) {}

    public function index(Request $request): Response
    {
        if (!$this->auth->currentPelangganId()) {
            return Response::redirect('/login');
        }

        $html = View::render('bantuan/index', [
            'title' => 'Bantuan',
            'layout' => 'pelanggan',
            'nama' => Session::get('auth.pelanggan_nama', 'Pelanggan'),
            'bankConfig' => app_settings(),
        ]);

        return Response::html($html);
    }
}
