<?php

declare(strict_types=1);

/**
 * CLI: Kirim pengingat pembayaran sebelum batas_waktu_bayar.
 * Usage: php scripts/send-payment-reminders.php
 */

require __DIR__ . '/../vendor/autoload.php';

define('BASE_PATH', dirname(__DIR__));

const REMINDER_HOURS_BEFORE = 6;

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

use App\Enums\JenisNotifikasi;
use App\Enums\TipePenerima;
use App\Repositories\TransaksiRepository;
use App\Services\NotifikasiService;

$transaksiRepo = new TransaksiRepository();
$notifikasiService = new NotifikasiService();

$pending = $transaksiRepo->findPendingPaymentReminders(REMINDER_HOURS_BEFORE);
$count = 0;

foreach ($pending as $transaksi) {
    $transaksiId = (string) $transaksi['id'];
    $pelangganId = (string) $transaksi['pelanggan_id'];

    if ($notifikasiService->hasNotifikasiForReferensi(
        $pelangganId,
        TipePenerima::PELANGGAN,
        JenisNotifikasi::REMINDER_PEMBAYARAN,
        $transaksiId,
    )) {
        continue;
    }

    $batasWaktu = date('d/m/Y H:i', strtotime((string) $transaksi['batas_waktu_bayar']));
    $total = number_format((float) $transaksi['total_bayar'], 0, ',', '.');

    $notifikasiService->notifyPelanggan(
        $pelangganId,
        JenisNotifikasi::REMINDER_PEMBAYARAN,
        'Pengingat pembayaran',
        "Sisa waktu bayar kurang dari " . REMINDER_HOURS_BEFORE . " jam. Total Rp {$total}, batas {$batasWaktu} WIB.",
        $transaksiId,
        'transaksi',
    );

    $count++;
}

echo "Sent {$count} payment reminder(s)." . PHP_EOL;
