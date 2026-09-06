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

$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
$btnPrimary = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-3.5 py-2 text-xs font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin disabled:opacity-60';

$renderCard = static function (array $booking) use ($statusLabels, $opsiLabels, $refundLabels, $minVaksin, $filterStatus, $filterCheckIn, $filterMonitoring): void {
    require __DIR__ . '/_card.php';
};
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
                <?php if ($autoFiltered && $filterMonitoring === 'belum_input'): ?>
                    <p class="mt-2 text-xs font-medium text-amber-800">
                        Menampilkan penitipan aktif yang belum diinput monitoring hari ini.
                    </p>
                <?php elseif ($autoFiltered): ?>
                    <p class="mt-2 text-xs font-medium text-amber-800">
                        Menampilkan booking yang menunggu konfirmasi terlebih dahulu.
                    </p>
                <?php endif; ?>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-admin text-white shadow-soft" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"/>
                </svg>
            </div>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <?php if ($bookingList !== [] || $hasFilter || $countMenungguGlobal > 0 || $countMenungguVerifikasi > 0 || $countBelumMonitoringGlobal > 0): ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            <a href="/admin/penitipan/booking?status=<?= e(StatusPenitipan::MENUNGGU_KONFIRMASI->value) ?>"
               class="block rounded-2xl border <?= $countMenungguGlobal > 0 ? 'border-amber-200 bg-warning-bg/40' : 'border-white/80 bg-card' ?> p-5 shadow-soft transition duration-soft hover:border-amber-400 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin cursor-pointer">
                <p class="text-sm text-content-secondary">Menunggu Konfirmasi</p>
                <p class="mt-1 font-heading text-2xl <?= $countMenungguGlobal > 0 ? 'text-amber-700' : 'text-admin' ?>"><?= e((string) $countMenungguGlobal) ?></p>
                <?php if ($countMenungguGlobal > 0): ?>
                    <p class="mt-1 text-xs text-content-secondary">Klik untuk filter</p>
                <?php endif; ?>
            </a>
            <a href="/admin/penitipan/pembayaran"
               class="block rounded-2xl border <?= $countMenungguVerifikasi > 0 ? 'border-amber-200 bg-warning-bg/40' : 'border-white/80 bg-card' ?> p-5 shadow-soft transition duration-soft hover:border-amber-400 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin cursor-pointer">
                <p class="text-sm text-content-secondary">Menunggu Verifikasi Bukti</p>
                <p class="mt-1 font-heading text-2xl <?= $countMenungguVerifikasi > 0 ? 'text-amber-700' : 'text-admin' ?>"><?= e((string) $countMenungguVerifikasi) ?></p>
                <?php if ($countMenungguVerifikasi > 0): ?>
                    <p class="mt-1 text-xs text-content-secondary">Klik untuk verifikasi</p>
                <?php endif; ?>
            </a>
            <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Siap / Check-in</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) $countCheckIn) ?></p>
            </article>
            <a href="/admin/penitipan/booking?status=<?= e(StatusPenitipan::SEDANG_DITITIPKAN->value) ?>"
               class="block rounded-2xl border border-white/80 bg-card p-5 shadow-soft transition duration-soft hover:border-success/40 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin cursor-pointer">
                <p class="text-sm text-content-secondary">Sedang Dititipkan</p>
                <p class="mt-1 font-heading text-2xl text-success"><?= e((string) $countAktif) ?></p>
                <?php if ($countAktif > 0): ?>
                    <p class="mt-1 text-xs text-content-secondary">Klik untuk filter</p>
                <?php endif; ?>
            </a>
            <a href="/admin/penitipan/booking?status=<?= e(StatusPenitipan::SEDANG_DITITIPKAN->value) ?>&monitoring=belum_input"
               class="block rounded-2xl border <?= $countBelumMonitoringGlobal > 0 ? 'border-amber-200 bg-warning-bg/40' : 'border-white/80 bg-card' ?> p-5 shadow-soft transition duration-soft hover:border-amber-400 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin cursor-pointer">
                <p class="text-sm text-content-secondary">Belum Input Monitoring</p>
                <p class="mt-1 font-heading text-2xl <?= $countBelumMonitoringGlobal > 0 ? 'text-amber-700' : 'text-admin' ?>"><?= e((string) $countBelumMonitoringGlobal) ?></p>
                <?php if ($countBelumMonitoringGlobal > 0): ?>
                    <p class="mt-1 text-xs text-content-secondary">Hari ini · klik untuk filter</p>
                <?php endif; ?>
            </a>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/penitipan/booking" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-heading text-lg text-content-primary">Filter</h2>
            <?php if ($hasFilter): ?>
                <a href="/admin/penitipan/booking?status=&check_in=&monitoring=" class="cursor-pointer text-xs font-semibold text-content-secondary hover:text-admin focus:outline-none focus-visible:underline">Reset</a>
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
            $ctaHref = '/admin/penitipan/booking?status=&check_in=&monitoring=';
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
        <?php if ($showPinnedPending && $pendingConfirmList !== []): ?>
            <section class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-heading text-lg text-content-primary">
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
                    <h2 class="font-heading text-lg text-content-primary">
                        Perlu Monitoring Hari Ini
                        <span class="ml-1 text-sm font-semibold text-amber-700">(<?= count($pendingMonitoringList) ?>)</span>
                    </h2>
                    <a href="/admin/penitipan/booking?status=<?= e(StatusPenitipan::SEDANG_DITITIPKAN->value) ?>&monitoring=belum_input"
                       class="cursor-pointer text-xs font-semibold text-admin hover:text-admin-hover focus:outline-none focus-visible:underline">
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
                    <h2 class="font-heading text-lg text-content-primary">Booking Lainnya</h2>
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
