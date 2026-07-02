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
<div class="flex gap-4 mb-6 text-sm border-b border-gray-200">
    <?php foreach ($tabs as $key => $tab): ?>
        <?php $isActive = $activeTab === $key; ?>
        <a href="<?= e($tab['href']) ?>"
           class="<?= $isActive
               ? 'text-admin font-medium border-b-2 border-admin pb-2 -mb-px'
               : 'text-gray-500 hover:text-admin pb-2' ?>">
            <?= e($tab['label']) ?>
        </a>
    <?php endforeach; ?>
</div>
