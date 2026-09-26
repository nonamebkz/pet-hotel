<?php

declare(strict_types=1);

$paketList = $paketList ?? [];
$promoEligible = $promoEligible ?? false;
$promoConfig = $promoConfig ?? [];
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Pet Hotel / Penitipan',
        'Booking',
        'Titipkan kucing Anda dengan aman dan nyaman.',
    );
    ?>

    <?php if ($promoEligible): ?>
        <aside class="<?= e(design_cn(design_advice_panel_surface('warning'), 'border-emerald-500/30 bg-emerald-500/5 rounded-2xl p-4 text-sm text-emerald-900 dark:text-emerald-100')) ?>">
            <span class="font-semibold">Promo penitipan aktif!</span>
            Potongan <?= (int) ($promoConfig['promo_discount_percent'] ?? 10) ?>%
            untuk durasi lebih dari <?= (int) ($promoConfig['promo_min_days'] ?? 7) ?> hari (1× per akun).
        </aside>
    <?php endif; ?>

    <div class="grid gap-4 sm:grid-cols-2">
        <?php foreach ($paketList as $paket): ?>
            <div class="<?= e(design_cn(design_surface('metric'), 'p-5')) ?>">
                <div class="font-semibold text-lg text-foreground"><?= e((string) $paket['nama']) ?></div>
                <div class="text-primary font-medium mt-1 tabular-nums">
                    Rp <?= e(number_format((float) $paket['harga_per_hari'], 0, ',', '.')) ?> / hari
                </div>
                <?php if (!empty($paket['deskripsi'])): ?>
                    <p class="text-sm text-muted-foreground mt-2"><?= e((string) $paket['deskripsi']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <section class="<?= e(design_cn(design_surface('metric'), 'p-6 max-w-xl')) ?>">
        <h2 class="font-semibold text-foreground mb-2">Syarat Pet Hotel</h2>
        <ul class="text-sm text-muted-foreground list-disc list-inside space-y-1 mb-4">
            <li>Minimal 1 riwayat vaksin lengkap (jenis & tanggal) per kucing</li>
            <li>Sertifikat vaksin opsional</li>
            <li>Kucing harus terdaftar di menu Kucing Saya</li>
        </ul>
        <a href="/penitipan/booking" class="<?= e(ui_btn_primary()) ?>">
            Ajukan Penitipan
        </a>
    </section>
</div>
