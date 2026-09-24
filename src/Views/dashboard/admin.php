<?php

declare(strict_types=1);

use App\Enums\StaffRole;

$today = $today ?? date('Y-m-d');
$bookingsToday = $bookingsToday ?? ['grooming' => 0, 'penitipan' => 0, 'pet_care' => 0, 'total' => 0];
$bookingsYesterday = $bookingsYesterday ?? ['total' => 0];
$pendingVerification = $pendingVerification ?? ['grooming' => 0, 'penitipan' => 0, 'total' => 0];
$pendingPenitipanConfirmation = $pendingPenitipanConfirmation ?? 0;
$pendingPenitipanConfirmationPreview = $pendingPenitipanConfirmationPreview ?? [];
$pendingMonitoringPenitipan = $pendingMonitoringPenitipan ?? 0;
$pendingMonitoringPenitipanPreview = $pendingMonitoringPenitipanPreview ?? [];
$penitipanAktif = $penitipanAktif ?? 0;
$pendapatan = $pendapatan ?? ['harian' => 0.0, 'kemarin' => 0.0, 'mingguan' => 0.0, 'mingguMulai' => $today, 'mingguAkhir' => $today];
$pendingVerificationPreview = $pendingVerificationPreview ?? [];

$bookingDelta = (int) $bookingsToday['total'] - (int) $bookingsYesterday['total'];
$revenueDelta = (float) $pendapatan['harian'] - (float) ($pendapatan['kemarin'] ?? 0);
$hasPending = (int) $pendingVerification['total'] > 0;
$hasPendingConfirmation = $pendingPenitipanConfirmation > 0;
$hasPendingMonitoring = $pendingMonitoringPenitipan > 0;
$hasActionCenter = $hasPending || $hasPendingConfirmation || $hasPendingMonitoring;
$monitoringQuickUrl = '/admin/penitipan/booking?status=SEDANG_DITITIPKAN&monitoring=belum_input';
if ($pendingMonitoringPenitipanPreview !== []) {
    $monitoringQuickUrl = (string) $pendingMonitoringPenitipanPreview[0]['url'];
} elseif ($penitipanAktif > 0 && !$hasPendingMonitoring) {
    $monitoringQuickUrl = '/admin/penitipan/booking?status=SEDANG_DITITIPKAN';
}
$isOwner = ($role ?? null) === StaffRole::OWNER || ($role ?? null)?->value === 'OWNER';

$hariIndo = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
];
$bulanIndo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];
$ts = strtotime($today) ?: time();
$tanggalLabel = ($hariIndo[date('l', $ts)] ?? date('l', $ts))
    . ', ' . date('j', $ts) . ' ' . ($bulanIndo[(int) date('n', $ts)] ?? date('F', $ts)) . ' ' . date('Y', $ts);

$actionListItemClass = design_cn(
    design_interactive('listArticle'),
    design_interactive('listRowHover'),
    'flex items-start justify-between gap-3 border-amber-200/80 bg-card px-4 py-3',
);
$actionSubpanelClass = design_cn(
    design_surface('panel'),
    'border-amber-200/80 bg-card/90 p-4 sm:p-5 space-y-4',
);
$quickCardClass = static function (bool $urgent = false, bool $success = false): string {
    $tone = 'border-border bg-muted/30';
    if ($urgent) {
        $tone = 'border-amber-200 bg-amber-500/5';
    } elseif ($success) {
        $tone = 'border-emerald-500/30 bg-emerald-500/5';
    }

    return design_cn(
        design_interactive('cardLink'),
        'flex flex-col gap-3 p-4',
        $tone,
    );
};
?>
<div class="font-body space-y-6 md:space-y-8">
    <section class="<?= e(design_cn(design_surface('panel'), 'relative overflow-hidden p-6 sm:p-8')) ?>">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-primary/5 blur-2xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-10 h-40 w-40 rounded-full bg-emerald-500/10 blur-2xl" aria-hidden="true"></div>

        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">
                    Dashboard <?= e($roleLabel ?? 'Internal') ?>
                </p>
                <h1 class="mt-1 font-heading text-2xl md:text-3xl font-bold tracking-tight text-foreground">
                    Selamat datang, <?= e((string) $nama) ?>
                </h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Ringkasan operasional — <?= e($tanggalLabel) ?>
                </p>
            </div>
            <div class="<?= e(design_icon_badge('default', 'h-12 w-12 shrink-0 rounded-2xl p-0')) ?>" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                </svg>
            </div>
        </div>
    </section>

    <?php if ($hasActionCenter): ?>
        <section
            class="<?= e(design_cn(design_advice_panel_surface('warning'), design_surface('panel'), 'space-y-6 p-6 sm:p-7')) ?>"
            aria-labelledby="action-center-title"
        >
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="<?= e(design_icon_badge('warning', 'h-11 w-11 shrink-0 rounded-xl')) ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </span>
                    <div>
                        <div class="mb-1 flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-800 dark:text-amber-200">Action Center</span>
                            <?php if ($hasPending): ?>
                                <span class="<?= e(design_cn('rounded-full px-2.5 py-1 text-xs font-medium', 'bg-amber-500/10 text-amber-700 dark:text-amber-300')) ?>">
                                    <?= e((string) $pendingVerification['total']) ?> verifikasi
                                </span>
                            <?php endif; ?>
                        </div>
                        <h2 id="action-center-title" class="font-heading text-lg font-semibold text-foreground">
                            Perlu Tindakan Staff
                        </h2>
                    </div>
                </div>
            </div>

            <?php if ($hasPending): ?>
                <div class="<?= e($actionSubpanelClass) ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-base font-semibold text-foreground">Bukti Transfer Menunggu Verifikasi</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Grooming <?= e((string) $pendingVerification['grooming']) ?>
                                · Penitipan <?= e((string) $pendingVerification['penitipan']) ?>
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <?php if ($pendingVerification['grooming'] > 0): ?>
                                <a href="/admin/grooming/pembayaran" class="<?= e(ui_btn_primary()) ?>">
                                    Verifikasi Grooming
                                </a>
                            <?php endif; ?>
                            <?php if ($pendingVerification['penitipan'] > 0): ?>
                                <a href="/admin/penitipan/pembayaran" class="<?= e(ui_btn_secondary()) ?>">
                                    Verifikasi Penitipan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($pendingVerificationPreview !== []): ?>
                        <ul class="space-y-2">
                            <?php foreach ($pendingVerificationPreview as $item): ?>
                                <li>
                                    <a href="<?= e((string) $item['url']) ?>" class="<?= e($actionListItemClass) ?>">
                                        <div class="min-w-0">
                                            <div class="truncate font-semibold text-foreground"><?= e((string) $item['pelanggan_nama']) ?></div>
                                            <div class="text-sm text-muted-foreground"><?= e((string) $item['layanan_label']) ?></div>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <div class="text-sm font-semibold tabular-nums text-primary">
                                                Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($hasPendingConfirmation): ?>
                <div class="<?= e($actionSubpanelClass) ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-base font-semibold text-foreground">Konfirmasi Penitipan Baru</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                <?= e((string) $pendingPenitipanConfirmation) ?> booking menunggu konfirmasi staff.
                            </p>
                        </div>
                        <a href="/admin/penitipan/booking?status=MENUNGGU_KONFIRMASI" class="<?= e(ui_btn_primary()) ?>">
                            Review Booking
                        </a>
                    </div>
                    <?php if ($pendingPenitipanConfirmationPreview !== []): ?>
                        <ul class="space-y-2">
                            <?php foreach ($pendingPenitipanConfirmationPreview as $item): ?>
                                <li>
                                    <a href="<?= e((string) $item['url']) ?>" class="<?= e($actionListItemClass) ?>">
                                        <div class="min-w-0">
                                            <div class="truncate font-semibold text-foreground">
                                                <?= e((string) $item['pelanggan_nama']) ?> · <?= e((string) $item['kucing_nama']) ?>
                                            </div>
                                            <div class="text-sm text-muted-foreground">
                                                Check-in <?= e(date('d/m/Y', strtotime((string) $item['check_in']))) ?>
                                                · <?= (int) $item['lama_hari'] ?> hari
                                            </div>
                                        </div>
                                        <div class="shrink-0 text-sm font-semibold tabular-nums text-primary">
                                            Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($hasPendingMonitoring): ?>
                <div class="<?= e($actionSubpanelClass) ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-base font-semibold text-foreground">Monitoring Harian Belum Diinput</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                <?= e((string) $pendingMonitoringPenitipan) ?> kucing belum diinput monitoring hari ini.
                            </p>
                        </div>
                        <a href="/admin/penitipan/booking?status=SEDANG_DITITIPKAN&monitoring=belum_input" class="<?= e(ui_btn_primary()) ?>">
                            Input Monitoring
                        </a>
                    </div>
                    <?php if ($pendingMonitoringPenitipanPreview !== []): ?>
                        <ul class="space-y-2">
                            <?php foreach ($pendingMonitoringPenitipanPreview as $item): ?>
                                <li>
                                    <a href="<?= e((string) $item['url']) ?>" class="<?= e($actionListItemClass) ?>">
                                        <div class="min-w-0">
                                            <div class="truncate font-semibold text-foreground">
                                                <?= e((string) $item['pelanggan_nama']) ?> · <?= e((string) $item['kucing_nama']) ?>
                                            </div>
                                            <div class="text-sm text-muted-foreground">
                                                Check-out <?= e(date('d/m/Y', strtotime((string) $item['check_out']))) ?>
                                            </div>
                                        </div>
                                        <span class="shrink-0 text-sm font-semibold text-primary">Input →</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <?php
        $variant = 'success';
        $title = 'Semua tindakan operasional sudah beres';
        $description = 'Tidak ada booking penitipan menunggu konfirmasi, bukti transfer menunggu verifikasi, atau monitoring harian yang belum diinput.';
        $ctaLabel = 'Lihat riwayat transaksi';
        $ctaHref = '/admin/transaksi';
        require __DIR__ . '/../partials/ui/empty-state.php';
        ?>
    <?php endif; ?>

    <section aria-labelledby="kpi-heading" class="space-y-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">KPI</p>
            <h2 id="kpi-heading" class="text-base font-semibold text-foreground">Ringkasan Hari Ini</h2>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <article class="<?= e(design_cn(design_surface('metric'), 'p-5')) ?>">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <span class="<?= e(design_icon_badge('default')) ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                    </span>
                    <span class="<?= e(design_status_badge($bookingDelta >= 0 ? 'success' : 'danger')) ?> tabular-nums">
                        <?= $bookingDelta >= 0 ? '+' : '' ?><?= e((string) $bookingDelta) ?>
                    </span>
                </div>
                <p class="text-sm text-muted-foreground">Booking Hari Ini</p>
                <p class="mt-1 font-heading text-3xl font-semibold tabular-nums text-primary"><?= e((string) $bookingsToday['total']) ?></p>
                <p class="mt-2 text-xs text-muted-foreground">
                    <?php if ((int) $bookingsToday['total'] === 0): ?>
                        Belum ada booking hari ini
                    <?php else: ?>
                        Grooming <?= e((string) $bookingsToday['grooming']) ?>
                        · Penitipan <?= e((string) $bookingsToday['penitipan']) ?>
                        · Pet Care <?= e((string) $bookingsToday['pet_care']) ?>
                    <?php endif; ?>
                </p>
                <p class="mt-1 text-xs text-muted-foreground">vs kemarin</p>
            </article>

            <article class="<?= e(design_cn(
                design_surface('metric'),
                'p-5',
                $hasPending ? design_advice_panel_surface('warning') : '',
            )) ?>">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <span class="<?= e(design_icon_badge($hasPending ? 'warning' : 'default')) ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                        </svg>
                    </span>
                    <?php if ($hasPending): ?>
                        <span class="<?= e(design_cn('rounded-full px-2.5 py-1 text-xs font-medium', 'bg-amber-500/10 text-amber-700 dark:text-amber-300')) ?>"><?= e((string) $pendingVerification['total']) ?></span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-muted-foreground">Menunggu Verifikasi</p>
                <p class="mt-1 font-heading text-3xl font-semibold tabular-nums <?= $hasPending ? 'text-amber-700 dark:text-amber-300' : 'text-primary' ?>">
                    <?= e((string) $pendingVerification['total']) ?>
                </p>
                <p class="mt-2 text-xs text-muted-foreground">Prioritas operasional hari ini</p>
                <a href="/admin/grooming/pembayaran" class="mt-3 inline-flex text-xs font-semibold text-primary hover:opacity-90">
                    Kelola verifikasi →
                </a>
            </article>

            <a
                href="/admin/penitipan/booking?status=SEDANG_DITITIPKAN"
                class="<?= e(design_cn(design_interactive('cardLink'), 'block p-5')) ?>"
            >
                <div class="mb-4 flex items-start justify-between gap-3">
                    <span class="<?= e(design_icon_badge('success')) ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"/>
                        </svg>
                    </span>
                </div>
                <p class="text-sm text-muted-foreground">Penitipan Aktif</p>
                <p class="mt-1 font-heading text-3xl font-semibold tabular-nums text-primary"><?= e((string) $penitipanAktif) ?></p>
                <p class="mt-2 text-xs text-muted-foreground">
                    <?= $penitipanAktif === 0 ? 'Belum ada penitipan aktif' : 'Check-in & sedang dititipkan' ?>
                </p>
            </a>

            <a
                href="<?= e($monitoringQuickUrl) ?>"
                class="<?= e(design_cn(
                    design_interactive('cardLink'),
                    'block p-5',
                    $hasPendingMonitoring ? design_advice_panel_surface('warning') : '',
                )) ?>"
            >
                <div class="mb-4 flex items-start justify-between gap-3">
                    <span class="<?= e(design_icon_badge($hasPendingMonitoring ? 'warning' : 'default')) ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                        </svg>
                    </span>
                    <?php if ($hasPendingMonitoring): ?>
                        <span class="text-xs font-medium text-amber-700 dark:text-amber-300">Perlu input →</span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-muted-foreground">Belum Input Monitoring</p>
                <p class="mt-1 font-heading text-3xl font-semibold tabular-nums <?= $hasPendingMonitoring ? 'text-amber-700 dark:text-amber-300' : 'text-primary' ?>">
                    <?= e((string) $pendingMonitoringPenitipan) ?>
                </p>
                <p class="mt-2 text-xs text-muted-foreground">Hari ini · klik untuk input</p>
            </a>

            <article class="<?= e(design_cn(design_surface('metric'), 'p-5 sm:col-span-2 lg:col-span-1')) ?>">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <span class="<?= e(design_icon_badge('default')) ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                        </svg>
                    </span>
                    <span class="<?= e(design_status_badge($revenueDelta >= 0 ? 'success' : 'danger')) ?> tabular-nums">
                        <?= $revenueDelta >= 0 ? '+' : '−' ?>Rp <?= e(number_format(abs($revenueDelta), 0, ',', '.')) ?>
                    </span>
                </div>
                <p class="text-sm text-muted-foreground">Pendapatan Terverifikasi</p>
                <p class="mt-1 font-heading text-xl font-semibold leading-snug tabular-nums text-primary">
                    <?php if ((float) $pendapatan['harian'] <= 0): ?>
                        Belum ada transaksi hari ini
                    <?php else: ?>
                        Rp <?= e(number_format((float) $pendapatan['harian'], 0, ',', '.')) ?>
                    <?php endif; ?>
                </p>
                <p class="mt-1 text-xs text-muted-foreground">Hari ini · vs kemarin</p>
                <p class="mt-3 text-sm font-semibold tabular-nums text-foreground">
                    Minggu ini: Rp <?= e(number_format((float) $pendapatan['mingguan'], 0, ',', '.')) ?>
                </p>
                <a href="/admin/laporan" class="mt-2 inline-flex text-xs font-semibold text-primary hover:opacity-90">
                    Laporan detail →
                </a>
            </article>
        </div>
    </section>

    <section aria-labelledby="insights-heading" class="space-y-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Insights</p>
            <h2 id="insights-heading" class="text-base font-semibold text-foreground">Tren & Konteks</h2>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <article class="<?= e(design_cn(design_surface('chart'), 'space-y-4 p-5')) ?>">
                <h3 class="text-sm font-semibold text-foreground">Booking vs Kemarin</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="<?= e(design_interactive('metricCell')) ?>">
                        <p class="text-xs text-muted-foreground">Hari ini</p>
                        <p class="mt-1 text-2xl font-semibold tabular-nums text-primary"><?= e((string) $bookingsToday['total']) ?></p>
                    </div>
                    <div class="<?= e(design_interactive('metricCell')) ?>">
                        <p class="text-xs text-muted-foreground">Kemarin</p>
                        <p class="mt-1 text-2xl font-semibold tabular-nums text-foreground"><?= e((string) $bookingsYesterday['total']) ?></p>
                    </div>
                </div>
                <p class="text-xs text-muted-foreground">
                    <?php if ($bookingDelta > 0): ?>
                        Naik <?= e((string) $bookingDelta) ?> booking dibanding kemarin.
                    <?php elseif ($bookingDelta < 0): ?>
                        Turun <?= e((string) abs($bookingDelta)) ?> booking dibanding kemarin.
                    <?php else: ?>
                        Sama dengan kemarin.
                    <?php endif; ?>
                </p>
                <div class="flex flex-wrap gap-2 text-xs text-muted-foreground">
                    <span class="<?= e(design_status_badge('muted')) ?>">Grooming <?= e((string) $bookingsToday['grooming']) ?></span>
                    <span class="<?= e(design_status_badge('muted')) ?>">Penitipan <?= e((string) $bookingsToday['penitipan']) ?></span>
                    <span class="<?= e(design_status_badge('muted')) ?>">Pet Care <?= e((string) $bookingsToday['pet_care']) ?></span>
                </div>
            </article>

            <article class="<?= e(design_cn(design_surface('chart'), 'flex flex-col gap-4 p-5')) ?>">
                <h3 class="text-sm font-semibold text-foreground">Pendapatan Terverifikasi</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="<?= e(design_interactive('metricCellOutlined')) ?>">
                        <p class="text-xs text-muted-foreground">Hari ini</p>
                        <p class="mt-1 text-lg font-semibold tabular-nums text-primary">
                            Rp <?= e(number_format((float) $pendapatan['harian'], 0, ',', '.')) ?>
                        </p>
                    </div>
                    <div class="<?= e(design_interactive('metricCellOutlined')) ?>">
                        <p class="text-xs text-muted-foreground">Minggu ini</p>
                        <p class="mt-1 text-lg font-semibold tabular-nums text-foreground">
                            Rp <?= e(number_format((float) $pendapatan['mingguan'], 0, ',', '.')) ?>
                        </p>
                    </div>
                </div>
                <p class="text-xs text-muted-foreground">
                    Periode minggu: <?= e(date('d/m', strtotime((string) $pendapatan['mingguMulai']))) ?>
                    – <?= e(date('d/m/Y', strtotime((string) $pendapatan['mingguAkhir']))) ?>
                </p>
                <a href="/admin/laporan" class="<?= e(ui_btn_secondary()) ?> w-full sm:w-auto">
                    Buka laporan lengkap
                </a>
            </article>
        </div>
    </section>

    <section aria-labelledby="quick-actions-heading" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6')) ?>">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">Operasional</p>
                <h2 id="quick-actions-heading" class="font-heading text-lg font-semibold text-foreground">Aksi Cepat</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <a href="<?= e($monitoringQuickUrl) ?>" class="<?= e($quickCardClass($hasPendingMonitoring)) ?> relative">
                <span class="<?= e(design_icon_badge($hasPendingMonitoring ? 'warning' : ($penitipanAktif > 0 ? 'success' : 'default'))) ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="font-semibold text-foreground">Monitoring Penitipan</p>
                    <p class="mt-0.5 text-xs text-muted-foreground">
                        <?php if ($hasPendingMonitoring): ?>
                            <?= e((string) $pendingMonitoringPenitipan) ?> kucing belum diinput
                        <?php elseif ($penitipanAktif > 0): ?>
                            Semua sudah diinput hari ini · <?= e((string) $penitipanAktif) ?> aktif
                        <?php else: ?>
                            Belum ada penitipan aktif
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($hasPendingMonitoring): ?>
                    <span class="absolute right-3 top-3 <?= e(design_cn('inline-flex min-w-[1.5rem] items-center justify-center rounded-full px-2 py-0.5 text-xs font-bold', 'bg-amber-500/10 text-amber-700 dark:text-amber-300')) ?>">
                        <?= e((string) $pendingMonitoringPenitipan) ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="/admin/penitipan/booking?status=MENUNGGU_KONFIRMASI" class="<?= e($quickCardClass($hasPendingConfirmation)) ?>">
                <span class="<?= e(design_icon_badge($hasPendingConfirmation ? 'warning' : 'default')) ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <div>
                    <p class="font-semibold text-foreground">Konfirmasi Penitipan</p>
                    <p class="mt-0.5 text-xs text-muted-foreground">
                        <?= $hasPendingConfirmation ? e((string) $pendingPenitipanConfirmation) . ' menunggu review' : 'Tidak ada antrian' ?>
                    </p>
                </div>
            </a>

            <a href="/admin/penitipan/pembayaran" class="<?= e($quickCardClass(($pendingVerification['penitipan'] ?? 0) > 0)) ?>">
                <span class="<?= e(design_icon_badge(($pendingVerification['penitipan'] ?? 0) > 0 ? 'warning' : 'default')) ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                </span>
                <div>
                    <p class="font-semibold text-foreground">Verifikasi Bukti Penitipan</p>
                    <p class="mt-0.5 text-xs text-muted-foreground">
                        <?= (int) ($pendingVerification['penitipan'] ?? 0) > 0 ? e((string) $pendingVerification['penitipan']) . ' bukti menunggu' : 'Tidak ada antrian' ?>
                    </p>
                </div>
            </a>

            <a href="/admin/penitipan/booking?status=SEDANG_DITITIPKAN" class="<?= e($quickCardClass(false, true)) ?>">
                <span class="<?= e(design_icon_badge('success')) ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"/>
                    </svg>
                </span>
                <div>
                    <p class="font-semibold text-foreground">Penitipan Aktif</p>
                    <p class="mt-0.5 text-xs text-muted-foreground"><?= e((string) $penitipanAktif) ?> kucing sedang dititipkan</p>
                </div>
            </a>
        </div>
    </section>

    <?php if ($isOwner): ?>
        <aside class="<?= e(design_cn(design_alert_inline('warning'), 'flex flex-wrap items-center justify-between gap-3 px-5 py-4 text-sm')) ?>">
            <div class="flex min-w-0 items-start gap-3">
                <span class="<?= e(design_icon_badge('warning', 'mt-0.5 h-9 w-9 shrink-0 rounded-xl p-0')) ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </span>
                <p class="text-amber-950 dark:text-amber-100">
                    Anda login sebagai <strong class="font-semibold">Owner</strong> — akses penuh operasional staff + manajemen akun staff.
                </p>
            </div>
            <a href="/admin/staff" class="<?= e(ui_btn_secondary()) ?> shrink-0">
                Manajemen Staff →
            </a>
        </aside>
    <?php endif; ?>
</div>
