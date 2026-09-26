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

$inputClass = design_cn(
    'w-full rounded-xl border border-input bg-background px-3.5 py-2.5 text-base sm:text-sm text-foreground transition',
    'hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25',
);
$inputClassSm = design_cn($inputClass, 'flex-1 min-w-[6rem] rounded-lg px-2 py-1.5 text-xs');
$btnPrimary = design_cn(ui_btn_primary(), 'text-xs px-3 py-2 disabled:opacity-60');
$btnPrimaryLg = design_cn(ui_btn_primary(), 'min-h-[42px] w-full px-5 sm:w-auto');
$btnSuccess = design_cn(ui_btn_primary(), 'text-xs px-3 py-2 bg-emerald-600 hover:opacity-90');
$btnDanger = design_cn(
    ui_btn_secondary(),
    'text-xs px-3 py-2 border-destructive/30 bg-destructive/10 text-destructive hover:bg-destructive/15',
);
$metricCard = design_surface('metric');
$metricCardWarning = design_cn(design_surface('metric'), design_advice_panel_surface('warning'));
$panelForm = design_cn(design_surface('panel'), 'p-5 sm:p-6');
$tableShell = design_cn(design_surface('panel'), 'hidden overflow-hidden lg:block');
$listArticle = design_cn(design_interactive('listArticle'), 'space-y-3');
$listArticleWarning = design_cn(design_interactive('listArticle'), design_advice_panel_surface('warning'), 'space-y-3');
$filterHidden = static function () use ($filterStatus, $filterTanggal): void {
    echo '<input type="hidden" name="filter_status" value="' . e($filterStatus) . '">';
    echo '<input type="hidden" name="filter_tanggal" value="' . e($filterTanggal) . '">';
};
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Booking Grooming',
        'Grooming',
        'Konfirmasi jam, lanjutkan proses layanan, dan kelola pembatalan/refund.',
    );
    ?>

    <?php
    $activeTab = 'booking';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if ($bookingList !== [] || $hasFilter): ?>
        <div class="grid sm:grid-cols-3 gap-4">
            <article class="<?= e($countMenunggu > 0 ? $metricCardWarning : $metricCard) ?>">
                <p class="text-sm text-muted-foreground">Menunggu Konfirmasi</p>
                <p class="mt-1 font-heading text-2xl tabular-nums <?= $countMenunggu > 0 ? 'text-amber-700 dark:text-amber-300' : 'text-primary' ?>"><?= e((string) $countMenunggu) ?></p>
                <p class="mt-1 text-xs text-muted-foreground">Perlu set jam grooming</p>
            </article>
            <article class="<?= e($metricCard) ?>">
                <p class="text-sm text-muted-foreground">Terkonfirmasi</p>
                <p class="mt-1 font-heading text-2xl tabular-nums text-primary"><?= e((string) $countTerkonfirmasi) ?></p>
                <p class="mt-1 text-xs text-muted-foreground">Siap diproses</p>
            </article>
            <article class="<?= e($metricCard) ?>">
                <p class="text-sm text-muted-foreground">Sedang Proses</p>
                <p class="mt-1 font-heading text-2xl tabular-nums text-primary"><?= e((string) $countProses) ?></p>
                <p class="mt-1 text-xs text-muted-foreground">Dalam pengerjaan</p>
            </article>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/grooming/booking" class="<?= e($panelForm) ?>">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-heading text-lg text-foreground">Filter</h2>
            <?php if ($hasFilter): ?>
                <a href="/admin/grooming/booking"
                   class="text-xs font-semibold text-muted-foreground transition hover:text-primary focus:outline-none focus-visible:underline">
                    Reset filter
                </a>
            <?php endif; ?>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="tanggal" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= e($filterTanggal) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-foreground">Status</label>
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
                <button type="submit" class="<?= e($btnPrimaryLg) ?>">
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
            $ctaClass = ui_btn_primary();
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
        <div class="<?= e($tableShell) ?>">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border bg-muted/40">
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Pelanggan / Kucing</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Layanan</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Jadwal</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Pengantaran</th>
                            <th class="text-right px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Total</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider min-w-[260px]">Aksi</th>
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
                            <tr class="align-top transition hover:bg-muted/30 <?= $needsConfirm ? 'bg-amber-500/5' : '' ?>">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-foreground"><?= e((string) $booking['pelanggan_nama']) ?></div>
                                    <div class="text-muted-foreground"><?= e((string) $booking['kucing_nama']) ?></div>
                                    <div class="text-xs text-muted-foreground/80 mt-0.5"><?= e((string) $booking['pelanggan_email']) ?></div>
                                    <?php if (!empty($booking['catatan'])): ?>
                                        <div class="mt-2 rounded-lg bg-muted/50 px-2.5 py-1.5 text-xs text-muted-foreground italic">
                                            “<?= e((string) $booking['catatan']) ?>”
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-foreground"><?= e((string) $booking['jenis_nama']) ?></td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="font-medium text-foreground"><?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></div>
                                    <?php if (!empty($booking['jam_grooming'])): ?>
                                        <div class="text-xs text-muted-foreground mt-0.5"><?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?> WIB</div>
                                    <?php else: ?>
                                        <div class="text-xs text-amber-700 mt-0.5 font-medium">Jam belum diset</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-foreground"><?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?></div>
                                    <?php if ($booking['opsi_pengantaran'] === OpsiPengantaran::ANTAR_JEMPUT->value && $booking['jarak_km'] !== null): ?>
                                        <div class="text-xs text-muted-foreground mt-0.5"><?= e(number_format((float) $booking['jarak_km'], 2, ',', '.')) ?> km</div>
                                        <div class="text-xs text-muted-foreground">+Rp <?= e(number_format((float) $booking['biaya_antar_jemput'], 0, ',', '.')) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-right font-semibold text-primary whitespace-nowrap">
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
                                                           class="<?= e(design_cn($inputClassSm, 'min-w-[7rem]')) ?>">
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
                                                           class="<?= e($inputClassSm) ?>">
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
                                            <span class="text-xs text-muted-foreground/70">Tidak ada aksi</span>
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
                <article class="<?= e($needsConfirm ? $listArticleWarning : $listArticle) ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-foreground truncate"><?= e((string) $booking['pelanggan_nama']) ?></p>
                            <p class="text-sm text-muted-foreground"><?= e((string) $booking['kucing_nama']) ?> · <?= e((string) $booking['jenis_nama']) ?></p>
                        </div>
                        <?php if ($statusEnum): ?>
                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-lg font-medium <?= e($statusEnum->badgeClass()) ?>">
                                <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <dl class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <dt class="text-xs text-muted-foreground">Jadwal</dt>
                            <dd class="font-medium text-foreground">
                                <?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?>
                                <?php if (!empty($booking['jam_grooming'])): ?>
                                    · <?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <div class="text-right">
                            <dt class="text-xs text-muted-foreground">Total</dt>
                            <dd class="font-heading text-primary">Rp <?= e(number_format($total, 0, ',', '.')) ?></dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-xs text-muted-foreground">Pengantaran</dt>
                            <dd class="text-foreground">
                                <?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?>
                                <?php if ($booking['opsi_pengantaran'] === OpsiPengantaran::ANTAR_JEMPUT->value && $booking['jarak_km'] !== null): ?>
                                    <span class="text-xs text-muted-foreground">
                                        (<?= e(number_format((float) $booking['jarak_km'], 2, ',', '.')) ?> km)
                                    </span>
                                <?php endif; ?>
                            </dd>
                        </div>
                    </dl>

                    <?php if (!empty($booking['catatan'])): ?>
                        <p class="rounded-lg bg-muted/50 px-2.5 py-1.5 text-xs text-muted-foreground italic">
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
                                <label class="block text-xs font-semibold text-foreground">Set jam grooming</label>
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
