<?php

declare(strict_types=1);

$jenisList = $jenisList ?? [];
$pickupSettings = $pickupSettings ?? [];
$freeRadiusKm = (float) ($pickupSettings['pickup_free_radius_km'] ?? 3);
$feePerKm = (int) ($pickupSettings['pickup_extra_fee_per_km'] ?? 5000);
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Grooming</h1>
            <p class="text-sm text-gray-500 mt-1">Layanan perawatan kucing — booking online dengan opsi antar-jemput.</p>
        </div>
    </div>

    <?php if ($jenisList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada jenis grooming tersedia';
        $description = 'Hubungi petshop jika layanan grooming belum tampil.';
        $ctaLabel = null;
        $ctaHref = null;
        $ctaClass = 'bg-orange-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-orange-700 inline-block';
        require __DIR__ . '/../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($jenisList as $index => $jenis): ?>
                <?php $isRecommended = $index === 0; ?>
                <div class="bg-white rounded-xl border p-5 flex flex-col relative <?= $isRecommended ? 'ring-2 ring-orange-200 border-orange-200' : '' ?>">
                    <?php if ($isRecommended): ?>
                        <span class="absolute -top-2.5 left-4 text-xs font-semibold bg-orange-600 text-white px-2 py-0.5 rounded-full">
                            Rekomendasi
                        </span>
                    <?php endif; ?>
                    <h2 class="font-semibold text-gray-800 mb-2 mt-1"><?= e((string) $jenis['nama']) ?></h2>
                    <?php if (!empty($jenis['deskripsi'])): ?>
                        <p class="text-sm text-gray-600 mb-3 flex-1 line-clamp-3"><?= e((string) $jenis['deskripsi']) ?></p>
                    <?php else: ?>
                        <div class="flex-1"></div>
                    <?php endif; ?>
                    <div class="text-sm text-gray-500 pt-3 border-t mb-4">
                        Harga: <strong class="text-gray-800">Rp <?= e(number_format((float) $jenis['harga'], 0, ',', '.')) ?></strong>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="/grooming/booking?jenis_grooming_id=<?= e(urlencode((string) $jenis['id'])) ?>"
                           class="flex-1 text-center bg-orange-600 text-white rounded-lg px-3 py-2 text-sm font-medium hover:bg-orange-700">
                            Ajukan
                        </a>
                        <a href="/grooming/booking?jenis_grooming_id=<?= e(urlencode((string) $jenis['id'])) ?>#detail"
                           class="flex-1 text-center border border-gray-300 text-gray-700 rounded-lg px-3 py-2 text-sm font-medium hover:bg-gray-50">
                            Detail
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-6 bg-orange-50 border border-orange-100 rounded-xl p-4 text-sm text-orange-900">
            <strong>Antar-jemput:</strong> Gratis jika jarak ≤ <?= e(number_format($freeRadiusKm, 1, ',', '.')) ?> km dari petshop.
            Di atas <?= e(number_format($freeRadiusKm, 1, ',', '.')) ?> km dikenakan biaya
            Rp <?= e(number_format($feePerKm, 0, ',', '.')) ?> per km tambahan.
        </div>
    <?php endif; ?>
</div>
