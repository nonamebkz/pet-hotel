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
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card shadow-soft p-6 sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-admin/5 blur-2xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-10 h-40 w-40 rounded-full bg-success/10 blur-2xl" aria-hidden="true"></div>

        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">
                    Dashboard <?= e($roleLabel ?? 'Internal') ?>
                </p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">
                    Selamat datang, <?= e((string) $nama) ?>
                </h1>
                <p class="mt-2 text-sm text-content-secondary">
                    Ringkasan operasional — <?= e($tanggalLabel) ?>
                </p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-admin text-white shadow-soft" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                </svg>
            </div>
        </div>
    </section>

    <section aria-labelledby="quick-actions-heading" class="rounded-2xl border border-white/80 bg-card shadow-soft p-5 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Operasional</p>
                <h2 id="quick-actions-heading" class="font-heading text-lg text-content-primary">Aksi Cepat</h2>
            </div>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <a href="<?= e($monitoringQuickUrl) ?>"
               class="group relative flex flex-col gap-3 rounded-xl border p-4 shadow-soft transition duration-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2 <?= $hasPendingMonitoring ? 'border-amber-200 bg-warning-bg/40 hover:border-amber-400 hover:shadow-md' : ($penitipanAktif > 0 ? 'border-success/30 bg-success-bg/20 hover:border-success/50 hover:shadow-md' : 'border-white/80 bg-page/40 hover:border-admin/20 hover:shadow-md') ?>">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl <?= $hasPendingMonitoring ? 'bg-amber-100 text-amber-700' : ($penitipanAktif > 0 ? 'bg-success-bg text-success' : 'bg-admin-soft text-admin') ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="font-semibold text-content-primary group-hover:text-admin transition duration-soft">
                        <?= $hasPendingMonitoring ? 'Input Monitoring Hari Ini' : ($penitipanAktif > 0 ? 'Monitoring Penitipan' : 'Monitoring Penitipan') ?>
                    </p>
                    <p class="mt-0.5 text-xs text-content-secondary">
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
                    <span class="absolute top-3 right-3 inline-flex min-w-[1.5rem] items-center justify-center rounded-full bg-amber-600 px-2 py-0.5 text-xs font-bold text-white">
                        <?= e((string) $pendingMonitoringPenitipan) ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="/admin/penitipan/booking?status=MENUNGGU_KONFIRMASI"
               class="group flex flex-col gap-3 rounded-xl border p-4 shadow-soft transition duration-soft hover:border-admin/20 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2 <?= $hasPendingConfirmation ? 'border-amber-200 bg-warning-bg/25' : 'border-white/80 bg-page/40' ?>">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl <?= $hasPendingConfirmation ? 'bg-amber-100 text-amber-700' : 'bg-admin-soft text-admin' ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <div>
                    <p class="font-semibold text-content-primary">Konfirmasi Penitipan</p>
                    <p class="mt-0.5 text-xs text-content-secondary">
                        <?= $hasPendingConfirmation ? e((string) $pendingPenitipanConfirmation) . ' menunggu review' : 'Tidak ada antrian' ?>
                    </p>
                </div>
            </a>

            <a href="/admin/penitipan/pembayaran"
               class="group flex flex-col gap-3 rounded-xl border p-4 shadow-soft transition duration-soft hover:border-admin/20 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2 <?= ($pendingVerification['penitipan'] ?? 0) > 0 ? 'border-amber-200 bg-warning-bg/25' : 'border-white/80 bg-page/40' ?>">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl <?= ($pendingVerification['penitipan'] ?? 0) > 0 ? 'bg-amber-100 text-amber-700' : 'bg-admin-soft text-admin' ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                </span>
                <div>
                    <p class="font-semibold text-content-primary">Verifikasi Bukti Penitipan</p>
                    <p class="mt-0.5 text-xs text-content-secondary">
                        <?= (int) ($pendingVerification['penitipan'] ?? 0) > 0 ? e((string) $pendingVerification['penitipan']) . ' bukti menunggu' : 'Tidak ada antrian' ?>
                    </p>
                </div>
            </a>

            <a href="/admin/penitipan/booking?status=SEDANG_DITITIPKAN"
               class="group flex flex-col gap-3 rounded-xl border border-white/80 bg-page/40 p-4 shadow-soft transition duration-soft hover:border-success/30 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-success-bg text-success">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"/>
                    </svg>
                </span>
                <div>
                    <p class="font-semibold text-content-primary">Penitipan Aktif</p>
                    <p class="mt-0.5 text-xs text-content-secondary"><?= e((string) $penitipanAktif) ?> kucing sedang dititipkan</p>
                </div>
            </a>
        </div>
    </section>

    <?php if ($hasActionCenter): ?>
        <section class="rounded-2xl border border-warning/40 bg-warning-bg/80 shadow-soft p-6 sm:p-7 space-y-6" aria-labelledby="action-center-title">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 shadow-soft">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </span>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-xs font-semibold uppercase tracking-wider text-amber-800">Action Center</span>
                        </div>
                        <h2 id="action-center-title" class="font-heading text-lg text-content-primary">
                            Perlu Tindakan Staff
                        </h2>
                    </div>
                </div>
            </div>

            <?php if ($hasPendingConfirmation): ?>
                <div class="rounded-xl border border-amber-200/80 bg-white/80 p-4 sm:p-5 space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-base text-content-primary">Konfirmasi Penitipan Baru</h3>
                            <p class="mt-1 text-sm text-content-secondary">
                                <?= e((string) $pendingPenitipanConfirmation) ?> booking menunggu konfirmasi staff.
                            </p>
                        </div>
                        <a href="/admin/penitipan/booking?status=MENUNGGU_KONFIRMASI"
                           class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover">
                            Review Booking
                        </a>
                    </div>
                    <?php if ($pendingPenitipanConfirmationPreview !== []): ?>
                        <ul class="space-y-2">
                            <?php foreach ($pendingPenitipanConfirmationPreview as $item): ?>
                                <li>
                                    <a href="<?= e((string) $item['url']) ?>"
                                       class="group flex items-start justify-between gap-3 rounded-xl border border-amber-200/80 bg-white px-4 py-3 shadow-soft-inset transition duration-soft hover:border-amber-400">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-content-primary truncate">
                                                <?= e((string) $item['pelanggan_nama']) ?> · <?= e((string) $item['kucing_nama']) ?>
                                            </div>
                                            <div class="text-sm text-content-secondary">
                                                Check-in <?= e(date('d/m/Y', strtotime((string) $item['check_in']))) ?>
                                                · <?= (int) $item['lama_hari'] ?> hari
                                            </div>
                                        </div>
                                        <div class="shrink-0 text-sm font-semibold text-admin">
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
                <div class="rounded-xl border border-amber-200/80 bg-white/80 p-4 sm:p-5 space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-base text-content-primary">Monitoring Harian Belum Diinput</h3>
                            <p class="mt-1 text-sm text-content-secondary">
                                <?= e((string) $pendingMonitoringPenitipan) ?> kucing belum diinput monitoring hari ini.
                            </p>
                        </div>
                        <a href="/admin/penitipan/booking?status=SEDANG_DITITIPKAN&monitoring=belum_input"
                           class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover">
                            Input Monitoring
                        </a>
                    </div>
                    <?php if ($pendingMonitoringPenitipanPreview !== []): ?>
                        <ul class="space-y-2">
                            <?php foreach ($pendingMonitoringPenitipanPreview as $item): ?>
                                <li>
                                    <a href="<?= e((string) $item['url']) ?>"
                                       class="group flex items-start justify-between gap-3 rounded-xl border border-amber-200/80 bg-white px-4 py-3 shadow-soft-inset transition duration-soft hover:border-amber-400">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-content-primary truncate">
                                                <?= e((string) $item['pelanggan_nama']) ?> · <?= e((string) $item['kucing_nama']) ?>
                                            </div>
                                            <div class="text-sm text-content-secondary">
                                                Check-out <?= e(date('d/m/Y', strtotime((string) $item['check_out']))) ?>
                                            </div>
                                        </div>
                                        <span class="shrink-0 text-sm font-semibold text-admin">Input →</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($hasPending): ?>
                <div class="rounded-xl border border-amber-200/80 bg-white/80 p-4 sm:p-5 space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-base text-content-primary">Bukti Transfer Menunggu Verifikasi</h3>
                            <p class="mt-1 text-sm text-content-secondary">
                                Grooming <?= e((string) $pendingVerification['grooming']) ?>
                                · Penitipan <?= e((string) $pendingVerification['penitipan']) ?>
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <?php if ($pendingVerification['grooming'] > 0): ?>
                                <a href="/admin/grooming/pembayaran"
                                   class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover">
                                    Verifikasi Grooming
                                </a>
                            <?php endif; ?>
                            <?php if ($pendingVerification['penitipan'] > 0): ?>
                                <a href="/admin/penitipan/pembayaran"
                                   class="cursor-pointer inline-flex items-center justify-center rounded-xl border border-admin/30 bg-white px-4 py-2.5 text-sm font-semibold text-admin shadow-soft transition duration-soft hover:bg-admin-soft">
                                    Verifikasi Penitipan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($pendingVerificationPreview !== []): ?>
                        <ul class="space-y-2">
                            <?php foreach ($pendingVerificationPreview as $item): ?>
                                <li>
                                    <a href="<?= e((string) $item['url']) ?>"
                                       class="group flex items-start justify-between gap-3 rounded-xl border border-amber-200/80 bg-white px-4 py-3 shadow-soft-inset transition duration-soft hover:border-amber-400 hover:shadow-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-content-primary truncate"><?= e((string) $item['pelanggan_nama']) ?></div>
                                            <div class="text-sm text-content-secondary"><?= e((string) $item['layanan_label']) ?></div>
                                        </div>
                                        <div class="shrink-0 text-right">
                                            <div class="text-sm font-semibold text-admin">
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
        </section>
    <?php else: ?>
        <?php
        $variant = 'success';
        $title = 'Semua tindakan operasional sudah beres';
        $description = 'Tidak ada booking penitipan menunggu konfirmasi, bukti transfer menunggu verifikasi, atau monitoring harian yang belum diinput.';
        $ctaLabel = 'Lihat riwayat transaksi';
        $ctaHref = '/admin/transaksi';
        $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2';
        require __DIR__ . '/../partials/ui/empty-state.php';
        ?>
    <?php endif; ?>

    <section aria-labelledby="kpi-heading">
        <h2 id="kpi-heading" class="sr-only">Indikator operasional</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft transition duration-soft hover:shadow-soft-lg">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-admin-soft text-admin">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                    </span>
                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold <?= $bookingDelta >= 0 ? 'bg-success-bg text-success' : 'bg-red-50 text-red-600' ?>">
                        <?= $bookingDelta >= 0 ? '+' : '' ?><?= e((string) $bookingDelta) ?>
                    </span>
                </div>
                <p class="text-sm text-content-secondary">Booking Hari Ini</p>
                <p class="mt-1 font-heading text-3xl text-admin"><?= e((string) $bookingsToday['total']) ?></p>
                <p class="mt-2 text-xs text-content-secondary">
                    Grooming <?= e((string) $bookingsToday['grooming']) ?>
                    · Penitipan <?= e((string) $bookingsToday['penitipan']) ?>
                    · Pet Care <?= e((string) $bookingsToday['pet_care']) ?>
                </p>
                <p class="mt-1 text-xs text-content-secondary/80">vs kemarin</p>
            </article>

            <article class="rounded-2xl border <?= $hasPending ? 'border-amber-200 bg-warning-bg/40' : 'border-white/80 bg-card' ?> p-5 shadow-soft transition duration-soft hover:shadow-soft-lg">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl <?= $hasPending ? 'bg-amber-100 text-amber-700' : 'bg-admin-soft text-admin' ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                        </svg>
                    </span>
                </div>
                <p class="text-sm text-content-secondary">Menunggu Verifikasi</p>
                <p class="mt-1 font-heading text-3xl <?= $hasPending ? 'text-amber-700' : 'text-admin' ?>">
                    <?= e((string) $pendingVerification['total']) ?>
                </p>
                <p class="mt-2 text-xs text-content-secondary">Prioritas operasional hari ini</p>
                <a href="/admin/grooming/pembayaran"
                   class="mt-3 inline-flex cursor-pointer text-xs font-semibold text-admin transition duration-soft hover:text-admin-hover focus:outline-none focus-visible:underline">
                    Kelola verifikasi →
                </a>
            </article>

            <a href="/admin/penitipan/booking?status=SEDANG_DITITIPKAN"
               class="group block rounded-2xl border border-white/80 bg-card p-5 shadow-soft transition duration-soft hover:shadow-soft-lg hover:border-admin/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-success-bg text-success transition duration-soft group-hover:scale-105">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"/>
                        </svg>
                    </span>
                    <span class="text-xs font-medium text-content-secondary opacity-0 transition duration-soft group-hover:opacity-100">Buka →</span>
                </div>
                <p class="text-sm text-content-secondary">Penitipan Aktif</p>
                <p class="mt-1 font-heading text-3xl text-admin"><?= e((string) $penitipanAktif) ?></p>
                <p class="mt-2 text-xs text-content-secondary">
                    <?= $penitipanAktif === 0 ? 'Belum ada penitipan aktif' : 'Check-in & sedang dititipkan' ?>
                </p>
            </a>

            <a href="<?= e($monitoringQuickUrl) ?>"
               class="group block rounded-2xl border <?= $hasPendingMonitoring ? 'border-amber-200 bg-warning-bg/40' : 'border-white/80 bg-card' ?> p-5 shadow-soft transition duration-soft hover:shadow-soft-lg hover:border-amber-400/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl <?= $hasPendingMonitoring ? 'bg-amber-100 text-amber-700' : 'bg-admin-soft text-admin' ?> transition duration-soft group-hover:scale-105">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                        </svg>
                    </span>
                    <?php if ($hasPendingMonitoring): ?>
                        <span class="text-xs font-medium text-amber-700">Perlu input →</span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-content-secondary">Belum Input Monitoring</p>
                <p class="mt-1 font-heading text-3xl <?= $hasPendingMonitoring ? 'text-amber-700' : 'text-admin' ?>">
                    <?= e((string) $pendingMonitoringPenitipan) ?>
                </p>
                <p class="mt-2 text-xs text-content-secondary">Hari ini · klik untuk input</p>
            </a>

            <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft transition duration-soft hover:shadow-soft-lg">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-soft text-primary">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                        </svg>
                    </span>
                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold <?= $revenueDelta >= 0 ? 'bg-success-bg text-success' : 'bg-red-50 text-red-600' ?>">
                        <?= $revenueDelta >= 0 ? '+' : '−' ?>Rp <?= e(number_format(abs($revenueDelta), 0, ',', '.')) ?>
                    </span>
                </div>
                <p class="text-sm text-content-secondary">Pendapatan Terverifikasi</p>
                <p class="mt-1 font-heading text-xl text-admin leading-snug">
                    Rp <?= e(number_format((float) $pendapatan['harian'], 0, ',', '.')) ?>
                </p>
                <p class="mt-1 text-xs text-content-secondary">Hari ini · vs kemarin</p>
                <p class="mt-3 text-sm font-semibold text-content-primary">
                    Minggu ini: Rp <?= e(number_format((float) $pendapatan['mingguan'], 0, ',', '.')) ?>
                </p>
                <a href="/admin/laporan"
                   class="mt-2 inline-flex cursor-pointer text-xs font-semibold text-admin transition duration-soft hover:text-admin-hover focus:outline-none focus-visible:underline">
                    Laporan detail →
                </a>
            </article>
        </div>
    </section>

    <?php if ($isOwner): ?>
        <aside class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-amber-200/80 bg-warning-bg/70 px-5 py-4 shadow-soft">
            <div class="flex items-start gap-3 min-w-0">
                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 shadow-soft">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </span>
                <p class="text-sm text-amber-950">
                    Anda login sebagai <strong class="font-semibold">Owner</strong> — akses penuh operasional staff + manajemen akun staff.
                </p>
            </div>
            <a href="/admin/staff"
               class="cursor-pointer shrink-0 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-admin shadow-soft transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2">
                Manajemen Staff →
            </a>
        </aside>
    <?php endif; ?>
</div>
