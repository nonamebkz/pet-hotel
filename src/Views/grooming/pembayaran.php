<?php

declare(strict_types=1);

use App\Core\Csrf;

$booking = $booking ?? [];
$transaksi = $transaksi ?? [];
$bukti = $bukti ?? null;
$bankConfig = $bankConfig ?? [];
$errors = $errors ?? [];
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <p>
        <a href="/grooming/detail?id=<?= e((string) $booking['id']) ?>" class="<?= e(ui_back_link_class()) ?>">&larr; Detail Booking</a>
    </p>
    <?php
    ui_page_header(
        'Pembayaran Grooming',
        'Grooming',
        'Transfer sesuai nominal lalu upload bukti untuk verifikasi.',
    );
    ?>

    <div class="grid gap-6 lg:grid-cols-2 max-w-4xl">
        <div class="<?= e(design_cn(design_surface('panel'), 'p-6 space-y-4')) ?>">
            <h2 class="font-semibold text-foreground">Rekening Tujuan</h2>
            <div class="text-sm text-muted-foreground space-y-1">
                <div>Bank: <strong><?= e((string) ($bankConfig['bank_name'] ?? '')) ?></strong></div>
                <div>No. Rekening: <strong><?= e((string) ($bankConfig['bank_account_number'] ?? '')) ?></strong></div>
                <div>Atas Nama: <strong><?= e((string) ($bankConfig['bank_account_name'] ?? '')) ?></strong></div>
            </div>

            <div class="bg-muted/50 rounded-lg p-4 text-sm">
                <div class="font-medium text-foreground mb-2">Total yang harus ditransfer</div>
                <div class="text-2xl font-bold text-primary">
                    Rp <?= e(number_format((float) $transaksi['total_bayar'], 0, ',', '.')) ?>
                </div>
                <?php if (!empty($transaksi['batas_waktu_bayar'])): ?>
                    <p class="<?= e(design_cn(design_alert_inline('warning'), 'mt-2')) ?>">
                        Batas waktu: <?= e(date('d/m/Y H:i', strtotime((string) $transaksi['batas_waktu_bayar']))) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="<?= e(design_cn(design_surface('panel'), 'p-6')) ?>">
            <h2 class="font-semibold text-foreground mb-4">Upload Bukti Transfer</h2>

            <?php if ($bukti && (string) $bukti['status_verifikasi'] === 'DITOLAK'): ?>
                <div class="<?= e(design_cn(design_advice_panel_surface('danger'), design_surface('panel'), 'mb-4 p-3 text-sm text-destructive')) ?>">
                    Bukti sebelumnya ditolak. Upload bukti baru.
                </div>
            <?php endif; ?>

            <form method="POST" action="/grooming/pembayaran" enctype="multipart/form-data" class="space-y-4">
                <?= Csrf::field() ?>
                <input type="hidden" name="transaksi_id" value="<?= e((string) $transaksi['id']) ?>">
                <input type="hidden" name="booking_id" value="<?= e((string) $booking['id']) ?>">

                <?php if (!empty($errors['general'])): ?>
                    <p class="text-sm text-destructive"><?= e((string) $errors['general']) ?></p>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">
                        Bukti Transfer <span class="text-destructive">*</span>
                    </label>
                    <input type="file" name="bukti" accept="image/jpeg,image/png,image/webp,application/pdf"
                           class="<?= e(ui_form_input_class(!empty($errors['bukti']))) ?> text-sm py-2" required>
                    <p class="text-xs text-muted-foreground mt-1">JPG, PNG, WebP, atau PDF. Maks. 2 MB.</p>
                    <?php if (!empty($errors['bukti'])): ?>
                        <p class="text-xs text-destructive mt-1"><?= e((string) $errors['bukti']) ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit"
                        class="<?= e(design_cn(ui_btn_primary(), 'w-full')) ?>">
                    Kirim Bukti Transfer
                </button>
            </form>
        </div>
    </div>
</div>
