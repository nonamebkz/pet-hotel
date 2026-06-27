<?php

declare(strict_types=1);

namespace App\Controllers\Pelanggan;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusPenitipan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPerpanjanganPenitipan;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\BuktiTransferRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\KucingRepository;
use App\Repositories\MonitoringPenitipanRepository;
use App\Repositories\PaketPenitipanRepository;
use App\Repositories\PelangganRepository;
use App\Repositories\PerpanjanganPenitipanRepository;
use App\Repositories\RiwayatVaksinRepository;
use App\Repositories\TransaksiRepository;
use App\Services\AuthService;
use App\Services\KucingService;
use App\Services\LayananAntarJemputService;
use App\Services\PembayaranService;
use App\Services\PenitipanBookingService;
use App\Services\PenitipanPromoService;
use App\Services\PerpanjanganPenitipanService;

final class PenitipanController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly PaketPenitipanRepository $paketRepo = new PaketPenitipanRepository(),
        private readonly KucingRepository $kucingRepo = new KucingRepository(),
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly RiwayatVaksinRepository $vaksinRepo = new RiwayatVaksinRepository(),
        private readonly BookingPenitipanRepository $bookingRepo = new BookingPenitipanRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly BuktiTransferRepository $buktiRepo = new BuktiTransferRepository(),
        private readonly InvoiceRepository $invoiceRepo = new InvoiceRepository(),
        private readonly MonitoringPenitipanRepository $monitoringRepo = new MonitoringPenitipanRepository(),
        private readonly PerpanjanganPenitipanRepository $perpanjanganRepo = new PerpanjanganPenitipanRepository(),
        private readonly PenitipanBookingService $bookingService = new PenitipanBookingService(),
        private readonly PenitipanPromoService $promoService = new PenitipanPromoService(),
        private readonly PerpanjanganPenitipanService $perpanjanganService = new PerpanjanganPenitipanService(),
        private readonly LayananAntarJemputService $pickupService = new LayananAntarJemputService(),
        private readonly PembayaranService $pembayaranService = new PembayaranService(),
        private readonly KucingService $kucingService = new KucingService(),
    ) {}

    public function index(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $pelanggan = $this->pelangganRepo->findById($pelangganId);

        return $this->view('penitipan/index', 'Pet Hotel / Penitipan', [
            'paketList' => $this->paketRepo->findAllActive(),
            'promoEligible' => $pelanggan ? $this->promoService->isEligibleForDisplay($pelanggan) : false,
            'promoConfig' => app_settings(),
            'pickupSettings' => app_settings(),
        ]);
    }

    public function showBookingForm(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();

        if ($this->kucingRepo->countByPelanggan($pelangganId) < 1) {
            Session::flash('error', 'Minimal 1 kucing harus terdaftar sebelum booking penitipan.');

            return Response::redirect('/kucing');
        }

        $pelanggan = $this->pelangganRepo->findById($pelangganId);
        $kucingList = $this->kucingRepo->findAllByPelanggan($pelangganId);

        foreach ($kucingList as &$kucing) {
            $kucing['eligible_pet_hotel'] = $this->kucingService->isEligiblePetHotel((string) $kucing['id']);
            $kucing['vaksin_list'] = $this->vaksinRepo->findByKucingId((string) $kucing['id']);
        }
        unset($kucing);

        return $this->view('penitipan/booking-form', 'Booking Penitipan', [
            'paketList' => $this->paketRepo->findAllActive(),
            'kucingList' => $kucingList,
            'opsiLabels' => OpsiPengantaran::labels(),
            'addressComplete' => $pelanggan ? $this->pickupService->estimasiUntukPelanggan($pelanggan)['success'] : false,
            'promoEligible' => $pelanggan ? $this->promoService->isEligibleForDisplay($pelanggan) : false,
            'promoConfig' => app_settings(),
            'errors' => Session::getFlash('errors', []),
        ]);
    }

    public function estimasiBiaya(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $result = $this->bookingService->estimasiBiaya($pelangganId, $request->all());

        if (!$result['success']) {
            return Response::json(['success' => false, 'errors' => $result['errors'] ?? []], 422);
        }

        return Response::json(['success' => true, 'data' => $result['data']]);
    }

    public function storeBooking(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/penitipan/booking');
        }

        $pelangganId = $this->requirePelangganId();
        $result = $this->bookingService->createBooking($pelangganId, $request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors'] ?? []);
            Session::pullOld($request->all());

            return Response::redirect('/penitipan/booking');
        }

        Session::flash('success', 'Penitipan diajukan! Menunggu konfirmasi dari staff petshop.');

        return Response::redirect('/penitipan/riwayat');
    }

    public function riwayat(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $bookings = $this->bookingRepo->findAllByPelanggan($pelangganId);

        foreach ($bookings as &$booking) {
            $transaksi = $this->transaksiRepo->findByPenitipanBooking((string) $booking['id']);
            $booking['transaksi_lunas'] = $transaksi
                && (string) $transaksi['status_pembayaran'] === StatusPembayaran::LUNAS->value;
            $status = StatusPenitipan::tryFrom((string) $booking['status']);
            $booking['status_label'] = $status
                ? $status->displayLabel((bool) $booking['transaksi_lunas'])
                : (string) $booking['status'];
        }
        unset($booking);

        return $this->view('penitipan/riwayat', 'Riwayat Penitipan', [
            'bookingList' => $bookings,
        ]);
    }

    public function detail(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $id = (string) $request->input('id', '');
        $booking = $this->bookingRepo->findDetailByIdAndPelanggan($id, $pelangganId);

        if (!$booking) {
            Session::flash('error', 'Booking tidak ditemukan.');

            return Response::redirect('/penitipan/riwayat');
        }

        $transaksi = $this->transaksiRepo->findByPenitipanBooking($id);
        $transaksiLunas = $transaksi
            && (string) $transaksi['status_pembayaran'] === StatusPembayaran::LUNAS->value;
        $bukti = $transaksi ? $this->buktiRepo->findByTransaksiId((string) $transaksi['id']) : null;
        $invoice = $transaksi ? $this->invoiceRepo->findByTransaksiId((string) $transaksi['id']) : null;
        $statusEnum = StatusPenitipan::tryFrom((string) $booking['status']);
        $vaksinList = $this->vaksinRepo->findByKucingId((string) $booking['kucing_id']);
        $monitoringList = $this->monitoringRepo->findByBookingId($id);
        $perpanjanganList = $this->perpanjanganRepo->findByBookingId($id);

        foreach ($perpanjanganList as &$pp) {
            $ppTransaksi = $this->transaksiRepo->findByPerpanjanganId((string) $pp['id']);
            $pp['transaksi'] = $ppTransaksi;
        }
        unset($pp);

        return $this->view('penitipan/detail', 'Detail Penitipan', [
            'booking' => $booking,
            'transaksi' => $transaksi,
            'transaksiLunas' => $transaksiLunas,
            'bukti' => $bukti,
            'invoice' => $invoice,
            'statusEnum' => $statusEnum,
            'statusLabel' => $statusEnum ? $statusEnum->displayLabel($transaksiLunas) : '',
            'vaksinList' => $vaksinList,
            'monitoringList' => $monitoringList,
            'perpanjanganList' => $perpanjanganList,
            'opsiLabels' => OpsiPengantaran::labels(),
            'perpanjanganLabels' => StatusPerpanjanganPenitipan::labels(),
            'bankConfig' => app_settings(),
            'canCancel' => $statusEnum?->canCancelByPelanggan($transaksiLunas) ?? false,
            'canPerpanjang' => $statusEnum?->canRequestPerpanjangan($transaksiLunas) ?? false,
        ]);
    }

    public function showPembayaran(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $bookingId = (string) $request->input('id', '');
        $booking = $this->bookingRepo->findByIdAndPelanggan($bookingId, $pelangganId);

        if (!$booking || (string) $booking['status'] !== StatusPenitipan::MENUNGGU_PEMBAYARAN->value) {
            Session::flash('error', 'Booking tidak siap untuk pembayaran.');

            return Response::redirect('/penitipan/riwayat');
        }

        $transaksi = $this->transaksiRepo->findByPenitipanBooking($bookingId);

        if (!$transaksi || (string) $transaksi['status_pembayaran'] !== StatusPembayaran::MENUNGGU_PEMBAYARAN->value) {
            Session::flash('error', 'Tagihan tidak ditemukan atau sudah dibayar.');

            return Response::redirect('/penitipan/detail?id=' . urlencode($bookingId));
        }

        $bukti = $this->buktiRepo->findByTransaksiId((string) $transaksi['id']);

        return $this->view('penitipan/pembayaran', 'Pembayaran Penitipan', [
            'booking' => $booking,
            'transaksi' => $transaksi,
            'bukti' => $bukti,
            'bankConfig' => app_settings(),
            'errors' => Session::getFlash('errors', []),
            'isPerpanjangan' => false,
        ]);
    }

    public function storePembayaran(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/penitipan/riwayat');
        }

        $pelangganId = $this->requirePelangganId();
        $transaksiId = (string) $request->input('transaksi_id', '');
        $bookingId = (string) $request->input('booking_id', '');

        $result = $this->pembayaranService->uploadBuktiTransfer(
            $transaksiId,
            $pelangganId,
            $request->file('bukti'),
        );

        if (!$result['success']) {
            Session::flash('errors', $result['errors'] ?? ['general' => $result['error'] ?? 'Gagal upload bukti.']);

            return Response::redirect('/penitipan/pembayaran?id=' . urlencode($bookingId));
        }

        Session::flash('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi staff.');

        return Response::redirect('/penitipan/detail?id=' . urlencode($bookingId));
    }

    public function invoice(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $bookingId = (string) $request->input('id', '');
        $booking = $this->bookingRepo->findDetailByIdAndPelanggan($bookingId, $pelangganId);

        if (!$booking) {
            Session::flash('error', 'Booking tidak ditemukan.');

            return Response::redirect('/penitipan/riwayat');
        }

        $transaksi = $this->transaksiRepo->findByPenitipanBooking($bookingId);
        $invoice = $transaksi ? $this->invoiceRepo->findByTransaksiId((string) $transaksi['id']) : null;

        if (!$transaksi || !$invoice || (string) $transaksi['status_pembayaran'] !== StatusPembayaran::LUNAS->value) {
            Session::flash('error', 'Invoice belum tersedia.');

            return Response::redirect('/penitipan/detail?id=' . urlencode($bookingId));
        }

        return $this->view('penitipan/invoice', 'Invoice Penitipan', [
            'booking' => $booking,
            'transaksi' => $transaksi,
            'invoice' => $invoice,
            'opsiLabels' => OpsiPengantaran::labels(),
        ]);
    }

    public function cancelBooking(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/penitipan/riwayat');
        }

        $pelangganId = $this->requirePelangganId();
        $bookingId = (string) $request->input('id', '');
        $result = $this->bookingService->cancelByPelanggan($bookingId, $pelangganId);

        Session::flash(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Penitipan berhasil dibatalkan.' : ($result['error'] ?? 'Gagal membatalkan.'),
        );

        return Response::redirect('/penitipan/riwayat');
    }

    public function estimasiPerpanjangan(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $bookingId = (string) $request->input('booking_id', '');
        $checkOutBaru = (string) $request->input('check_out_baru', '');

        $result = $this->perpanjanganService->estimasi($bookingId, $pelangganId, $checkOutBaru);

        if (!$result['success']) {
            return Response::json(['success' => false, 'errors' => $result['errors'] ?? []], 422);
        }

        return Response::json(['success' => true, 'data' => $result['data']]);
    }

    public function storePerpanjangan(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/penitipan/riwayat');
        }

        $pelangganId = $this->requirePelangganId();
        $bookingId = (string) $request->input('booking_id', '');
        $checkOutBaru = (string) $request->input('check_out_baru', '');

        $result = $this->perpanjanganService->ajukan($bookingId, $pelangganId, $checkOutBaru);

        if (!$result['success']) {
            Session::flash('errors', $result['errors'] ?? []);
            Session::flash('error', $result['errors']['general'] ?? 'Gagal mengajukan perpanjangan.');

            return Response::redirect('/penitipan/detail?id=' . urlencode($bookingId));
        }

        Session::flash('success', 'Permintaan perpanjangan diajukan. Menunggu konfirmasi staff.');

        return Response::redirect('/penitipan/detail?id=' . urlencode($bookingId));
    }

    public function showPembayaranPerpanjangan(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $perpanjanganId = (string) $request->input('id', '');
        $perpanjangan = $this->perpanjanganRepo->findByIdAndPelanggan($perpanjanganId, $pelangganId);

        if (!$perpanjangan
            || (string) $perpanjangan['status'] !== StatusPerpanjanganPenitipan::MENUNGGU_PEMBAYARAN->value) {
            Session::flash('error', 'Perpanjangan tidak siap untuk pembayaran.');

            return Response::redirect('/penitipan/riwayat');
        }

        $transaksi = $this->transaksiRepo->findByPerpanjanganId($perpanjanganId);
        $booking = $this->bookingRepo->findById((string) $perpanjangan['booking_penitipan_id']);
        $bukti = $transaksi ? $this->buktiRepo->findByTransaksiId((string) $transaksi['id']) : null;

        return $this->view('penitipan/pembayaran', 'Pembayaran Perpanjangan', [
            'booking' => $booking,
            'transaksi' => $transaksi,
            'bukti' => $bukti,
            'perpanjangan' => $perpanjangan,
            'bankConfig' => app_settings(),
            'errors' => Session::getFlash('errors', []),
            'isPerpanjangan' => true,
        ]);
    }

    public function storePembayaranPerpanjangan(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/penitipan/riwayat');
        }

        $pelangganId = $this->requirePelangganId();
        $transaksiId = (string) $request->input('transaksi_id', '');
        $perpanjanganId = (string) $request->input('perpanjangan_id', '');
        $bookingId = (string) $request->input('booking_id', '');

        $result = $this->pembayaranService->uploadBuktiTransfer(
            $transaksiId,
            $pelangganId,
            $request->file('bukti'),
        );

        if (!$result['success']) {
            Session::flash('errors', $result['errors'] ?? ['general' => $result['error'] ?? 'Gagal upload bukti.']);

            return Response::redirect('/penitipan/perpanjangan/pembayaran?id=' . urlencode($perpanjanganId));
        }

        Session::flash('success', 'Bukti transfer perpanjangan berhasil diupload.');

        return Response::redirect('/penitipan/detail?id=' . urlencode($bookingId));
    }

    public function estimasiPickup(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $pelanggan = $this->pelangganRepo->findById($pelangganId);

        if (!$pelanggan) {
            return Response::json(['success' => false, 'error' => 'Pelanggan tidak ditemukan.'], 404);
        }

        $result = $this->pickupService->estimasiUntukPelanggan($pelanggan);

        if (!$result['success']) {
            return Response::json($result, 422);
        }

        return Response::json([
            'success' => true,
            'jarak_km' => $result['jarak_km'],
            'biaya_antar_jemput' => $result['biaya_antar_jemput'],
            'gratis' => $result['gratis'],
            'free_radius_km' => (float) app_settings('pickup_free_radius_km'),
        ]);
    }

    private function requirePelangganId(): string
    {
        return (string) $this->auth->currentPelangganId();
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
