<?php

declare(strict_types=1);

/** @var string $activeTab layanan|kuota|booking|pembayaran */
$activeTab = $activeTab ?? 'layanan';

$tabs = [
    'layanan' => ['label' => 'Jenis', 'href' => '/admin/grooming/layanan'],
    'kuota' => ['label' => 'Kuota', 'href' => '/admin/grooming/kuota'],
    'booking' => ['label' => 'Booking', 'href' => '/admin/grooming/booking'],
    'pembayaran' => ['label' => 'Verifikasi Bukti', 'href' => '/admin/grooming/pembayaran'],
];
?>
<nav class="-mb-px mb-6 flex flex-wrap gap-x-1 gap-y-2 border-b border-border" aria-label="Navigasi grooming">
    <?php foreach ($tabs as $key => $tab): ?>
        <?php $isActive = $activeTab === $key; ?>
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
