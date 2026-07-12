<?php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$tabs = [
    'paket' => ['label' => 'Paket', 'href' => '/admin/penitipan/paket', 'match' => '/admin/penitipan/paket'],
    'kamar' => ['label' => 'Kamar', 'href' => '/admin/penitipan/kamar', 'match' => '/admin/penitipan/kamar'],
    'kuota' => ['label' => 'Kuota', 'href' => '/admin/penitipan/kuota', 'match' => '/admin/penitipan/kuota'],
    'booking' => ['label' => 'Booking', 'href' => '/admin/penitipan/booking', 'match' => '/admin/penitipan/booking'],
    'perpanjangan' => ['label' => 'Perpanjangan', 'href' => '/admin/penitipan/perpanjangan', 'match' => '/admin/penitipan/perpanjangan'],
    'pembayaran' => ['label' => 'Verifikasi Bukti', 'href' => '/admin/penitipan/pembayaran', 'match' => '/admin/penitipan/pembayaran'],
];
?>
<nav class="flex flex-wrap gap-2 mb-6" aria-label="Navigasi penitipan">
    <?php foreach ($tabs as $tab): ?>
        <?php
        $isActive = $path === $tab['match'] || str_starts_with($path, $tab['match'] . '/');
        if ($tab['match'] === '/admin/penitipan/booking' && str_starts_with($path, '/admin/penitipan/monitoring')) {
            $isActive = true;
        }
        ?>
        <?php if ($isActive): ?>
            <span class="inline-flex items-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft">
                <?= e($tab['label']) ?>
            </span>
        <?php else: ?>
            <a href="<?= e($tab['href']) ?>"
               class="cursor-pointer inline-flex items-center rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-medium text-content-secondary shadow-soft transition duration-soft hover:bg-admin-soft hover:text-admin focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                <?= e($tab['label']) ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
