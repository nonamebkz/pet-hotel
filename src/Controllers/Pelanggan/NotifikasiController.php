<?php

declare(strict_types=1);

namespace App\Controllers\Pelanggan;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
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
        $pelangganId = $this->auth->currentPelangganId();

        if (!$pelangganId) {
            return Response::redirect('/login');
        }

        $pelangganId = (string) $pelangganId;
        $this->notifikasiRepo->markAllAsReadByPelanggan($pelangganId);

        $html = View::render('notifikasi/index', [
            'title' => 'Notifikasi',
            'layout' => 'pelanggan',
            'nama' => Session::get('auth.pelanggan_nama', 'Pelanggan'),
            'notifikasiList' => $this->notifikasiRepo->findAllByPelanggan($pelangganId),
            'unreadNotificationCount' => 0,
        ]);

        return Response::html($html);
    }
}
