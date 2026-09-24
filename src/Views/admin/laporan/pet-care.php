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
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Data Booking Pet Care</h1>
                <p class="mt-2 text-sm text-content-secondary">
                    Periode: <span class="font-medium text-content-primary"><?= e($periodeLabel) ?></span>
                    · Pembayaran di loket (tanpa pendapatan di sistem)
                </p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-sm" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75"/>
                </svg>
            </div>
        </div>
    </section>

    <?php require __DIR__ . '/_subnav.php'; ?>

    <form method="GET" action="/admin/laporan/pet-care" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 print:hidden')) ?>">
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[9rem] flex-1">
                <label for="mulai" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal Mulai</label>
                <input type="date" id="mulai" name="mulai" value="<?= e($mulai) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div class="min-w-[9rem] flex-1">
                <label for="akhir" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal Akhir</label>
                <input type="date" id="akhir" name="akhir" value="<?= e($akhir) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div class="min-w-[9rem] flex-1">
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
            <div class="min-w-[9rem] flex-1">
                <label for="layanan_id" class="mb-1.5 block text-sm font-semibold text-content-primary">Layanan</label>
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
            <h2 class="font-heading text-xl text-content-primary">Laporan Data Booking Pet Care</h2>
            <p class="text-sm text-content-secondary">
                Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-content-secondary">Jumlah Booking</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) ($metrics['jumlah_booking'] ?? 0)) ?></p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-content-secondary mb-2">Per Layanan</p>
                <?php if (($metrics['breakdown_layanan'] ?? []) === []): ?>
                    <p class="text-sm text-content-secondary">—</p>
                <?php else: ?>
                    <ul class="text-sm space-y-1.5">
                        <?php foreach ($metrics['breakdown_layanan'] as $item): ?>
                            <li class="flex justify-between gap-2">
                                <span class="text-content-primary"><?= e($item['layanan_nama']) ?></span>
                                <span class="text-content-secondary shrink-0"><?= e((string) $item['jumlah']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-content-secondary mb-2">Per Slot (Top 10)</p>
                <?php if (($metrics['breakdown_slot'] ?? []) === []): ?>
                    <p class="text-sm text-content-secondary">—</p>
                <?php else: ?>
                    <ul class="text-sm space-y-1.5">
                        <?php foreach (array_slice($metrics['breakdown_slot'], 0, 10) as $item): ?>
                            <li class="flex justify-between gap-2">
                                <span class="text-content-primary"><?= e(date('d/m/Y', strtotime($item['tanggal']))) ?> <?= e(substr($item['slot_waktu'], 0, 5)) ?></span>
                                <span class="text-content-secondary shrink-0"><?= e((string) $item['jumlah']) ?></span>
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
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Slot</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Pelanggan</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Kucing</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Layanan</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/80">
                            <?php foreach ($rows as $row): ?>
                                <?php $statusEnum = StatusBookingPetCare::tryFrom((string) $row['status']); ?>
                                <tr class="transition duration-soft hover:bg-admin-soft/30">
                                    <td class="px-4 py-3.5 whitespace-nowrap text-content-primary"><?= e(date('d/m/Y', strtotime((string) $row['tanggal']))) ?></td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-content-secondary"><?= e(substr((string) $row['slot_waktu'], 0, 5)) ?> WIB</td>
                                    <td class="px-4 py-3.5 font-medium text-content-primary"><?= e((string) $row['pelanggan_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-content-secondary"><?= e((string) $row['kucing_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-content-secondary"><?= e((string) $row['layanan_nama']) ?></td>
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
