<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusBookingGrooming;
use App\Enums\StatusBookingPetCare;
use App\Enums\StatusPenitipan;
use App\Repositories\BookingGroomingRepository;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\BookingPetCareRepository;
use App\Repositories\NotifikasiRepository;
use App\Repositories\TransaksiRepository;

final class PelangganDashboardService
{
    private const BOOKING_LIMIT = 8;

    public function __construct(
        private readonly BookingGroomingRepository $groomingRepo = new BookingGroomingRepository(),
        private readonly BookingPenitipanRepository $penitipanRepo = new BookingPenitipanRepository(),
        private readonly BookingPetCareRepository $petCareRepo = new BookingPetCareRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly NotifikasiRepository $notifikasiRepo = new NotifikasiRepository(),
        private readonly PenitipanPromoService $promoService = new PenitipanPromoService(),
    ) {}

    /**
     * @param array<string, mixed>|null $pelanggan
     * @return array{
     *   activeBookings: list<array<string, mixed>>,
     *   pendingPayments: list<array<string, mixed>>,
     *   promoEligible: bool,
     *   promoConfig: array<string, mixed>,
     *   recentNotifications: list<array<string, mixed>>,
     *   unreadNotificationCount: int
     * }
     */
    public function getHomeSummary(string $pelangganId, ?array $pelanggan): array
    {
        return [
            'activeBookings' => $this->getActiveBookings($pelangganId),
            'pendingPayments' => $this->transaksiRepo->findPendingPaymentByPelanggan($pelangganId),
            'promoEligible' => $pelanggan ? $this->promoService->isEligibleForDisplay($pelanggan) : false,
            'promoConfig' => app_settings(),
            'recentNotifications' => $this->notifikasiRepo->findRecentByPelanggan($pelangganId, 5),
            'unreadNotificationCount' => $this->notifikasiRepo->countUnreadByPelanggan($pelangganId),
        ];
    }

    /** @return list<array<string, mixed>> */
    private function getActiveBookings(string $pelangganId): array
    {
        $items = [];

        foreach ($this->groomingRepo->findAllByPelanggan($pelangganId) as $booking) {
            $status = StatusBookingGrooming::tryFrom((string) $booking['status']);

            if (!$status || in_array($status, [StatusBookingGrooming::SELESAI, StatusBookingGrooming::DIBATALKAN], true)) {
                continue;
            }

            $items[] = [
                'layanan' => 'GROOMING',
                'layanan_label' => 'Grooming',
                'label' => (string) $booking['jenis_nama'],
                'kucing' => (string) $booking['kucing_nama'],
                'tanggal' => (string) $booking['tanggal'],
                'tanggal_display' => date('d/m/Y', strtotime((string) $booking['tanggal'])),
                'status' => $status,
                'status_label' => StatusBookingGrooming::labels()[$status->value],
                'url' => '/grooming/detail?id=' . urlencode((string) $booking['id']),
                'sort_key' => (string) $booking['tanggal'],
            ];
        }

        foreach ($this->penitipanRepo->findAllByPelanggan($pelangganId) as $booking) {
            $status = StatusPenitipan::tryFrom((string) $booking['status']);

            if (!$status || in_array($status, [StatusPenitipan::CHECK_OUT, StatusPenitipan::DIBATALKAN], true)) {
                continue;
            }

            $checkIn = (string) $booking['check_in'];
            $checkOut = (string) $booking['check_out'];

            $items[] = [
                'layanan' => 'PENITIPAN',
                'layanan_label' => 'Penitipan',
                'label' => (string) $booking['paket_nama'],
                'kucing' => (string) $booking['kucing_nama'],
                'tanggal' => $checkIn,
                'tanggal_display' => date('d/m/Y', strtotime($checkIn))
                    . ' — ' . date('d/m/Y', strtotime($checkOut)),
                'status' => $status,
                'status_label' => $status->displayLabel(),
                'url' => '/penitipan/detail?id=' . urlencode((string) $booking['id']),
                'sort_key' => $checkIn,
            ];
        }

        foreach ($this->petCareRepo->findAllByPelanggan($pelangganId) as $booking) {
            $status = StatusBookingPetCare::tryFrom((string) $booking['status']);

            if (!$status || !$status->isActive()) {
                continue;
            }

            $items[] = [
                'layanan' => 'PET_CARE',
                'layanan_label' => 'Pet Care',
                'label' => (string) $booking['layanan_nama'],
                'kucing' => (string) $booking['kucing_nama'],
                'tanggal' => (string) $booking['tanggal'],
                'tanggal_display' => date('d/m/Y', strtotime((string) $booking['tanggal']))
                    . ' · ' . substr((string) $booking['slot_waktu'], 0, 5),
                'status' => $status,
                'status_label' => StatusBookingPetCare::labels()[$status->value],
                'url' => '/pet-care/riwayat',
                'sort_key' => (string) $booking['tanggal'] . ' ' . (string) $booking['slot_waktu'],
            ];
        }

        usort($items, static fn (array $a, array $b): int => strcmp((string) $a['sort_key'], (string) $b['sort_key']));

        return array_slice($items, 0, self::BOOKING_LIMIT);
    }
}
