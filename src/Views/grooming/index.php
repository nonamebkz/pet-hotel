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
        <a href="/grooming/booking"
           class="bg-orange-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-orange-700">
            Ajukan Booking
        </a>
    </div>

    <?php if ($jenisList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">
            Belum ada jenis grooming tersedia.
        </div>
    <?php else: ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($jenisList as $jenis): ?>
                <div class="bg-white rounded-xl border p-5 flex flex-col">
                    <h2 class="font-semibold text-gray-800 mb-2"><?= e((string) $jenis['nama']) ?></h2>
                    <?php if (!empty($jenis['deskripsi'])): ?>
                        <p class="text-sm text-gray-600 mb-3 flex-1"><?= e((string) $jenis['deskripsi']) ?></p>
                    <?php endif; ?>
                    <div class="text-sm text-gray-500 pt-3 border-t">
                        Harga: <strong class="text-gray-800">Rp <?= e(number_format((float) $jenis['harga'], 0, ',', '.')) ?></strong>
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
