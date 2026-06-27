<?php

declare(strict_types=1);

namespace App\Controllers\Pelanggan;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\PelangganRepository;
use App\Services\AuthService;
use App\Services\GeocodingService;
use App\Services\PelangganProfileService;

final class ProfilController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly PelangganProfileService $profileService = new PelangganProfileService(),
        private readonly GeocodingService $geocoding = new GeocodingService(),
    ) {}

    public function index(Request $request): Response
    {
        $pelanggan = $this->getPelangganOrRedirect();

        if ($pelanggan instanceof Response) {
            return $pelanggan;
        }

        return $this->view('profil/index', 'Profil Saya', [
            'pelanggan' => $pelanggan,
            'addressComplete' => $this->profileService->isAddressComplete($pelanggan),
        ]);
    }

    public function update(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/profil');
        }

        $pelangganId = (string) $this->auth->currentPelangganId();
        $pelanggan = $this->pelangganRepo->findById($pelangganId);

        if (!$pelanggan) {
            return Response::redirect('/login');
        }

        $result = $this->profileService->updateProfile(
            $pelangganId,
            (string) $request->input('nama', ''),
            (string) $request->input('no_telepon', ''),
            (string) $request->input('alamat_lengkap', ''),
            $this->parseCoordinate($request->input('latitude')),
            $this->parseCoordinate($request->input('longitude')),
            $request->file('foto_profil'),
        );

        if (!$result['success']) {
            Session::pullOld($request->all());

            return $this->view('profil/index', 'Profil Saya', [
                'pelanggan' => array_merge($pelanggan, [
                    'nama' => $request->input('nama', $pelanggan['nama']),
                    'no_telepon' => $request->input('no_telepon', $pelanggan['no_telepon']),
                    'alamat_lengkap' => $request->input('alamat_lengkap', $pelanggan['alamat_lengkap']),
                    'latitude' => $request->input('latitude', $pelanggan['latitude']),
                    'longitude' => $request->input('longitude', $pelanggan['longitude']),
                ]),
                'addressComplete' => $this->profileService->isAddressComplete($pelanggan),
                'errors' => $result['errors'] ?? [],
            ]);
        }

        Session::set('auth.pelanggan_nama', $result['pelanggan']['nama'] ?? Session::get('auth.pelanggan_nama'));
        Session::flash('success', 'Profil berhasil diperbarui.');

        return Response::redirect('/profil');
    }

    public function geocode(Request $request): Response
    {
        $address = trim((string) $request->input('q', ''));

        if (mb_strlen($address) < 5) {
            return Response::json([
                'success' => false,
                'error' => 'Alamat terlalu pendek untuk dicari.',
            ], 422);
        }

        $result = $this->geocoding->geocode($address);

        if ($result === null) {
            return Response::json([
                'success' => false,
                'error' => 'Lokasi tidak ditemukan. Perjelas alamat atau pilih manual di peta.',
            ], 404);
        }

        return Response::json([
            'success' => true,
            'lat' => $result['lat'],
            'lng' => $result['lng'],
            'display_name' => $result['display_name'] ?? null,
        ]);
    }

    /** @return array<string, mixed>|Response */
    private function getPelangganOrRedirect(): array|Response
    {
        $pelangganId = $this->auth->currentPelangganId();

        if (!$pelangganId) {
            return Response::redirect('/login');
        }

        $pelanggan = $this->pelangganRepo->findById($pelangganId);

        if (!$pelanggan) {
            return Response::redirect('/login');
        }

        return $pelanggan;
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

    private function parseCoordinate(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}
