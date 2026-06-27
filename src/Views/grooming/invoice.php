<?php

declare(strict_types=1);

use App\Enums\OpsiPengantaran;

$booking = $booking ?? [];
$transaksi = $transaksi ?? [];
$invoice = $invoice ?? [];
$opsiLabels = $opsiLabels ?? [];
?>
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl border p-8 print:shadow-none print:border-0" id="invoice-print">
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-gray-800">INVOICE</h1>
            <p class="text-sm text-gray-500"><?= e((string) $invoice['nomor_invoice']) ?></p>
            <p class="text-xs text-gray-400 mt-1"><?= e(date('d/m/Y H:i', strtotime((string) $invoice['issued_at']))) ?></p>
        </div>

        <div class="space-y-3 text-sm mb-6">
            <div class="flex justify-between">
                <span class="text-gray-500">Pelanggan</span>
                <span class="text-gray-800"><?= e((string) $booking['pelanggan_nama']) ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Layanan</span>
                <span class="text-gray-800"><?= e((string) $booking['jenis_nama']) ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Kucing</span>
                <span class="text-gray-800"><?= e((string) $booking['kucing_nama']) ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span class="text-gray-800"><?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></span>
            </div>
            <?php if (!empty($booking['jam_grooming'])): ?>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jam</span>
                    <span class="text-gray-800"><?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?> WIB</span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between">
                <span class="text-gray-500">Pengantaran</span>
                <span class="text-gray-800"><?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?></span>
            </div>
        </div>

        <div class="border-t pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal layanan</span>
                <span>Rp <?= e(number_format((float) $transaksi['subtotal_layanan'], 0, ',', '.')) ?></span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Biaya antar-jemput</span>
                <span>Rp <?= e(number_format((float) $transaksi['biaya_antar_jemput'], 0, ',', '.')) ?></span>
            </div>
            <div class="flex justify-between font-bold text-gray-800 pt-2 border-t">
                <span>Total</span>
                <span>Rp <?= e(number_format((float) $transaksi['total_bayar'], 0, ',', '.')) ?></span>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-green-700 font-medium">LUNAS</div>
    </div>

    <div class="flex gap-3 mt-6 print:hidden">
        <button onclick="window.print()"
                class="flex-1 bg-orange-600 text-white rounded-lg py-2 text-sm font-medium hover:bg-orange-700">
            Cetak / Simpan PDF
        </button>
        <a href="/grooming/detail?id=<?= e((string) $booking['id']) ?>"
           class="flex-1 text-center border border-gray-300 rounded-lg py-2 text-sm hover:bg-gray-50">
            Kembali
        </a>
    </div>
</div>
