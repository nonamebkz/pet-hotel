<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusPenitipan;

$bookingList = $bookingList ?? [];
$statusLabels = $statusLabels ?? [];
$opsiLabels = $opsiLabels ?? [];
$filterStatus = $filterStatus ?? '';
$filterCheckIn = $filterCheckIn ?? '';
$refundLabels = $refundLabels ?? [];
$minVaksin = $minVaksin ?? 1;
?>
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Booking Penitipan</h1>
    <?php require __DIR__ . '/../_nav.php'; ?>

    <form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
        <div><label class="text-sm">Check-in</label>
            <input type="date" name="check_in" value="<?= e($filterCheckIn) ?>" class="border rounded-lg px-3 py-2 text-sm block mt-1"></div>
        <div><label class="text-sm">Status</label>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm block mt-1">
                <option value="">Semua</option>
                <?php foreach ($statusLabels as $v => $l): ?>
                    <option value="<?= e($v) ?>" <?= $filterStatus === $v ? 'selected' : '' ?>><?= e($l) ?></option>
                <?php endforeach; ?>
            </select></div>
        <button type="submit" class="bg-gray-100 border rounded-lg px-4 py-2 text-sm">Filter</button>
    </form>

    <div class="space-y-4">
        <?php foreach ($bookingList as $booking): ?>
            <?php
            $statusEnum = StatusPenitipan::tryFrom((string) $booking['status']);
            $nextStatus = $statusEnum?->nextOperationalStatus();
            $transaksiLunas = !empty($booking['transaksi_lunas']);
            $vaksinOk = (int) ($booking['vaksin_count'] ?? 0) >= $minVaksin;
            ?>
            <div class="bg-white rounded-xl border p-4">
                <div class="flex justify-between mb-2">
                    <div>
                        <div class="font-semibold"><?= e((string) $booking['pelanggan_nama']) ?> · <?= e((string) $booking['kucing_nama']) ?></div>
                        <div class="text-sm text-gray-500"><?= e((string) $booking['paket_nama']) ?> · <?= e((string) $booking['nama_kamar']) ?></div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100"><?= e((string) ($booking['status_label'] ?? $booking['status'])) ?></span>
                </div>
                <div class="text-sm text-gray-600 grid sm:grid-cols-2 gap-1 mb-2">
                    <div><?= e(date('d/m/Y', strtotime((string) $booking['check_in']))) ?> — <?= e(date('d/m/Y', strtotime((string) $booking['check_out']))) ?> (<?= (int) $booking['lama_hari'] ?> hari)</div>
                    <div>Pengantaran: <?= e($opsiLabels[$booking['opsi_pengantaran']] ?? '') ?></div>
                    <div>Subtotal: Rp <?= e(number_format((float) $booking['subtotal_penitipan'], 0, ',', '.')) ?></div>
                    <?php if ((float) $booking['potongan_promo'] > 0): ?>
                        <div class="text-green-700">Promo: - Rp <?= e(number_format((float) $booking['potongan_promo'], 0, ',', '.')) ?></div>
                    <?php endif; ?>
                    <div>Vaksin lengkap: <?= $vaksinOk ? 'Ya' : 'Tidak' ?> (<?= (int) $booking['vaksin_count'] ?> entri)</div>
                </div>
                <?php if (!empty($booking['vaksin_list'])): ?>
                    <div class="mb-3 bg-gray-50 rounded-lg p-3 text-sm">
                        <div class="font-medium text-gray-700 mb-2">Riwayat Vaksin</div>
                        <div class="space-y-3">
                            <?php foreach ($booking['vaksin_list'] as $vaksin): ?>
                                <div class="flex flex-wrap gap-3 items-start">
                                    <div class="min-w-[140px]">
                                        <div class="text-gray-800"><?= e((string) $vaksin['jenis_vaksin']) ?></div>
                                        <div class="text-xs text-gray-500">
                                            <?= e(date('d/m/Y', strtotime((string) $vaksin['tanggal_vaksin']))) ?>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-[120px]">
                                        <?php if (!empty($vaksin['sertifikat_url'])): ?>
                                            <?php
                                            $fileUrl = (string) $vaksin['sertifikat_url'];
                                            $label = 'Sertifikat';
                                            $maxHeightClass = 'max-h-24';
                                            require __DIR__ . '/../../../partials/uploaded-file-preview.php';
                                            ?>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">Tanpa sertifikat</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="flex flex-wrap gap-2 pt-2 border-t">
                    <?php if ((string) $booking['status'] === StatusPenitipan::MENUNGGU_KONFIRMASI->value): ?>
                        <?php if ($vaksinOk): ?>
                            <form method="POST" action="/admin/penitipan/booking/konfirmasi"><?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <button type="submit" class="text-sm bg-green-600 text-white rounded-lg px-3 py-1.5">Konfirmasi</button>
                            </form>
                        <?php else: ?>
                            <span class="text-xs text-red-600">Vaksin tidak memenuhi syarat — tolak booking</span>
                        <?php endif; ?>
                        <form method="POST" action="/admin/penitipan/booking/tolak" onsubmit="return confirm('Tolak?')"><?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                            <button type="submit" class="text-sm border border-red-300 text-red-600 rounded-lg px-3 py-1.5">Tolak</button>
                        </form>
                    <?php endif; ?>
                    <?php if ($statusEnum?->canCheckIn($transaksiLunas)): ?>
                        <form method="POST" action="/admin/penitipan/booking/check-in"><?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                            <button type="submit" class="text-sm bg-indigo-600 text-white rounded-lg px-3 py-1.5">Check-in</button>
                        </form>
                    <?php endif; ?>
                    <?php if ($nextStatus): ?>
                        <form method="POST" action="/admin/penitipan/booking/status"><?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                            <input type="hidden" name="status" value="<?= e($nextStatus->value) ?>">
                            <button type="submit" class="text-sm bg-blue-600 text-white rounded-lg px-3 py-1.5">→ <?= e($statusLabels[$nextStatus->value] ?? $nextStatus->value) ?></button>
                        </form>
                    <?php endif; ?>
                    <?php if ((string) $booking['status'] === StatusPenitipan::SEDANG_DITITIPKAN->value): ?>
                        <a href="/admin/penitipan/monitoring/tambah?booking_id=<?= e((string) $booking['id']) ?>" class="text-sm border rounded-lg px-3 py-1.5 hover:bg-gray-50">Input Monitoring</a>
                    <?php endif; ?>

                    <?php
                    $refundEnum = \App\Enums\StatusRefund::tryFrom((string) ($booking['status_refund'] ?? 'TIDAK_ADA'));
                    if ($refundEnum && $refundEnum !== \App\Enums\StatusRefund::TIDAK_ADA): ?>
                        <span class="text-xs px-2 py-1 rounded-full <?= e($refundEnum->badgeClass()) ?>">
                            <?= e($refundLabels[$refundEnum->value] ?? $refundEnum->value) ?>
                        </span>
                    <?php endif; ?>

                    <?php if (!empty($booking['can_staff_cancel_refund'])): ?>
                        <form method="POST" action="/admin/penitipan/booking/batalkan-refund"
                              class="flex items-center gap-2"
                              onsubmit="return confirm('Batalkan booking lunas ini? Refund akan ditandai pending.')">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                            <input type="text" name="alasan" placeholder="Alasan (opsional)"
                                   class="text-sm border rounded-lg px-2 py-1.5 w-40">
                            <button type="submit" class="text-sm border border-red-300 text-red-600 rounded-lg px-3 py-1.5 hover:bg-red-50">
                                Batalkan (Refund)
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if (!empty($booking['can_mark_refund']) && !empty($booking['transaksi_id'])): ?>
                        <form method="POST" action="/admin/penitipan/transaksi/refund-selesai"
                              onsubmit="return confirm('Tandai refund selesai?')">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="transaksi_id" value="<?= e((string) $booking['transaksi_id']) ?>">
                            <button type="submit" class="text-sm bg-green-600 text-white rounded-lg px-3 py-1.5 hover:bg-green-700">
                                Tandai Refund Selesai
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
