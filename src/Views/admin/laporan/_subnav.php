<?php

declare(strict_types=1);

$activeTab = $activeTab ?? 'index';
$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$query = http_build_query(['mulai' => $mulai, 'akhir' => $akhir]);
$querySuffix = $query !== '' ? '?' . $query : '';
?>
<div class="flex gap-4 mb-6 text-sm print:hidden">
    <a href="/admin/laporan<?= e($querySuffix) ?>"
       class="<?= $activeTab === 'index' ? 'text-admin font-medium border-b-2 border-admin pb-1' : 'text-gray-500 hover:text-admin' ?>">
        Ringkasan
    </a>
    <a href="/admin/laporan/grooming<?= e($querySuffix) ?>"
       class="<?= $activeTab === 'grooming' ? 'text-admin font-medium border-b-2 border-admin pb-1' : 'text-gray-500 hover:text-admin' ?>">
        Grooming
    </a>
    <a href="/admin/laporan/penitipan<?= e($querySuffix) ?>"
       class="<?= $activeTab === 'penitipan' ? 'text-admin font-medium border-b-2 border-admin pb-1' : 'text-gray-500 hover:text-admin' ?>">
        Pet Hotel
    </a>
    <a href="/admin/laporan/pet-care<?= e($querySuffix) ?>"
       class="<?= $activeTab === 'pet-care' ? 'text-admin font-medium border-b-2 border-admin pb-1' : 'text-gray-500 hover:text-admin' ?>">
        Pet Care
    </a>
</div>
