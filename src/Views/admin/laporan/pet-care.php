<?php

declare(strict_types=1);

use App\Enums\StatusBookingPetCare;

$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$filterStatus = $filterStatus ?? '';
$filterLayananId = $filterLayananId ?? '';
$metrics = $metrics ?? [];
$rows = $rows ?? [];
$statusLabels = $statusLabels ?? [];
$layananList = $layananList ?? [];
$activeTab = $activeTab ?? 'pet-care';

$inputClass = design_cn(ui_form_input_class(), 'text-sm py-2');
$btnPrimary = design_cn(ui_btn_primary(), 'gap-2');
$btnSecondary = ui_btn_secondary();
$periodeLabel = date('d/m/Y', strtotime($mulai)) . ' — ' . date('d/m/Y', strtotime($akhir));
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Data Booking Pet Care',
        'Laporan · Administrasi',
        'Detail booking pet care. Periode: ' . $periodeLabel . ' · Pembayaran di loket (tanpa pendapatan di sistem)',
    );
    ?>

    <?php require __DIR__ . '/_subnav.php'; ?>

    <form method="GET" action="/admin/laporan/pet-care" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 print:hidden')) ?>">
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[9rem] flex-1">
                <label for="mulai" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal Mulai</label>
                <input type="date" id="mulai" name="mulai" value="<?= e($mulai) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div class="min-w-[9rem] flex-1">
                <label for="akhir" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal Akhir</label>
                <input type="date" id="akhir" name="akhir" value="<?= e($akhir) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div class="min-w-[9rem] flex-1">
                <label for="status" class="mb-1.5 block text-sm font-semibold text-foreground">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?>">
                    <option value="">Semua</option>
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="min-w-[9rem] flex-1">
                <label for="layanan_id" class="mb-1.5 block text-sm font-semibold text-foreground">Layanan</label>
                <select id="layanan_id" name="layanan_id" class="<?= e($inputClass) ?>">
                    <option value="">Semua</option>
                    <?php foreach ($layananList as $layanan): ?>
                        <option value="<?= e((string) $layanan['id']) ?>"
                            <?= $filterLayananId === (string) $layanan['id'] ? 'selected' : '' ?>>
                            <?= e((string) $layanan['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="<?= e($btnSecondary) ?>">
                Filter
            </button>
        </div>
    </form>

    <div id="laporan-print" class="space-y-6 print:shadow-none">
        <div class="hidden print:block">
            <h2 class="font-heading text-xl text-foreground">Laporan Data Booking Pet Care</h2>
            <p class="text-sm text-muted-foreground">
                Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-muted-foreground">Jumlah Booking</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) ($metrics['jumlah_booking'] ?? 0)) ?></p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-muted-foreground mb-2">Per Layanan</p>
                <?php if (($metrics['breakdown_layanan'] ?? []) === []): ?>
                    <p class="text-sm text-muted-foreground">—</p>
                <?php else: ?>
                    <ul class="text-sm space-y-1.5">
                        <?php foreach ($metrics['breakdown_layanan'] as $item): ?>
                            <li class="flex justify-between gap-2">
                                <span class="text-foreground"><?= e($item['layanan_nama']) ?></span>
                                <span class="text-muted-foreground shrink-0"><?= e((string) $item['jumlah']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-muted-foreground mb-2">Per Slot (Top 10)</p>
                <?php if (($metrics['breakdown_slot'] ?? []) === []): ?>
                    <p class="text-sm text-muted-foreground">—</p>
                <?php else: ?>
                    <ul class="text-sm space-y-1.5">
                        <?php foreach (array_slice($metrics['breakdown_slot'], 0, 10) as $item): ?>
                            <li class="flex justify-between gap-2">
                                <span class="text-foreground"><?= e(date('d/m/Y', strtotime($item['tanggal']))) ?> <?= e(substr($item['slot_waktu'], 0, 5)) ?></span>
                                <span class="text-muted-foreground shrink-0"><?= e((string) $item['jumlah']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
        </div>

        <div class="<?= e(design_cn(design_surface('panel'), 'overflow-hidden print:border print:shadow-none')) ?>">
            <div class="px-5 py-4 border-b border-border bg-muted/50">
                <h2 class="font-heading text-lg text-foreground">Detail Booking</h2>
            </div>
            <?php if ($rows === []): ?>
                <div class="p-8 text-center text-muted-foreground">Tidak ada data pada periode ini.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/50 text-left">
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Slot</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Pelanggan</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Kucing</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Layanan</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/80">
                            <?php foreach ($rows as $row): ?>
                                <?php $statusEnum = StatusBookingPetCare::tryFrom((string) $row['status']); ?>
                                <tr class="transition duration-soft hover:bg-muted/50">
                                    <td class="px-4 py-3.5 whitespace-nowrap text-foreground"><?= e(date('d/m/Y', strtotime((string) $row['tanggal']))) ?></td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-muted-foreground"><?= e(substr((string) $row['slot_waktu'], 0, 5)) ?> WIB</td>
                                    <td class="px-4 py-3.5 font-medium text-foreground"><?= e((string) $row['pelanggan_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-muted-foreground"><?= e((string) $row['kucing_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-muted-foreground"><?= e((string) $row['layanan_nama']) ?></td>
                                    <td class="px-4 py-3.5">
                                        <?php if ($statusEnum): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= e($statusEnum->badgeClass()) ?>">
                                                <?= e($statusLabels[$row['status']] ?? (string) $row['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
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
