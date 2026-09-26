<?php

declare(strict_types=1);

$booking = $booking ?? [];
$transaksi = $transaksi ?? [];
$invoice = $invoice ?? [];
$opsiLabels = $opsiLabels ?? [];
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <p>
        <a href="/penitipan/detail?id=<?= e((string) $booking['id']) ?>" class="<?= e(ui_back_link_class()) ?>">&larr; Detail</a>
    </p>
    <?php
    ui_page_header(
        'Invoice Penitipan',
        'Penitipan',
        (string) ($invoice['nomor_invoice'] ?? ''),
    );
    ?>

    <div class="<?= e(design_cn(design_surface('panel'), 'p-8 max-w-lg mx-auto')) ?>">
        <div class="text-center mb-6">
            <div class="text-sm text-muted-foreground">Petshop</div>
            <div class="font-bold text-lg text-foreground">INVOICE</div>
            <div class="text-sm text-muted-foreground"><?= e((string) $invoice['nomor_invoice']) ?></div>
            <div class="text-xs text-muted-foreground mt-1">
                <?= e(date('d/m/Y H:i', strtotime((string) $invoice['issued_at']))) ?>
            </div>
        </div>

        <div class="text-sm space-y-2 text-muted-foreground mb-6">
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
                <div class="flex justify-between text-emerald-700 dark:text-emerald-300">
                    <span>Potongan promo</span>
                    <span>- Rp <?= e(number_format((float) $transaksi['potongan_promo'], 0, ',', '.')) ?></span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between">
                <span>Antar-jemput</span>
                <span>Rp <?= e(number_format((float) $transaksi['biaya_antar_jemput'], 0, ',', '.')) ?></span>
            </div>
            <div class="flex justify-between font-bold text-foreground pt-2 border-t">
                <span>Total Lunas</span>
                <span>Rp <?= e(number_format((float) $transaksi['total_bayar'], 0, ',', '.')) ?></span>
            </div>
        </div>
    </div>
</div>
