<?php

declare(strict_types=1);

use App\Core\Csrf;

$booking = $booking ?? [];
$transaksi = $transaksi ?? [];
$bukti = $bukti ?? null;
$perpanjangan = $perpanjangan ?? null;
$bankConfig = $bankConfig ?? [];
$errors = $errors ?? [];
$isPerpanjangan = $isPerpanjangan ?? false;
?>
<div>
    <div class="mb-6">
        <?php if ($isPerpanjangan && $perpanjangan): ?>
            <a href="/penitipan/detail?id=<?= e((string) ($booking['id'] ?? '')) ?>" class="text-sm text-gray-500 hover:text-orange-600">&larr; Detail Booking</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Pembayaran Perpanjangan</h1>
        <?php else: ?>
            <a href="/penitipan/detail?id=<?= e((string) $booking['id']) ?>" class="text-sm text-gray-500 hover:text-orange-600">&larr; Detail Booking</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Pembayaran Penitipan</h1>
        <?php endif; ?>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 max-w-4xl">
        <div class="bg-white rounded-xl border p-6 space-y-4">
            <h2 class="font-semibold text-gray-800">Rekening Tujuan</h2>
            <div class="text-sm text-gray-600 space-y-1">
                <div>Bank: <strong><?= e((string) ($bankConfig['bank_name'] ?? '')) ?></strong></div>
                <div>No. Rekening: <strong><?= e((string) ($bankConfig['bank_account_number'] ?? '')) ?></strong></div>
                <div>Atas Nama: <strong><?= e((string) ($bankConfig['bank_account_name'] ?? '')) ?></strong></div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-sm">
                <div class="font-medium text-gray-800 mb-2">Total transfer</div>
                <div class="text-2xl font-bold text-orange-600">
                    Rp <?= e(number_format((float) ($transaksi['total_bayar'] ?? 0), 0, ',', '.')) ?>
                </div>
                <?php if (!empty($transaksi['batas_waktu_bayar'])): ?>
                    <div class="text-xs text-amber-700 mt-2">
                        Batas waktu: <?= e(date('d/m/Y H:i', strtotime((string) $transaksi['batas_waktu_bayar']))) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Upload Bukti Transfer</h2>
            <form method="POST"
                  action="<?= $isPerpanjangan ? '/penitipan/perpanjangan/pembayaran' : '/penitipan/pembayaran' ?>"
                  enctype="multipart/form-data" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="transaksi_id" value="<?= e((string) ($transaksi['id'] ?? '')) ?>">
                <input type="hidden" name="booking_id" value="<?= e((string) ($booking['id'] ?? '')) ?>">
                <?php if ($isPerpanjangan && $perpanjangan): ?>
                    <input type="hidden" name="perpanjangan_id" value="<?= e((string) $perpanjangan['id']) ?>">
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer *</label>
                    <input type="file" name="bukti" accept="image/jpeg,image/png,image/webp,application/pdf"
                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2" required>
                    <?php if (!empty($errors['bukti'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['bukti']) ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit"
                        class="w-full bg-orange-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-orange-700">
                    Kirim Bukti Transfer
                </button>
            </form>
        </div>
    </div>
</div>
