<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Enums\JenisLayanan;
use App\Enums\JenisNotifikasi;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusPenitipan;
use App\Enums\StatusPembayaran;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\KamarPenitipanRepository;
use App\Repositories\KucingRepository;
use App\Repositories\KuotaPenitipanRepository;
use App\Repositories\PaketPenitipanRepository;
use App\Repositories\PelangganRepository;
use App\Repositories\RiwayatVaksinRepository;
use App\Repositories\TransaksiRepository;
use PDO;
use function uuid;

final class PenitipanBookingService
{
    public function __construct(
        private readonly BookingPenitipanRepository $bookingRepo = new BookingPenitipanRepository(),
        private readonly PaketPenitipanRepository $paketRepo = new PaketPenitipanRepository(),
        private readonly KamarPenitipanRepository $kamarRepo = new KamarPenitipanRepository(),
        private readonly KuotaPenitipanRepository $kuotaRepo = new KuotaPenitipanRepository(),
        private readonly KucingRepository $kucingRepo = new KucingRepository(),
        private readonly PelangganRepository $pelangganRepo = new PelangganRepository(),
        private readonly RiwayatVaksinRepository $vaksinRepo = new RiwayatVaksinRepository(),
        private readonly TransaksiRepository $transaksiRepo = new TransaksiRepository(),
        private readonly PenitipanPromoService $promoService = new PenitipanPromoService(),
        private readonly LayananAntarJemputService $pickupService = new LayananAntarJemputService(),
        private readonly KucingService $kucingService = new KucingService(),
        private readonly AppSettingsService $settings = new AppSettingsService(),
        private readonly NotifikasiService $notifikasiService = new NotifikasiService(),
    ) {}

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, data?: array<string, mixed>}
     */
    public function estimasiBiaya(string $pelangganId, array $input): array
    {
        $calc = $this->buildPricing($pelangganId, $input);

        if (!$calc['success']) {
            return $calc;
        }

        return ['success' => true, 'data' => $calc['data']];
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, bookingId?: string}
     */
    public function createBooking(string $pelangganId, array $input): array
    {
        if ($this->kucingRepo->countByPelanggan($pelangganId) < 1) {
            return [
                'success' => false,
                'errors' => ['kucing' => 'Minimal 1 kucing harus terdaftar sebelum booking.'],
            ];
        }

        $calc = $this->buildPricing($pelangganId, $input);

        if (!$calc['success']) {
            return $calc;
        }

        $data = $calc['data'];
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $kamarId = $this->findAndLockAvailableKamar(
                (string) $data['check_in'],
                (string) $data['check_out'],
                $pdo,
            );

            if ($kamarId === null) {
                $pdo->rollBack();

                return ['success' => false, 'errors' => ['general' => 'Kuota penitipan penuh untuk rentang tanggal ini.']];
            }

            $this->incrementKuotaRange($kamarId, (string) $data['check_in'], (string) $data['check_out'], $pdo);

            $bookingId = uuid();
            $transaksiId = uuid();

            $this->bookingRepo->create(
                $bookingId,
                $pelangganId,
                (string) $data['kucing_id'],
                (string) $data['paket_id'],
                $kamarId,
                (string) $data['check_in'],
                (string) $data['check_out'],
                (int) $data['lama_hari'],
                (bool) $data['promo_dipakai'],
                (float) $data['subtotal'],
                (float) $data['potongan_promo'],
                (string) $data['opsi_pengantaran'],
                $data['jarak_km'],
                (float) $data['biaya_antar_jemput'],
                $data['catatan_makan'],
                $pdo,
            );

            $this->transaksiRepo->create(
                $transaksiId,
                $pelangganId,
                JenisLayanan::PENITIPAN->value,
                $bookingId,
                (float) $data['subtotal'],
                (float) $data['biaya_antar_jemput'],
                (float) $data['total_bayar'],
                $pdo,
                (float) $data['potongan_promo'],
            );

            $pdo->commit();

            return ['success' => true, 'bookingId' => $bookingId];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'errors' => ['general' => 'Gagal membuat booking penitipan.']];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function cancelByPelanggan(string $bookingId, string $pelangganId): array
    {
        $booking = $this->bookingRepo->findByIdAndPelanggan($bookingId, $pelangganId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $transaksi = $this->transaksiRepo->findByPenitipanBooking($bookingId);
        $transaksiLunas = $transaksi
            && (string) $transaksi['status_pembayaran'] === StatusPembayaran::LUNAS->value;
        $status = StatusPenitipan::tryFrom((string) $booking['status']);

        if (!$status || !$status->canCancelByPelanggan($transaksiLunas)) {
            return ['success' => false, 'error' => 'Booking tidak dapat dibatalkan. Hubungi petshop untuk bantuan.'];
        }

        $result = $this->cancelBooking($booking);

        if ($result['success']) {
            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::BOOKING_DIBATALKAN,
                'Booking penitipan dibatalkan',
                'Booking penitipan Anda telah dibatalkan.',
                (string) $booking['id'],
                'booking_penitipan',
            );
        }

        return $result;
    }

    /** @return array{success: bool, error?: string} */
    public function confirmByStaff(string $bookingId, string $staffId): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        if ((string) $booking['status'] !== StatusPenitipan::MENUNGGU_KONFIRMASI->value) {
            return ['success' => false, 'error' => 'Booking tidak dalam status menunggu konfirmasi.'];
        }

        $minVaksin = (int) $this->settings->get('min_vaccination_count');

        if ($this->vaksinRepo->countLengkapByKucingId((string) $booking['kucing_id']) < $minVaksin) {
            return [
                'success' => false,
                'error' => 'Riwayat vaksin kucing tidak memenuhi syarat. Booking harus ditolak.',
            ];
        }

        $transaksi = $this->transaksiRepo->findByPenitipanBooking($bookingId);

        if (!$transaksi) {
            return ['success' => false, 'error' => 'Data transaksi tidak ditemukan.'];
        }

        $deadlineHours = (int) $this->settings->get('payment_deadline_hours');
        $batasWaktu = date('Y-m-d H:i:s', strtotime("+{$deadlineHours} hours"));
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();
            $this->bookingRepo->confirm($bookingId, $staffId, $pdo);
            $this->transaksiRepo->setBatasWaktuBayar((string) $transaksi['id'], $batasWaktu, $pdo);
            $pdo->commit();

            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::BOOKING_DISETUJUI,
                'Booking penitipan disetujui',
                'Permintaan penitipan Anda disetujui. Silakan lakukan pembayaran sebelum batas waktu.',
                $bookingId,
                'booking_penitipan',
            );

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal mengkonfirmasi booking.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function rejectByStaff(string $bookingId): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        if ((string) $booking['status'] !== StatusPenitipan::MENUNGGU_KONFIRMASI->value) {
            return ['success' => false, 'error' => 'Booking tidak dapat ditolak.'];
        }

        $result = $this->cancelBooking($booking);

        if ($result['success']) {
            $this->notifikasiService->notifyPelanggan(
                (string) $booking['pelanggan_id'],
                JenisNotifikasi::BOOKING_DITOLAK,
                'Booking penitipan ditolak',
                'Permintaan penitipan Anda ditolak oleh staff petshop.',
                (string) $booking['id'],
                'booking_penitipan',
            );
        }

        return $result;
    }

    /** @return array{success: bool, error?: string} */
    public function checkIn(string $bookingId): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $transaksi = $this->transaksiRepo->findByPenitipanBooking($bookingId);
        $transaksiLunas = $transaksi
            && (string) $transaksi['status_pembayaran'] === StatusPembayaran::LUNAS->value;
        $status = StatusPenitipan::tryFrom((string) $booking['status']);

        if (!$status || !$status->canCheckIn($transaksiLunas)) {
            return ['success' => false, 'error' => 'Booking belum siap untuk check-in.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();
            $this->bookingRepo->updateStatus($bookingId, StatusPenitipan::CHECK_IN->value, $pdo);
            $pdo->commit();

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal melakukan check-in.'];
        }
    }

    /** @return array{success: bool, error?: string} */
    public function updateOperationalStatus(string $bookingId, string $newStatus): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return ['success' => false, 'error' => 'Booking tidak ditemukan.'];
        }

        $current = StatusPenitipan::tryFrom((string) $booking['status']);
        $target = StatusPenitipan::tryFrom($newStatus);

        if (!$current || !$target || $current->nextOperationalStatus() !== $target) {
            return ['success' => false, 'error' => 'Transisi status tidak diizinkan.'];
        }

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();
            $this->bookingRepo->updateStatus($bookingId, $target->value, $pdo);
            $pdo->commit();

            if ($target === StatusPenitipan::CHECK_OUT) {
                $this->notifikasiService->notifyPelanggan(
                    (string) $booking['pelanggan_id'],
                    JenisNotifikasi::LAYANAN_SELESAI,
                    'Penitipan selesai',
                    'Kucing Anda telah check-out dari pet hotel. Terima kasih!',
                    $bookingId,
                    'booking_penitipan',
                );
            }

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal memperbarui status.'];
        }
    }

    public function incrementKuotaRange(
        string $kamarId,
        string $checkIn,
        string $checkOut,
        PDO $pdo,
    ): void {
        foreach ($this->kuotaRepo->datesInRange($checkIn, $checkOut) as $tanggal) {
            $kuota = $this->kuotaRepo->findByKamarAndDateForUpdate($kamarId, $tanggal, $pdo);

            if ($kuota) {
                $this->kuotaRepo->incrementTerisi((string) $kuota['id'], $pdo);
            }
        }
    }

    public function decrementKuotaRange(
        string $kamarId,
        string $checkIn,
        string $checkOut,
        PDO $pdo,
    ): void {
        foreach ($this->kuotaRepo->datesInRange($checkIn, $checkOut) as $tanggal) {
            $kuota = $this->kuotaRepo->findByKamarAndDate($kamarId, $tanggal);

            if ($kuota) {
                $this->kuotaRepo->decrementTerisi((string) $kuota['id'], $pdo);
            }
        }
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, errors?: array<string, string>, data?: array<string, mixed>}
     */
    private function buildPricing(string $pelangganId, array $input): array
    {
        $paketId = trim((string) ($input['paket_penitipan_id'] ?? ''));
        $kucingId = trim((string) ($input['kucing_id'] ?? ''));
        $checkIn = trim((string) ($input['check_in'] ?? ''));
        $checkOut = trim((string) ($input['check_out'] ?? ''));
        $opsiPengantaran = trim((string) ($input['opsi_pengantaran'] ?? OpsiPengantaran::ANTAR_SENDIRI->value));
        $catatanMakan = trim((string) ($input['catatan_makan'] ?? '')) ?: null;
        $errors = [];

        if ($paketId === '') {
            $errors['paket_penitipan_id'] = 'Pilih paket penitipan.';
        }

        if ($kucingId === '') {
            $errors['kucing_id'] = 'Pilih kucing.';
        }

        if ($checkIn === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkIn)) {
            $errors['check_in'] = 'Tanggal check-in tidak valid.';
        }

        if ($checkOut === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $checkOut)) {
            $errors['check_out'] = 'Tanggal check-out tidak valid.';
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        if ($checkOut <= $checkIn) {
            return ['success' => false, 'errors' => ['check_out' => 'Check-out harus setelah check-in.']];
        }

        if ($checkIn < date('Y-m-d')) {
            return ['success' => false, 'errors' => ['check_in' => 'Check-in tidak boleh di masa lalu.']];
        }

        $kucing = $this->kucingRepo->findByIdAndPelanggan($kucingId, $pelangganId);

        if (!$kucing) {
            return ['success' => false, 'errors' => ['kucing_id' => 'Kucing tidak ditemukan.']];
        }

        if (!$this->kucingService->isEligiblePetHotel($kucingId)) {
            return [
                'success' => false,
                'errors' => [
                    'kucing_id' => 'Lengkapi riwayat vaksin (jenis & tanggal) di menu Kucing Saya terlebih dahulu.',
                ],
            ];
        }

        $paket = $this->paketRepo->findActiveById($paketId);

        if (!$paket) {
            return ['success' => false, 'errors' => ['paket_penitipan_id' => 'Paket penitipan tidak tersedia.']];
        }

        $pelanggan = $this->pelangganRepo->findById($pelangganId);

        if (!$pelanggan) {
            return ['success' => false, 'errors' => ['general' => 'Data pelanggan tidak ditemukan.']];
        }

        if (!in_array($opsiPengantaran, [
            OpsiPengantaran::ANTAR_JEMPUT->value,
            OpsiPengantaran::ANTAR_SENDIRI->value,
        ], true)) {
            return ['success' => false, 'errors' => ['opsi_pengantaran' => 'Opsi pengantaran tidak valid.']];
        }

        if ($opsiPengantaran === OpsiPengantaran::ANTAR_JEMPUT->value
            && !$this->pickupService->estimasiUntukPelanggan($pelanggan)['success']) {
            return [
                'success' => false,
                'errors' => ['opsi_pengantaran' => 'Alamat profil belum lengkap untuk antar-jemput.'],
            ];
        }

        try {
            $pickup = $this->pickupService->hitungUntukBooking($opsiPengantaran, $pelanggan);
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'errors' => ['opsi_pengantaran' => $e->getMessage()]];
        }

        $lamaHari = count($this->kuotaRepo->datesInRange($checkIn, $checkOut));
        $hargaPerHari = (float) $paket['harga_per_hari'];
        $subtotal = round($hargaPerHari * $lamaHari, 2);
        $promo = $this->promoService->hitung($pelanggan, $lamaHari, $subtotal);
        $biayaAntar = $pickup['biaya_antar_jemput'];
        $totalBayar = round($subtotal - $promo['potongan_promo'] + $biayaAntar, 2);

        return [
            'success' => true,
            'data' => [
                'paket_id' => $paketId,
                'kucing_id' => $kucingId,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'lama_hari' => $lamaHari,
                'harga_per_hari' => $hargaPerHari,
                'subtotal' => $subtotal,
                'promo_dipakai' => $promo['promo_dipakai'],
                'potongan_promo' => $promo['potongan_promo'],
                'promo_eligible' => $promo['eligible'],
                'opsi_pengantaran' => $opsiPengantaran,
                'jarak_km' => $pickup['jarak_km'],
                'biaya_antar_jemput' => $biayaAntar,
                'total_bayar' => $totalBayar,
                'catatan_makan' => $catatanMakan,
            ],
        ];
    }

    private function findAndLockAvailableKamar(string $checkIn, string $checkOut, PDO $pdo): ?string
    {
        foreach ($this->kamarRepo->findAllActive() as $kamar) {
            $kamarId = (string) $kamar['id'];
            $available = true;

            foreach ($this->kuotaRepo->datesInRange($checkIn, $checkOut) as $tanggal) {
                $kuota = $this->kuotaRepo->findByKamarAndDateForUpdate($kamarId, $tanggal, $pdo);

                if (!$kuota || (int) $kuota['slot_terisi'] >= (int) $kuota['slot_maksimal']) {
                    $available = false;
                    break;
                }
            }

            if ($available) {
                return $kamarId;
            }
        }

        return null;
    }

    /** @param array<string, mixed> $booking */
    private function cancelBooking(array $booking): array
    {
        $transaksi = $this->transaksiRepo->findByPenitipanBooking((string) $booking['id']);
        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();
            $this->bookingRepo->cancel((string) $booking['id'], $pdo);
            $this->decrementKuotaRange(
                (string) $booking['kamar_penitipan_id'],
                (string) $booking['check_in'],
                (string) $booking['check_out'],
                $pdo,
            );

            if ($transaksi) {
                $this->transaksiRepo->updateStatusPembayaran(
                    (string) $transaksi['id'],
                    StatusPembayaran::DIBATALKAN->value,
                    $pdo,
                );
            }

            $pdo->commit();

            return ['success' => true];
        } catch (\Throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'error' => 'Gagal membatalkan booking.'];
        }
    }
}
