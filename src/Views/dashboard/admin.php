<?php

declare(strict_types=1);

use App\Enums\StaffRole;

$today = $today ?? date('Y-m-d');
$bookingsToday = $bookingsToday ?? ['grooming' => 0, 'penitipan' => 0, 'pet_care' => 0, 'total' => 0];
$bookingsYesterday = $bookingsYesterday ?? ['total' => 0];
$pendingVerification = $pendingVerification ?? ['grooming' => 0, 'penitipan' => 0, 'total' => 0];
$penitipanAktif = $penitipanAktif ?? 0;
$pendapatan = $pendapatan ?? ['harian' => 0.0, 'kemarin' => 0.0, 'mingguan' => 0.0, 'mingguMulai' => $today, 'mingguAkhir' => $today];
$pendingVerificationPreview = $pendingVerificationPreview ?? [];

$bookingDelta = (int) $bookingsToday['total'] - (int) $bookingsYesterday['total'];
$revenueDelta = (float) $pendapatan['harian'] - (float) ($pendapatan['kemarin'] ?? 0);
$hasPending = (int) $pendingVerification['total'] > 0;
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

    <?php if ($hasPending): ?>
        <section class="rounded-2xl border border-warning/40 bg-warning-bg/80 shadow-soft p-6 sm:p-7" aria-labelledby="action-center-title">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 shadow-soft">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </span>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-xs font-semibold uppercase tracking-wider text-amber-800">Action Center</span>
                            <span class="inline-flex items-center rounded-lg bg-amber-200/80 px-2 py-0.5 text-xs font-semibold text-amber-900">
                                <?= e((string) $pendingVerification['total']) ?> menunggu
                            </span>
                        </div>
                        <h2 id="action-center-title" class="font-heading text-lg text-content-primary">
                            Bukti Transfer Menunggu Verifikasi
                        </h2>
                        <p class="mt-1 text-sm text-content-secondary">
                            Grooming <?= e((string) $pendingVerification['grooming']) ?>
                            · Penitipan <?= e((string) $pendingVerification['penitipan']) ?>
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php if ($pendingVerification['grooming'] > 0): ?>
                        <a href="/admin/grooming/pembayaran"
                           class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2">
                            Verifikasi Grooming
                        </a>
                    <?php endif; ?>
                    <?php if ($pendingVerification['penitipan'] > 0): ?>
                        <a href="/admin/penitipan/pembayaran"
                           class="cursor-pointer inline-flex items-center justify-center rounded-xl border border-admin/30 bg-white px-4 py-2.5 text-sm font-semibold text-admin shadow-soft transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2">
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
                                    <div class="mt-0.5 text-xs font-medium text-amber-700 opacity-0 transition duration-soft group-hover:opacity-100">
                                        Tinjau →
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <?php
        $variant = 'success';
        $title = 'Semua pembayaran sudah diverifikasi';
        $description = 'Tidak ada bukti transfer yang menunggu tindakan.';
        $ctaLabel = 'Lihat riwayat transaksi';
        $ctaHref = '/admin/transaksi';
        $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2';
        require __DIR__ . '/../partials/ui/empty-state.php';
        ?>
    <?php endif; ?>

    <section aria-labelledby="kpi-heading">
        <h2 id="kpi-heading" class="sr-only">Indikator operasional</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
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

            <a href="/admin/penitipan/booking"
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
