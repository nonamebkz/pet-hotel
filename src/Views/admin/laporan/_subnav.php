<?php

declare(strict_types=1);

$activeTab = $activeTab ?? 'index';
$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$query = http_build_query(['mulai' => $mulai, 'akhir' => $akhir]);
$querySuffix = $query !== '' ? '?' . $query : '';

$tabs = [
    'index' => ['label' => 'Ringkasan', 'href' => '/admin/laporan' . $querySuffix],
    'grooming' => ['label' => 'Grooming', 'href' => '/admin/laporan/grooming' . $querySuffix],
    'penitipan' => ['label' => 'Pet Hotel', 'href' => '/admin/laporan/penitipan' . $querySuffix],
    'pet-care' => ['label' => 'Pet Care', 'href' => '/admin/laporan/pet-care' . $querySuffix],
];
?>
<nav class="flex flex-wrap gap-2 mb-6 print:hidden" aria-label="Navigasi laporan">
    <?php foreach ($tabs as $key => $tab): ?>
        <?php $isActive = $activeTab === $key; ?>
        <?php if ($isActive): ?>
            <span class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold <?= e(design_cn(ui_btn_primary())) ?>">
                <?= e($tab['label']) ?>
            </span>
        <?php else: ?>
            <a href="<?= e($tab['href']) ?>"
               class="<?= e(design_cn(ui_btn_secondary(), 'rounded-xl px-4 py-2.5 text-sm font-medium text-muted-foreground')) ?>">
                <?= e($tab['label']) ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>
