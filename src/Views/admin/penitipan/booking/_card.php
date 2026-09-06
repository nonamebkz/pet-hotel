<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusPenitipan;
use App\Enums\StatusRefund;

$booking = $booking ?? [];
$statusLabels = $statusLabels ?? [];
$opsiLabels = $opsiLabels ?? [];
$refundLabels = $refundLabels ?? [];
$minVaksin = $minVaksin ?? 1;
$filterStatus = $filterStatus ?? '';
$filterCheckIn = $filterCheckIn ?? '';
$filterMonitoring = $filterMonitoring ?? '';

$statusEnum = StatusPenitipan::tryFrom((string) $booking['status']);
$nextStatus = $statusEnum?->nextOperationalStatus();
$transaksiLunas = !empty($booking['transaksi_lunas']);
$vaksinOk = (int) ($booking['vaksin_count'] ?? 0) >= $minVaksin;
$needsConfirm = (string) $booking['status'] === StatusPenitipan::MENUNGGU_KONFIRMASI->value;
$refundEnum = StatusRefund::tryFrom((string) ($booking['status_refund'] ?? StatusRefund::TIDAK_ADA->value));

$btnPrimary = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-3.5 py-2 text-xs font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin disabled:opacity-60';
$btnSuccess = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-success px-3.5 py-2 text-xs font-semibold text-white shadow-soft transition duration-soft hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-success';
$btnDanger = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition duration-soft hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400';
$btnGhost = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-border bg-page/60 px-3.5 py-2 text-xs font-semibold text-admin transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';

$filterHidden = static function () use ($filterStatus, $filterCheckIn, $filterMonitoring): void {
    echo '<input type="hidden" name="filter_status" value="' . e($filterStatus) . '">';
    echo '<input type="hidden" name="filter_check_in" value="' . e($filterCheckIn) . '">';
    echo '<input type="hidden" name="filter_monitoring" value="' . e($filterMonitoring) . '">';
};
?>
<article class="rounded-2xl border <?= $needsConfirm ? 'border-amber-200 bg-warning-bg/25' : 'border-white/80 bg-card' ?> p-5 sm:p-6 shadow-soft space-y-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0">
            <h2 class="font-heading text-lg text-content-primary">
                <?= e((string) $booking['pelanggan_nama']) ?>
                <span class="text-content-secondary font-body text-base">· <?= e((string) $booking['kucing_nama']) ?></span>
            </h2>
            <p class="mt-1 text-sm text-content-secondary">
                <?= e((string) $booking['paket_nama']) ?> · <?= e((string) $booking['nama_kamar']) ?>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 justify-end">
            <span class="text-xs px-2.5 py-1 rounded-lg font-medium <?= e($statusEnum?->badgeClass() ?? 'bg-admin-soft text-admin') ?>">
                <?= e((string) ($booking['status_label'] ?? $booking['status'])) ?>
            </span>
            <?php if ($transaksiLunas): ?>
                <span class="text-[10px] font-semibold uppercase tracking-wide text-success">Lunas</span>
            <?php endif; ?>
            <?php if ($refundEnum && $refundEnum !== StatusRefund::TIDAK_ADA): ?>
                <span class="text-xs px-2 py-0.5 rounded-lg font-medium <?= e($refundEnum->badgeClass()) ?>">
                    <?= e($refundLabels[$refundEnum->value] ?? $refundEnum->value) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <dl class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
        <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset">
            <dt class="text-xs text-content-secondary">Periode</dt>
            <dd class="mt-0.5 font-medium text-content-primary">
                <?= e(date('d/m/Y', strtotime((string) $booking['check_in']))) ?>
                — <?= e(date('d/m/Y', strtotime((string) $booking['check_out']))) ?>
                <span class="text-content-secondary font-normal">(<?= (int) $booking['lama_hari'] ?> hari)</span>
            </dd>
        </div>
        <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset">
            <dt class="text-xs text-content-secondary">Pengantaran</dt>
            <dd class="mt-0.5 font-medium text-content-primary"><?= e($opsiLabels[$booking['opsi_pengantaran']] ?? '') ?></dd>
        </div>
        <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset">
            <dt class="text-xs text-content-secondary">Subtotal</dt>
            <dd class="mt-0.5 font-semibold text-admin">
                Rp <?= e(number_format((float) $booking['subtotal_penitipan'], 0, ',', '.')) ?>
                <?php if ((float) $booking['potongan_promo'] > 0): ?>
                    <span class="block text-xs font-medium text-success">Promo −Rp <?= e(number_format((float) $booking['potongan_promo'], 0, ',', '.')) ?></span>
                <?php endif; ?>
            </dd>
        </div>
        <?php if ($needsConfirm): ?>
            <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset">
                <dt class="text-xs text-content-secondary">Total bayar</dt>
                <dd class="mt-0.5 font-semibold text-admin">
                    Rp <?= e(number_format((float) ($booking['total_bayar'] ?? 0), 0, ',', '.')) ?>
                    <?php if ((float) ($booking['biaya_antar_jemput'] ?? 0) > 0): ?>
                        <span class="block text-xs font-normal text-content-secondary">
                            Termasuk antar-jemput Rp <?= e(number_format((float) $booking['biaya_antar_jemput'], 0, ',', '.')) ?>
                        </span>
                    <?php endif; ?>
                </dd>
            </div>
        <?php endif; ?>
        <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset sm:col-span-2 lg:col-span-3">
            <dt class="text-xs text-content-secondary">Syarat vaksin</dt>
            <dd class="mt-0.5 flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded-lg px-2 py-0.5 text-xs font-semibold <?= $vaksinOk ? 'bg-success-bg text-success' : 'bg-red-100 text-red-700' ?>">
                    <?= $vaksinOk ? 'Memenuhi syarat' : 'Belum memenuhi' ?>
                </span>
                <span class="text-xs text-content-secondary"><?= (int) $booking['vaksin_count'] ?> entri (min. <?= (int) $minVaksin ?>)</span>
            </dd>
        </div>
        <?php if ($needsConfirm && !empty($booking['catatan_makan'])): ?>
            <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset sm:col-span-2 lg:col-span-3">
                <dt class="text-xs text-content-secondary">Catatan makan</dt>
                <dd class="mt-0.5 text-content-primary"><?= e((string) $booking['catatan_makan']) ?></dd>
            </div>
        <?php endif; ?>
    </dl>

    <?php if (!empty($booking['vaksin_list'])): ?>
        <details class="rounded-xl border border-border bg-page/40 open:bg-page/70" <?= $needsConfirm ? 'open' : '' ?>>
            <summary class="cursor-pointer list-none px-4 py-3 text-sm font-semibold text-admin flex items-center justify-between gap-2">
                <span>Riwayat vaksin</span>
                <svg class="h-4 w-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <div class="px-4 pb-4 border-t border-border/80 pt-3">
                <?php
                $vaksinList = $booking['vaksin_list'];
                require __DIR__ . '/../../../partials/vaksin-readonly-list.php';
                ?>
            </div>
        </details>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2 pt-1 border-t border-border/80">
        <?php if ($needsConfirm): ?>
            <div class="w-full rounded-xl border border-amber-200/80 bg-warning-bg/40 p-3 space-y-3">
                <?php if ($vaksinOk): ?>
                    <form method="POST" action="/admin/penitipan/booking/konfirmasi" class="flex flex-wrap items-center gap-2" data-loading-submit>
                        <?= Csrf::field() ?>
                        <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                        <?php $filterHidden(); ?>
                        <button type="submit" class="<?= e($btnSuccess) ?>">Konfirmasi</button>
                    </form>
                <?php else: ?>
                    <p class="text-xs font-medium text-red-700">Vaksin tidak memenuhi syarat — tolak booking.</p>
                <?php endif; ?>
                <form method="POST" action="/admin/penitipan/booking/tolak" class="space-y-2" data-confirm="Tolak booking ini?">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                    <?php $filterHidden(); ?>
                    <label class="block text-xs font-semibold text-content-secondary">Alasan penolakan <span class="font-normal">(opsional)</span></label>
                    <textarea name="alasan" rows="2" placeholder="Contoh: Jadwal penuh, dokumen vaksin tidak valid"
                              class="w-full rounded-xl border border-border bg-white px-3 py-2 text-xs shadow-soft-inset focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25"></textarea>
                    <button type="submit" class="<?= e($btnDanger) ?>">Tolak</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($statusEnum?->canCheckIn($transaksiLunas)): ?>
            <form method="POST" action="/admin/penitipan/booking/check-in" data-loading-submit>
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                <?php $filterHidden(); ?>
                <button type="submit" class="<?= e($btnPrimary) ?>">Check-in</button>
            </form>
        <?php endif; ?>

        <?php if ($nextStatus): ?>
            <form method="POST" action="/admin/penitipan/booking/status" data-loading-submit>
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                <input type="hidden" name="status" value="<?= e($nextStatus->value) ?>">
                <?php $filterHidden(); ?>
                <button type="submit" class="<?= e($btnPrimary) ?>">
                    Lanjut → <?= e($statusLabels[$nextStatus->value] ?? $nextStatus->value) ?>
                </button>
            </form>
        <?php endif; ?>

        <?php
        $showMonitoring = in_array(
            (string) $booking['status'],
            [StatusPenitipan::SEDANG_DITITIPKAN->value, StatusPenitipan::CHECK_OUT->value],
            true,
        );
        $monitoringUrl = '/admin/penitipan/monitoring/tambah?booking_id=' . urlencode((string) $booking['id']);
        $isActiveStay = (string) $booking['status'] === StatusPenitipan::SEDANG_DITITIPKAN->value;
        ?>
        <?php if ($showMonitoring): ?>
            <a href="<?= e($monitoringUrl . ($isActiveStay ? '' : '&tab=riwayat')) ?>"
               class="<?= e($btnGhost) ?> gap-1.5">
                Monitoring
                <?php if ($isActiveStay && !empty($booking['monitoring_has_today'])): ?>
                    <span class="inline-flex rounded-md bg-success-bg px-1.5 py-0.5 text-[10px] font-semibold text-success">Hari ini</span>
                <?php elseif ($isActiveStay): ?>
                    <span class="inline-flex rounded-md bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-800">Belum input</span>
                <?php elseif ((int) ($booking['monitoring_count'] ?? 0) > 0): ?>
                    <span class="inline-flex rounded-md bg-admin-soft px-1.5 py-0.5 text-[10px] font-semibold text-admin"><?= (int) $booking['monitoring_count'] ?> laporan</span>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <?php if (!empty($booking['can_staff_cancel_refund'])): ?>
            <form method="POST" action="/admin/penitipan/booking/batalkan-refund"
                  class="flex flex-wrap items-center gap-2 w-full sm:w-auto"
                  data-confirm="Batalkan booking lunas ini? Refund akan ditandai pending.">
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                <?php $filterHidden(); ?>
                <input type="text" name="alasan" placeholder="Alasan (opsional)"
                       class="rounded-xl border border-border bg-white px-3 py-2 text-xs shadow-soft-inset focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25 min-w-[10rem]">
                <button type="submit" class="<?= e($btnDanger) ?>">Batalkan (Refund)</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($booking['can_mark_refund']) && !empty($booking['transaksi_id'])): ?>
            <form method="POST" action="/admin/penitipan/transaksi/refund-selesai"
                  data-confirm="Tandai refund selesai?">
                <?= Csrf::field() ?>
                <input type="hidden" name="transaksi_id" value="<?= e((string) $booking['transaksi_id']) ?>">
                <?php $filterHidden(); ?>
                <button type="submit" class="<?= e($btnSuccess) ?>">Refund Selesai</button>
            </form>
        <?php endif; ?>
    </div>
</article>
