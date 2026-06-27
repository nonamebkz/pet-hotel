<?php

declare(strict_types=1);

namespace App\Controllers\Pelanggan;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusBookingGrooming;
use App\Enums\StatusPembayaran;
use App\Repositories\BookingGroomingRepository;
use App\Repositories\BuktiTransferRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\JenisGroomingRepository;
use App\Repositories\KucingRepository;
use App\Repositories\KuotaGroomingRepository;
use App\Repositories\PelangganRepository;
use App\Repositories\TransaksiRepository;
use App\Services\AuthService;
use App\Services\GroomingBookingService;
use App\Services\LayananAntarJemputService;
use App\Services\PembayaranService;

final class GroomingController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly JenisGroomingRepository $jenisRepo = new JenisGroomingRepository(),
        private readonly KuotaGroomingRepository $kuotaRepo = new KuotaGroomingRepository(),
        private readonly KucingRepository $kucingRepo = new KucingRepository(),
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly BookingGroomingRepository $bookingRepo = new BookingGroomingRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly BuktiTransferRepository $buktiRepo = new BuktiTransferRepository(),
        private readonly InvoiceRepository $invoiceRepo = new InvoiceRepository(),
        private readonly GroomingBookingService $bookingService = new GroomingBookingService(),
        private readonly LayananAntarJemputService $pickupService = new LayananAntarJemputService(),
        private readonly PembayaranService $pembayaranService = new PembayaranService(),
    ) {}

    public function index(Request $request): Response
    {
        return $this->view('grooming/index', 'Grooming', [
            'jenisList' => $this->jenisRepo->findAllActive(),
            'pickupSettings' => app_settings(),
        ]);
    }

    public function showBookingForm(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();

        if ($this->kucingRepo->countByPelanggan($pelangganId) < 1) {
            Session::flash('error', 'Minimal 1 kucing harus terdaftar sebelum booking grooming.');

            return Response::redirect('/kucing');
        }

        $tanggal = trim((string) $request->input('tanggal', ''));
        $availableDates = $this->kuotaRepo->findDatesWithAvailableSlots();

        if ($tanggal === '' && $availableDates !== []) {
            $tanggal = $availableDates[0];
        }

        $kuota = $tanggal !== '' ? $this->kuotaRepo->findByDate($tanggal) : null;
        $pelanggan = $this->pelangganRepo->findById($pelangganId);

        $dateOptions = [];
        foreach ($availableDates as $date) {
            $k = $this->kuotaRepo->findByDate($date);
            $dateOptions[] = [
                'tanggal' => $date,
                'sisa' => $k ? ((int) $k['slot_maksimal'] - (int) $k['slot_terisi']) : 0,
            ];
        }

        return $this->view('grooming/booking-form', 'Booking Grooming', [
            'tanggal' => $tanggal,
            'dateOptions' => $dateOptions,
            'kuota' => $kuota,
            'kucingList' => $this->kucingRepo->findAllByPelanggan($pelangganId),
            'jenisList' => $this->jenisRepo->findAllActive(),
            'opsiLabels' => OpsiPengantaran::labels(),
            'addressComplete' => $pelanggan ? $this->pickupService->estimasiUntukPelanggan($pelanggan)['success'] : false,
            'errors' => Session::getFlash('errors', []),
        ]);
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

    public function storeBooking(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/grooming/booking');
        }

        $pelangganId = $this->requirePelangganId();
        $result = $this->bookingService->createBooking($pelangganId, $request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors'] ?? []);
            Session::pullOld($request->all());

            $tanggal = (string) $request->input('tanggal', '');

            return Response::redirect('/grooming/booking' . ($tanggal !== '' ? '?tanggal=' . urlencode($tanggal) : ''));
        }

        Session::flash('success', 'Booking grooming diajukan! Menunggu konfirmasi jam dari staff petshop.');

        return Response::redirect('/grooming/riwayat');
    }

    public function riwayat(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();

        return $this->view('grooming/riwayat', 'Riwayat Grooming', [
            'bookingList' => $this->bookingRepo->findAllByPelanggan($pelangganId),
            'statusLabels' => StatusBookingGrooming::labels(),
        ]);
    }

    public function detail(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $id = (string) $request->input('id', '');
        $booking = $this->bookingRepo->findDetailByIdAndPelanggan($id, $pelangganId);

        if (!$booking) {
            Session::flash('error', 'Booking tidak ditemukan.');

            return Response::redirect('/grooming/riwayat');
        }

        $transaksi = $this->transaksiRepo->findByGroomingBooking($id);
        $bukti = $transaksi ? $this->buktiRepo->findByTransaksiId((string) $transaksi['id']) : null;
        $invoice = $transaksi ? $this->invoiceRepo->findByTransaksiId((string) $transaksi['id']) : null;
        $transaksiLunas = $transaksi
            && (string) $transaksi['status_pembayaran'] === StatusPembayaran::LUNAS->value;

        return $this->view('grooming/detail', 'Detail Booking Grooming', [
            'booking' => $booking,
            'transaksi' => $transaksi,
            'transaksiLunas' => $transaksiLunas,
            'bukti' => $bukti,
            'invoice' => $invoice,
            'statusLabels' => StatusBookingGrooming::labels(),
            'opsiLabels' => OpsiPengantaran::labels(),
            'bankConfig' => app_settings(),
        ]);
    }

    public function showPembayaran(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $bookingId = (string) $request->input('id', '');
        $booking = $this->bookingRepo->findByIdAndPelanggan($bookingId, $pelangganId);

        if (!$booking || (string) $booking['status'] !== StatusBookingGrooming::MENUNGGU_PEMBAYARAN->value) {
            Session::flash('error', 'Booking tidak siap untuk pembayaran.');

            return Response::redirect('/grooming/riwayat');
        }

        $transaksi = $this->transaksiRepo->findByGroomingBooking($bookingId);

        if (!$transaksi || (string) $transaksi['status_pembayaran'] !== StatusPembayaran::MENUNGGU_PEMBAYARAN->value) {
            Session::flash('error', 'Tagihan tidak ditemukan atau sudah dibayar.');

            return Response::redirect('/grooming/detail?id=' . urlencode($bookingId));
        }

        $bukti = $this->buktiRepo->findByTransaksiId((string) $transaksi['id']);

        return $this->view('grooming/pembayaran', 'Pembayaran Grooming', [
            'booking' => $booking,
            'transaksi' => $transaksi,
            'bukti' => $bukti,
            'bankConfig' => app_settings(),
            'errors' => Session::getFlash('errors', []),
        ]);
    }

    public function storePembayaran(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/grooming/riwayat');
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

            return Response::redirect('/grooming/pembayaran?id=' . urlencode($bookingId));
        }

        Session::flash('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi staff.');

        return Response::redirect('/grooming/detail?id=' . urlencode($bookingId));
    }

    public function invoice(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();
        $bookingId = (string) $request->input('id', '');
        $booking = $this->bookingRepo->findDetailByIdAndPelanggan($bookingId, $pelangganId);

        if (!$booking) {
            Session::flash('error', 'Booking tidak ditemukan.');

            return Response::redirect('/grooming/riwayat');
        }

        $transaksi = $this->transaksiRepo->findByGroomingBooking($bookingId);
        $invoice = $transaksi ? $this->invoiceRepo->findByTransaksiId((string) $transaksi['id']) : null;

        if (!$transaksi || !$invoice || (string) $transaksi['status_pembayaran'] !== StatusPembayaran::LUNAS->value) {
            Session::flash('error', 'Invoice belum tersedia.');

            return Response::redirect('/grooming/detail?id=' . urlencode($bookingId));
        }

        return $this->view('grooming/invoice', 'Invoice Grooming', [
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

            return Response::redirect('/grooming/riwayat');
        }

        $pelangganId = $this->requirePelangganId();
        $bookingId = (string) $request->input('id', '');
        $result = $this->bookingService->cancelByPelanggan($bookingId, $pelangganId);

        Session::flash(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Booking berhasil dibatalkan.' : ($result['error'] ?? 'Gagal membatalkan booking.'),
        );

        return Response::redirect('/grooming/riwayat');
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
