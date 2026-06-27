<?php

declare(strict_types=1);

$paketList = $paketList ?? [];
$promoEligible = $promoEligible ?? false;
$promoConfig = $promoConfig ?? [];
?>
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pet Hotel / Penitipan Kucing</h1>
        <p class="text-sm text-gray-500 mt-1">Titipkan kucing Anda dengan aman dan nyaman.</p>
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
            <div class="bg-white rounded-xl border p-5">
                <div class="font-semibold text-lg text-gray-800"><?= e((string) $paket['nama']) ?></div>
                <div class="text-orange-600 font-medium mt-1">
                    Rp <?= e(number_format((float) $paket['harga_per_hari'], 0, ',', '.')) ?> / hari
                </div>
                <?php if (!empty($paket['deskripsi'])): ?>
                    <p class="text-sm text-gray-500 mt-2"><?= e((string) $paket['deskripsi']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-xl border p-6 max-w-xl">
        <h2 class="font-semibold text-gray-800 mb-2">Syarat Pet Hotel</h2>
        <ul class="text-sm text-gray-600 list-disc list-inside space-y-1 mb-4">
            <li>Minimal 1 riwayat vaksin lengkap (jenis & tanggal) per kucing</li>
            <li>Sertifikat vaksin opsional</li>
            <li>Kucing harus terdaftar di menu "Kucing Saya"</li>
        </ul>
        <a href="/penitipan/booking"
           class="inline-block bg-orange-600 text-white rounded-lg px-5 py-2.5 text-sm font-medium hover:bg-orange-700">
            Ajukan Penitipan
        </a>
    </div>
</div>
