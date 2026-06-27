<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\OpsiPengantaran;
use App\Enums\StaffRole;
use App\Enums\StatusBookingGrooming;
use App\Enums\StatusPembayaran;
use App\Enums\StatusRefund;
use App\Services\AuthService;
use App\Services\GroomingBookingService;
use App\Services\JenisGroomingService;
use App\Services\KuotaGroomingService;
use App\Services\PembatalanRefundService;
use App\Services\PembayaranService;
use App\Repositories\BookingGroomingRepository;
use App\Repositories\JenisGroomingRepository;
use App\Repositories\KuotaGroomingRepository;
use App\Repositories\TransaksiRepository;

final class GroomingController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly JenisGroomingRepository $jenisRepo = new JenisGroomingRepository(),
        private readonly KuotaGroomingRepository $kuotaRepo = new KuotaGroomingRepository(),
        private readonly BookingGroomingRepository $bookingRepo = new BookingGroomingRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly JenisGroomingService $jenisService = new JenisGroomingService(),
        private readonly KuotaGroomingService $kuotaService = new KuotaGroomingService(),
        private readonly GroomingBookingService $bookingService = new GroomingBookingService(),
        private readonly PembayaranService $pembayaranService = new PembayaranService(),
        private readonly PembatalanRefundService $refundService = new PembatalanRefundService(),
    ) {}

    public function layananIndex(Request $request): Response
    {
        return $this->adminView('admin/grooming/layanan/index', 'Jenis Grooming', [
            'jenisList' => $this->jenisRepo->findAll(),
        ]);
    }

    public function layananCreate(Request $request): Response
    {
        return $this->adminView('admin/grooming/layanan/form', 'Tambah Jenis Grooming', [
            'jenis' => null,
            'action' => '/admin/grooming/layanan/tambah',
            'submitLabel' => 'Simpan',
        ]);
    }

    public function layananStore(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/layanan/tambah');
        }

        $result = $this->jenisService->create($request->all());

        if (!$result['success']) {
            return $this->jenisFormWithErrors(null, '/admin/grooming/layanan/tambah', 'Simpan', $request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Jenis grooming berhasil ditambahkan.');

        return Response::redirect('/admin/grooming/layanan');
    }

    public function layananEdit(Request $request): Response
    {
        $id = (string) $request->input('id', '');
        $jenis = $this->jenisRepo->findById($id);

        if (!$jenis) {
            Session::flash('error', 'Jenis grooming tidak ditemukan.');

            return Response::redirect('/admin/grooming/layanan');
        }

        return $this->adminView('admin/grooming/layanan/form', 'Edit Jenis Grooming', [
            'jenis' => $jenis,
            'action' => '/admin/grooming/layanan/edit?id=' . urlencode($id),
            'submitLabel' => 'Simpan Perubahan',
        ]);
    }

    public function layananUpdate(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/layanan');
        }

        $id = (string) $request->input('id', '');
        $jenis = $this->jenisRepo->findById($id);

        if (!$jenis) {
            Session::flash('error', 'Jenis grooming tidak ditemukan.');

            return Response::redirect('/admin/grooming/layanan');
        }

        $result = $this->jenisService->update($id, $request->all());

        if (!$result['success']) {
            return $this->jenisFormWithErrors($jenis, '/admin/grooming/layanan/edit?id=' . urlencode($id), 'Simpan Perubahan', $request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Jenis grooming berhasil diperbarui.');

        return Response::redirect('/admin/grooming/layanan');
    }

    public function layananDestroy(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/layanan');
        }

        $id = (string) $request->input('id', '');
        $result = $this->jenisService->delete($id);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Jenis grooming dihapus.' : ($result['error'] ?? 'Gagal menghapus.'));

        return Response::redirect('/admin/grooming/layanan');
    }

    public function kuotaIndex(Request $request): Response
    {
        return $this->adminView('admin/grooming/kuota/index', 'Kuota Grooming', [
            'kuotaList' => $this->kuotaRepo->findAllFromToday(),
        ]);
    }

    public function kuotaCreate(Request $request): Response
    {
        return $this->adminView('admin/grooming/kuota/form', 'Tambah Kuota Grooming', [
            'action' => '/admin/grooming/kuota/tambah',
            'submitLabel' => 'Simpan Kuota',
            'tanggal' => (string) $request->input('tanggal', date('Y-m-d')),
        ]);
    }

    public function kuotaStore(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/kuota/tambah');
        }

        $result = $this->kuotaService->create($request->all());

        if (!$result['success']) {
            Session::pullOld($request->all());
            Session::flash('errors', $result['errors'] ?? []);

            return Response::redirect('/admin/grooming/kuota/tambah');
        }

        Session::flash('success', 'Kuota berhasil ditambahkan.');

        return Response::redirect('/admin/grooming/kuota');
    }

    public function kuotaEdit(Request $request): Response
    {
        $id = (string) $request->input('id', '');
        $kuota = $this->kuotaRepo->findById($id);

        if (!$kuota) {
            Session::flash('error', 'Kuota tidak ditemukan.');

            return Response::redirect('/admin/grooming/kuota');
        }

        return $this->adminView('admin/grooming/kuota/form', 'Edit Kuota Grooming', [
            'kuota' => $kuota,
            'action' => '/admin/grooming/kuota/edit?id=' . urlencode($id),
            'submitLabel' => 'Simpan Perubahan',
        ]);
    }

    public function kuotaUpdate(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/kuota');
        }

        $id = (string) $request->input('id', '');
        $result = $this->kuotaService->update($id, $request->all());

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Kuota diperbarui.' : ($result['errors']['slot_maksimal'] ?? $result['errors']['general'] ?? 'Gagal memperbarui.'));

        return Response::redirect('/admin/grooming/kuota');
    }

    public function kuotaDestroy(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/kuota');
        }

        $id = (string) $request->input('id', '');
        $result = $this->kuotaService->delete($id);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Kuota dihapus.' : ($result['error'] ?? 'Gagal menghapus.'));

        return Response::redirect('/admin/grooming/kuota');
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

        $bookings = $this->bookingRepo->findAllForAdmin($filters);

        foreach ($bookings as &$booking) {
            $transaksi = $this->transaksiRepo->findByGroomingBooking((string) $booking['id']);
            $booking['transaksi_id'] = $transaksi['id'] ?? null;
            $booking['transaksi_lunas'] = $transaksi
                && (string) $transaksi['status_pembayaran'] === StatusPembayaran::LUNAS->value;
            $booking['status_refund'] = $transaksi['status_refund'] ?? StatusRefund::TIDAK_ADA->value;
            $booking['can_staff_cancel_refund'] = $this->refundService->canStaffCancelGroomingWithRefund(
                $booking,
                $transaksi,
            );
            $booking['can_mark_refund'] = $this->refundService->canMarkRefundCompleted($transaksi, $booking);
        }
        unset($booking);

        return $this->adminView('admin/grooming/booking/index', 'Booking Grooming', [
            'bookingList' => $bookings,
            'statusLabels' => StatusBookingGrooming::labels(),
            'refundLabels' => StatusRefund::labels(),
            'opsiLabels' => OpsiPengantaran::labels(),
            'filterStatus' => (string) $request->input('status', ''),
            'filterTanggal' => (string) $request->input('tanggal', ''),
        ]);
    }

    public function bookingConfirm(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/booking');
        }

        $id = (string) $request->input('id', '');
        $jam = trim((string) $request->input('jam_grooming', ''));
        $staffId = (string) $this->auth->currentStaffId();
        $result = $this->bookingService->confirmByStaff($id, $staffId, $jam);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Booking dikonfirmasi. Pelanggan diminta melakukan pembayaran.' : ($result['error'] ?? 'Gagal konfirmasi.'));

        return Response::redirect($this->bookingRedirectUrl($request));
    }

    public function bookingReject(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/booking');
        }

        $id = (string) $request->input('id', '');
        $result = $this->bookingService->rejectByStaff($id);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Booking ditolak.' : ($result['error'] ?? 'Gagal menolak.'));

        return Response::redirect($this->bookingRedirectUrl($request));
    }

    public function bookingUpdateStatus(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/booking');
        }

        $id = (string) $request->input('id', '');
        $status = (string) $request->input('status', '');
        $result = $this->bookingService->updateOperationalStatus($id, $status);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Status layanan diperbarui.' : ($result['error'] ?? 'Gagal memperbarui status.'));

        return Response::redirect($this->bookingRedirectUrl($request));
    }

    public function bookingCancelRefund(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/booking');
        }

        $id = (string) $request->input('id', '');
        $alasan = trim((string) $request->input('alasan', '')) ?: null;
        $result = $this->refundService->cancelGroomingByStaff($id, $alasan);

        Session::flash(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Booking dibatalkan. Refund ditandai pending.' : ($result['error'] ?? 'Gagal membatalkan.'),
        );

        return Response::redirect($this->bookingRedirectUrl($request));
    }

    public function transaksiRefundSelesai(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/booking');
        }

        $transaksiId = (string) $request->input('transaksi_id', '');
        $result = $this->refundService->markRefundCompleted($transaksiId);

        Session::flash(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Refund ditandai selesai.' : ($result['error'] ?? 'Gagal memperbarui refund.'),
        );

        return Response::redirect($this->bookingRedirectUrl($request));
    }

    public function pembayaranIndex(Request $request): Response
    {
        return $this->adminView('admin/grooming/pembayaran/index', 'Verifikasi Bukti Transfer', [
            'pendingList' => $this->transaksiRepo->findPendingVerification(),
        ]);
    }

    public function pembayaranSetujui(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/pembayaran');
        }

        $buktiId = (string) $request->input('bukti_id', '');
        $staffId = (string) $this->auth->currentStaffId();
        $result = $this->pembayaranService->setujuiBukti($buktiId, $staffId);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Bukti transfer disetujui. Booking terkonfirmasi.' : ($result['error'] ?? 'Gagal menyetujui.'));

        return Response::redirect('/admin/grooming/pembayaran');
    }

    public function pembayaranTolak(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/admin/grooming/pembayaran');
        }

        $buktiId = (string) $request->input('bukti_id', '');
        $catatan = trim((string) $request->input('catatan', '')) ?: null;
        $staffId = (string) $this->auth->currentStaffId();
        $result = $this->pembayaranService->tolakBukti($buktiId, $staffId, $catatan);

        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Bukti transfer ditolak. Pelanggan diminta upload ulang.' : ($result['error'] ?? 'Gagal menolak.'));

        return Response::redirect('/admin/grooming/pembayaran');
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

        return '/admin/grooming/booking' . $query;
    }

    /**
     * @param array<string, mixed>|null $jenis
     * @param array<string, string> $errors
     */
    private function jenisFormWithErrors(
        ?array $jenis,
        string $action,
        string $submitLabel,
        Request $request,
        array $errors,
    ): Response {
        Session::pullOld($request->all());

        $merged = $jenis ?? [];
        $merged['nama'] = $request->input('nama', $merged['nama'] ?? '');
        $merged['deskripsi'] = $request->input('deskripsi', $merged['deskripsi'] ?? '');
        $merged['harga'] = $request->input('harga', $merged['harga'] ?? '');
        $merged['aktif'] = $request->input('aktif', $merged['aktif'] ?? 1);

        return $this->adminView('admin/grooming/layanan/form', $jenis ? 'Edit Jenis Grooming' : 'Tambah Jenis Grooming', [
            'jenis' => $merged ?: null,
            'action' => $action,
            'submitLabel' => $submitLabel,
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
