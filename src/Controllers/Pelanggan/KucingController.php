<?php

declare(strict_types=1);

namespace App\Controllers\Pelanggan;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\JenisKelamin;
use App\Repositories\KucingRepository;
use App\Repositories\RiwayatVaksinRepository;
use App\Services\AuthService;
use App\Services\KucingService;

final class KucingController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly KucingRepository $kucingRepo = new KucingRepository(),
        private readonly RiwayatVaksinRepository $vaksinRepo = new RiwayatVaksinRepository(),
        private readonly KucingService $kucingService = new KucingService(),
    ) {}

    public function index(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();

        $kucingList = $this->kucingRepo->findAllByPelanggan($pelangganId);

        foreach ($kucingList as &$kucing) {
            $kucing['vaksin_count'] = $this->vaksinRepo->countLengkapByKucingId($kucing['id']);
            $kucing['eligible_pet_hotel'] = $this->kucingService->isEligiblePetHotel($kucing['id']);
        }
        unset($kucing);

        return $this->view('kucing/index', 'Kucing Saya', [
            'kucingList' => $kucingList,
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->view('kucing/form', 'Tambah Kucing', [
            'kucing' => null,
            'vaksinList' => [],
            'action' => '/kucing',
            'submitLabel' => 'Simpan Kucing',
        ]);
    }

    public function store(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/kucing/tambah');
        }

        $pelangganId = $this->requirePelangganId();

        $result = $this->kucingService->create(
            $pelangganId,
            $request->all(),
            $request->file('foto'),
            $request->files('vaksin_sertifikat'),
        );

        if (!$result['success']) {
            return $this->formWithErrors('kucing/form', 'Tambah Kucing', null, [], '/kucing', 'Simpan Kucing', $request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Kucing berhasil ditambahkan.');

        return Response::redirect('/kucing');
    }

    public function edit(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $kucingId = (string) $request->input('id', '');

        $kucing = $this->kucingRepo->findByIdAndPelanggan($kucingId, $pelangganId);

        if (!$kucing) {
            Session::flash('error', 'Kucing tidak ditemukan.');

            return Response::redirect('/kucing');
        }

        return $this->view('kucing/form', 'Edit Kucing', [
            'kucing' => $kucing,
            'vaksinList' => $this->vaksinRepo->findByKucingId($kucingId),
            'action' => '/kucing/update',
            'submitLabel' => 'Simpan Perubahan',
        ]);
    }

    public function update(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/kucing');
        }

        $pelangganId = $this->requirePelangganId();
        $kucingId = (string) $request->input('id', '');

        $kucing = $this->kucingRepo->findByIdAndPelanggan($kucingId, $pelangganId);

        if (!$kucing) {
            Session::flash('error', 'Kucing tidak ditemukan.');

            return Response::redirect('/kucing');
        }

        $result = $this->kucingService->update(
            $kucingId,
            $pelangganId,
            $request->all(),
            $request->file('foto'),
            $request->files('vaksin_sertifikat'),
        );

        if (!$result['success']) {
            return $this->formWithErrors(
                'kucing/form',
                'Edit Kucing',
                $kucing,
                $this->buildVaksinListFromInput($request),
                '/kucing/update',
                'Simpan Perubahan',
                $request,
                $result['errors'] ?? [],
            );
        }

        Session::flash('success', 'Data kucing berhasil diperbarui.');

        return Response::redirect('/kucing');
    }

    public function destroy(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/kucing');
        }

        $pelangganId = $this->requirePelangganId();
        $kucingId = (string) $request->input('id', '');

        $result = $this->kucingService->delete($kucingId, $pelangganId);

        if (!$result['success']) {
            Session::flash('error', $result['error'] ?? 'Gagal menghapus kucing.');
        } else {
            Session::flash('success', 'Kucing berhasil dihapus.');
        }

        return Response::redirect('/kucing');
    }

    private function requirePelangganId(): string
    {
        return (string) $this->auth->currentPelangganId();
    }

    /**
     * @param list<array<string, mixed>> $vaksinList
     * @param array<string, string> $errors
     */
    private function formWithErrors(
        string $view,
        string $title,
        ?array $kucing,
        array $vaksinList,
        string $action,
        string $submitLabel,
        Request $request,
        array $errors,
    ): Response {
        Session::pullOld($request->all());

        $mergedKucing = $kucing ?? [];
        $mergedKucing['nama'] = $request->input('nama', $mergedKucing['nama'] ?? '');
        $mergedKucing['jenis_kelamin'] = $request->input('jenis_kelamin', $mergedKucing['jenis_kelamin'] ?? '');
        $mergedKucing['ras'] = $request->input('ras', $mergedKucing['ras'] ?? '');
        $mergedKucing['tanggal_lahir'] = $request->input('tanggal_lahir', $mergedKucing['tanggal_lahir'] ?? '');
        $mergedKucing['berat_badan'] = $request->input('berat_badan', $mergedKucing['berat_badan'] ?? '');
        $mergedKucing['catatan_kesehatan'] = $request->input('catatan_kesehatan', $mergedKucing['catatan_kesehatan'] ?? '');

        if ($vaksinList === []) {
            $vaksinList = $this->buildVaksinListFromInput($request);
        }

        return $this->view($view, $title, [
            'kucing' => $mergedKucing ?: null,
            'vaksinList' => $vaksinList,
            'action' => $action,
            'submitLabel' => $submitLabel,
            'errors' => $errors,
        ]);
    }

    /** @return list<array<string, mixed>> */
    private function buildVaksinListFromInput(Request $request): array
    {
        $jenisList = $request->input('vaksin_jenis', []);
        $tanggalList = $request->input('vaksin_tanggal', []);
        $existingList = $request->input('vaksin_sertifikat_existing', []);

        if (!is_array($jenisList)) {
            $jenisList = [];
        }

        if (!is_array($tanggalList)) {
            $tanggalList = [];
        }

        if (!is_array($existingList)) {
            $existingList = [];
        }

        $count = max(count($jenisList), count($tanggalList), count($existingList));
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $result[] = [
                'jenis_vaksin' => $jenisList[$i] ?? '',
                'tanggal_vaksin' => $tanggalList[$i] ?? '',
                'sertifikat_url' => $existingList[$i] ?? '',
            ];
        }

        return $result;
    }

    /** @param array<string, mixed> $data */
    private function view(string $view, string $title, array $data = []): Response
    {
        $html = View::render($view, array_merge([
            'title' => $title,
            'layout' => 'pelanggan',
            'nama' => Session::get('auth.pelanggan_nama', 'Pelanggan'),
            'jenisKelaminLabels' => JenisKelamin::labels(),
        ], $data));

        return Response::html($html);
    }
}
