<?php

declare(strict_types=1);

namespace App\Controllers\Pelanggan;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
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
        $pelangganId = (string) $this->auth->currentPelangganId();
        $data = $this->riwayatService->getPelangganRiwayat($pelangganId, $request->all());

        return $this->view('transaksi/index', 'Riwayat Transaksi', $data);
    }

    /** @param array<string, mixed> $data */
    private function view(string $view, string $title, array $data = []): Response
    {
        $html = View::render($view, array_merge([
            'title' => $title,
            'layout' => 'pelanggan',
            'nama' => Session::get('auth.pelanggan_nama', 'Pelanggan'),
        ], $data));

        return Response::html($html);
    }
}
