<?php

declare(strict_types=1);

use App\Enums\JenisLayanan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusRefund;

$rows = $rows ?? [];
$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$filterStatus = $filterStatus ?? '';
$filterJenis = $filterJenis ?? '';
$filterQ = $filterQ ?? '';
$statusLabels = $statusLabels ?? StatusPembayaran::labels();
$refundLabels = $refundLabels ?? StatusRefund::labels();
$activeFilters = $activeFilters ?? [];
$hasActiveFilter = ($filterStatus !== '' || $filterJenis !== '' || $filterQ !== ''
    || $mulai !== date('Y-m-01') || $akhir !== date('Y-m-t'));

$countLunas = 0;
$countPending = 0;
$revenueLunas = 0.0;
foreach ($rows as $statRow) {
    $st = (string) ($statRow['status_pembayaran'] ?? '');
    if ($st === StatusPembayaran::LUNAS->value) {
        $countLunas++;
        $revenueLunas += (float) ($statRow['total_bayar'] ?? 0);
    } elseif ($st === StatusPembayaran::MENUNGGU_VERIFIKASI->value) {
        $countPending++;
    }
}

$btnPrimary = design_cn(ui_btn_primary(), 'gap-2');
$btnSecondary = ui_btn_secondary();
$inputClass = design_cn(
    'w-full rounded-xl border border-input bg-background px-3.5 py-2.5 text-base sm:text-sm text-foreground transition',
    'hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25',
);

$jenisBadge = static function (string $label): string {
    return match (true) {
        str_contains($label, 'Grooming') => design_status_badge('muted') . ' !bg-primary/10 !text-primary',
        str_contains($label, 'Perpanjangan') => design_status_badge('muted') . ' !bg-amber-500/10 !text-amber-800 dark:!text-amber-200',
        default => design_status_badge('success'),
    };
};

$periodeLabel = date('d/m/Y', strtotime($mulai)) . ' — ' . date('d/m/Y', strtotime($akhir));
$advancedOpen = $filterJenis !== '' || $filterQ !== '';
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Riwayat Transaksi',
        'Keuangan · Pembayaran',
        'Arsip pembayaran grooming, penitipan, dan perpanjangan. Periode: ' . $periodeLabel,
    );
    ?>

    <nav class="flex flex-wrap gap-2" aria-label="Navigasi pembayaran">
        <a href="/admin/grooming/pembayaran"
           class="<?= e(design_cn(ui_btn_secondary(), 'rounded-xl px-4 py-2.5 text-sm font-medium text-muted-foreground')) ?>">
            Verifikasi Grooming
        </a>
        <a href="/admin/penitipan/pembayaran"
           class="<?= e(design_cn(ui_btn_secondary(), 'rounded-xl px-4 py-2.5 text-sm font-medium text-muted-foreground')) ?>">
            Verifikasi Penitipan
        </a>
        <span class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold <?= e(design_cn(ui_btn_primary())) ?>">
            Riwayat Transaksi
        </span>
    </nav>

    <?php if ($rows !== [] || $hasActiveFilter): ?>
        <div class="grid sm:grid-cols-3 gap-4">
            <article class="<?= e(design_surface('metric')) ?>">
                <p class="text-sm text-muted-foreground">Ditampilkan</p>
                <p class="mt-1 font-heading text-2xl text-primary"><?= e((string) count($rows)) ?></p>
                <p class="mt-1 text-xs text-muted-foreground">transaksi pada filter aktif</p>
            </article>
            <article class="<?= e(design_surface('metric')) ?>">
                <p class="text-sm text-muted-foreground">Lunas</p>
                <p class="mt-1 font-heading text-2xl text-success"><?= e((string) $countLunas) ?></p>
                <p class="mt-1 text-xs text-muted-foreground">
                    Rp <?= e(number_format($revenueLunas, 0, ',', '.')) ?>
                </p>
            </article>
            <article class="rounded-2xl border bg-card p-5 <?= $countPending > 0 ? 'border-amber-200 bg-warning-bg/30' : '' ?>">
                <p class="text-sm text-muted-foreground">Menunggu Verifikasi</p>
                <p class="mt-1 font-heading text-2xl <?= $countPending > 0 ? 'text-amber-700' : 'text-primary' ?>"><?= e((string) $countPending) ?></p>
                <?php if ($countPending > 0): ?>
                    <a href="/admin/grooming/pembayaran" class="mt-1 inline-flex cursor-pointer text-xs font-semibold text-primary hover:underline focus:outline-none focus-visible:underline">
                        Ke antrian verifikasi →
                    </a>
                <?php else: ?>
                    <p class="mt-1 text-xs text-muted-foreground">Tidak ada antrean di hasil ini</p>
                <?php endif; ?>
            </article>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/transaksi" class="rounded-2xl border bg-card p-5 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-heading text-lg text-foreground">Filter</h2>
            <?php if ($hasActiveFilter): ?>
                <a href="/admin/transaksi"
                   class="cursor-pointer text-xs font-semibold text-muted-foreground transition duration-soft hover:text-primary focus:outline-none focus-visible:underline">
                    Reset ke bulan ini
                </a>
            <?php endif; ?>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="mulai" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal mulai</label>
                <input type="date" id="mulai" name="mulai" value="<?= e($mulai) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div>
                <label for="akhir" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal akhir</label>
                <input type="date" id="akhir" name="akhir" value="<?= e($akhir) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-foreground">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?>">
                    <option value="">Semua status</option>
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="<?= e(design_cn($btnPrimary, 'w-full')) ?>">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.474.09.917.424.917.933v.25a1 1 0 01-.553.894A21.04 21.04 0 0112 8.5a21.04 21.04 0 01-8.447-2.745A1 1 0 013 4.861v-.25c0-.509.443-.843.917-.933A48.35 48.35 0 0112 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.5v12.25"/>
                    </svg>
                    Terapkan
                </button>
            </div>
        </div>

        <details class="group rounded-xl border border-border bg-page/50 open:bg-page/80" <?= $advancedOpen ? 'open' : '' ?>>
            <summary class="cursor-pointer list-none flex items-center justify-between gap-2 px-4 py-3 text-sm font-semibold text-primary transition duration-soft hover:bg-muted/50/50 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                <span class="inline-flex items-center gap-2">
                    <svg class="h-4 w-4 transition duration-soft group-open:rotate-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                    Filter lanjutan
                </span>
                <?php if ($advancedOpen): ?>
                    <span class="text-[10px] font-semibold uppercase tracking-wide rounded-lg bg-muted/50 px-2 py-0.5 text-primary">Aktif</span>
                <?php endif; ?>
            </summary>
            <div class="grid gap-4 sm:grid-cols-2 px-4 pb-4 pt-1 border-t border-border/80">
                <div>
                    <label for="jenis" class="mb-1.5 block text-sm font-semibold text-foreground">Jenis layanan</label>
                    <select id="jenis" name="jenis" class="<?= e($inputClass) ?>">
                        <option value="">Semua layanan</option>
                        <option value="<?= e(JenisLayanan::GROOMING->value) ?>"
                            <?= $filterJenis === JenisLayanan::GROOMING->value ? 'selected' : '' ?>>
                            Grooming
                        </option>
                        <option value="<?= e(JenisLayanan::PENITIPAN->value) ?>"
                            <?= $filterJenis === JenisLayanan::PENITIPAN->value ? 'selected' : '' ?>>
                            Penitipan
                        </option>
                    </select>
                </div>
                <div>
                    <label for="q" class="mb-1.5 block text-sm font-semibold text-foreground">Pelanggan</label>
                    <input type="search" id="q" name="q" value="<?= e($filterQ) ?>"
                           placeholder="Cari nama pelanggan..."
                           class="<?= e($inputClass) ?>">
                </div>
            </div>
        </details>
    </form>

    <?php if ($activeFilters !== []): ?>
        <?php
        ui_filter_chips(
            $activeFilters,
            '/admin/transaksi',
            count($rows),
            'transaksi',
        );
        ?>
    <?php endif; ?>

    <?php if ($rows === []): ?>
        <?php
        if ($hasActiveFilter) {
            $variant = 'filtered';
            $title = 'Tidak ada transaksi untuk filter ini';
            $description = 'Coba ubah periode, status, jenis layanan, atau kata kunci pencarian.';
            $ctaLabel = 'Reset Filter';
            $ctaHref = '/admin/transaksi';
            $ctaClass = $btnPrimary;
        } else {
            $variant = 'empty';
            $title = 'Belum ada transaksi tercatat';
            $description = 'Transaksi pembayaran akan muncul setelah pelanggan melakukan booking.';
            $ctaLabel = null;
            $ctaHref = null;
        }
        require __DIR__ . '/../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="<?= e(design_cn(design_surface('panel'), 'hidden overflow-hidden md:block')) ?>">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border bg-muted/50/40">
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Tanggal</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Pelanggan</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Jenis</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Layanan</th>
                            <th class="text-right px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Total</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Bukti</th>
                            <th class="text-right px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/80">
                        <?php foreach ($rows as $row): ?>
                            <?php
                            $statusEnum = StatusPembayaran::tryFrom((string) $row['status_pembayaran']);
                            $refundEnum = StatusRefund::tryFrom((string) ($row['status_refund'] ?? StatusRefund::TIDAK_ADA->value));
                            $jenisLabel = (string) $row['tagihan_jenis'];
                            ?>
                            <tr class="transition duration-soft hover:bg-muted/50/30">
                                <td class="px-4 py-3.5 whitespace-nowrap align-top">
                                    <div class="font-medium text-foreground"><?= e(date('d/m/Y', strtotime((string) $row['created_at']))) ?></div>
                                    <div class="text-xs text-muted-foreground"><?= e(date('H:i', strtotime((string) $row['created_at']))) ?></div>
                                    <?php if (!empty($row['dibayar_at'])): ?>
                                        <div class="text-xs text-success mt-1 font-medium">
                                            Lunas <?= e(date('d/m H:i', strtotime((string) $row['dibayar_at']))) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 align-top">
                                    <div class="font-semibold text-foreground"><?= e((string) $row['pelanggan_nama']) ?></div>
                                    <?php if (!empty($row['pelanggan_telepon'])): ?>
                                        <div class="text-xs text-muted-foreground"><?= e((string) $row['pelanggan_telepon']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap align-top">
                                    <span class="inline-flex rounded-lg px-2 py-0.5 text-xs font-semibold <?= e($jenisBadge($jenisLabel)) ?>">
                                        <?= e($jenisLabel) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 align-top">
                                    <div class="text-foreground"><?= e((string) ($row['layanan_label'] ?? '')) ?></div>
                                    <div class="text-xs text-muted-foreground"><?= e((string) ($row['tanggal_display'] ?? '')) ?></div>
                                </td>
                                <td class="px-4 py-3.5 text-right font-semibold text-primary whitespace-nowrap align-top">
                                    Rp <?= e(number_format((float) $row['total_bayar'], 0, ',', '.')) ?>
                                </td>
                                <td class="px-4 py-3.5 align-top">
                                    <div class="flex flex-wrap gap-1">
                                        <?php if ($statusEnum): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-lg font-medium <?= e($statusEnum->badgeClass()) ?>">
                                                <?= e($statusLabels[$statusEnum->value] ?? $statusEnum->value) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($row['bukti_ditolak'])): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-lg font-medium bg-red-100 text-red-800">Bukti Ditolak</span>
                                        <?php endif; ?>
                                        <?php if ($refundEnum && $refundEnum !== StatusRefund::TIDAK_ADA): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-lg font-medium <?= e($refundEnum->badgeClass()) ?>">
                                                <?= e($refundLabels[$refundEnum->value] ?? $refundEnum->value) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($row['nomor_invoice'])): ?>
                                        <div class="text-xs text-muted-foreground mt-1.5 font-mono"><?= e((string) $row['nomor_invoice']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 align-top">
                                    <?php if (!empty($row['bukti_file_url'])): ?>
                                        <a href="<?= e((string) $row['bukti_file_url']) ?>"
                                           target="_blank" rel="noopener noreferrer"
                                           class="cursor-pointer inline-flex items-center gap-1 text-xs font-semibold text-primary transition duration-soft hover:underline focus:outline-none focus-visible:underline">
                                            Lihat bukti
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-muted-foreground/60">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-right whitespace-nowrap align-top">
                                    <a href="<?= e((string) $row['admin_booking_url']) ?>"
                                       class="cursor-pointer inline-flex items-center rounded-xl border border-border bg-background px-3 py-1.5 text-xs font-semibold text-primary transition duration-soft hover:bg-muted/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                                        Booking →
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="md:hidden space-y-3">
            <?php foreach ($rows as $row): ?>
                <?php
                $statusEnum = StatusPembayaran::tryFrom((string) $row['status_pembayaran']);
                $refundEnum = StatusRefund::tryFrom((string) ($row['status_refund'] ?? StatusRefund::TIDAK_ADA->value));
                $jenisLabel = (string) $row['tagihan_jenis'];
                ?>
                <article class="<?= e(design_cn(design_interactive('listArticle'), 'space-y-3')) ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-foreground truncate"><?= e((string) $row['pelanggan_nama']) ?></p>
                            <p class="text-xs text-muted-foreground mt-0.5">
                                <?= e(date('d/m/Y H:i', strtotime((string) $row['created_at']))) ?>
                            </p>
                        </div>
                        <span class="shrink-0 inline-flex rounded-lg px-2 py-0.5 text-xs font-semibold <?= e($jenisBadge($jenisLabel)) ?>">
                            <?= e($jenisLabel) ?>
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-foreground"><?= e((string) ($row['layanan_label'] ?? '')) ?></p>
                        <p class="text-xs text-muted-foreground"><?= e((string) ($row['tanggal_display'] ?? '')) ?></p>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-heading text-lg text-primary">
                            Rp <?= e(number_format((float) $row['total_bayar'], 0, ',', '.')) ?>
                        </p>
                        <div class="flex flex-wrap gap-1 justify-end">
                            <?php if ($statusEnum): ?>
                                <span class="text-xs px-2 py-0.5 rounded-lg font-medium <?= e($statusEnum->badgeClass()) ?>">
                                    <?= e($statusLabels[$statusEnum->value] ?? $statusEnum->value) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($row['bukti_ditolak'])): ?>
                                <span class="text-xs px-2 py-0.5 rounded-lg font-medium bg-red-100 text-red-800">Bukti Ditolak</span>
                            <?php endif; ?>
                            <?php if ($refundEnum && $refundEnum !== StatusRefund::TIDAK_ADA): ?>
                                <span class="text-xs px-2 py-0.5 rounded-lg font-medium <?= e($refundEnum->badgeClass()) ?>">
                                    <?= e($refundLabels[$refundEnum->value] ?? $refundEnum->value) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-border/80">
                        <?php if (!empty($row['bukti_file_url'])): ?>
                            <a href="<?= e((string) $row['bukti_file_url']) ?>"
                               target="_blank" rel="noopener noreferrer"
                               class="cursor-pointer inline-flex flex-1 items-center justify-center rounded-xl border border-border bg-background px-3 py-2 text-xs font-semibold text-primary transition duration-soft hover:bg-muted/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                                Lihat bukti
                            </a>
                        <?php endif; ?>
                        <a href="<?= e((string) $row['admin_booking_url']) ?>"
                           class="<?= e(design_cn($btnPrimary, 'flex-1 text-xs')) ?>">
                            Lihat booking
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="text-xs text-muted-foreground px-1">
            <?= e((string) count($rows)) ?> transaksi ditampilkan untuk periode <?= e($periodeLabel) ?>.
        </p>
    <?php endif; ?>
</div>
