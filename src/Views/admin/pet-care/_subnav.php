<?php

declare(strict_types=1);

/** @var string $activeTab layanan|slot|booking */
$activeTab = $activeTab ?? 'layanan';

$tabs = [
    'layanan' => ['label' => 'Layanan', 'href' => '/admin/pet-care/layanan'],
    'slot' => ['label' => 'Slot Dokter', 'href' => '/admin/pet-care/slot'],
    'booking' => ['label' => 'Booking', 'href' => '/admin/pet-care/booking'],
];
?>
<nav class="flex flex-wrap gap-2 mb-6" aria-label="Navigasi pet care">
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
