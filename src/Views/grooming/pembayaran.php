<?php

declare(strict_types=1);

use App\Core\Csrf;

$booking = $booking ?? [];
$transaksi = $transaksi ?? [];
$bukti = $bukti ?? null;
$bankConfig = $bankConfig ?? [];
$errors = $errors ?? [];
?>
<div>
    <div class="mb-6">
        <a href="/grooming/detail?id=<?= e((string) $booking['id']) ?>" class="<?= e(ui_back_link_class()) ?>">&larr; Detail Booking</a>
        <h1 class="text-2xl font-bold text-foreground mt-2">Pembayaran Grooming</h1>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 max-w-4xl">
        <div class="<?= e(design_cn(design_surface('metric'), 'p-6 space-y-4')) ?>">
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
                    <div class="text-xs text-amber-700 mt-2">
                        Batas waktu: <?= e(date('d/m/Y H:i', strtotime((string) $transaksi['batas_waktu_bayar']))) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="<?= e(design_cn(design_surface('metric'), 'p-6')) ?>">
            <h2 class="font-semibold text-foreground mb-4">Upload Bukti Transfer</h2>

            <?php if ($bukti && (string) $bukti['status_verifikasi'] === 'DITOLAK'): ?>
                <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded-lg text-sm text-red-800">
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
                           class="w-full text-sm border border-border rounded-lg px-3 py-2" required>
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
