<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusBookingPetCare;

$bookingList = $bookingList ?? [];
$statusLabels = $statusLabels ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Pet Care</h1>
        <a href="/pet-care/booking"
           class="bg-orange-600 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-orange-700">
            + Booking Baru
        </a>
    </div>

    <?php if ($bookingList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center">
            <p class="text-gray-600 mb-4">Belum ada booking pet care.</p>
            <a href="/pet-care/booking" class="text-orange-600 hover:underline font-medium">Ajukan booking pertama</a>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($bookingList as $booking): ?>
                <?php
                $statusEnum = StatusBookingPetCare::tryFrom((string) $booking['status']);
                $canCancel = $statusEnum?->canCancel() ?? false;
                ?>
                <div class="bg-white rounded-xl border p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                        <div>
                            <div class="font-semibold text-gray-800"><?= e((string) $booking['layanan_nama']) ?></div>
                            <div class="text-sm text-gray-500">Kucing: <?= e((string) $booking['kucing_nama']) ?></div>
                        </div>
                        <?php if ($statusEnum): ?>
                            <span class="text-xs px-2 py-1 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                                <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                        <div>Tanggal: <?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></div>
                        <div>Slot: <?= e(substr((string) $booking['slot_waktu'], 0, 5)) ?> WIB</div>
                        <div>Estimasi: Rp <?= e(number_format((float) $booking['harga_layanan'], 0, ',', '.')) ?></div>
                        <div>Dibuat: <?= e(date('d/m/Y H:i', strtotime((string) $booking['created_at']))) ?></div>
                    </div>

                    <?php if (!empty($booking['catatan'])): ?>
                        <p class="text-sm text-gray-500 mb-3">Catatan: <?= e((string) $booking['catatan']) ?></p>
                    <?php endif; ?>

                    <?php if ($canCancel): ?>
                        <form method="POST" action="/pet-care/booking/batalkan" class="pt-3 border-t"
                              onsubmit="return confirm('Batalkan booking ini?')">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                            <button type="submit" class="text-sm text-red-600 hover:underline">
                                Batalkan Booking
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
