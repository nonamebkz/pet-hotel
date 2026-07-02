<?php

declare(strict_types=1);

$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$ringkasan = $ringkasan ?? ['grooming' => 0, 'penitipan' => 0, 'pet_care' => 0];
$trend = $trend ?? [];
$activeTab = 'index';
$query = http_build_query(['mulai' => $mulai, 'akhir' => $akhir]);
$querySuffix = $query !== '' ? '?' . $query : '';

$maxGrooming = 1;
$maxRevenue = 1.0;
foreach ($trend as $point) {
    $maxGrooming = max($maxGrooming, (int) ($point['grooming'] ?? 0));
    $maxRevenue = max($maxRevenue, (float) ($point['revenue'] ?? 0));
}
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

    <div id="laporan-print" class="print:shadow-none space-y-6">
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

        <?php if ($trend !== []): ?>
            <div class="bg-white rounded-xl border p-6 print:hidden">
                <h2 class="text-base font-semibold text-gray-800 mb-1">Trend Harian</h2>
                <p class="text-xs text-gray-500 mb-6">Booking grooming dan pendapatan terverifikasi per hari dalam periode.</p>

                <div class="flex items-end gap-1 sm:gap-2 h-48 overflow-x-auto pb-2">
                    <?php foreach ($trend as $point): ?>
                        <?php
                        $grooming = (int) ($point['grooming'] ?? 0);
                        $revenue = (float) ($point['revenue'] ?? 0);
                        $groomingPct = $maxGrooming > 0 ? max(4, (int) round(($grooming / $maxGrooming) * 100)) : 4;
                        $revenuePct = $maxRevenue > 0 ? max(4, (int) round(($revenue / $maxRevenue) * 100)) : 4;
                        ?>
                        <div class="flex flex-col items-center min-w-[2.5rem] flex-1 group" title="<?= e((string) ($point['label'] ?? '')) ?>">
                            <div class="flex items-end gap-0.5 h-36 w-full justify-center">
                                <div class="w-2 sm:w-3 bg-slate-600 rounded-t transition-all group-hover:bg-slate-800"
                                     style="height: <?= e((string) $groomingPct) ?>%"
                                     title="Booking: <?= e((string) $grooming) ?>"></div>
                                <div class="w-2 sm:w-3 bg-green-500 rounded-t transition-all group-hover:bg-green-600"
                                     style="height: <?= e((string) $revenuePct) ?>%"
                                     title="Pendapatan: Rp <?= e(number_format($revenue, 0, ',', '.')) ?>"></div>
                            </div>
                            <span class="text-[10px] sm:text-xs text-gray-500 mt-2 truncate w-full text-center">
                                <?= e((string) ($point['label'] ?? '')) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex flex-wrap gap-4 mt-4 pt-4 border-t text-xs text-gray-600">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-slate-600"></span> Booking grooming
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-green-500"></span> Pendapatan (Rp)
                    </span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-6 print:hidden">
        <button type="button" onclick="window.print()"
                class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">
            Cetak / Simpan PDF
        </button>
    </div>
</div>
