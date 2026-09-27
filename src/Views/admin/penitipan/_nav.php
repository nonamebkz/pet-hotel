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
<nav class="mb-6 flex flex-wrap gap-x-1 gap-y-2 border-b border-border" aria-label="Navigasi penitipan">
    <?php foreach ($tabs as $tab): ?>
        <?php
        $isActive = $path === $tab['match'] || str_starts_with($path, $tab['match'] . '/');
        if ($tab['match'] === '/admin/penitipan/booking' && str_starts_with($path, '/admin/penitipan/monitoring')) {
            $isActive = true;
        }
        ?>
        <?php if ($isActive): ?>
            <span class="inline-flex items-center border-b-2 border-primary px-4 py-2.5 text-sm font-medium text-foreground">
                <?= e($tab['label']) ?>
            </span>
        <?php else: ?>
            <a href="<?= e($tab['href']) ?>"
               class="inline-flex touch-target items-center border-b-2 border-transparent px-4 py-2.5 text-sm font-medium text-muted-foreground transition hover:text-foreground focus:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                <?= e($tab['label']) ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
