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
?>
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Booking Grooming</h1>

    <div class="flex gap-4 mb-6 text-sm">
        <a href="/admin/grooming/layanan" class="text-gray-500 hover:text-slate-800">Jenis</a>
        <a href="/admin/grooming/kuota" class="text-gray-500 hover:text-slate-800">Kuota</a>
        <a href="/admin/grooming/booking" class="text-slate-800 font-medium border-b-2 border-slate-800 pb-1">Booking</a>
        <a href="/admin/grooming/pembayaran" class="text-gray-500 hover:text-slate-800">Verifikasi Bukti</a>
    </div>

    <form method="GET" action="/admin/grooming/booking" class="mb-6 flex flex-wrap items-end gap-3">
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
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">Tidak ada booking.</div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($bookingList as $booking): ?>
                <?php
                $statusEnum = StatusBookingGrooming::tryFrom((string) $booking['status']);
                $nextStatus = $statusEnum?->nextOperationalStatus();
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
                        <div>Jenis: <strong><?= e((string) $booking['jenis_nama']) ?></strong></div>
                        <div>Tanggal: <?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></div>
                        <?php if (!empty($booking['jam_grooming'])): ?>
                            <div>Jam: <?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?> WIB</div>
                        <?php endif; ?>
                        <div>Pengantaran: <?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?></div>
                        <?php if ($booking['opsi_pengantaran'] === OpsiPengantaran::ANTAR_JEMPUT->value && $booking['jarak_km'] !== null): ?>
                            <div>Jarak: <?= e(number_format((float) $booking['jarak_km'], 2, ',', '.')) ?> km</div>
                            <div>Biaya antar-jemput: Rp <?= e(number_format((float) $booking['biaya_antar_jemput'], 0, ',', '.')) ?></div>
                        <?php endif; ?>
                        <div>Total: Rp <?= e(number_format((float) $booking['harga_layanan'] + (float) $booking['biaya_antar_jemput'], 0, ',', '.')) ?></div>
                    </div>

                    <?php if (!empty($booking['catatan'])): ?>
                        <p class="text-sm text-gray-500 mb-3">Catatan: <?= e((string) $booking['catatan']) ?></p>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-2 pt-3 border-t">
                        <?php if ((string) $booking['status'] === StatusBookingGrooming::MENUNGGU_KONFIRMASI->value): ?>
                            <form method="POST" action="/admin/grooming/booking/konfirmasi" class="flex items-center gap-2">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                <input type="time" name="jam_grooming" required
                                       class="text-sm border border-gray-300 rounded-lg px-2 py-1.5">
                                <button type="submit" class="text-sm bg-green-600 text-white rounded-lg px-3 py-1.5 hover:bg-green-700">
                                    Konfirmasi & Set Jam
                                </button>
                            </form>
                            <form method="POST" action="/admin/grooming/booking/tolak"
                                  onsubmit="return confirm('Tolak booking ini?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                <button type="submit" class="text-sm border border-red-300 text-red-600 rounded-lg px-3 py-1.5 hover:bg-red-50">
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
                                <button type="submit" class="text-sm bg-blue-600 text-white rounded-lg px-3 py-1.5 hover:bg-blue-700">
                                    → <?= e($statusLabels[$nextStatus->value] ?? $nextStatus->value) ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php
                        $refundEnum = \App\Enums\StatusRefund::tryFrom((string) ($booking['status_refund'] ?? 'TIDAK_ADA'));
                        if ($refundEnum && $refundEnum !== \App\Enums\StatusRefund::TIDAK_ADA): ?>
                            <span class="text-xs px-2 py-1 rounded-full <?= e($refundEnum->badgeClass()) ?>">
                                <?= e(($refundLabels ?? [])[$refundEnum->value] ?? $refundEnum->value) ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($booking['can_staff_cancel_refund'])): ?>
                            <form method="POST" action="/admin/grooming/booking/batalkan-refund"
                                  class="flex items-center gap-2"
                                  onsubmit="return confirm('Batalkan booking lunas ini? Refund akan ditandai pending.')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                <input type="text" name="alasan" placeholder="Alasan (opsional)"
                                       class="text-sm border border-gray-300 rounded-lg px-2 py-1.5 w-40">
                                <button type="submit" class="text-sm border border-red-300 text-red-600 rounded-lg px-3 py-1.5 hover:bg-red-50">
                                    Batalkan (Refund)
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if (!empty($booking['can_mark_refund']) && !empty($booking['transaksi_id'])): ?>
                            <form method="POST" action="/admin/grooming/transaksi/refund-selesai"
                                  onsubmit="return confirm('Tandai refund selesai?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="transaksi_id" value="<?= e((string) $booking['transaksi_id']) ?>">
                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                <button type="submit" class="text-sm bg-green-600 text-white rounded-lg px-3 py-1.5 hover:bg-green-700">
                                    Tandai Refund Selesai
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
