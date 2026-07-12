<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusBookingGrooming;
use App\Enums\StatusRefund;

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

$countMenunggu = 0;
$countProses = 0;
$countTerkonfirmasi = 0;
foreach ($bookingList as $statRow) {
    $st = (string) ($statRow['status'] ?? '');
    if ($st === StatusBookingGrooming::MENUNGGU_KONFIRMASI->value) {
        $countMenunggu++;
    } elseif ($st === StatusBookingGrooming::SEDANG_PROSES->value) {
        $countProses++;
    } elseif ($st === StatusBookingGrooming::TERKONFIRMASI->value) {
        $countTerkonfirmasi++;
    }
}

$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
$btnPrimary = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-3 py-2 text-xs font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin disabled:opacity-60';
$btnSuccess = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-success px-3 py-2 text-xs font-semibold text-white shadow-soft transition duration-soft hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-success';
$btnDanger = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition duration-soft hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400';
$btnGhost = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-border bg-page/60 px-3 py-2 text-xs font-semibold text-admin transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';
$filterHidden = static function () use ($filterStatus, $filterTanggal): void {
    echo '<input type="hidden" name="filter_status" value="' . e($filterStatus) . '">';
    echo '<input type="hidden" name="filter_tanggal" value="' . e($filterTanggal) . '">';
};
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Grooming</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Booking Grooming</h1>
                <p class="mt-2 text-sm text-content-secondary max-w-xl">
                    Konfirmasi jam, lanjutkan proses layanan, dan kelola pembatalan/refund.
                </p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-admin text-white shadow-soft" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
            </div>
        </div>
    </section>

    <?php
    $activeTab = 'booking';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if ($bookingList !== [] || $hasFilter): ?>
        <div class="grid sm:grid-cols-3 gap-4">
            <article class="rounded-2xl border <?= $countMenunggu > 0 ? 'border-amber-200 bg-warning-bg/40' : 'border-white/80 bg-card' ?> p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Menunggu Konfirmasi</p>
                <p class="mt-1 font-heading text-2xl <?= $countMenunggu > 0 ? 'text-amber-700' : 'text-admin' ?>"><?= e((string) $countMenunggu) ?></p>
                <p class="mt-1 text-xs text-content-secondary">Perlu set jam grooming</p>
            </article>
            <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Terkonfirmasi</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) $countTerkonfirmasi) ?></p>
                <p class="mt-1 text-xs text-content-secondary">Siap diproses</p>
            </article>
            <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Sedang Proses</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) $countProses) ?></p>
                <p class="mt-1 text-xs text-content-secondary">Dalam pengerjaan</p>
            </article>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/grooming/booking" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-heading text-lg text-content-primary">Filter</h2>
            <?php if ($hasFilter): ?>
                <a href="/admin/grooming/booking"
                   class="cursor-pointer text-xs font-semibold text-content-secondary transition duration-soft hover:text-admin focus:outline-none focus-visible:underline">
                    Reset filter
                </a>
            <?php endif; ?>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="tanggal" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= e($filterTanggal) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-content-primary">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?>">
                    <option value="">Semua status</option>
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end sm:col-span-2 lg:col-span-2">
                <button type="submit" class="<?= e($btnPrimary) ?> w-full sm:w-auto min-h-[42px] px-5">
                    Terapkan Filter
                </button>
            </div>
        </div>
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
            $title = 'Tidak ditemukan booking untuk filter ini';
            $description = 'Coba ubah tanggal atau status, atau reset filter untuk melihat semua booking.';
            $ctaLabel = 'Reset Filter';
            $ctaHref = '/admin/grooming/booking';
            $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2';
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
        <div class="hidden lg:block rounded-2xl border border-white/80 bg-card shadow-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border bg-admin-soft/40">
                            <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Pelanggan / Kucing</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Layanan</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Jadwal</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Pengantaran</th>
                            <th class="text-right px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Total</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Status</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider min-w-[260px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/80">
                        <?php foreach ($bookingList as $booking): ?>
                            <?php
                            $statusEnum = StatusBookingGrooming::tryFrom((string) $booking['status']);
                            $nextStatus = $statusEnum?->nextOperationalStatus();
                            $total = (float) $booking['harga_layanan'] + (float) $booking['biaya_antar_jemput'];
                            $refundEnum = StatusRefund::tryFrom((string) ($booking['status_refund'] ?? StatusRefund::TIDAK_ADA->value));
                            $needsConfirm = (string) $booking['status'] === StatusBookingGrooming::MENUNGGU_KONFIRMASI->value;
                            ?>
                            <tr class="align-top transition duration-soft hover:bg-admin-soft/30 <?= $needsConfirm ? 'bg-warning-bg/20' : '' ?>">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-content-primary"><?= e((string) $booking['pelanggan_nama']) ?></div>
                                    <div class="text-content-secondary"><?= e((string) $booking['kucing_nama']) ?></div>
                                    <div class="text-xs text-content-secondary/80 mt-0.5"><?= e((string) $booking['pelanggan_email']) ?></div>
                                    <?php if (!empty($booking['catatan'])): ?>
                                        <div class="mt-2 rounded-lg bg-page/80 px-2.5 py-1.5 text-xs text-content-secondary italic">
                                            “<?= e((string) $booking['catatan']) ?>”
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-content-primary"><?= e((string) $booking['jenis_nama']) ?></td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-medium text-content-primary"><?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></div>
                                    <?php if (!empty($booking['jam_grooming'])): ?>
                                        <div class="text-xs text-content-secondary mt-0.5"><?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?> WIB</div>
                                    <?php else: ?>
                                        <div class="text-xs text-amber-700 mt-0.5 font-medium">Jam belum diset</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-content-primary"><?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?></div>
                                    <?php if ($booking['opsi_pengantaran'] === OpsiPengantaran::ANTAR_JEMPUT->value && $booking['jarak_km'] !== null): ?>
                                        <div class="text-xs text-content-secondary mt-0.5"><?= e(number_format((float) $booking['jarak_km'], 2, ',', '.')) ?> km</div>
                                        <div class="text-xs text-content-secondary">+Rp <?= e(number_format((float) $booking['biaya_antar_jemput'], 0, ',', '.')) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-right font-semibold text-admin whitespace-nowrap">
                                    Rp <?= e(number_format($total, 0, ',', '.')) ?>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        <?php if ($statusEnum): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-lg font-medium <?= e($statusEnum->badgeClass()) ?>">
                                                <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($refundEnum && $refundEnum !== StatusRefund::TIDAK_ADA): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-lg font-medium <?= e($refundEnum->badgeClass()) ?>">
                                                <?= e($refundLabels[$refundEnum->value] ?? $refundEnum->value) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($booking['transaksi_lunas'])): ?>
                                            <span class="text-[10px] font-semibold uppercase tracking-wide text-success">Lunas</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col gap-2 max-w-xs">
                                        <?php if ($needsConfirm): ?>
                                            <form method="POST" action="/admin/grooming/booking/konfirmasi" class="rounded-xl border border-amber-200/80 bg-warning-bg/50 p-2.5 space-y-2" data-loading-submit>
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                                <?php $filterHidden(); ?>
                                                <label class="block text-[11px] font-semibold text-amber-900">Set jam grooming</label>
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <input type="time" name="jam_grooming" required
                                                           class="flex-1 min-w-[7rem] rounded-lg border border-border bg-white px-2 py-1.5 text-xs shadow-soft-inset focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25">
                                                    <button type="submit" class="<?= e($btnSuccess) ?>">Konfirmasi</button>
                                                </div>
                                            </form>
                                            <form method="POST" action="/admin/grooming/booking/tolak" data-confirm="Tolak booking ini?">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                                <?php $filterHidden(); ?>
                                                <button type="submit" class="<?= e($btnDanger) ?> w-full">Tolak</button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($nextStatus): ?>
                                            <form method="POST" action="/admin/grooming/booking/status" data-loading-submit>
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                                <input type="hidden" name="status" value="<?= e($nextStatus->value) ?>">
                                                <?php $filterHidden(); ?>
                                                <button type="submit" class="<?= e($btnPrimary) ?> w-full">
                                                    Lanjut → <?= e($statusLabels[$nextStatus->value] ?? $nextStatus->value) ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (!empty($booking['can_staff_cancel_refund'])): ?>
                                            <form method="POST" action="/admin/grooming/booking/batalkan-refund"
                                                  class="rounded-xl border border-red-100 bg-red-50/50 p-2.5 space-y-2"
                                                  data-confirm="Batalkan booking lunas ini? Refund akan ditandai pending.">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                                <?php $filterHidden(); ?>
                                                <label class="block text-[11px] font-semibold text-red-800">Batalkan + refund</label>
                                                <div class="flex flex-wrap gap-1.5">
                                                    <input type="text" name="alasan" placeholder="Alasan (opsional)"
                                                           class="flex-1 min-w-[6rem] rounded-lg border border-border bg-white px-2 py-1.5 text-xs shadow-soft-inset focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25">
                                                    <button type="submit" class="<?= e($btnDanger) ?>">Refund</button>
                                                </div>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (!empty($booking['can_mark_refund']) && !empty($booking['transaksi_id'])): ?>
                                            <form method="POST" action="/admin/grooming/transaksi/refund-selesai"
                                                  data-confirm="Tandai refund selesai?">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="transaksi_id" value="<?= e((string) $booking['transaksi_id']) ?>">
                                                <?php $filterHidden(); ?>
                                                <button type="submit" class="<?= e($btnSuccess) ?> w-full">Refund Selesai</button>
                                            </form>
                                        <?php endif; ?>

                                        <?php
                                        $hasAnyAction = $needsConfirm || $nextStatus
                                            || !empty($booking['can_staff_cancel_refund'])
                                            || (!empty($booking['can_mark_refund']) && !empty($booking['transaksi_id']));
                                        if (!$hasAnyAction): ?>
                                            <span class="text-xs text-content-secondary/70">Tidak ada aksi</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:hidden space-y-3">
            <?php foreach ($bookingList as $booking): ?>
                <?php
                $statusEnum = StatusBookingGrooming::tryFrom((string) $booking['status']);
                $nextStatus = $statusEnum?->nextOperationalStatus();
                $total = (float) $booking['harga_layanan'] + (float) $booking['biaya_antar_jemput'];
                $refundEnum = StatusRefund::tryFrom((string) ($booking['status_refund'] ?? StatusRefund::TIDAK_ADA->value));
                $needsConfirm = (string) $booking['status'] === StatusBookingGrooming::MENUNGGU_KONFIRMASI->value;
                ?>
                <article class="rounded-2xl border <?= $needsConfirm ? 'border-amber-200 bg-warning-bg/30' : 'border-white/80 bg-card' ?> p-4 shadow-soft space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-content-primary truncate"><?= e((string) $booking['pelanggan_nama']) ?></p>
                            <p class="text-sm text-content-secondary"><?= e((string) $booking['kucing_nama']) ?> · <?= e((string) $booking['jenis_nama']) ?></p>
                        </div>
                        <?php if ($statusEnum): ?>
                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-lg font-medium <?= e($statusEnum->badgeClass()) ?>">
                                <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <dl class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <dt class="text-xs text-content-secondary">Jadwal</dt>
                            <dd class="font-medium text-content-primary">
                                <?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?>
                                <?php if (!empty($booking['jam_grooming'])): ?>
                                    · <?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <div class="text-right">
                            <dt class="text-xs text-content-secondary">Total</dt>
                            <dd class="font-heading text-admin">Rp <?= e(number_format($total, 0, ',', '.')) ?></dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-xs text-content-secondary">Pengantaran</dt>
                            <dd class="text-content-primary">
                                <?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?>
                                <?php if ($booking['opsi_pengantaran'] === OpsiPengantaran::ANTAR_JEMPUT->value && $booking['jarak_km'] !== null): ?>
                                    <span class="text-xs text-content-secondary">
                                        (<?= e(number_format((float) $booking['jarak_km'], 2, ',', '.')) ?> km)
                                    </span>
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>

                    <?php if (!empty($booking['catatan'])): ?>
                        <p class="rounded-lg bg-page/80 px-2.5 py-1.5 text-xs text-content-secondary italic">
                            “<?= e((string) $booking['catatan']) ?>”
                        </p>
                    <?php endif; ?>

                    <?php if ($refundEnum && $refundEnum !== StatusRefund::TIDAK_ADA): ?>
                        <span class="inline-flex text-xs px-2 py-0.5 rounded-lg font-medium <?= e($refundEnum->badgeClass()) ?>">
                            <?= e($refundLabels[$refundEnum->value] ?? $refundEnum->value) ?>
                        </span>
                    <?php endif; ?>

                    <div class="space-y-2 pt-1 border-t border-border/80">
                        <?php if ($needsConfirm): ?>
                            <form method="POST" action="/admin/grooming/booking/konfirmasi" class="space-y-2" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <?php $filterHidden(); ?>
                                <label class="block text-xs font-semibold text-content-primary">Set jam grooming</label>
                                <input type="time" name="jam_grooming" required class="<?= e($inputClass) ?>">
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="submit" class="<?= e($btnSuccess) ?>">Konfirmasi</button>
                                    <button type="submit" form="tolak-<?= e((string) $booking['id']) ?>" class="<?= e($btnDanger) ?>">Tolak</button>
                                </div>
                            </form>
                            <form id="tolak-<?= e((string) $booking['id']) ?>" method="POST" action="/admin/grooming/booking/tolak"
                                  data-confirm="Tolak booking ini?" class="hidden">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <?php $filterHidden(); ?>
                            </form>
                        <?php endif; ?>

                        <?php if ($nextStatus): ?>
                            <form method="POST" action="/admin/grooming/booking/status" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <input type="hidden" name="status" value="<?= e($nextStatus->value) ?>">
                                <?php $filterHidden(); ?>
                                <button type="submit" class="<?= e($btnPrimary) ?> w-full">
                                    Lanjut → <?= e($statusLabels[$nextStatus->value] ?? $nextStatus->value) ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if (!empty($booking['can_staff_cancel_refund'])): ?>
                            <form method="POST" action="/admin/grooming/booking/batalkan-refund"
                                  class="space-y-2"
                                  data-confirm="Batalkan booking lunas ini? Refund akan ditandai pending.">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <?php $filterHidden(); ?>
                                <input type="text" name="alasan" placeholder="Alasan pembatalan (opsional)" class="<?= e($inputClass) ?>">
                                <button type="submit" class="<?= e($btnDanger) ?> w-full">Batalkan + Refund</button>
                            </form>
                        <?php endif; ?>

                        <?php if (!empty($booking['can_mark_refund']) && !empty($booking['transaksi_id'])): ?>
                            <form method="POST" action="/admin/grooming/transaksi/refund-selesai"
                                  data-confirm="Tandai refund selesai?">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="transaksi_id" value="<?= e((string) $booking['transaksi_id']) ?>">
                                <?php $filterHidden(); ?>
                                <button type="submit" class="<?= e($btnSuccess) ?> w-full">Refund Selesai</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
