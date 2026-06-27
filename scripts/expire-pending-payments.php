<?php

declare(strict_types=1);

/**
 * CLI: Batalkan transaksi lewat batas_waktu_bayar dan kembalikan kuota.
 * Usage: php scripts/expire-pending-payments.php
 */

require __DIR__ . '/../vendor/autoload.php';

define('BASE_PATH', dirname(__DIR__));

$dotenv = dirname(__DIR__) . '/.env';
if (is_file($dotenv)) {
    foreach (file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

use App\Core\Database;
use App\Enums\JenisLayanan;
use App\Enums\JenisNotifikasi;
use App\Enums\StatusBookingGrooming;
use App\Enums\StatusPenitipan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPerpanjanganPenitipan;
use App\Repositories\BookingGroomingRepository;
use App\Repositories\BookingPenitipanRepository;
use App\Repositories\KuotaGroomingRepository;
use App\Repositories\PerpanjanganPenitipanRepository;
use App\Repositories\TransaksiRepository;
use App\Services\PenitipanBookingService;
use App\Services\PerpanjanganPenitipanService;
use App\Services\NotifikasiService;

$pdo = Database::connection();
$transaksiRepo = new TransaksiRepository();
$groomingBookingRepo = new BookingGroomingRepository();
$penitipanBookingRepo = new BookingPenitipanRepository();
$perpanjanganRepo = new PerpanjanganPenitipanRepository();
$kuotaGroomingRepo = new KuotaGroomingRepository();
$penitipanBookingService = new PenitipanBookingService();
$perpanjanganService = new PerpanjanganPenitipanService();
$notifikasiService = new NotifikasiService();

$stmt = $pdo->query(
    "SELECT * FROM transaksi
     WHERE status_pembayaran = 'MENUNGGU_PEMBAYARAN'
       AND batas_waktu_bayar IS NOT NULL
       AND batas_waktu_bayar < NOW()"
);

$expired = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = 0;

foreach ($expired as $transaksi) {
    try {
        $pdo->beginTransaction();

        $jenis = (string) $transaksi['jenis_layanan'];

        if ($jenis === JenisLayanan::GROOMING->value) {
            $booking = $groomingBookingRepo->findById((string) $transaksi['booking_id']);

            if ($booking && (string) $booking['status'] === StatusBookingGrooming::MENUNGGU_PEMBAYARAN->value) {
                $groomingBookingRepo->cancel((string) $booking['id'], $pdo);
                $kuotaGroomingRepo->decrementTerisi((string) $booking['kuota_grooming_id'], $pdo);
            }
        } elseif ($jenis === JenisLayanan::PENITIPAN->value) {
            if (!empty($transaksi['perpanjangan_penitipan_id'])) {
                $perpanjanganId = (string) $transaksi['perpanjangan_penitipan_id'];
                $perpanjangan = $perpanjanganRepo->findById($perpanjanganId);

                if ($perpanjangan
                    && (string) $perpanjangan['status'] === StatusPerpanjanganPenitipan::MENUNGGU_PEMBAYARAN->value) {
                    $booking = $penitipanBookingRepo->findById((string) $transaksi['booking_id']);

                    if ($booking) {
                        $penitipanBookingService->decrementKuotaRange(
                            (string) $booking['kamar_penitipan_id'],
                            (string) $perpanjangan['check_out_sebelum'],
                            (string) $perpanjangan['check_out_baru'],
                            $pdo,
                        );
                    }

                    $perpanjanganRepo->cancel($perpanjanganId, $pdo);
                }
            } else {
                $booking = $penitipanBookingRepo->findById((string) $transaksi['booking_id']);

                if ($booking && (string) $booking['status'] === StatusPenitipan::MENUNGGU_PEMBAYARAN->value) {
                    $penitipanBookingRepo->cancel((string) $booking['id'], $pdo);
                    $penitipanBookingService->decrementKuotaRange(
                        (string) $booking['kamar_penitipan_id'],
                        (string) $booking['check_in'],
                        (string) $booking['check_out'],
                        $pdo,
                    );
                }
            }
        }

        $transaksiRepo->updateStatusPembayaran(
            (string) $transaksi['id'],
            StatusPembayaran::KEDALUWARSA->value,
            $pdo,
        );

        $pdo->commit();

        $notifikasiService->notifyPelanggan(
            (string) $transaksi['pelanggan_id'],
            JenisNotifikasi::PEMBAYARAN_JATUH_TEMPO,
            'Pembayaran kedaluwarsa',
            'Batas waktu pembayaran telah lewat. Booking atau permintaan perpanjangan dibatalkan otomatis.',
            (string) $transaksi['id'],
            'transaksi',
        );

        $count++;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    }
}

echo "Expired {$count} transaction(s)." . PHP_EOL;
