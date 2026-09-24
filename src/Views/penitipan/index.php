<?php

declare(strict_types=1);

$paketList = $paketList ?? [];
$promoEligible = $promoEligible ?? false;
$promoConfig = $promoConfig ?? [];
?>
<div>
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-foreground">Pet Hotel / Penitipan Kucing</h1>
        <p class="text-sm text-muted-foreground mt-1">Titipkan kucing Anda dengan aman dan nyaman.</p>
    </div>

    <?php if ($promoEligible): ?>
        <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800">
            Promo penitipan aktif! Potongan <?= (int) ($promoConfig['promo_discount_percent'] ?? 10) ?>%
            untuk durasi lebih dari <?= (int) ($promoConfig['promo_min_days'] ?? 7) ?> hari
            (1× per akun).
        </div>
    <?php endif; ?>

    <div class="grid gap-4 sm:grid-cols-2 mb-8">
        <?php foreach ($paketList as $paket): ?>
            <div class="<?= e(design_cn(design_surface('metric'), 'p-5')) ?>">
                <div class="font-semibold text-lg text-foreground"><?= e((string) $paket['nama']) ?></div>
                <div class="text-primary font-medium mt-1">
                    Rp <?= e(number_format((float) $paket['harga_per_hari'], 0, ',', '.')) ?> / hari
                </div>
                <?php if (!empty($paket['deskripsi'])): ?>
                    <p class="text-sm text-muted-foreground mt-2"><?= e((string) $paket['deskripsi']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="<?= e(design_cn(design_surface('metric'), 'p-6 max-w-xl')) ?>">
        <h2 class="font-semibold text-foreground mb-2">Syarat Pet Hotel</h2>
        <ul class="text-sm text-muted-foreground list-disc list-inside space-y-1 mb-4">
            <li>Minimal 1 riwayat vaksin lengkap (jenis & tanggal) per kucing</li>
            <li>Sertifikat vaksin opsional</li>
            <li>Kucing harus terdaftar di menu "Kucing Saya"</li>
        </ul>
        <a href="/penitipan/booking"
           class="inline-block bg-primary text-primary-foreground rounded-lg px-5 py-2.5 text-sm font-medium hover:opacity-90">
            Ajukan Penitipan
        </a>
    </div>
</div>
