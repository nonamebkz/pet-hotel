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
        $statusParamProvided = array_key_exists('status', $_GET);
        $checkInParamProvided = array_key_exists('check_in', $_GET);
        $monitoringParamProvided = array_key_exists('monitoring', $_GET);
        $filterMonitoring = $monitoringParamProvided ? trim((string) $request->input('monitoring', '')) : '';
        $countMenungguGlobal = $this->bookingRepo->countByStatus(StatusPenitipan::MENUNGGU_KONFIRMASI->value);
        $countBelumMonitoringGlobal = $this->monitoringRepo->countBookingsMissingTodayMonitoring();
        $countSedangDitipkanGlobal = $this->bookingRepo->countByStatus(StatusPenitipan::SEDANG_DITITIPKAN->value);

        if (!$statusParamProvided && !$checkInParamProvided && !$monitoringParamProvided && $countMenungguGlobal > 0) {
            $status = StatusPenitipan::MENUNGGU_KONFIRMASI->value;
            $checkIn = '';
            $autoFiltered = true;
        } elseif ($filterMonitoring === 'belum_input') {
            $status = StatusPenitipan::SEDANG_DITITIPKAN->value;
            $checkIn = $checkInParamProvided ? trim((string) $request->input('check_in', '')) : '';
            $autoFiltered = true;
        } else {
            $status = $statusParamProvided ? trim((string) $request->input('status', '')) : '';
            $checkIn = $checkInParamProvided ? trim((string) $request->input('check_in', '')) : '';
            $autoFiltered = false;
        }

        $filters = [];

        if ($status !== '') {
            $filters['status'] = $status;
        }

        if ($checkIn !== '') {
            $filters['check_in'] = $checkIn;
        }

        $bookings = $this->bookingRepo->findAllForAdmin($filters);
        $bookingIds = array_map(static fn (array $b): string => (string) $b['id'], $bookings);
        $monitoringSummary = $this->monitoringRepo->findSummaryByBookingIds($bookingIds);

        foreach ($bookings as &$booking) {
            $this->enrichBookingRow($booking, $monitoringSummary);
        }
        unset($booking);

        if ($filterMonitoring === 'belum_input') {
            $bookings = array_values(array_filter(
                $bookings,
                static fn (array $b): bool => (string) $b['status'] === StatusPenitipan::SEDANG_DITITIPKAN->value
                    && empty($b['monitoring_has_today']),
            ));
        }

        $showPinnedPending = $status === '' && $filterMonitoring === '';
        $pendingConfirmList = [];
        $pendingMonitoringList = [];
        $otherBookingList = [];

        if ($showPinnedPending) {
            $activeIds = array_map(
                static fn (array $b): string => (string) $b['id'],
                $this->bookingRepo->findAllForAdmin(['status' => StatusPenitipan::SEDANG_DITITIPKAN->value]),
            );
            $activeSummary = $this->monitoringRepo->findSummaryByBookingIds($activeIds);

            foreach ($this->bookingRepo->findAllForAdmin(['status' => StatusPenitipan::SEDANG_DITITIPKAN->value]) as $activeBooking) {
                $this->enrichBookingRow($activeBooking, $activeSummary);

                if (empty($activeBooking['monitoring_has_today'])) {
                    $pendingMonitoringList[] = $activeBooking;
                }
            }

            usort(
                $pendingMonitoringList,
                static fn (array $a, array $b): int => strcmp((string) ($a['check_out'] ?? ''), (string) ($b['check_out'] ?? '')),
            );
        }

        foreach ($bookings as $booking) {
            if ((string) $booking['status'] === StatusPenitipan::MENUNGGU_KONFIRMASI->value) {
                $pendingConfirmList[] = $booking;
            } else {
                $otherBookingList[] = $booking;
            }
        }

        if ($showPinnedPending) {
            usort(
                $pendingConfirmList,
                static fn (array $a, array $b): int => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')),
            );

            $pinnedMonitoringIds = array_flip(array_map(
                static fn (array $b): string => (string) $b['id'],
                $pendingMonitoringList,
            ));
            $otherBookingList = array_values(array_filter(
                $otherBookingList,
                static fn (array $b): bool => !isset($pinnedMonitoringIds[(string) $b['id']]),
            ));
        } else {
            $otherBookingList = $bookings;
            $pendingConfirmList = [];
            $pendingMonitoringList = [];
        }

        return $this->adminView('admin/penitipan/booking/index', 'Booking Penitipan', [
            'pendingConfirmList' => $pendingConfirmList,
            'pendingMonitoringList' => $pendingMonitoringList,
            'otherBookingList' => $otherBookingList,
            'bookingList' => $bookings,
            'countMenungguGlobal' => $countMenungguGlobal,
            'countMenungguVerifikasi' => $this->transaksiRepo->countPendingVerification()['penitipan'],
            'countBelumMonitoringGlobal' => $countBelumMonitoringGlobal,
            'countSedangDitipkanGlobal' => $countSedangDitipkanGlobal,
            'autoFiltered' => $autoFiltered,
            'filterMonitoring' => $filterMonitoring,
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

        return $this->bookingRedirectWithFilters($request);
    }

    public function bookingReject(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/booking');
        }

        $alasan = trim((string) $request->input('alasan', '')) ?: null;
        $result = $this->bookingService->rejectByStaff((string) $request->input('id', ''), $alasan);
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Booking ditolak.' : ($result['error'] ?? 'Gagal menolak.'));

        return $this->bookingRedirectWithFilters($request);
    }

    public function bookingCheckIn(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            return $this->csrfFail('/admin/penitipan/booking');
        }

        $result = $this->bookingService->checkIn((string) $request->input('id', ''));
        Session::flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Check-in berhasil. Kucing sedang dititipkan.' : ($result['error'] ?? 'Gagal check-in.'));

        return $this->bookingRedirectWithFilters($request);
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

        return $this->bookingRedirectWithFilters($request);
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

        return $this->bookingRedirectWithFilters($request);
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

        return $this->bookingRedirectWithFilters($request);
    }

    public function monitoringCreate(Request $request): Response
    {
        $bookingId = (string) $request->input('booking_id', '');
        $booking = $this->bookingRepo->findDetailById($bookingId);

        if (!$booking) {
            Session::flash('error', 'Booking tidak ditemukan.');

            return Response::redirect('/admin/penitipan/booking');
        }

        $status = StatusPenitipan::tryFrom((string) $booking['status']);
        $canInput = $status === StatusPenitipan::SEDANG_DITITIPKAN;
        $canView = in_array($status, [StatusPenitipan::SEDANG_DITITIPKAN, StatusPenitipan::CHECK_OUT], true);

        if (!$canView) {
            Session::flash('error', 'Monitoring tidak tersedia untuk booking ini.');

            return Response::redirect('/admin/penitipan/booking');
        }

        $tab = trim((string) $request->input('tab', ''));
        if (!in_array($tab, ['input', 'riwayat'], true)) {
            $tab = $canInput ? 'input' : 'riwayat';
        } elseif ($tab === 'input' && !$canInput) {
            $tab = 'riwayat';
        }

        $monitoringList = $this->monitoringRepo->findByBookingId($bookingId);
        $lastMonitoring = $monitoringList[0] ?? null;
        $checkOut = (string) ($booking['check_out'] ?? '');
        $today = date('Y-m-d');
        $sisaHari = null;

        if ($checkOut !== '') {
            $sisaHari = max(0, (int) ((strtotime($checkOut) - strtotime($today)) / 86400));
        }

        return $this->adminView('admin/penitipan/monitoring/form', 'Monitoring Penitipan', [
            'booking' => $booking,
            'monitoringList' => $monitoringList,
            'lastMonitoring' => $lastMonitoring,
            'sisaHari' => $sisaHari,
            'canInput' => $canInput,
            'activeTab' => $tab,
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
            $errors = $result['errors'] ?? ['general' => $result['error'] ?? 'Gagal menyimpan.'];
            Session::flash('errors', $errors);
            Session::flash('error', $errors['general'] ?? ($errors['foto'] ?? reset($errors) ?: 'Gagal menyimpan monitoring.'));

            return Response::redirect('/admin/penitipan/monitoring/tambah?booking_id=' . urlencode($bookingId));
        }

        Session::flash('success', 'Monitoring harian tersimpan.');

        return Response::redirect(
            '/admin/penitipan/booking?status='
            . urlencode(StatusPenitipan::SEDANG_DITITIPKAN->value)
            . '&monitoring=belum_input',
        );
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
        $pendingList = $this->transaksiRepo->findPendingVerificationPenitipan();

        usort($pendingList, static function (array $a, array $b): int {
            $today = date('Y-m-d');
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $checkInA = (string) ($a['check_in'] ?? '');
            $checkInB = (string) ($b['check_in'] ?? '');
            $urgentA = $checkInA !== '' && $checkInA <= $tomorrow && empty($a['perpanjangan_penitipan_id']);
            $urgentB = $checkInB !== '' && $checkInB <= $tomorrow && empty($b['perpanjangan_penitipan_id']);

            if ($urgentA !== $urgentB) {
                return $urgentB <=> $urgentA;
            }

            return strcmp((string) ($a['bukti_uploaded_at'] ?? ''), (string) ($b['bukti_uploaded_at'] ?? ''));
        });

        return $this->adminView('admin/penitipan/pembayaran/index', 'Verifikasi Bukti Penitipan', [
            'pendingList' => $pendingList,
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

        if ($result['success']) {
            Session::flash('success', 'Bukti disetujui. Pembayaran lunas.');

            if (!empty($result['bookingId']) && empty($result['isPerpanjangan']) && !empty($result['checkIn'])) {
                Session::flash('success_cta_label', 'Lanjut Check-in');
                Session::flash(
                    'success_cta_href',
                    '/admin/penitipan/booking?status='
                    . urlencode(StatusPenitipan::MENUNGGU_VERIFIKASI_BUKTI->value)
                    . '&check_in='
                    . urlencode((string) $result['checkIn']),
                );
            }
        } else {
            Session::flash('error', $result['error'] ?? 'Gagal menyetujui.');
        }

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

    /** @param array<string, array{count: int, has_today: bool, last_tanggal: ?string}> $monitoringSummary */
    private function enrichBookingRow(array &$booking, array $monitoringSummary): void
    {
        $bookingId = (string) $booking['id'];
        $summary = $monitoringSummary[$bookingId] ?? ['count' => 0, 'has_today' => false, 'last_tanggal' => null];
        $booking['monitoring_count'] = $summary['count'];
        $booking['monitoring_has_today'] = $summary['has_today'];
        $booking['monitoring_last_tanggal'] = $summary['last_tanggal'];
        $transaksi = $this->transaksiRepo->findByPenitipanBooking($bookingId);
        $booking['transaksi_id'] = $transaksi['id'] ?? null;
        $booking['transaksi_lunas'] = $transaksi
            && (string) $transaksi['status_pembayaran'] === StatusPembayaran::LUNAS->value;
        $booking['status_refund'] = $transaksi['status_refund'] ?? StatusRefund::TIDAK_ADA->value;
        $booking['total_bayar'] = $transaksi
            ? (float) $transaksi['total_bayar']
            : (float) $booking['subtotal_penitipan'] - (float) $booking['potongan_promo'] + (float) $booking['biaya_antar_jemput'];
        $statusEnum = StatusPenitipan::tryFrom((string) $booking['status']);
        $booking['status_label'] = $statusEnum
            ? $statusEnum->displayLabel((bool) $booking['transaksi_lunas'])
            : (string) $booking['status'];
        $booking['vaksin_count'] = $this->vaksinRepo->countLengkapByKucingId((string) $booking['kucing_id']);
        $booking['vaksin_list'] = $this->vaksinRepo->findByKucingId((string) $booking['kucing_id']);
        $booking['can_staff_cancel_refund'] = $this->refundService->canStaffCancelPenitipanWithRefund(
            $booking,
            $transaksi,
        );
        $booking['can_mark_refund'] = $this->refundService->canMarkRefundCompleted($transaksi, $booking);
    }

    private function bookingRedirectWithFilters(Request $request): Response
    {
        $params = array_filter([
            'status' => trim((string) $request->input('filter_status', '')),
            'check_in' => trim((string) $request->input('filter_check_in', '')),
            'monitoring' => trim((string) $request->input('filter_monitoring', '')),
        ], static fn (string $value): bool => $value !== '');

        $url = '/admin/penitipan/booking';

        if ($params !== []) {
            $url .= '?' . http_build_query($params);
        }

        return Response::redirect($url);
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
