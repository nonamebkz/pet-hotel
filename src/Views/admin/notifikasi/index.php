<?php

declare(strict_types=1);

use App\Enums\JenisNotifikasi;

$notifikasiList = $notifikasiList ?? [];
$kategori = trim((string) ($_GET['kategori'] ?? ''));

$kategoriTabs = [
    '' => 'Semua',
    'booking' => 'Booking',
    'pembayaran' => 'Pembayaran',
    'reminder' => 'Reminder',
    'sistem' => 'Sistem',
];

$resolveKategori = static function (string $jenis): string {
    $bookingTypes = [
        JenisNotifikasi::BOOKING_DISETUJUI->value,
        JenisNotifikasi::BOOKING_DITOLAK->value,
        JenisNotifikasi::JAM_GROOMING_DIUPDATE->value,
        JenisNotifikasi::LAYANAN_SELESAI->value,
        JenisNotifikasi::BOOKING_DIBATALKAN->value,
        JenisNotifikasi::MONITORING_PENITIPAN->value,
        JenisNotifikasi::PERPANJANGAN_PENITIPAN_MENUNGGU_KONFIRMASI->value,
        JenisNotifikasi::PERPANJANGAN_PENITIPAN_DISETUJUI->value,
        JenisNotifikasi::PERPANJANGAN_PENITIPAN_DITOLAK->value,
        JenisNotifikasi::PERPANJANGAN_PENITIPAN_MENUNGGU_PEMBAYARAN->value,
    ];
    $pembayaranTypes = [
        JenisNotifikasi::PEMBAYARAN_JATUH_TEMPO->value,
        JenisNotifikasi::STATUS_REFUND->value,
    ];
    $reminderTypes = [
        JenisNotifikasi::REMINDER_PEMBAYARAN->value,
    ];

    if (in_array($jenis, $bookingTypes, true)) {
        return 'booking';
    }
    if (in_array($jenis, $pembayaranTypes, true)) {
        return 'pembayaran';
    }
    if (in_array($jenis, $reminderTypes, true)) {
        return 'reminder';
    }

    return 'sistem';
};

$filteredList = $notifikasiList;

if ($kategori !== '') {
    $filteredList = array_values(array_filter(
        $notifikasiList,
        static fn (array $notif): bool => $resolveKategori((string) ($notif['jenis'] ?? '')) === $kategori,
    ));
}

$btnSecondary = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-admin shadow-soft transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-admin/5 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Operasional</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Notifikasi</h1>
                <p class="mt-2 text-sm text-content-secondary max-w-xl">
                    Semua pemberitahuan operasional untuk akun Anda.
                </p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-admin text-white shadow-soft" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                </svg>
            </div>
        </div>
    </section>

    <nav class="flex flex-wrap gap-2" aria-label="Filter kategori notifikasi">
        <?php foreach ($kategoriTabs as $key => $label): ?>
            <?php $isActive = $kategori === $key; ?>
            <a href="/admin/notifikasi<?= $key !== '' ? '?kategori=' . urlencode($key) : '' ?>"
               class="cursor-pointer inline-flex items-center rounded-xl px-4 py-2 text-sm font-semibold transition duration-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin <?= $isActive
                   ? 'bg-admin text-white shadow-soft'
                   : 'border border-border bg-card text-content-secondary shadow-soft hover:bg-admin-soft hover:text-admin' ?>">
                <?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if ($filteredList === []): ?>
        <?php
        $variant = $kategori !== '' ? 'filtered' : 'empty';
        $title = $kategori !== ''
            ? 'Belum ada notifikasi untuk kategori ini'
            : 'Belum ada notifikasi';
        $description = 'Aktivitas booking, pembayaran, dan operasional akan muncul di sini secara otomatis.';
        $ctaLabel = 'Lihat Riwayat Transaksi';
        $ctaHref = '/admin/transaksi';
        $ctaClass = $btnSecondary;
        require __DIR__ . '/../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($filteredList as $notif): ?>
                <?php
                $jenis = (string) ($notif['jenis'] ?? '');
                $actionUrl = null;
                $kat = $resolveKategori($jenis);

                if ($jenis === JenisNotifikasi::PERPANJANGAN_PENITIPAN_MENUNGGU_KONFIRMASI->value) {
                    $actionUrl = '/admin/penitipan/perpanjangan';
                } elseif (in_array($kat, ['pembayaran', 'reminder'], true)) {
                    $actionUrl = '/admin/grooming/pembayaran';
                } elseif ($kat === 'booking') {
                    $actionUrl = '/admin/grooming/booking';
                }

                $katColors = [
                    'booking' => 'border-l-blue-400',
                    'pembayaran' => 'border-l-green-400',
                    'reminder' => 'border-l-amber-400',
                    'sistem' => 'border-l-gray-300',
                ];
                $isUnread = empty($notif['sudah_dibaca']);
                ?>
                <article class="rounded-2xl border border-white/80 border-l-4 <?= e($katColors[$kat] ?? $katColors['sistem']) ?> bg-card p-4 sm:p-5 shadow-soft transition duration-soft hover:shadow-soft-lg <?= $isUnread ? 'bg-admin-soft/30' : '' ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <?php if ($isUnread): ?>
                                    <span class="h-2 w-2 shrink-0 rounded-full bg-admin" aria-hidden="true"></span>
                                <?php endif; ?>
                                <h2 class="font-heading text-base text-content-primary"><?= e((string) $notif['judul']) ?></h2>
                            </div>
                            <p class="mt-1.5 text-sm text-content-secondary"><?= e((string) $notif['pesan']) ?></p>
                            <?php if ($actionUrl !== null): ?>
                                <a href="<?= e($actionUrl) ?>"
                                   class="mt-2.5 inline-flex cursor-pointer items-center gap-1 text-sm font-semibold text-admin transition duration-soft hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-admin rounded-lg">
                                    Lihat detail
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                        <time class="shrink-0 text-xs font-medium text-content-secondary">
                            <?= e(date('d/m/Y H:i', strtotime((string) $notif['created_at']))) ?>
                        </time>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
