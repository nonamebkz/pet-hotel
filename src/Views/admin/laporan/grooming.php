<?php

declare(strict_types=1);

use App\Enums\StatusBookingGrooming;

$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$filterStatus = $filterStatus ?? '';
$metrics = $metrics ?? [];
$rows = $rows ?? [];
$statusLabels = $statusLabels ?? [];
$opsiLabels = $opsiLabels ?? [];
$statusPembayaranLunas = $statusPembayaranLunas ?? 'LUNAS';
$activeTab = $activeTab ?? 'grooming';

$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
$btnPrimary = 'cursor-pointer inline-flex items-center justify-center gap-2 rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2';
$btnSecondary = 'cursor-pointer inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-content-secondary transition duration-soft hover:bg-admin-soft hover:text-admin focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';
$periodeLabel = date('d/m/Y', strtotime($mulai)) . ' — ' . date('d/m/Y', strtotime($akhir));
?>
<div class="font-body space-y-6">
    <section class="<?= e(design_cn(design_surface('panel'), 'relative overflow-hidden p-6 sm:p-8 print:hidden')) ?>">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Laporan</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Data Grooming</h1>
                <p class="mt-2 text-sm text-content-secondary">
                    Periode: <span class="font-medium text-content-primary"><?= e($periodeLabel) ?></span>
                </p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-sm" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21a48.309 48.309 0 01-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                </svg>
            </div>
        </div>
    </section>

    <?php require __DIR__ . '/_subnav.php'; ?>

    <form method="GET" action="/admin/laporan/grooming" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 print:hidden')) ?>">
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[10rem] flex-1">
                <label for="mulai" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal Mulai</label>
                <input type="date" id="mulai" name="mulai" value="<?= e($mulai) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div class="min-w-[10rem] flex-1">
                <label for="akhir" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal Akhir</label>
                <input type="date" id="akhir" name="akhir" value="<?= e($akhir) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div class="min-w-[10rem] flex-1">
                <label for="status" class="mb-1.5 block text-sm font-semibold text-content-primary">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?>">
                    <option value="">Semua</option>
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
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
            <h2 class="font-heading text-xl text-content-primary">Laporan Data Grooming</h2>
            <p class="text-sm text-content-secondary">
                Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
                <?php if ($filterStatus !== ''): ?>
                    · Status: <?= e($statusLabels[$filterStatus] ?? $filterStatus) ?>
                <?php endif; ?>
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-content-secondary">Jumlah Booking</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) ($metrics['jumlah_booking'] ?? 0)) ?></p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-content-secondary">Total Pendapatan (Lunas)</p>
                <p class="mt-1 font-heading text-2xl text-success">
                    Rp <?= e(number_format((float) ($metrics['total_pendapatan'] ?? 0), 0, ',', '.')) ?>
                </p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-content-secondary">Antar-jemput</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) ($metrics['antar_jemput_jumlah'] ?? 0)) ?> booking</p>
                <p class="mt-1 text-xs text-content-secondary">
                    Biaya: Rp <?= e(number_format((float) ($metrics['antar_jemput_pendapatan'] ?? 0), 0, ',', '.')) ?>
                </p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-content-secondary mb-2">Per Jenis Grooming</p>
                <?php if (($metrics['breakdown_jenis'] ?? []) === []): ?>
                    <p class="text-sm text-content-secondary">—</p>
                <?php else: ?>
                    <ul class="text-sm space-y-1.5">
                        <?php foreach ($metrics['breakdown_jenis'] as $item): ?>
                            <li class="flex justify-between gap-2">
                                <span class="text-content-primary"><?= e($item['jenis_nama']) ?></span>
                                <span class="text-content-secondary shrink-0"><?= e((string) $item['jumlah']) ?> · Rp <?= e(number_format((float) $item['pendapatan'], 0, ',', '.')) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
        </div>

        <div class="<?= e(design_cn(design_surface('panel'), 'overflow-hidden print:border print:shadow-none')) ?>">
            <div class="px-5 py-4 border-b border-border bg-admin-soft/40">
                <h2 class="font-heading text-lg text-content-primary">Detail Booking</h2>
            </div>
            <?php if ($rows === []): ?>
                <div class="p-8 text-center text-content-secondary">Tidak ada data pada periode ini.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-admin-soft/40 text-left">
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Pelanggan</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Kucing</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Jenis</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Pengantaran</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider text-right">Total Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/80">
                            <?php foreach ($rows as $row): ?>
                                <?php $statusEnum = StatusBookingGrooming::tryFrom((string) $row['status']); ?>
                                <tr class="transition duration-soft hover:bg-admin-soft/30">
                                    <td class="px-4 py-3.5 whitespace-nowrap text-content-primary"><?= e(date('d/m/Y', strtotime((string) $row['tanggal']))) ?></td>
                                    <td class="px-4 py-3.5 font-medium text-content-primary"><?= e((string) $row['pelanggan_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-content-secondary"><?= e((string) $row['kucing_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-content-secondary"><?= e((string) $row['jenis_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-content-secondary"><?= e($opsiLabels[$row['opsi_pengantaran']] ?? (string) $row['opsi_pengantaran']) ?></td>
                                    <td class="px-4 py-3.5">
                                        <?php if ($statusEnum): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= e($statusEnum->badgeClass()) ?>">
                                                <?= e($statusLabels[$row['status']] ?? (string) $row['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <?php if ((string) ($row['status_pembayaran'] ?? '') === $statusPembayaranLunas): ?>
                                            <span class="font-medium text-admin">Rp <?= e(number_format((float) $row['total_bayar'], 0, ',', '.')) ?></span>
                                        <?php else: ?>
                                            <span class="text-content-secondary">—</span>
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
