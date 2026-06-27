<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\StaffRole;
use App\Enums\StatusBookingPetCare;
use App\Enums\StatusLayanan;
use App\Enums\StatusSlotPetCare;
use App\Repositories\BookingPetCareRepository;
use App\Repositories\KuotaPetCareRepository;
use App\Repositories\LayananPetCareRepository;
use App\Services\AuthService;
use App\Services\KuotaPetCareService;
use App\Services\LayananPetCareService;
use App\Services\PetCareBookingService;

final class PetCareController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly LayananPetCareRepository $layananRepo = new LayananPetCareRepository(),
        private readonly KuotaPetCareRepository $kuotaRepo = new KuotaPetCareRepository(),
        private readonly BookingPetCareRepository $bookingRepo = new BookingPetCareRepository(),
        private readonly LayananPetCareService $layananService = new LayananPetCareService(),
        private readonly KuotaPetCareService $kuotaService = new KuotaPetCareService(),
        private readonly PetCareBookingService $bookingService = new PetCareBookingService(),
    ) {}

    public function layananIndex(Request $request): Response
    {
        return $this->adminView('admin/pet-care/layanan/index', 'Layanan Pet Care', [
            'layananList' => $this->layananRepo->findAllIncludingDeleted(),
            'statusLabels' => StatusLayanan::labels(),
        ]);
    }

    public function layananCreate(Request $request): Response
    {
        return $this->adminView('admin/pet-care/layanan/form', 'Tambah Layanan Pet Care', [
            'layanan' => null,
            'action' => '/admin/pet-care/layanan/tambah',
            'submitLabel' => 'Simpan Layanan',
            'statusLabels' => StatusLayanan::labels(),
        ]);
    }

    public function layananStore(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/pet-care/layanan/tambah');
        }

        $result = $this->layananService->create($request->all());

        if (!$result['success']) {
            return $this->layananFormWithErrors(null, '/admin/pet-care/layanan/tambah', 'Simpan Layanan', $request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Layanan berhasil ditambahkan.');

        return Response::redirect('/admin/pet-care/layanan');
    }

    public function layananEdit(Request $request): Response
    {
        $id = (string) $request->input('id', '');
        $layanan = $this->layananRepo->findById($id);

        if (!$layanan || $layanan['deleted_at'] !== null) {
            Session::flash('error', 'Layanan tidak ditemukan.');

            return Response::redirect('/admin/pet-care/layanan');
        }

        return $this->adminView('admin/pet-care/layanan/form', 'Edit Layanan Pet Care', [
            'layanan' => $layanan,
            'action' => '/admin/pet-care/layanan/edit?id=' . urlencode($id),
            'submitLabel' => 'Simpan Perubahan',
            'statusLabels' => StatusLayanan::labels(),
        ]);
    }

    public function layananUpdate(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/pet-care/layanan');
        }

        $id = (string) $request->input('id', '');
        $layanan = $this->layananRepo->findById($id);

        if (!$layanan || $layanan['deleted_at'] !== null) {
            Session::flash('error', 'Layanan tidak ditemukan.');

            return Response::redirect('/admin/pet-care/layanan');
        }

        $result = $this->layananService->update($id, $request->all());

        if (!$result['success']) {
            return $this->layananFormWithErrors($layanan, '/admin/pet-care/layanan/edit?id=' . urlencode($id), 'Simpan Perubahan', $request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Layanan berhasil diperbarui.');

        return Response::redirect('/admin/pet-care/layanan');
    }

    public function layananDestroy(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/pet-care/layanan');
        }

        $id = (string) $request->input('id', '');
        $result = $this->layananService->delete($id);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Layanan berhasil dihapus.' : ($result['error'] ?? 'Gagal menghapus layanan.'));

        return Response::redirect('/admin/pet-care/layanan');
    }

    public function slotIndex(Request $request): Response
    {
        $tanggal = (string) $request->input('tanggal', date('Y-m-d'));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $tanggal = date('Y-m-d');
        }

        return $this->adminView('admin/pet-care/slot/index', 'Slot Dokter Pet Care', [
            'tanggal' => $tanggal,
            'slotList' => $this->kuotaRepo->findByDate($tanggal),
            'statusSlotLabels' => StatusSlotPetCare::labels(),
        ]);
    }

    public function slotCreate(Request $request): Response
    {
        return $this->adminView('admin/pet-care/slot/form', 'Tambah Slot Dokter', [
            'action' => '/admin/pet-care/slot/tambah',
            'submitLabel' => 'Simpan Slot',
            'tanggal' => (string) $request->input('tanggal', date('Y-m-d')),
        ]);
    }

    public function slotStore(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/pet-care/slot/tambah');
        }

        $result = $this->kuotaService->create($request->all());

        if (!$result['success']) {
            Session::pullOld($request->all());
            Session::flash('errors', $result['errors'] ?? []);

            return Response::redirect('/admin/pet-care/slot/tambah');
        }

        Session::flash('success', 'Slot berhasil ditambahkan.');

        $tanggal = (string) $request->input('tanggal', date('Y-m-d'));

        return Response::redirect('/admin/pet-care/slot?tanggal=' . urlencode($tanggal));
    }

    public function slotClose(Request $request): Response
    {
        return $this->slotToggle($request, 'close');
    }

    public function slotOpen(Request $request): Response
    {
        return $this->slotToggle($request, 'open');
    }

    public function slotDestroy(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/pet-care/slot');
        }

        $id = (string) $request->input('id', '');
        $tanggal = (string) $request->input('tanggal', date('Y-m-d'));
        $result = $this->kuotaService->delete($id);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Slot berhasil dihapus.' : ($result['error'] ?? 'Gagal menghapus slot.'));

        return Response::redirect('/admin/pet-care/slot?tanggal=' . urlencode($tanggal));
    }

    public function bookingIndex(Request $request): Response
    {
        $filters = [
            'status' => trim((string) $request->input('status', '')),
            'tanggal' => trim((string) $request->input('tanggal', '')),
        ];

        if ($filters['status'] === '') {
            unset($filters['status']);
        }

        if ($filters['tanggal'] === '') {
            unset($filters['tanggal']);
        }

        return $this->adminView('admin/pet-care/booking/index', 'Booking Pet Care', [
            'bookingList' => $this->bookingRepo->findAllForAdmin($filters),
            'statusLabels' => StatusBookingPetCare::labels(),
            'filterStatus' => (string) $request->input('status', ''),
            'filterTanggal' => (string) $request->input('tanggal', ''),
        ]);
    }

    public function bookingUpdateStatus(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/pet-care/booking');
        }

        $id = (string) $request->input('id', '');
        $status = (string) $request->input('status', '');
        $result = $this->bookingService->updateStatus($id, $status);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Status booking diperbarui.' : ($result['error'] ?? 'Gagal memperbarui status.'));

        return Response::redirect($this->bookingRedirectUrl($request));
    }

    public function bookingCancel(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/pet-care/booking');
        }

        $id = (string) $request->input('id', '');
        $alasan = trim((string) $request->input('alasan', '')) ?: null;
        $staffId = (string) $this->auth->currentStaffId();
        $result = $this->bookingService->cancelByStaff($id, $staffId, $alasan);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Booking dibatalkan.' : ($result['error'] ?? 'Gagal membatalkan booking.'));

        return Response::redirect($this->bookingRedirectUrl($request));
    }

    private function slotToggle(Request $request, string $action): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/pet-care/slot');
        }

        $id = (string) $request->input('id', '');
        $tanggal = (string) $request->input('tanggal', date('Y-m-d'));
        $result = $action === 'close'
            ? $this->kuotaService->close($id)
            : $this->kuotaService->open($id);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Status slot diperbarui.' : ($result['error'] ?? 'Gagal memperbarui slot.'));

        return Response::redirect('/admin/pet-care/slot?tanggal=' . urlencode($tanggal));
    }

    private function bookingRedirectUrl(Request $request): string
    {
        $params = [];

        if ($request->input('filter_status')) {
            $params['status'] = (string) $request->input('filter_status');
        }

        if ($request->input('filter_tanggal')) {
            $params['tanggal'] = (string) $request->input('filter_tanggal');
        }

        $query = $params !== [] ? '?' . http_build_query($params) : '';

        return '/admin/pet-care/booking' . $query;
    }

    /**
     * @param array<string, mixed>|null $layanan
     * @param array<string, string> $errors
     */
    private function layananFormWithErrors(
        ?array $layanan,
        string $action,
        string $submitLabel,
        Request $request,
        array $errors,
    ): Response {
        Session::pullOld($request->all());

        $merged = $layanan ?? [];
        $merged['nama'] = $request->input('nama', $merged['nama'] ?? '');
        $merged['deskripsi'] = $request->input('deskripsi', $merged['deskripsi'] ?? '');
        $merged['harga'] = $request->input('harga', $merged['harga'] ?? '');
        $merged['estimasi_durasi_menit'] = $request->input('estimasi_durasi_menit', $merged['estimasi_durasi_menit'] ?? '');
        $merged['status'] = $request->input('status', $merged['status'] ?? StatusLayanan::AKTIF->value);

        return $this->adminView('admin/pet-care/layanan/form', $layanan ? 'Edit Layanan Pet Care' : 'Tambah Layanan Pet Care', [
            'layanan' => $merged ?: null,
            'action' => $action,
            'submitLabel' => $submitLabel,
            'statusLabels' => StatusLayanan::labels(),
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
