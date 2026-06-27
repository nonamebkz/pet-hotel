<?php

declare(strict_types=1);

$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$ringkasan = $ringkasan ?? ['grooming' => 0, 'penitipan' => 0, 'pet_care' => 0];
$activeTab = 'index';
$query = http_build_query(['mulai' => $mulai, 'akhir' => $akhir]);
$querySuffix = $query !== '' ? '?' . $query : '';
?>
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Laporan</h1>
    <p class="text-sm text-gray-500 mb-6 print:hidden">
        Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
    </p>

    <?php require __DIR__ . '/_subnav.php'; ?>

    <form method="GET" action="/admin/laporan" class="mb-6 flex flex-wrap items-end gap-3 print:hidden">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
            <input type="date" name="mulai" value="<?= e($mulai) ?>"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
            <input type="date" name="akhir" value="<?= e($akhir) ?>"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm hover:bg-gray-200">
            Terapkan Periode
        </button>
    </form>

    <div id="laporan-print" class="print:shadow-none">
        <div class="hidden print:block mb-4">
            <h2 class="text-xl font-bold text-gray-800">Ringkasan Laporan</h2>
            <p class="text-sm text-gray-600">
                Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
            </p>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
            <a href="/admin/laporan/grooming<?= e($querySuffix) ?>"
               class="bg-white rounded-xl border p-6 hover:border-slate-400 transition print:border print:shadow-none">
                <div class="text-sm text-gray-500 mb-1">Booking Grooming</div>
                <div class="text-3xl font-bold text-slate-800"><?= e((string) $ringkasan['grooming']) ?></div>
                <div class="text-xs text-gray-400 mt-2 print:hidden">Lihat detail →</div>
            </a>
            <a href="/admin/laporan/penitipan<?= e($querySuffix) ?>"
               class="bg-white rounded-xl border p-6 hover:border-slate-400 transition print:border print:shadow-none">
                <div class="text-sm text-gray-500 mb-1">Booking Pet Hotel</div>
                <div class="text-3xl font-bold text-slate-800"><?= e((string) $ringkasan['penitipan']) ?></div>
                <div class="text-xs text-gray-400 mt-2 print:hidden">Lihat detail →</div>
            </a>
            <a href="/admin/laporan/pet-care<?= e($querySuffix) ?>"
               class="bg-white rounded-xl border p-6 hover:border-slate-400 transition print:border print:shadow-none">
                <div class="text-sm text-gray-500 mb-1">Booking Pet Care</div>
                <div class="text-3xl font-bold text-slate-800"><?= e((string) $ringkasan['pet_care']) ?></div>
                <div class="text-xs text-gray-400 mt-2 print:hidden">Lihat detail →</div>
            </a>
        </div>
    </div>

    <div class="mt-6 print:hidden">
        <button type="button" onclick="window.print()"
                class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">
            Cetak / Simpan PDF
        </button>
    </div>
</div>
