<?php

declare(strict_types=1);

$bookingList = $bookingList ?? [];
?>
<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Penitipan</h1>
            <p class="text-sm text-gray-500 mt-1">Semua booking pet hotel Anda.</p>
        </div>
        <a href="/penitipan/booking"
           class="bg-orange-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-orange-700">
            + Ajukan Penitipan
        </a>
    </div>

    <?php if ($bookingList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-500">
            Belum ada riwayat penitipan.
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($bookingList as $booking): ?>
                <a href="/penitipan/detail?id=<?= e((string) $booking['id']) ?>"
                   class="block bg-white rounded-xl border p-4 hover:border-orange-300 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-medium text-gray-800"><?= e((string) $booking['kucing_nama']) ?></div>
                            <div class="text-sm text-gray-500">
                                <?= e((string) $booking['paket_nama']) ?> · <?= e((string) $booking['nama_kamar']) ?>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                <?= e(date('d/m/Y', strtotime((string) $booking['check_in']))) ?>
                                — <?= e(date('d/m/Y', strtotime((string) $booking['check_out']))) ?>
                                (<?= (int) $booking['lama_hari'] ?> hari)
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700 shrink-0">
                            <?= e((string) ($booking['status_label'] ?? $booking['status'])) ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
