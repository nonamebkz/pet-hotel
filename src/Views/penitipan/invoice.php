<?php

declare(strict_types=1);

$booking = $booking ?? [];
$transaksi = $transaksi ?? [];
$invoice = $invoice ?? [];
$opsiLabels = $opsiLabels ?? [];
?>
<div>
    <div class="mb-6">
        <a href="/penitipan/detail?id=<?= e((string) $booking['id']) ?>" class="text-sm text-gray-500 hover:text-orange-600">&larr; Detail</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Invoice Penitipan</h1>
    </div>

    <div class="bg-white rounded-xl border p-8 max-w-lg mx-auto">
        <div class="text-center mb-6">
            <div class="text-sm text-gray-500">Petshop</div>
            <div class="font-bold text-lg text-gray-800">INVOICE</div>
            <div class="text-sm text-gray-600"><?= e((string) $invoice['nomor_invoice']) ?></div>
            <div class="text-xs text-gray-500 mt-1">
                <?= e(date('d/m/Y H:i', strtotime((string) $invoice['issued_at']))) ?>
            </div>
        </div>

        <div class="text-sm space-y-2 text-gray-600 mb-6">
            <div>Kucing: <?= e((string) $booking['kucing_nama']) ?></div>
            <div>Paket: <?= e((string) $booking['paket_nama']) ?></div>
            <div>
                Periode: <?= e(date('d/m/Y', strtotime((string) $booking['check_in']))) ?>
                — <?= e(date('d/m/Y', strtotime((string) $booking['check_out']))) ?>
            </div>
            <div>Pengantaran: <?= e($opsiLabels[$booking['opsi_pengantaran']] ?? '') ?></div>
        </div>

        <div class="border-t pt-4 text-sm space-y-2">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>Rp <?= e(number_format((float) $transaksi['subtotal_layanan'], 0, ',', '.')) ?></span>
            </div>
            <?php if ((float) $transaksi['potongan_promo'] > 0): ?>
                <div class="flex justify-between text-green-700">
                    <span>Potongan promo</span>
                    <span>- Rp <?= e(number_format((float) $transaksi['potongan_promo'], 0, ',', '.')) ?></span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between">
                <span>Antar-jemput</span>
                <span>Rp <?= e(number_format((float) $transaksi['biaya_antar_jemput'], 0, ',', '.')) ?></span>
            </div>
            <div class="flex justify-between font-bold text-gray-800 pt-2 border-t">
                <span>Total Lunas</span>
                <span>Rp <?= e(number_format((float) $transaksi['total_bayar'], 0, ',', '.')) ?></span>
            </div>
        </div>
    </div>
</div>
