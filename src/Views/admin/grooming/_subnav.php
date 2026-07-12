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
<nav class="flex flex-wrap gap-2 mb-6" aria-label="Navigasi grooming">
    <?php foreach ($tabs as $key => $tab): ?>
        <?php $isActive = $activeTab === $key; ?>
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
