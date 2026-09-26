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

$inputClass = design_cn(ui_form_input_class(), 'text-sm py-2');
$btnPrimary = design_cn(ui_btn_primary(), 'gap-2');
$btnSecondary = ui_btn_secondary();
$periodeLabel = date('d/m/Y', strtotime($mulai)) . ' — ' . date('d/m/Y', strtotime($akhir));
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Data Pet Hotel',
        'Laporan · Administrasi',
        'Detail penitipan. Periode: ' . $periodeLabel,
    );
    ?>

    <?php require __DIR__ . '/_subnav.php'; ?>

    <form method="GET" action="/admin/laporan/penitipan" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 print:hidden')) ?>">
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[10rem] flex-1">
                <label for="mulai" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal Mulai</label>
                <input type="date" id="mulai" name="mulai" value="<?= e($mulai) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div class="min-w-[10rem] flex-1">
                <label for="akhir" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal Akhir</label>
                <input type="date" id="akhir" name="akhir" value="<?= e($akhir) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div class="min-w-[10rem] flex-1">
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
            <button type="submit" class="<?= e($btnSecondary) ?>">
                Filter
            </button>
        </div>
    </form>

    <div id="laporan-print" class="space-y-6 print:shadow-none">
        <div class="hidden print:block">
            <h2 class="font-heading text-xl text-foreground">Laporan Data Pet Hotel</h2>
            <p class="text-sm text-muted-foreground">
                Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
                <?php if ($filterStatus !== ''): ?>
                    · Status: <?= e($statusLabels[$filterStatus] ?? $filterStatus) ?>
                <?php endif; ?>
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-muted-foreground">Jumlah Booking</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) ($metrics['jumlah_booking'] ?? 0)) ?></p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-muted-foreground">Total Hari Dititipkan</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) ($metrics['total_hari'] ?? 0)) ?> hari</p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-muted-foreground">Total Pendapatan (Lunas)</p>
                <p class="mt-1 font-heading text-2xl text-success">
                    Rp <?= e(number_format($totalPendapatan, 0, ',', '.')) ?>
                </p>
                <p class="mt-1 text-xs text-muted-foreground">
                    Awal: Rp <?= e(number_format((float) ($metrics['pendapatan_awal'] ?? 0), 0, ',', '.')) ?>
                    · Perpanjangan: Rp <?= e(number_format((float) ($metrics['pendapatan_perpanjangan'] ?? 0), 0, ',', '.')) ?>
                </p>
            </article>
            <article class="<?= e(design_cn(design_surface('metric'), 'print:border print:shadow-none')) ?>">
                <p class="text-sm text-muted-foreground mb-2">Promo &amp; Antar-jemput</p>
                <p class="text-sm text-foreground">
                    Promo: <?= e((string) ($metrics['promo_jumlah'] ?? 0)) ?> booking
                    (Rp <?= e(number_format((float) ($metrics['promo_total_potongan'] ?? 0), 0, ',', '.')) ?> potongan)
                </p>
                <p class="mt-1.5 text-sm text-foreground">
                    Antar-jemput: <?= e((string) ($metrics['antar_jemput_jumlah'] ?? 0)) ?> booking
                    (Rp <?= e(number_format((float) ($metrics['antar_jemput_pendapatan'] ?? 0), 0, ',', '.')) ?>)
                </p>
            </article>
        </div>

        <div class="<?= e(design_cn(design_surface('panel'), 'overflow-hidden print:border print:shadow-none')) ?>">
            <div class="px-5 py-4 border-b border-border bg-muted/50">
                <h2 class="font-heading text-lg text-foreground">Detail Penitipan</h2>
            </div>
            <?php if ($rows === []): ?>
                <div class="p-8 text-center text-muted-foreground">Tidak ada data pada periode ini.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border bg-muted/50 text-left">
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Check-in</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Check-out</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Pelanggan</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Kucing</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Paket</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Hari</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Pengantaran</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider text-right">Bayar Awal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/80">
                            <?php foreach ($rows as $row): ?>
                                <?php $statusEnum = StatusPenitipan::tryFrom((string) $row['status']); ?>
                                <tr class="transition duration-soft hover:bg-muted/50">
                                    <td class="px-4 py-3.5 whitespace-nowrap text-foreground"><?= e(date('d/m/Y', strtotime((string) $row['check_in']))) ?></td>
                                    <td class="px-4 py-3.5 whitespace-nowrap text-foreground"><?= e(date('d/m/Y', strtotime((string) $row['check_out']))) ?></td>
                                    <td class="px-4 py-3.5 font-medium text-foreground"><?= e((string) $row['pelanggan_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-muted-foreground"><?= e((string) $row['kucing_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-muted-foreground"><?= e((string) $row['paket_nama']) ?></td>
                                    <td class="px-4 py-3.5 text-muted-foreground"><?= e((string) $row['lama_hari']) ?></td>
                                    <td class="px-4 py-3.5 text-muted-foreground"><?= e($opsiLabels[$row['opsi_pengantaran']] ?? (string) $row['opsi_pengantaran']) ?></td>
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
                                            <span class="text-muted-foreground">—</span>
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
