<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusPerpanjanganPenitipan;

$perpanjanganList = $perpanjanganList ?? [];
$statusLabels = $statusLabels ?? [];
$filterStatus = $filterStatus ?? '';
$hasFilter = $filterStatus !== '';

$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
$btnSuccess = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-success px-3.5 py-2 text-xs font-semibold text-white shadow-soft transition duration-soft hover:opacity-90';
$btnDanger = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-semibold text-red-700 transition duration-soft hover:bg-red-100';
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Penitipan</p>
            <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Perpanjangan</h1>
            <p class="mt-2 text-sm text-content-secondary">Konfirmasi permintaan perpanjangan check-out dari pelanggan.</p>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <form method="GET" action="/admin/penitipan/perpanjangan" class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-content-primary">Status</label>
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
        $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover';
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($perpanjanganList as $pp): ?>
                <?php $needsConfirm = (string) $pp['status'] === StatusPerpanjanganPenitipan::MENUNGGU_KONFIRMASI->value; ?>
                <article class="rounded-2xl border <?= $needsConfirm ? 'border-amber-200 bg-warning-bg/25' : 'border-white/80 bg-card' ?> p-5 shadow-soft space-y-3">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-content-primary">
                                <?= e((string) $pp['pelanggan_nama']) ?>
                                <span class="text-content-secondary font-normal">· <?= e((string) $pp['kucing_nama']) ?></span>
                            </h2>
                            <p class="mt-1 text-sm text-content-secondary">
                                <?= e(date('d/m/Y', strtotime((string) $pp['check_out_sebelum']))) ?>
                                → <?= e(date('d/m/Y', strtotime((string) $pp['check_out_baru']))) ?>
                                · +<?= (int) $pp['tambah_hari'] ?> hari
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex text-xs px-2.5 py-1 rounded-lg font-medium bg-admin-soft text-admin">
                                <?= e($statusLabels[$pp['status']] ?? (string) $pp['status']) ?>
                            </span>
                            <p class="mt-2 font-heading text-admin">
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
