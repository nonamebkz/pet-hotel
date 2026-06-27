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
use App\Enums\StatusPenitipan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPerpanjanganPenitipan;
use App\Enums\StatusRefund;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\KamarPenitipanRepository;
use App\Repositories\KuotaPenitipanRepository;
use App\Repositories\MonitoringPenitipanRepository;
use App\Repositories\PaketPenitipanRepository;
use App\Repositories\PerpanjanganPenitipanRepository;
use App\Repositories\RiwayatVaksinRepository;
use App\Repositories\TransaksiRepository;
use App\Services\AuthService;
use App\Services\KamarPenitipanService;
use App\Services\KuotaPenitipanService;
use App\Services\MonitoringPenitipanService;
use App\Services\PaketPenitipanService;
use App\Services\PembatalanRefundService;
use App\Services\PembayaranService;
use App\Services\PenitipanBookingService;
use App\Services\PerpanjanganPenitipanService;

final class PenitipanController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly PaketPenitipanRepository $paketRepo = new PaketPenitipanRepository(),
        private readonly KamarPenitipanRepository $kamarRepo = new KamarPenitipanRepository(),
        private readonly KuotaPenitipanRepository $kuotaRepo = new KuotaPenitipanRepository(),
        private readonly BookingPenitipanRepository $bookingRepo = new BookingPenitipanRepository(),
        private readonly PerpanjanganPenitipanRepository $perpanjanganRepo = new PerpanjanganPenitipanRepository(),
        private readonly MonitoringPenitipanRepository $monitoringRepo = new MonitoringPenitipanRepository(),
        private readonly RiwayatVaksinRepository $vaksinRepo = new RiwayatVaksinRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly PaketPenitipanService $paketService = new PaketPenitipanService(),
        private readonly KamarPenitipanService $kamarService = new KamarPenitipanService(),
        private readonly KuotaPenitipanService $kuotaService = new KuotaPenitipanService(),
        private readonly PenitipanBookingService $bookingService = new PenitipanBookingService(),
        private readonly PerpanjanganPenitipanService $perpanjanganService = new PerpanjanganPenitipanService(),
        private readonly MonitoringPenitipanService $monitoringService = new MonitoringPenitipanService(),
        private readonly PembayaranService $pembayaranService = new PembayaranService(),
        private readonly PembatalanRefundService $refundService = new PembatalanRefundService(),
    ) {}

    public function paketIndex(Request $request): Response
    {
        return $this->adminView('admin/penitipan/paket/index', 'Paket Penitipan', [
            'paketList' => $this->paketRepo->findAll(),
        ]);
    }

    public function paketCreate(Request $request): Response
    {
        return $this->adminView('admin/penitipan/paket/form', 'Tambah Paket', [
            'paket' => null,
            'action' => '/admin/penitipan/paket/tambah',
            'submitLabel' => 'Simpan',
        ]);
    }

    public function paketStore(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/paket/tambah');
        }

        $result = $this->paketService->create($request->all());

        if (!$result['success']) {
            return $this->paketFormWithErrors(null, '/admin/penitipan/paket/tambah', 'Simpan', $request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Paket berhasil ditambahkan.');

        return Response::redirect('/admin/penitipan/paket');
    }

    public function paketEdit(Request $request): Response
    {
        $id = (string) $request->input('id', '');
        $paket = $this->paketRepo->findById($id);

        if (!$paket) {
            Session::flash('error', 'Paket tidak ditemukan.');

            return Response::redirect('/admin/penitipan/paket');
        }

        return $this->adminView('admin/penitipan/paket/form', 'Edit Paket', [
            'paket' => $paket,
            'action' => '/admin/penitipan/paket/edit?id=' . urlencode($id),
            'submitLabel' => 'Simpan Perubahan',
        ]);
    }

    public function paketUpdate(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/paket');
        }

        $id = (string) $request->input('id', '');
        $result = $this->paketService->update($id, $request->all());

        if (!$result['success']) {
            return $this->paketFormWithErrors($this->paketRepo->findById($id), '/admin/penitipan/paket/edit?id=' . urlencode($id), 'Simpan Perubahan', $request, $result['errors'] ?? []);
        }

        Session::flash('success', 'Paket diperbarui.');

        return Response::redirect('/admin/penitipan/paket');
    }

    public function paketDestroy(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/paket');
        }

        $result = $this->paketService->delete((string) $request->input('id', ''));
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Paket dihapus.' : ($result['error'] ?? 'Gagal menghapus.'));

        return Response::redirect('/admin/penitipan/paket');
    }

    public function kamarIndex(Request $request): Response
    {
        return $this->adminView('admin/penitipan/kamar/index', 'Kamar Penitipan', [
            'kamarList' => $this->kamarRepo->findAll(),
        ]);
    }

    public function kamarCreate(Request $request): Response
    {
        return $this->adminView('admin/penitipan/kamar/form', 'Tambah Kamar', [
            'kamar' => null,
            'action' => '/admin/penitipan/kamar/tambah',
            'submitLabel' => 'Simpan',
        ]);
    }

    public function kamarStore(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/kamar/tambah');
        }

        $result = $this->kamarService->create($request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors'] ?? []);

            return Response::redirect('/admin/penitipan/kamar/tambah');
        }

        Session::flash('success', 'Kamar berhasil ditambahkan.');

        return Response::redirect('/admin/penitipan/kamar');
    }

    public function kamarEdit(Request $request): Response
    {
        $kamar = $this->kamarRepo->findById((string) $request->input('id', ''));

        if (!$kamar) {
            Session::flash('error', 'Kamar tidak ditemukan.');

            return Response::redirect('/admin/penitipan/kamar');
        }

        return $this->adminView('admin/penitipan/kamar/form', 'Edit Kamar', [
            'kamar' => $kamar,
            'action' => '/admin/penitipan/kamar/edit?id=' . urlencode((string) $kamar['id']),
            'submitLabel' => 'Simpan Perubahan',
        ]);
    }

    public function kamarUpdate(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/kamar');
        }

        $id = (string) $request->input('id', '');
        $result = $this->kamarService->update($id, $request->all());
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Kamar diperbarui.' : ($result['errors']['general'] ?? 'Gagal memperbarui.'));

        return Response::redirect('/admin/penitipan/kamar');
    }

    public function kamarDestroy(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/kamar');
        }

        $result = $this->kamarService->delete((string) $request->input('id', ''));
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Kamar dihapus.' : ($result['error'] ?? 'Gagal menghapus.'));

        return Response::redirect('/admin/penitipan/kamar');
    }

    public function kuotaIndex(Request $request): Response
    {
        $filters = [];
        $kamarId = trim((string) $request->input('kamar_id', ''));

        if ($kamarId !== '') {
            $filters['kamar_id'] = $kamarId;
        }

        return $this->adminView('admin/penitipan/kuota/index', 'Kuota Penitipan', [
            'kuotaList' => $this->kuotaRepo->findAllForAdmin($filters),
            'kamarList' => $this->kamarRepo->findAll(),
            'filterKamarId' => $kamarId,
        ]);
    }

    public function kuotaCreate(Request $request): Response
    {
        return $this->adminView('admin/penitipan/kuota/form', 'Tambah Kuota', [
            'kuota' => null,
            'kamarList' => $this->kamarRepo->findAllActive(),
            'action' => '/admin/penitipan/kuota/tambah',
            'submitLabel' => 'Simpan',
        ]);
    }

    public function kuotaStore(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/kuota/tambah');
        }

        $result = $this->kuotaService->create($request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors'] ?? []);

            return Response::redirect('/admin/penitipan/kuota/tambah');
        }

        Session::flash('success', 'Kuota berhasil ditambahkan.');

        return Response::redirect('/admin/penitipan/kuota');
    }

    public function kuotaEdit(Request $request): Response
    {
        $kuota = $this->kuotaRepo->findById((string) $request->input('id', ''));

        if (!$kuota) {
            Session::flash('error', 'Kuota tidak ditemukan.');

            return Response::redirect('/admin/penitipan/kuota');
        }

        return $this->adminView('admin/penitipan/kuota/form', 'Edit Kuota', [
            'kuota' => $kuota,
            'kamarList' => $this->kamarRepo->findAll(),
            'action' => '/admin/penitipan/kuota/edit?id=' . urlencode((string) $kuota['id']),
            'submitLabel' => 'Simpan Perubahan',
        ]);
    }

    public function kuotaUpdate(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/kuota');
        }

        $result = $this->kuotaService->update((string) $request->input('id', ''), $request->all());
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Kuota diperbarui.' : ($result['errors']['slot_maksimal'] ?? 'Gagal memperbarui.'));

        return Response::redirect('/admin/penitipan/kuota');
    }

    public function kuotaDestroy(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/kuota');
        }

        $result = $this->kuotaService->delete((string) $request->input('id', ''));
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Kuota dihapus.' : ($result['error'] ?? 'Gagal menghapus.'));

        return Response::redirect('/admin/penitipan/kuota');
    }

    public function bookingIndex(Request $request): Response
    {
        $filters = [];
        $status = trim((string) $request->input('status', ''));
        $checkIn = trim((string) $request->input('check_in', ''));

        if ($status !== '') {
            $filters['status'] = $status;
        }

        if ($checkIn !== '') {
            $filters['check_in'] = $checkIn;
        }

        $bookings = $this->bookingRepo->findAllForAdmin($filters);

        foreach ($bookings as &$booking) {
            $transaksi = $this->transaksiRepo->findByPenitipanBooking((string) $booking['id']);
            $booking['transaksi_id'] = $transaksi['id'] ?? null;
            $booking['transaksi_lunas'] = $transaksi
                && (string) $transaksi['status_pembayaran'] === StatusPembayaran::LUNAS->value;
            $booking['status_refund'] = $transaksi['status_refund'] ?? StatusRefund::TIDAK_ADA->value;
            $statusEnum = StatusPenitipan::tryFrom((string) $booking['status']);
            $booking['status_label'] = $statusEnum
                ? $statusEnum->displayLabel((bool) $booking['transaksi_lunas'])
                : (string) $booking['status'];
            $booking['vaksin_count'] = $this->vaksinRepo->countLengkapByKucingId((string) $booking['kucing_id']);
            $booking['can_staff_cancel_refund'] = $this->refundService->canStaffCancelPenitipanWithRefund(
                $booking,
                $transaksi,
            );
            $booking['can_mark_refund'] = $this->refundService->canMarkRefundCompleted($transaksi, $booking);
        }
        unset($booking);

        return $this->adminView('admin/penitipan/booking/index', 'Booking Penitipan', [
            'bookingList' => $bookings,
            'statusLabels' => StatusPenitipan::labels(),
            'refundLabels' => StatusRefund::labels(),
            'opsiLabels' => OpsiPengantaran::labels(),
            'filterStatus' => $status,
            'filterCheckIn' => $checkIn,
            'minVaksin' => (int) app_settings('min_vaccination_count'),
        ]);
    }

    public function bookingConfirm(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/booking');
        }

        $staffId = (string) $this->auth->currentStaffId();
        $result = $this->bookingService->confirmByStaff((string) $request->input('id', ''), $staffId);
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Penitipan dikonfirmasi.' : ($result['error'] ?? 'Gagal konfirmasi.'));

        return Response::redirect('/admin/penitipan/booking');
    }

    public function bookingReject(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/booking');
        }

        $result = $this->bookingService->rejectByStaff((string) $request->input('id', ''));
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Booking ditolak.' : ($result['error'] ?? 'Gagal menolak.'));

        return Response::redirect('/admin/penitipan/booking');
    }

    public function bookingCheckIn(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/booking');
        }

        $result = $this->bookingService->checkIn((string) $request->input('id', ''));
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Check-in berhasil.' : ($result['error'] ?? 'Gagal check-in.'));

        return Response::redirect('/admin/penitipan/booking');
    }

    public function bookingUpdateStatus(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/booking');
        }

        $result = $this->bookingService->updateOperationalStatus(
            (string) $request->input('id', ''),
            (string) $request->input('status', ''),
        );
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Status diperbarui.' : ($result['error'] ?? 'Gagal memperbarui.'));

        return Response::redirect('/admin/penitipan/booking');
    }

    public function bookingCancelRefund(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/booking');
        }

        $alasan = trim((string) $request->input('alasan', '')) ?: null;
        $result = $this->refundService->cancelPenitipanByStaff((string) $request->input('id', ''), $alasan);

        Session::flash(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Booking dibatalkan. Refund ditandai pending.' : ($result['error'] ?? 'Gagal membatalkan.'),
        );

        return Response::redirect('/admin/penitipan/booking');
    }

    public function transaksiRefundSelesai(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/booking');
        }

        $result = $this->refundService->markRefundCompleted((string) $request->input('transaksi_id', ''));

        Session::flash(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Refund ditandai selesai.' : ($result['error'] ?? 'Gagal memperbarui refund.'),
        );

        return Response::redirect('/admin/penitipan/booking');
    }

    public function monitoringCreate(Request $request): Response
    {
        $bookingId = (string) $request->input('booking_id', '');
        $booking = $this->bookingRepo->findDetailById($bookingId);

        if (!$booking) {
            Session::flash('error', 'Booking tidak ditemukan.');

            return Response::redirect('/admin/penitipan/booking');
        }

        return $this->adminView('admin/penitipan/monitoring/form', 'Input Monitoring', [
            'booking' => $booking,
            'monitoringList' => $this->monitoringRepo->findByBookingId($bookingId),
            'errors' => Session::getFlash('errors', []),
        ]);
    }

    public function monitoringStore(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/booking');
        }

        $bookingId = (string) $request->input('booking_id', '');
        $staffId = (string) $this->auth->currentStaffId();
        $result = $this->monitoringService->create($bookingId, $staffId, $request->all(), $request->file('foto'));

        if (!$result['success']) {
            Session::flash('errors', $result['errors'] ?? ['general' => $result['error'] ?? 'Gagal menyimpan.']);

            return Response::redirect('/admin/penitipan/monitoring/tambah?booking_id=' . urlencode($bookingId));
        }

        Session::flash('success', 'Monitoring harian tersimpan.');

        return Response::redirect('/admin/penitipan/monitoring/tambah?booking_id=' . urlencode($bookingId));
    }

    public function perpanjanganIndex(Request $request): Response
    {
        $filters = [];
        $status = trim((string) $request->input('status', ''));

        if ($status !== '') {
            $filters['status'] = $status;
        }

        return $this->adminView('admin/penitipan/perpanjangan/index', 'Perpanjangan Penitipan', [
            'perpanjanganList' => $this->perpanjanganRepo->findAllForAdmin($filters),
            'statusLabels' => StatusPerpanjanganPenitipan::labels(),
            'filterStatus' => $status,
        ]);
    }

    public function perpanjanganConfirm(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/perpanjangan');
        }

        $staffId = (string) $this->auth->currentStaffId();
        $result = $this->perpanjanganService->confirmByStaff((string) $request->input('id', ''), $staffId);
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Perpanjangan dikonfirmasi. Menunggu pembayaran.' : ($result['error'] ?? 'Gagal konfirmasi.'));

        return Response::redirect('/admin/penitipan/perpanjangan');
    }

    public function perpanjanganReject(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/perpanjangan');
        }

        $staffId = (string) $this->auth->currentStaffId();
        $catatan = trim((string) $request->input('catatan', '')) ?: null;
        $result = $this->perpanjanganService->rejectByStaff((string) $request->input('id', ''), $staffId, $catatan);
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Perpanjangan ditolak.' : ($result['error'] ?? 'Gagal menolak.'));

        return Response::redirect('/admin/penitipan/perpanjangan');
    }

    public function pembayaranIndex(Request $request): Response
    {
        return $this->adminView('admin/penitipan/pembayaran/index', 'Verifikasi Bukti Penitipan', [
            'pendingList' => $this->transaksiRepo->findPendingVerificationPenitipan(),
        ]);
    }

    public function pembayaranSetujui(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/pembayaran');
        }

        $result = $this->pembayaranService->setujuiBukti(
            (string) $request->input('bukti_id', ''),
            (string) $this->auth->currentStaffId(),
        );
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Bukti disetujui.' : ($result['error'] ?? 'Gagal menyetujui.'));

        return Response::redirect('/admin/penitipan/pembayaran');
    }

    public function pembayaranTolak(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/pembayaran');
        }

        $result = $this->pembayaranService->tolakBukti(
            (string) $request->input('bukti_id', ''),
            (string) $this->auth->currentStaffId(),
            trim((string) $request->input('catatan', '')) ?: null,
        );
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Bukti ditolak.' : ($result['error'] ?? 'Gagal menolak.'));

        return Response::redirect('/admin/penitipan/pembayaran');
    }

    private function csrfFail(string $redirect): Response
    {
        Session::flash('error', 'Token CSRF tidak valid.');

        return Response::redirect($redirect);
    }

    /**
     * @param array<string, mixed>|null $paket
     * @param array<string, string> $errors
     */
    private function paketFormWithErrors(
        ?array $paket,
        string $action,
        string $submitLabel,
        Request $request,
        array $errors,
    ): Response {
        Session::pullOld($request->all());

        return $this->adminView('admin/penitipan/paket/form', $paket ? 'Edit Paket' : 'Tambah Paket', [
            'paket' => array_merge($paket ?? [], $request->all()),
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
