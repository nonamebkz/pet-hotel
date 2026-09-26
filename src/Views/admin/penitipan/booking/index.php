<?php

declare(strict_types=1);

use App\Enums\StatusPenitipan;

$pendingConfirmList = $pendingConfirmList ?? [];
$pendingMonitoringList = $pendingMonitoringList ?? [];
$otherBookingList = $otherBookingList ?? [];
$bookingList = $bookingList ?? [];
$statusLabels = $statusLabels ?? [];
$opsiLabels = $opsiLabels ?? [];
$filterStatus = $filterStatus ?? '';
$filterCheckIn = $filterCheckIn ?? '';
$filterMonitoring = $filterMonitoring ?? '';
$refundLabels = $refundLabels ?? [];
$minVaksin = $minVaksin ?? 1;
$countMenungguGlobal = $countMenungguGlobal ?? 0;
$countMenungguVerifikasi = $countMenungguVerifikasi ?? 0;
$countBelumMonitoringGlobal = $countBelumMonitoringGlobal ?? 0;
$countSedangDitipkanGlobal = $countSedangDitipkanGlobal ?? 0;
$autoFiltered = $autoFiltered ?? false;
$hasFilter = $filterStatus !== '' || $filterCheckIn !== '' || $filterMonitoring !== '';
$showPinnedPending = $filterStatus === '' && $filterMonitoring === '' && ($pendingConfirmList !== [] || $pendingMonitoringList !== []);

$countCheckIn = 0;
foreach ($bookingList as $statRow) {
    $st = (string) ($statRow['status'] ?? '');
    if ($st === StatusPenitipan::CHECK_IN->value || ($st === StatusPenitipan::MENUNGGU_VERIFIKASI_BUKTI->value && !empty($statRow['transaksi_lunas']))) {
        $countCheckIn++;
    }
}
$countAktif = $countSedangDitipkanGlobal;

$filterFormClass = design_cn(design_surface('metric'), 'p-5 sm:p-6');
$metricCardClass = design_cn(design_surface('metric'), 'p-5');
$metricLinkClass = design_cn(design_interactive('cardLink'), 'p-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring');

$inputClass = ui_form_input_class();
$btnPrimary = design_cn(ui_btn_primary(), 'min-h-[42px] px-5 disabled:opacity-60');

$renderCard = static function (array $booking) use ($statusLabels, $opsiLabels, $refundLabels, $minVaksin, $filterStatus, $filterCheckIn, $filterMonitoring): void {
    require __DIR__ . '/_card.php';
};
?>
<?php
$headerDesc = 'Konfirmasi booking, check-in, monitoring harian, dan kelola refund.';
if ($autoFiltered && $filterMonitoring === 'belum_input') {
    $headerDesc .= ' Menampilkan penitipan aktif yang belum diinput monitoring hari ini.';
} elseif ($autoFiltered) {
    $headerDesc .= ' Menampilkan booking yang menunggu konfirmasi terlebih dahulu.';
}
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Booking Penitipan',
        'Penitipan',
        $headerDesc,
    );
    ?>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <?php if ($bookingList !== [] || $hasFilter || $countMenungguGlobal > 0 || $countMenungguVerifikasi > 0 || $countBelumMonitoringGlobal > 0): ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <a href="/admin/penitipan/booking?status=<?= e(StatusPenitipan::MENUNGGU_KONFIRMASI->value) ?>"
               class="<?= e(design_cn($metricLinkClass, $countMenungguGlobal > 0 ? 'border-amber-200 bg-warning-bg/40 hover:border-amber-400' : '')) ?>">
                <p class="text-sm text-muted-foreground">Menunggu Konfirmasi</p>
                <p class="mt-1 font-heading text-2xl tabular-nums <?= $countMenungguGlobal > 0 ? 'text-amber-700 dark:text-amber-300' : 'text-primary' ?>"><?= e((string) $countMenungguGlobal) ?></p>
                <?php if ($countMenungguGlobal > 0): ?>
                    <p class="mt-1 text-xs text-muted-foreground">Klik untuk filter</p>
                <?php endif; ?>
            </a>
            <a href="/admin/penitipan/pembayaran"
               class="<?= e(design_cn($metricLinkClass, $countMenungguVerifikasi > 0 ? 'border-amber-200 bg-warning-bg/40 hover:border-amber-400' : '')) ?>">
                <p class="text-sm text-muted-foreground">Menunggu Verifikasi Bukti</p>
                <p class="mt-1 font-heading text-2xl <?= $countMenungguVerifikasi > 0 ? 'text-amber-700' : 'text-primary' ?>"><?= e((string) $countMenungguVerifikasi) ?></p>
                <?php if ($countMenungguVerifikasi > 0): ?>
                    <p class="mt-1 text-xs text-muted-foreground">Klik untuk verifikasi</p>
                <?php endif; ?>
            </a>
            <article class="<?= e($metricCardClass) ?>">
                <p class="text-sm text-muted-foreground">Siap / Check-in</p>
                <p class="mt-1 font-heading text-2xl text-primary"><?= e((string) $countCheckIn) ?></p>
            </article>
            <a href="/admin/penitipan/booking?status=<?= e(StatusPenitipan::SEDANG_DITITIPKAN->value) ?>"
               class="<?= e(design_cn($metricLinkClass, 'hover:border-emerald-500/40')) ?>">
                <p class="text-sm text-muted-foreground">Sedang Dititipkan</p>
                <p class="mt-1 font-heading text-2xl text-success"><?= e((string) $countAktif) ?></p>
                <?php if ($countAktif > 0): ?>
                    <p class="mt-1 text-xs text-muted-foreground">Klik untuk filter</p>
                <?php endif; ?>
            </a>
            <a href="/admin/penitipan/booking?status=<?= e(StatusPenitipan::SEDANG_DITITIPKAN->value) ?>&monitoring=belum_input"
               class="<?= e(design_cn($metricLinkClass, $countBelumMonitoringGlobal > 0 ? 'border-amber-200 bg-warning-bg/40 hover:border-amber-400' : '')) ?>">
                <p class="text-sm text-muted-foreground">Belum Input Monitoring</p>
                <p class="mt-1 font-heading text-2xl <?= $countBelumMonitoringGlobal > 0 ? 'text-amber-700' : 'text-primary' ?>"><?= e((string) $countBelumMonitoringGlobal) ?></p>
                <?php if ($countBelumMonitoringGlobal > 0): ?>
                    <p class="mt-1 text-xs text-muted-foreground">Hari ini · klik untuk filter</p>
                <?php endif; ?>
            </a>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/penitipan/booking" class="<?= e($filterFormClass) ?>">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-heading text-lg text-foreground">Filter</h2>
            <?php if ($hasFilter): ?>
                <a href="/admin/penitipan/booking?status=&check_in=&monitoring=" class="cursor-pointer text-xs font-semibold text-muted-foreground hover:text-primary focus:outline-none focus-visible:underline">Reset</a>
            <?php endif; ?>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="check_in" class="mb-1.5 block text-sm font-semibold text-foreground">Check-in</label>
                <input type="date" id="check_in" name="check_in" value="<?= e($filterCheckIn) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-foreground">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?>">
                    <option value="">Semua status</option>
                    <?php foreach ($statusLabels as $v => $l): ?>
                        <option value="<?= e($v) ?>" <?= $filterStatus === $v ? 'selected' : '' ?>><?= e($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end sm:col-span-2">
                <button type="submit" class="<?= e($btnPrimary) ?>">Terapkan Filter</button>
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
            $ctaHref = '/admin/penitipan/booking?status=&check_in=&monitoring=';
            $ctaClass = ui_btn_primary();
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
        <?php if ($showPinnedPending && $pendingConfirmList !== []): ?>
            <section class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-heading text-lg text-foreground">
                        Perlu Konfirmasi
                        <span class="ml-1 text-sm font-semibold text-amber-700">(<?= count($pendingConfirmList) ?>)</span>
                    </h2>
                </div>
                <div class="space-y-4">
                    <?php foreach ($pendingConfirmList as $booking): ?>
                        <?php $renderCard($booking); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($showPinnedPending && $pendingMonitoringList !== []): ?>
            <section class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-heading text-lg text-foreground">
                        Perlu Monitoring Hari Ini
                        <span class="ml-1 text-sm font-semibold text-amber-700">(<?= count($pendingMonitoringList) ?>)</span>
                    </h2>
                    <a href="/admin/penitipan/booking?status=<?= e(StatusPenitipan::SEDANG_DITITIPKAN->value) ?>&monitoring=belum_input"
                       class="cursor-pointer text-xs font-semibold text-primary hover:opacity-90 focus:outline-none focus-visible:underline">
                        Lihat semua →
                    </a>
                </div>
                <div class="space-y-4">
                    <?php foreach ($pendingMonitoringList as $booking): ?>
                        <?php $renderCard($booking); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($otherBookingList !== []): ?>
            <section class="space-y-3">
                <?php if ($showPinnedPending && ($pendingConfirmList !== [] || $pendingMonitoringList !== [])): ?>
                    <h2 class="font-heading text-lg text-foreground">Booking Lainnya</h2>
                <?php endif; ?>
                <div class="space-y-4">
                    <?php foreach ($otherBookingList as $booking): ?>
                        <?php $renderCard($booking); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!$showPinnedPending && $otherBookingList === [] && $pendingConfirmList !== []): ?>
            <div class="space-y-4">
                <?php foreach ($pendingConfirmList as $booking): ?>
                    <?php $renderCard($booking); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
