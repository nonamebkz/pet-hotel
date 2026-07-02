<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusBookingGrooming;

$bookingList = $bookingList ?? [];
$statusLabels = $statusLabels ?? [];
$refundLabels = $refundLabels ?? [];
$opsiLabels = $opsiLabels ?? [];
$filterStatus = $filterStatus ?? '';
$filterTanggal = $filterTanggal ?? '';
$hasFilter = $filterStatus !== '' || $filterTanggal !== '';

$chips = [];
$baseBookingUrl = '/admin/grooming/booking';
if ($filterTanggal !== '') {
    $chips[] = [
        'label' => 'Tanggal: ' . date('d/m/Y', strtotime($filterTanggal)),
        'removeHref' => $baseBookingUrl . ($filterStatus !== '' ? '?status=' . urlencode($filterStatus) : ''),
    ];
}
if ($filterStatus !== '') {
    $chips[] = [
        'label' => 'Status: ' . ($statusLabels[$filterStatus] ?? $filterStatus),
        'removeHref' => $baseBookingUrl . ($filterTanggal !== '' ? '?tanggal=' . urlencode($filterTanggal) : ''),
    ];
}
?>
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Booking Grooming</h1>

    <?php
    $activeTab = 'booking';
    require __DIR__ . '/../_subnav.php';
    ?>

    <form method="GET" action="/admin/grooming/booking" class="mb-4 flex flex-wrap items-end gap-3">
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

    <?php if ($hasFilter || $bookingList !== []): ?>
        <?php
        ui_filter_chips(
            $chips,
            '/admin/grooming/booking',
            count($bookingList),
            'booking',
        );
        ?>
    <?php endif; ?>

    <?php if ($bookingList === []): ?>
        <?php
        if ($hasFilter) {
            $variant = 'filtered';
            $title = 'Tidak ditemukan booking untuk filter yang dipilih';
            $description = 'Coba ubah tanggal atau status, atau reset filter untuk melihat semua booking.';
            $ctaLabel = 'Reset Filter';
            $ctaHref = '/admin/grooming/booking';
        } else {
            $variant = 'empty';
            $title = 'Belum ada booking grooming';
            $description = 'Booking dari pelanggan akan muncul di sini setelah diajukan.';
            $ctaLabel = null;
            $ctaHref = null;
        }
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Pelanggan / Kucing</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Layanan</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal & Jam</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Pengantaran</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Total</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-600 min-w-[220px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($bookingList as $booking): ?>
                            <?php
                            $statusEnum = StatusBookingGrooming::tryFrom((string) $booking['status']);
                            $nextStatus = $statusEnum?->nextOperationalStatus();
                            $total = (float) $booking['harga_layanan'] + (float) $booking['biaya_antar_jemput'];
                            ?>
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800"><?= e((string) $booking['pelanggan_nama']) ?></div>
                                    <div class="text-gray-600"><?= e((string) $booking['kucing_nama']) ?></div>
                                    <div class="text-xs text-gray-400 mt-0.5"><?= e((string) $booking['pelanggan_email']) ?></div>
                                    <?php if (!empty($booking['catatan'])): ?>
                                        <div class="text-xs text-gray-500 mt-1 italic">"<?= e((string) $booking['catatan']) ?>"</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-gray-700"><?= e((string) $booking['jenis_nama']) ?></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div><?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></div>
                                    <?php if (!empty($booking['jam_grooming'])): ?>
                                        <div class="text-xs text-gray-500"><?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?> WIB</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div><?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?></div>
                                    <?php if ($booking['opsi_pengantaran'] === OpsiPengantaran::ANTAR_JEMPUT->value && $booking['jarak_km'] !== null): ?>
                                        <div class="text-xs text-gray-500"><?= e(number_format((float) $booking['jarak_km'], 2, ',', '.')) ?> km</div>
                                        <div class="text-xs text-gray-500">+Rp <?= e(number_format((float) $booking['biaya_antar_jemput'], 0, ',', '.')) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-right font-medium whitespace-nowrap">
                                    Rp <?= e(number_format($total, 0, ',', '.')) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($statusEnum): ?>
                                        <span class="text-xs px-2 py-1 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                                            <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php
                                    $refundEnum = \App\Enums\StatusRefund::tryFrom((string) ($booking['status_refund'] ?? 'TIDAK_ADA'));
                                    if ($refundEnum && $refundEnum !== \App\Enums\StatusRefund::TIDAK_ADA): ?>
                                        <span class="inline-block mt-1 text-xs px-2 py-1 rounded-full <?= e($refundEnum->badgeClass()) ?>">
                                            <?= e(($refundLabels ?? [])[$refundEnum->value] ?? $refundEnum->value) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        <?php if ((string) $booking['status'] === StatusBookingGrooming::MENUNGGU_KONFIRMASI->value): ?>
                                            <form method="POST" action="/admin/grooming/booking/konfirmasi" class="flex items-center gap-1">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                                <input type="time" name="jam_grooming" required
                                                       class="text-xs border border-gray-300 rounded px-1.5 py-1">
                                                <button type="submit" class="text-xs bg-green-600 text-white rounded px-2 py-1 hover:bg-green-700">
                                                    Konfirmasi
                                                </button>
                                            </form>
                                            <form method="POST" action="/admin/grooming/booking/tolak"
                                                  data-confirm="Tolak booking ini?">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                                <button type="submit" class="text-xs border border-red-300 text-red-600 rounded px-2 py-1 hover:bg-red-50">
                                                    Tolak
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($nextStatus): ?>
                                            <form method="POST" action="/admin/grooming/booking/status">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                                <input type="hidden" name="status" value="<?= e($nextStatus->value) ?>">
                                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                                <button type="submit" class="text-xs bg-blue-600 text-white rounded px-2 py-1 hover:bg-blue-700">
                                                    → <?= e($statusLabels[$nextStatus->value] ?? $nextStatus->value) ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (!empty($booking['can_staff_cancel_refund'])): ?>
                                            <form method="POST" action="/admin/grooming/booking/batalkan-refund"
                                                  class="flex items-center gap-1"
                                                  data-confirm="Batalkan booking lunas ini? Refund akan ditandai pending.">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                                <input type="text" name="alasan" placeholder="Alasan"
                                                       class="text-xs border border-gray-300 rounded px-1.5 py-1 w-24">
                                                <button type="submit" class="text-xs border border-red-300 text-red-600 rounded px-2 py-1 hover:bg-red-50">
                                                    Refund
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (!empty($booking['can_mark_refund']) && !empty($booking['transaksi_id'])): ?>
                                            <form method="POST" action="/admin/grooming/transaksi/refund-selesai"
                                                  data-confirm="Tandai refund selesai?">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="transaksi_id" value="<?= e((string) $booking['transaksi_id']) ?>">
                                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                                <button type="submit" class="text-xs bg-green-600 text-white rounded px-2 py-1 hover:bg-green-700">
                                                    Refund Selesai
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
