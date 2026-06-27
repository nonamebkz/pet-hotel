<?php

declare(strict_types=1);

namespace App\Controllers\Pelanggan;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Enums\StatusBookingPetCare;
use App\Repositories\KucingRepository;
use App\Repositories\KuotaPetCareRepository;
use App\Repositories\LayananPetCareRepository;
use App\Repositories\BookingPetCareRepository;
use App\Services\AuthService;
use App\Services\PetCareBookingService;

final class PetCareController
{
    public function __construct(
        private readonly AuthService $auth = new AuthService(),
        private readonly LayananPetCareRepository $layananRepo = new LayananPetCareRepository(),
        private readonly KuotaPetCareRepository $kuotaRepo = new KuotaPetCareRepository(),
        private readonly KucingRepository $kucingRepo = new KucingRepository(),
        private readonly BookingPetCareRepository $bookingRepo = new BookingPetCareRepository(),
        private readonly PetCareBookingService $bookingService = new PetCareBookingService(),
    ) {}

    public function index(Request $request): Response
    {
        return $this->view('pet-care/index', 'Pet Care', [
            'layananList' => $this->layananRepo->findAllActive(),
        ]);
    }

    public function showBookingForm(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();

        if ($this->kucingRepo->countByPelanggan($pelangganId) < 1) {
            Session::flash('error', 'Minimal 1 kucing harus terdaftar sebelum booking pet care.');

            return Response::redirect('/kucing');
        }

        $tanggal = trim((string) $request->input('tanggal', ''));
        $availableDates = $this->kuotaRepo->findDatesWithAvailableSlots();

        if ($tanggal === '' && $availableDates !== []) {
            $tanggal = $availableDates[0];
        }

        $slots = $tanggal !== '' ? $this->kuotaRepo->findAvailableByDate($tanggal) : [];
        $kucingList = $this->kucingRepo->findAllByPelanggan($pelangganId);
        $layananList = $this->layananRepo->findAllActive();

        return $this->view('pet-care/booking-form', 'Booking Pet Care', [
            'tanggal' => $tanggal,
            'availableDates' => $availableDates,
            'slots' => $slots,
            'kucingList' => $kucingList,
            'layananList' => $layananList,
            'errors' => Session::getFlash('errors', []),
        ]);
    }

    public function storeBooking(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/pet-care/booking');
        }

        $pelangganId = $this->requirePelangganId();
        $result = $this->bookingService->createBooking($pelangganId, $request->all());

        if (!$result['success']) {
            Session::flash('errors', $result['errors'] ?? []);
            Session::pullOld($request->all());

            $tanggal = (string) $request->input('tanggal', '');

            return Response::redirect('/pet-care/booking' . ($tanggal !== '' ? '?tanggal=' . urlencode($tanggal) : ''));
        }

        Session::flash('success', 'Booking pet care berhasil! Status: Terkonfirmasi. Pembayaran dilakukan di loket saat kunjungan.');

        return Response::redirect('/pet-care/riwayat');
    }

    public function riwayat(Request $request): Response
    {
        $pelangganId = $this->requirePelangganId();

        return $this->view('pet-care/riwayat', 'Riwayat Pet Care', [
            'bookingList' => $this->bookingRepo->findAllByPelanggan($pelangganId),
            'statusLabels' => StatusBookingPetCare::labels(),
        ]);
    }

    public function cancelBooking(Request $request): Response
    {
        if (!Csrf::verifyRequest()) {
            Session::flash('error', 'Token CSRF tidak valid.');

            return Response::redirect('/pet-care/riwayat');
        }

        $pelangganId = $this->requirePelangganId();
        $bookingId = (string) $request->input('id', '');
        $result = $this->bookingService->cancelByPelanggan($bookingId, $pelangganId);

        Session::flash(
            $result['success'] ? 'success' : 'error',
            $result['success'] ? 'Booking berhasil dibatalkan.' : ($result['error'] ?? 'Gagal membatalkan booking.'),
        );

        return Response::redirect('/pet-care/riwayat');
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
