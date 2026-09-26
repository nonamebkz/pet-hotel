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
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <p>
        <a href="/penitipan/detail?id=<?= e((string) ($booking['id'] ?? '')) ?>" class="<?= e(ui_back_link_class()) ?>">&larr; Detail Booking</a>
    </p>
    <?php
    ui_page_header(
        $isPerpanjangan ? 'Pembayaran Perpanjangan' : 'Pembayaran Penitipan',
        'Penitipan',
        'Transfer sesuai nominal lalu upload bukti untuk verifikasi.',
    );
    ?>

    <div class="grid gap-6 lg:grid-cols-2 max-w-4xl">
        <div class="<?= e(design_cn(design_surface('panel'), 'p-6 space-y-4')) ?>">
            <h2 class="font-semibold text-foreground">Rekening Tujuan</h2>
            <div class="text-sm text-muted-foreground space-y-1">
                <div>Bank: <strong class="text-foreground"><?= e((string) ($bankConfig['bank_name'] ?? '')) ?></strong></div>
                <div>No. Rekening: <strong class="text-foreground"><?= e((string) ($bankConfig['bank_account_number'] ?? '')) ?></strong></div>
                <div>Atas Nama: <strong class="text-foreground"><?= e((string) ($bankConfig['bank_account_name'] ?? '')) ?></strong></div>
            </div>
            <div class="bg-muted/50 rounded-lg p-4 text-sm">
                <div class="font-medium text-foreground mb-2">Total transfer</div>
                <div class="text-2xl font-bold text-primary tabular-nums">
                    Rp <?= e(number_format((float) ($transaksi['total_bayar'] ?? 0), 0, ',', '.')) ?>
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
                    <label class="<?= e(ui_form_label_class()) ?>">Bukti Transfer <span class="text-destructive">*</span></label>
                    <input type="file" name="bukti" accept="image/jpeg,image/png,image/webp,application/pdf"
                           class="<?= e(ui_form_input_class(!empty($errors['bukti']))) ?> text-sm py-2" required>
                    <?php if (!empty($errors['bukti'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['bukti']) ?></p>
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
