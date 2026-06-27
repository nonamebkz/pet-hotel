<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusBookingPetCare;

$bookingList = $bookingList ?? [];
$statusLabels = $statusLabels ?? [];
$filterStatus = $filterStatus ?? '';
$filterTanggal = $filterTanggal ?? '';
?>
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Booking Pet Care</h1>

    <div class="flex gap-4 mb-6 text-sm">
        <a href="/admin/pet-care/layanan" class="text-gray-500 hover:text-slate-800">Layanan</a>
        <a href="/admin/pet-care/slot" class="text-gray-500 hover:text-slate-800">Slot Dokter</a>
        <a href="/admin/pet-care/booking" class="text-slate-800 font-medium border-b-2 border-slate-800 pb-1">Booking</a>
    </div>

    <form method="GET" action="/admin/pet-care/booking" class="mb-6 flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="<?= e($filterTanggal) ?>"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                <?php foreach ($statusLabels as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm hover:bg-gray-200">
            Filter
        </button>
    </form>

    <?php if ($bookingList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">
            Tidak ada booking.
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($bookingList as $booking): ?>
                <?php
                $statusEnum = StatusBookingPetCare::tryFrom((string) $booking['status']);
                $nextStatus = $statusEnum?->nextStatus();
                $canCancel = $statusEnum?->canCancel() ?? false;
                ?>
                <div class="bg-white rounded-xl border p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                        <div>
                            <div class="font-semibold text-gray-800">
                                <?= e((string) $booking['pelanggan_nama']) ?>
                                <span class="text-gray-400 font-normal">·</span>
                                <?= e((string) $booking['kucing_nama']) ?>
                            </div>
                            <div class="text-sm text-gray-500"><?= e((string) $booking['pelanggan_email']) ?></div>
                        </div>
                        <?php if ($statusEnum): ?>
                            <span class="text-xs px-2 py-1 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                                <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                        <div>Layanan: <strong><?= e((string) $booking['layanan_nama']) ?></strong></div>
                        <div>Harga: Rp <?= e(number_format((float) $booking['harga_layanan'], 0, ',', '.')) ?></div>
                        <div>Tanggal: <?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></div>
                        <div>Slot: <?= e(substr((string) $booking['slot_waktu'], 0, 5)) ?> WIB</div>
                    </div>

                    <?php if (!empty($booking['catatan'])): ?>
                        <p class="text-sm text-gray-500 mb-3">Catatan: <?= e((string) $booking['catatan']) ?></p>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-2 pt-3 border-t">
                        <?php if ($nextStatus): ?>
                            <form method="POST" action="/admin/pet-care/booking/status">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <input type="hidden" name="status" value="<?= e($nextStatus->value) ?>">
                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                <button type="submit" class="text-sm bg-blue-600 text-white rounded-lg px-3 py-1.5 hover:bg-blue-700">
                                    → <?= e($statusLabels[$nextStatus->value] ?? $nextStatus->value) ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($canCancel): ?>
                            <form method="POST" action="/admin/pet-care/booking/batalkan" class="flex items-center gap-2"
                                  onsubmit="return confirm('Batalkan booking ini?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                <input type="text" name="alasan" placeholder="Alasan (opsional)"
                                       class="text-sm border border-gray-300 rounded-lg px-2 py-1.5 w-40">
                                <button type="submit" class="text-sm border border-red-300 text-red-600 rounded-lg px-3 py-1.5 hover:bg-red-50">
                                    Batalkan
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
