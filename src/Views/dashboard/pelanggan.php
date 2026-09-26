<?php

declare(strict_types=1);

$addressComplete = $addressComplete ?? false;
$kucingCount = $kucingCount ?? 0;
$activeBookings = $activeBookings ?? [];
$pendingPayments = $pendingPayments ?? [];
$promoEligible = $promoEligible ?? false;
$promoConfig = $promoConfig ?? [];
$recentNotifications = $recentNotifications ?? [];

$syaratDone = ($addressComplete ? 1 : 0) + ($kucingCount >= 1 ? 1 : 0);
$bookingReady = $syaratDone >= 2;
$prasyaratRowClass = static function (bool $ok): string {
    return design_cn(
        'flex items-center justify-between p-3 rounded-lg border',
        $ok
            ? 'border-emerald-500/30 bg-emerald-500/5'
            : design_advice_panel_surface('warning'),
    );
};
$pendingPaymentCount = count($pendingPayments);
$headerMeta = '<span class="' . e(design_status_badge('muted')) . '">Booking aktif: ' . e((string) count($activeBookings)) . '</span>'
    . '<span class="' . e(design_status_badge($pendingPaymentCount > 0 ? 'muted' : 'success')) . '">Tagihan pending: ' . e((string) $pendingPaymentCount) . '</span>'
    . '<span class="' . e(design_status_badge($bookingReady ? 'success' : 'muted')) . '">Syarat booking: ' . e((string) $syaratDone) . '/2</span>';
if (!$bookingReady) {
    $headerMeta .= '<span class="' . e(design_cn('rounded-full px-2.5 py-1 text-xs font-medium', 'bg-amber-500/10 text-amber-800 dark:text-amber-200')) . '">Perlu dilengkapi</span>';
}
$headerActions = $pendingPaymentCount > 0
    ? '<a href="' . e((string) $pendingPayments[0]['payment_url']) . '" class="' . e(ui_btn_primary()) . '">Bayar tagihan</a>'
    : ($bookingReady
        ? '<a href="/grooming/booking" class="' . e(ui_btn_primary()) . '">Ajukan layanan</a>'
        : '<a href="/profil" class="' . e(ui_btn_secondary()) . '">Lengkapi profil</a>');
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Halo, ' . (string) $nama . '!',
        'Portal pelanggan',
        !empty($email) ? (string) $email : 'Ringkasan booking, tagihan, dan syarat layanan.',
        $headerActions,
        $headerMeta,
    );
    ?>

    <?php if ($pendingPaymentCount > 0): ?>
        <section
            class="<?= e(design_cn(design_advice_panel_surface('warning'), design_surface('panel'), 'p-5 sm:p-6 space-y-4')) ?>"
            aria-labelledby="tagihan-prioritas-heading"
        >
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="<?= e(design_icon_badge('warning', 'h-11 w-11 shrink-0 rounded-xl p-0')) ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-800 dark:text-amber-200">Perlu tindakan</p>
                        <h2 id="tagihan-prioritas-heading" class="font-heading text-lg font-semibold text-foreground">
                            <?= e((string) $pendingPaymentCount) ?> tagihan menunggu pembayaran
                        </h2>
                        <p class="mt-1 text-sm text-muted-foreground">Selesaikan pembayaran agar booking tidak dibatalkan otomatis.</p>
                    </div>
                </div>
                <a href="/transaksi" class="<?= e(ui_btn_secondary()) ?> shrink-0">Riwayat transaksi</a>
            </div>
            <div class="space-y-3">
                <?php foreach ($pendingPayments as $payment): ?>
                    <div class="<?= e(design_cn('rounded-2xl border p-3', design_advice_panel_surface('warning'))) ?>">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div>
                                <div class="font-medium text-foreground"><?= e((string) $payment['tagihan_jenis']) ?></div>
                                <div class="text-sm text-muted-foreground"><?= e((string) ($payment['layanan_label'] ?? '')) ?></div>
                            </div>
                            <div class="text-sm font-semibold tabular-nums text-primary shrink-0">
                                Rp <?= e(number_format((float) $payment['total_bayar'], 0, ',', '.')) ?>
                            </div>
                        </div>
                        <?php if (!empty($payment['batas_waktu_bayar'])): ?>
                            <p class="text-xs text-amber-800 dark:text-amber-200 mb-2">
                                Batas bayar: <?= e(date('d/m/Y H:i', strtotime((string) $payment['batas_waktu_bayar']))) ?> WIB
                            </p>
                        <?php endif; ?>
                        <a href="<?= e((string) $payment['payment_url']) ?>" class="<?= e(ui_btn_primary()) ?> text-sm px-3 py-1.5">
                            Bayar & upload bukti
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="<?= e(design_cn(design_surface('metric'), 'p-6')) ?>">
            <h2 class="text-lg font-semibold text-foreground mb-4">Booking Aktif & Mendatang</h2>

            <?php if ($activeBookings === []): ?>
                <p class="text-sm text-muted-foreground mb-4">
                    Belum ada booking aktif atau terjadwal. Anda bisa mulai dari menu layanan setelah syarat booking lengkap.
                </p>
                <?php if ($bookingReady): ?>
                    <div class="flex flex-wrap gap-2 text-sm">
                        <a href="/grooming/booking" class="text-primary hover:underline font-medium">Ajukan grooming</a>
                        <span class="text-border">·</span>
                        <a href="/penitipan/booking" class="text-primary hover:underline font-medium">Ajukan penitipan</a>
                        <span class="text-border">·</span>
                        <a href="/pet-care/booking" class="text-primary hover:underline font-medium">Ajukan pet care</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($activeBookings as $booking): ?>
                        <?php $status = $booking['status'] ?? null; ?>
                        <a href="<?= e((string) $booking['url']) ?>"
                           class="<?= e(design_cn(design_interactive('listArticle'), design_interactive('listRowHover'), 'block p-3')) ?>">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs text-muted-foreground uppercase tracking-wide">
                                        <?= e((string) $booking['layanan_label']) ?>
                                    </div>
                                    <div class="font-medium text-foreground"><?= e((string) $booking['label']) ?></div>
                                    <div class="text-sm text-muted-foreground">Kucing: <?= e((string) $booking['kucing']) ?></div>
                                    <div class="text-sm text-muted-foreground mt-0.5"><?= e((string) $booking['tanggal_display']) ?></div>
                                </div>
                                <?php if ($status && method_exists($status, 'badgeClass')): ?>
                                    <span class="text-xs px-2 py-1 rounded-full shrink-0 <?= e($status->badgeClass()) ?>">
                                        <?= e((string) $booking['status_label']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="<?= e(design_cn(design_surface('metric'), 'p-6')) ?>">
            <div class="flex items-center justify-between mb-4 gap-2">
                <h2 class="text-lg font-semibold text-foreground">Tagihan</h2>
                <a href="/transaksi" class="text-sm text-primary hover:underline shrink-0">Riwayat transaksi →</a>
            </div>

            <?php if ($pendingPaymentCount === 0): ?>
                <p class="text-sm text-muted-foreground">
                    Tidak ada tagihan yang menunggu pembayaran saat ini.
                </p>
            <?php else: ?>
                <p class="text-sm text-muted-foreground">
                    <?= e((string) $pendingPaymentCount) ?> tagihan aktif — detail lengkap ada di bagian prioritas di atas.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <section class="<?= e(design_cn(design_surface('metric'), 'p-6')) ?>" aria-labelledby="prasyarat-heading">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 id="prasyarat-heading" class="text-lg font-semibold text-foreground">Prasyarat Booking</h2>
            <span class="<?= e(design_status_badge($bookingReady ? 'success' : 'muted')) ?>">
                <?= e((string) $syaratDone) ?> dari 2 selesai
            </span>
        </div>
        <p class="text-sm text-muted-foreground mb-4">
            Lengkapi data berikut sebelum mengajukan grooming, penitipan, atau pet care.
        </p>

        <div class="space-y-3">
            <div class="<?= e($prasyaratRowClass($addressComplete)) ?>">
                <div>
                    <p class="font-medium text-foreground">Profil & Alamat</p>
                    <p class="text-sm text-muted-foreground">
                        <?= $addressComplete
                            ? 'Alamat lengkap — siap untuk opsi antar-jemput'
                            : 'Alamat belum lengkap — wajib jika pilih antar-jemput' ?>
                    </p>
                </div>
                <a href="/profil" class="<?= e(ui_btn_secondary()) ?> text-sm px-3 py-1.5 shrink-0 ml-4">
                    <?= $addressComplete ? 'Lihat' : 'Lengkapi' ?>
                </a>
            </div>

            <div class="<?= e($prasyaratRowClass($kucingCount >= 1)) ?>">
                <div>
                    <p class="font-medium text-foreground">Data Kucing</p>
                    <p class="text-sm text-muted-foreground">
                        <?= $kucingCount >= 1
                            ? $kucingCount . ' kucing terdaftar'
                            : 'Belum ada kucing — minimal 1 kucing diperlukan' ?>
                    </p>
                </div>
                <a href="/kucing" class="<?= e(ui_btn_secondary()) ?> text-sm px-3 py-1.5 shrink-0 ml-4">
                    <?= $kucingCount >= 1 ? 'Kelola' : 'Tambah' ?>
                </a>
            </div>
        </div>
    </section>

    <section class="<?= e(design_cn(design_surface('metric'), 'p-6')) ?>">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">Notifikasi Terbaru</h2>
            <a href="/notifikasi" class="text-sm text-primary hover:underline">Lihat semua</a>
        </div>

        <?php if ($recentNotifications === []): ?>
            <p class="text-sm text-muted-foreground">
                Belum ada notifikasi baru. Update booking dan pembayaran akan muncul di sini secara otomatis.
            </p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($recentNotifications as $notif): ?>
                    <div class="<?= e(design_cn(
                        'p-3 rounded-lg border border-border',
                        empty($notif['sudah_dibaca']) ? 'bg-primary/5 border-primary/20' : '',
                    )) ?>">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-medium text-foreground text-sm"><?= e((string) $notif['judul']) ?></div>
                                <p class="text-sm text-muted-foreground mt-0.5 line-clamp-2"><?= e((string) $notif['pesan']) ?></p>
                            </div>
                            <time class="text-xs text-muted-foreground shrink-0">
                                <?= e(date('d/m H:i', strtotime((string) $notif['created_at']))) ?>
                            </time>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($promoEligible && $bookingReady): ?>
        <aside class="<?= e(design_cn(design_advice_panel_surface('warning'), 'border-emerald-500/30 bg-emerald-500/5 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3')) ?>">
            <div class="text-sm text-emerald-900 dark:text-emerald-100">
                <span class="font-semibold">Promo penitipan aktif!</span>
                Potongan <?= (int) ($promoConfig['promo_discount_percent'] ?? 10) ?>%
                untuk durasi lebih dari <?= (int) ($promoConfig['promo_min_days'] ?? 7) ?> hari (1× per akun).
            </div>
            <a href="/penitipan/booking" class="<?= e(ui_btn_primary()) ?> shrink-0">
                Ajukan penitipan
            </a>
        </aside>
    <?php endif; ?>

    <section class="<?= e(design_cn(design_surface('metric'), 'p-6')) ?>">
        <h2 class="text-lg font-semibold text-foreground mb-3">Shortcut</h2>
        <div class="flex flex-wrap gap-3">
            <a href="/kucing/tambah" class="<?= e(ui_btn_secondary()) ?>">+ Tambah Kucing</a>
            <a href="/profil" class="<?= e(ui_btn_secondary()) ?>">Edit Profil</a>
            <?php if ($bookingReady): ?>
                <a href="/pet-care/booking" class="<?= e(ui_btn_primary()) ?>">Booking Pet Care</a>
                <a href="/grooming/booking" class="<?= e(ui_btn_primary()) ?>">Booking Grooming</a>
                <a href="/penitipan/booking" class="<?= e(ui_btn_primary()) ?>">Booking Penitipan</a>
            <?php endif; ?>
        </div>
    </section>
</div>
