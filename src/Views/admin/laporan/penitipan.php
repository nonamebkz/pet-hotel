<?php

declare(strict_types=1);

use App\Enums\StatusPenitipan;

$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$filterStatus = $filterStatus ?? '';
$metrics = $metrics ?? [];
$rows = $rows ?? [];
$statusLabels = $statusLabels ?? [];
$opsiLabels = $opsiLabels ?? [];
$statusPembayaranLunas = $statusPembayaranLunas ?? 'LUNAS';
$activeTab = $activeTab ?? 'penitipan';
$totalPendapatan = (float) ($metrics['pendapatan_awal'] ?? 0) + (float) ($metrics['pendapatan_perpanjangan'] ?? 0);

$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
$btnPrimary = 'cursor-pointer inline-flex items-center justify-center gap-2 rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2';
$btnSecondary = 'cursor-pointer inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-card px-4 py-2.5 text-sm font-semibold text-content-secondary transition duration-soft hover:bg-admin-soft hover:text-admin focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';
$periodeLabel = date('d/m/Y', strtotime($mulai)) . ' — ' . date('d/m/Y', strtotime($akhir));
?>
<div class="font-body space-y-6">
    <section class="<?= e(design_cn(design_surface('panel'), 'relative overflow-hidden p-6 sm:p-8 print:hidden')) ?>">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-success/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Laporan</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Data Pet Hotel</h1>
                <p class="mt-2 text-sm text-content-secondary">
                    Periode: <span class="font-medium text-content-primary"><?= e($periodeLabel) ?></span>
                </p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-sm" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
            </div>
        </div>
    </section>

    <?php require __DIR__ . '/_subnav.php'; ?>

    <form method="GET" action="/admin/laporan/penitipan" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 print:hidden')) ?>">
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
            <h2 class="font-heading text-xl text-content-primary">Laporan Data Pet Hotel</h2>
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
                <p class="text-sm text-content-secondary">Total Hari Dititipkan</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) ($metrics['total_hari'] ?? 0)) ?> hari</p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-content-secondary">Total Pendapatan (Lunas)</p>
                <p class="mt-1 font-heading text-2xl text-success">
                    Rp <?= e(number_format($totalPendapatan, 0, ',', '.')) ?>
                </p>
                <p class="mt-1 text-xs text-content-secondary">
                    Awal: Rp <?= e(number_format((float) ($metrics['pendapatan_awal'] ?? 0), 0, ',', '.')) ?>
                    · Perpanjangan: Rp <?= e(number_format((float) ($metrics['pendapatan_perpanjangan'] ?? 0), 0, ',', '.')) ?>
                </p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-content-secondary mb-2">Promo &amp; Antar-jemput</p>
                <p class="text-sm text-content-primary">
                    Promo: <?= e((string) ($metrics['promo_jumlah'] ?? 0)) ?> booking
                    (Rp <?= e(number_format((float) ($metrics['promo_total_potongan'] ?? 0), 0, ',', '.')) ?> potongan)
                </p>
                <p class="mt-1.5 text-sm text-content-primary">
                    Antar-jemput: <?= e((string) ($metrics['antar_jemput_jumlah'] ?? 0)) ?> booking
                    (Rp <?= e(number_format((float) ($metrics['antar_jemput_pendapatan'] ?? 0), 0, ',', '.')) ?>)
                </p>
            </article>
        </div>

        <div class="<?= e(design_cn(design_surface('panel'), 'overflow-hidden print:border print:shadow-none')) ?>">
            <div class="px-5 py-4 border-b border-border bg-admin-soft/40">
                <h2 class="font-heading text-lg text-content-primary">Detail Penitipan</h2>
            </div>
            <?php if ($rows === []): ?>
                <div class="p-8 text-center text-content-secondary">Tidak ada data pada periode ini.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-admin-soft/40 text-left">
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Check-in</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Check-out</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Pelanggan</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Kucing</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Paket</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Hari</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Pengantaran</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider text-right">Bayar Awal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/80">
                            <?php foreach ($rows as $row): ?>
                                <?php $statusEnum = StatusPenitipan::tryFrom((string) $row['status']); ?>
                                <tr class="transition duration-soft hover:bg-admin-soft/30">
                                    <td class="px-4 py-3.5 whitespace-nowrap text-content-primary"><?= e(date('d/m/Y', strtotime((string) $row['check_in']))) ?></td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-content-primary"><?= e(date('d/m/Y', strtotime((string) $row['check_out']))) ?></td>
                                    <td class="px-4 py-3.5 font-medium text-content-primary"><?= e((string) $row['pelanggan_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-content-secondary"><?= e((string) $row['kucing_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-content-secondary"><?= e((string) $row['paket_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-content-secondary"><?= e((string) $row['lama_hari']) ?></td>
                                    <td class="px-4 py-3.5 text-content-secondary"><?= e($opsiLabels[$row['opsi_pengantaran']] ?? (string) $row['opsi_pengantaran']) ?></td>
                                    <td class="px-4 py-3.5">
                                        <?php if ($statusEnum): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= e($statusEnum->badgeClass()) ?>">
                                                <?= e($statusLabels[$row['status']] ?? (string) $row['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <?php if ((string) ($row['status_pembayaran_awal'] ?? '') === $statusPembayaranLunas): ?>
                                            <span class="font-medium text-admin">Rp <?= e(number_format((float) $row['total_bayar_awal'], 0, ',', '.')) ?></span>
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
