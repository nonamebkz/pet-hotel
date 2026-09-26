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

$inputClass = design_cn(ui_form_input_class(), 'text-sm py-2');
$btnPrimary = design_cn(ui_btn_primary(), 'gap-2');
$btnSecondary = ui_btn_secondary();
$periodeLabel = date('d/m/Y', strtotime($mulai)) . ' — ' . date('d/m/Y', strtotime($akhir));
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Laporan',
        'Analitik · Administrasi',
        'Ringkasan pendapatan dan aktivitas layanan. Periode: ' . $periodeLabel,
    );
    ?>

    <?php require __DIR__ . '/_subnav.php'; ?>

    <form method="GET" action="/admin/laporan" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 print:hidden')) ?>">
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[10rem] flex-1">
                <label for="mulai" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal Mulai</label>
                <input type="date" id="mulai" name="mulai" value="<?= e($mulai) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div class="min-w-[10rem] flex-1">
                <label for="akhir" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal Akhir</label>
                <input type="date" id="akhir" name="akhir" value="<?= e($akhir) ?>" class="<?= e($inputClass) ?>">
            </div>
            <button type="submit" class="<?= e($btnSecondary) ?>">
                Terapkan Periode
            </button>
        </div>
    </form>

    <div id="laporan-print" class="print:shadow-none space-y-6">
        <div class="hidden print:block mb-4">
            <h2 class="font-heading text-xl text-foreground">Ringkasan Laporan</h2>
            <p class="text-sm text-muted-foreground">
                Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
            </p>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
            <a href="/admin/laporan/grooming<?= e($querySuffix) ?>"
               class="rounded-2xl border bg-card p-5 sm:p-6 transition duration-soft hover:border-admin/30 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin print:border print:shadow-none">
                <p class="text-sm text-muted-foreground">Booking Grooming</p>
                <p class="mt-1 font-heading text-3xl text-admin"><?= e((string) $ringkasan['grooming']) ?></p>
                <p class="mt-2 text-xs font-semibold text-admin print:hidden">Lihat detail →</p>
            </a>
            <a href="/admin/laporan/penitipan<?= e($querySuffix) ?>"
               class="rounded-2xl border bg-card p-5 sm:p-6 transition duration-soft hover:border-admin/30 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin print:border print:shadow-none">
                <p class="text-sm text-muted-foreground">Booking Pet Hotel</p>
                <p class="mt-1 font-heading text-3xl text-admin"><?= e((string) $ringkasan['penitipan']) ?></p>
                <p class="mt-2 text-xs font-semibold text-admin print:hidden">Lihat detail →</p>
            </a>
            <a href="/admin/laporan/pet-care<?= e($querySuffix) ?>"
               class="rounded-2xl border bg-card p-5 sm:p-6 transition duration-soft hover:border-admin/30 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-admin print:border print:shadow-none">
                <p class="text-sm text-muted-foreground">Booking Pet Care</p>
                <p class="mt-1 font-heading text-3xl text-admin"><?= e((string) $ringkasan['pet_care']) ?></p>
                <p class="mt-2 text-xs font-semibold text-admin print:hidden">Lihat detail →</p>
            </a>
        </div>

        <?php if ($trend !== []): ?>
            <div class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 print:hidden')) ?>">
                <h2 class="font-heading text-lg text-foreground">Trend Harian</h2>
                <p class="mt-1 text-xs text-muted-foreground mb-6">Booking grooming dan pendapatan terverifikasi per hari dalam periode.</p>

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
                                <div class="w-2 sm:w-3 bg-primary/70 rounded-t transition-all group-hover:bg-primary"
                                     style="height: <?= e((string) $groomingPct) ?>%"
                                     title="Booking: <?= e((string) $grooming) ?>"></div>
                                <div class="w-2 sm:w-3 bg-success rounded-t transition-all group-hover:opacity-90"
                                     style="height: <?= e((string) $revenuePct) ?>%"
                                     title="Pendapatan: Rp <?= e(number_format($revenue, 0, ',', '.')) ?>"></div>
                            </div>
                            <span class="text-[10px] sm:text-xs text-muted-foreground mt-2 truncate w-full text-center">
                                <?= e((string) ($point['label'] ?? '')) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex flex-wrap gap-4 mt-4 pt-4 border-t border-border text-xs text-muted-foreground">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-primary/70"></span> Booking grooming
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-sm bg-success"></span> Pendapatan (Rp)
                    </span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="print:hidden">
        <button type="button" onclick="window.print()" class="<?= e($btnPrimary) ?>">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.37V3.75a1.5 1.5 0 011.5-1.5h7.56a1.5 1.5 0 011.5 1.5v9.62m-10.56 0H4.5A1.5 1.5 0 003 14.87v3.38A1.5 1.5 0 004.5 19.75h15a1.5 1.5 0 001.5-1.5v-3.38a1.5 1.5 0 00-1.5-1.5h-2.22m-10.56 0h10.56"/>
            </svg>
            Cetak / Simpan PDF
        </button>
    </div>
</div>
