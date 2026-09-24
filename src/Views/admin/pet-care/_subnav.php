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
<nav class="-mb-px mb-6 flex flex-wrap gap-x-1 gap-y-2 border-b border-border" aria-label="Navigasi pet care">
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
