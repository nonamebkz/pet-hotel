<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusPerpanjanganPenitipan;

$perpanjanganList = $perpanjanganList ?? [];
$statusLabels = $statusLabels ?? [];
$filterStatus = $filterStatus ?? '';
$hasFilter = $filterStatus !== '';

$heroClass = design_cn(design_surface('panel'), 'relative overflow-hidden p-6 sm:p-8');
$filterFormClass = design_cn(design_surface('metric'), 'p-5');
$inputClass = 'w-full rounded-lg border border-input bg-background px-3.5 py-2.5 text-base sm:text-sm text-foreground transition hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
$btnSuccess = 'cursor-pointer inline-flex items-center justify-center rounded-lg px-3.5 py-2 text-xs font-medium touch-target bg-emerald-600 text-white hover:opacity-90';
$btnDanger = 'cursor-pointer inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/10 px-3.5 py-2 text-xs font-semibold text-destructive transition hover:bg-destructive/15';
?>
<div class="font-body space-y-6">
    <section class="<?= e($heroClass) ?>">
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Penitipan</p>
            <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-foreground">Perpanjangan</h1>
            <p class="mt-2 text-sm text-muted-foreground">Konfirmasi permintaan perpanjangan check-out dari pelanggan.</p>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <form method="GET" action="/admin/penitipan/perpanjangan" class="<?= e($filterFormClass) ?>">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-foreground">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?>" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    <?php foreach ($statusLabels as $v => $l): ?>
                        <option value="<?= e($v) ?>" <?= $filterStatus === $v ? 'selected' : '' ?>><?= e($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <?php if ($perpanjanganList === []): ?>
        <?php
        $variant = $hasFilter ? 'filtered' : 'empty';
        $title = $hasFilter ? 'Tidak ada perpanjangan untuk filter ini' : 'Belum ada permintaan perpanjangan';
        $description = $hasFilter ? 'Coba ubah filter status.' : 'Permintaan perpanjangan dari pelanggan akan muncul di sini.';
        $ctaLabel = $hasFilter ? 'Reset Filter' : null;
        $ctaHref = $hasFilter ? '/admin/penitipan/perpanjangan' : null;
        $ctaClass = ui_btn_primary();
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($perpanjanganList as $pp): ?>
                <?php
                $needsConfirm = (string) $pp['status'] === StatusPerpanjanganPenitipan::MENUNGGU_KONFIRMASI->value;
                $articleClass = design_cn(
                    design_interactive('listArticle'),
                    'p-5 space-y-3',
                    $needsConfirm ? 'border-amber-200 bg-warning-bg/25' : '',
                );
                ?>
                <article class="<?= e($articleClass) ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-foreground">
                                <?= e((string) $pp['pelanggan_nama']) ?>
                                <span class="font-normal text-muted-foreground">· <?= e((string) $pp['kucing_nama']) ?></span>
                            </h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                <?= e(date('d/m/Y', strtotime((string) $pp['check_out_sebelum']))) ?>
                                → <?= e(date('d/m/Y', strtotime((string) $pp['check_out_baru']))) ?>
                                · +<?= (int) $pp['tambah_hari'] ?> hari
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex text-xs px-2.5 py-1 rounded-lg font-medium bg-primary/10 text-primary">
                                <?= e($statusLabels[$pp['status']] ?? (string) $pp['status']) ?>
                            </span>
                            <p class="mt-2 font-heading text-primary">
                                Rp <?= e(number_format((float) $pp['subtotal_tambahan'], 0, ',', '.')) ?>
                            </p>
                        </div>
                    </div>

                    <?php if ($needsConfirm): ?>
                        <div class="flex flex-wrap gap-2 pt-2 border-t border-border/80">
                            <form method="POST" action="/admin/penitipan/perpanjangan/konfirmasi" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $pp['id']) ?>">
                                <button type="submit" class="<?= e($btnSuccess) ?>">Konfirmasi</button>
                            </form>
                            <form method="POST" action="/admin/penitipan/perpanjangan/tolak" class="flex flex-wrap items-center gap-2" data-confirm="Tolak perpanjangan ini?">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $pp['id']) ?>">
                                <input type="text" name="catatan" placeholder="Alasan" class="<?= e($inputClass) ?> !w-auto min-w-[8rem]">
                                <button type="submit" class="<?= e($btnDanger) ?>">Tolak</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
