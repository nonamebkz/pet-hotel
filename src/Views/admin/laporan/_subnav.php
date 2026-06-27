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
       class="<?= $activeTab === 'index' ? 'text-slate-800 font-medium border-b-2 border-slate-800 pb-1' : 'text-gray-500 hover:text-slate-800' ?>">
        Ringkasan
    </a>
    <a href="/admin/laporan/grooming<?= e($querySuffix) ?>"
       class="<?= $activeTab === 'grooming' ? 'text-slate-800 font-medium border-b-2 border-slate-800 pb-1' : 'text-gray-500 hover:text-slate-800' ?>">
        Grooming
    </a>
    <a href="/admin/laporan/penitipan<?= e($querySuffix) ?>"
       class="<?= $activeTab === 'penitipan' ? 'text-slate-800 font-medium border-b-2 border-slate-800 pb-1' : 'text-gray-500 hover:text-slate-800' ?>">
        Pet Hotel
    </a>
    <a href="/admin/laporan/pet-care<?= e($querySuffix) ?>"
       class="<?= $activeTab === 'pet-care' ? 'text-slate-800 font-medium border-b-2 border-slate-800 pb-1' : 'text-gray-500 hover:text-slate-800' ?>">
        Pet Care
    </a>
</div>
