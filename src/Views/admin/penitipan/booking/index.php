<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusPenitipan;
use App\Enums\StatusRefund;

$bookingList = $bookingList ?? [];
$statusLabels = $statusLabels ?? [];
$opsiLabels = $opsiLabels ?? [];
$filterStatus = $filterStatus ?? '';
$filterCheckIn = $filterCheckIn ?? '';
$refundLabels = $refundLabels ?? [];
$minVaksin = $minVaksin ?? 1;
$hasFilter = $filterStatus !== '' || $filterCheckIn !== '';

$countMenunggu = 0;
$countAktif = 0;
$countCheckIn = 0;
foreach ($bookingList as $statRow) {
    $st = (string) ($statRow['status'] ?? '');
    if ($st === StatusPenitipan::MENUNGGU_KONFIRMASI->value) {
        $countMenunggu++;
    } elseif ($st === StatusPenitipan::SEDANG_DITITIPKAN->value) {
        $countAktif++;
    } elseif ($st === StatusPenitipan::CHECK_IN->value || ($st === StatusPenitipan::MENUNGGU_VERIFIKASI_BUKTI->value && !empty($statRow['transaksi_lunas']))) {
        $countCheckIn++;
    }
}

$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
$btnPrimary = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-3.5 py-2 text-xs font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin disabled:opacity-60';
$btnSuccess = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-success px-3.5 py-2 text-xs font-semibold text-white shadow-soft transition duration-soft hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-success';
$btnDanger = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-semibold text-red-700 transition duration-soft hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400';
$btnGhost = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-border bg-page/60 px-3.5 py-2 text-xs font-semibold text-admin transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-success/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Penitipan</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Booking Penitipan</h1>
                <p class="mt-2 text-sm text-content-secondary max-w-xl">
                    Konfirmasi booking, check-in, monitoring harian, dan kelola refund.
                </p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-admin text-white shadow-soft" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"/>
                </svg>
            </div>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <?php if ($bookingList !== [] || $hasFilter): ?>
        <div class="grid sm:grid-cols-3 gap-4">
            <article class="rounded-2xl border <?= $countMenunggu > 0 ? 'border-amber-200 bg-warning-bg/40' : 'border-white/80 bg-card' ?> p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Menunggu Konfirmasi</p>
                <p class="mt-1 font-heading text-2xl <?= $countMenunggu > 0 ? 'text-amber-700' : 'text-admin' ?>"><?= e((string) $countMenunggu) ?></p>
            </article>
            <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Siap / Check-in</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) $countCheckIn) ?></p>
            </article>
            <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Sedang Dititipkan</p>
                <p class="mt-1 font-heading text-2xl text-success"><?= e((string) $countAktif) ?></p>
            </article>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/penitipan/booking" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-heading text-lg text-content-primary">Filter</h2>
            <?php if ($hasFilter): ?>
                <a href="/admin/penitipan/booking" class="cursor-pointer text-xs font-semibold text-content-secondary hover:text-admin focus:outline-none focus-visible:underline">Reset</a>
            <?php endif; ?>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="check_in" class="mb-1.5 block text-sm font-semibold text-content-primary">Check-in</label>
                <input type="date" id="check_in" name="check_in" value="<?= e($filterCheckIn) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-content-primary">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?>">
                    <option value="">Semua status</option>
                    <?php foreach ($statusLabels as $v => $l): ?>
                        <option value="<?= e($v) ?>" <?= $filterStatus === $v ? 'selected' : '' ?>><?= e($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end sm:col-span-2">
                <button type="submit" class="<?= e($btnPrimary) ?> min-h-[42px] px-5">Terapkan Filter</button>
            </div>
        </div>
    </form>

    <?php if ($bookingList === []): ?>
        <?php
        if ($hasFilter) {
            $variant = 'filtered';
            $title = 'Tidak ada booking untuk filter ini';
            $description = 'Coba ubah tanggal check-in atau status.';
            $ctaLabel = 'Reset Filter';
            $ctaHref = '/admin/penitipan/booking';
            $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover';
        } else {
            $variant = 'empty';
            $title = 'Belum ada booking penitipan';
            $description = 'Booking dari pelanggan akan muncul di sini setelah diajukan.';
            $ctaLabel = null;
            $ctaHref = null;
        }
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($bookingList as $booking): ?>
                <?php
                $statusEnum = StatusPenitipan::tryFrom((string) $booking['status']);
                $nextStatus = $statusEnum?->nextOperationalStatus();
                $transaksiLunas = !empty($booking['transaksi_lunas']);
                $vaksinOk = (int) ($booking['vaksin_count'] ?? 0) >= $minVaksin;
                $needsConfirm = (string) $booking['status'] === StatusPenitipan::MENUNGGU_KONFIRMASI->value;
                $refundEnum = StatusRefund::tryFrom((string) ($booking['status_refund'] ?? StatusRefund::TIDAK_ADA->value));
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
                        <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset sm:col-span-2 lg:col-span-3">
                            <dt class="text-xs text-content-secondary">Syarat vaksin</dt>
                            <dd class="mt-0.5 flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-lg px-2 py-0.5 text-xs font-semibold <?= $vaksinOk ? 'bg-success-bg text-success' : 'bg-red-100 text-red-700' ?>">
                                    <?= $vaksinOk ? 'Memenuhi syarat' : 'Belum memenuhi' ?>
                                </span>
                                <span class="text-xs text-content-secondary"><?= (int) $booking['vaksin_count'] ?> entri (min. <?= (int) $minVaksin ?>)</span>
                            </dd>
                        </div>
                    </dl>

                    <?php if (!empty($booking['vaksin_list'])): ?>
                        <details class="rounded-xl border border-border bg-page/40 open:bg-page/70">
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
                            <?php if ($vaksinOk): ?>
                                <form method="POST" action="/admin/penitipan/booking/konfirmasi" data-loading-submit>
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                    <button type="submit" class="<?= e($btnSuccess) ?>">Konfirmasi</button>
                                </form>
                            <?php else: ?>
                                <span class="inline-flex items-center rounded-xl bg-red-50 px-3 py-2 text-xs font-medium text-red-700">
                                    Vaksin tidak memenuhi syarat — tolak booking
                                </span>
                            <?php endif; ?>
                            <form method="POST" action="/admin/penitipan/booking/tolak" data-confirm="Tolak booking ini?">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <button type="submit" class="<?= e($btnDanger) ?>">Tolak</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($statusEnum?->canCheckIn($transaksiLunas)): ?>
                            <form method="POST" action="/admin/penitipan/booking/check-in" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <button type="submit" class="<?= e($btnPrimary) ?>">Check-in</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($nextStatus): ?>
                            <form method="POST" action="/admin/penitipan/booking/status" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <input type="hidden" name="status" value="<?= e($nextStatus->value) ?>">
                                <button type="submit" class="<?= e($btnPrimary) ?>">
                                    Lanjut → <?= e($statusLabels[$nextStatus->value] ?? $nextStatus->value) ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ((string) $booking['status'] === StatusPenitipan::SEDANG_DITITIPKAN->value): ?>
                            <a href="/admin/penitipan/monitoring/tambah?booking_id=<?= e(urlencode((string) $booking['id'])) ?>"
                               class="<?= e($btnGhost) ?>">Input Monitoring</a>
                        <?php endif; ?>

                        <?php if (!empty($booking['can_staff_cancel_refund'])): ?>
                            <form method="POST" action="/admin/penitipan/booking/batalkan-refund"
                                  class="flex flex-wrap items-center gap-2 w-full sm:w-auto"
                                  data-confirm="Batalkan booking lunas ini? Refund akan ditandai pending.">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
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
                                <button type="submit" class="<?= e($btnSuccess) ?>">Refund Selesai</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
