<?php

declare(strict_types=1);

use App\Enums\OpsiPengantaran;

$booking = $booking ?? [];
$transaksi = $transaksi ?? [];
$invoice = $invoice ?? [];
$opsiLabels = $opsiLabels ?? [];
?>
<div class="max-w-lg mx-auto">
    <div class="<?= e(design_cn(design_surface('metric'), 'p-8 print:shadow-none print:border-0')) ?>" id="invoice-print">
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-foreground">INVOICE</h1>
            <p class="text-sm text-muted-foreground"><?= e((string) $invoice['nomor_invoice']) ?></p>
            <p class="text-xs text-muted-foreground/70 mt-1"><?= e(date('d/m/Y H:i', strtotime((string) $invoice['issued_at']))) ?></p>
        </div>

        <div class="space-y-3 text-sm mb-6">
            <div class="flex justify-between">
                <span class="text-muted-foreground">Pelanggan</span>
                <span class="text-foreground"><?= e((string) $booking['pelanggan_nama']) ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">Layanan</span>
                <span class="text-foreground"><?= e((string) $booking['jenis_nama']) ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">Kucing</span>
                <span class="text-foreground"><?= e((string) $booking['kucing_nama']) ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground">Tanggal</span>
                <span class="text-foreground"><?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></span>
            </div>
            <?php if (!empty($booking['jam_grooming'])): ?>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Jam</span>
                    <span class="text-foreground"><?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?> WIB</span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between">
                <span class="text-muted-foreground">Pengantaran</span>
                <span class="text-foreground"><?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?></span>
            </div>
        </div>

        <div class="border-t pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-muted-foreground">
                <span>Subtotal layanan</span>
                <span>Rp <?= e(number_format((float) $transaksi['subtotal_layanan'], 0, ',', '.')) ?></span>
            </div>
            <div class="flex justify-between text-muted-foreground">
                <span>Biaya antar-jemput</span>
                <span>Rp <?= e(number_format((float) $transaksi['biaya_antar_jemput'], 0, ',', '.')) ?></span>
            </div>
            <div class="flex justify-between font-bold text-foreground pt-2 border-t">
                <span>Total</span>
                <span>Rp <?= e(number_format((float) $transaksi['total_bayar'], 0, ',', '.')) ?></span>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-green-700 font-medium">LUNAS</div>
    </div>

    <div class="flex gap-3 mt-6 print:hidden">
        <button onclick="window.print()"
                class="<?= e(design_cn(ui_btn_primary(), 'flex-1')) ?>">
            Cetak / Simpan PDF
        </button>
        <a href="/grooming/detail?id=<?= e((string) $booking['id']) ?>"
           class="<?= e(design_cn(ui_btn_secondary(), 'flex-1 text-center')) ?>">
            Kembali
        </a>
    </div>
</div>
