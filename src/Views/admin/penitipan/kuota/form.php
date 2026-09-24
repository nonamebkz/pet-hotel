<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuota = $kuota ?? null;
$kamarList = $kamarList ?? [];
$action = $action ?? '';
$isEdit = $kuota !== null && !empty($kuota['id']);
$formPanelClass = design_cn(design_surface('metric'), 'p-5 sm:p-6 space-y-5');
$inputClass = 'w-full rounded-lg border border-input bg-background px-3.5 py-3 text-base sm:text-sm text-foreground transition hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
$infoBoxClass = design_cn(design_interactive('metricCellOutlined'), 'px-4 py-3 text-sm');
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground" aria-label="Breadcrumb">
        <a href="/admin/penitipan/kuota" class="cursor-pointer hover:text-primary focus:outline-none focus-visible:underline">Kuota</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-foreground"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-foreground"><?= $isEdit ? 'Edit kuota' : 'Tambah kuota' ?></h1>
        <p class="mt-2 text-sm text-muted-foreground">
            <?= $isEdit ? 'Perbarui slot maksimal untuk tanggal yang sudah dibuat.' : 'Buat slot harian untuk kamar tertentu.' ?>
        </p>
    </header>

    <form method="POST" action="<?= e($action) ?>" class="<?= e($formPanelClass) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kuota['id']) ?>">
            <div class="<?= e($infoBoxClass) ?>">
                <p class="font-semibold text-primary"><?= e((string) ($kuota['nama_kamar'] ?? '')) ?></p>
                <p class="text-muted-foreground mt-0.5"><?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?></p>
            </div>
        <?php else: ?>
            <div>
                <label for="kamar_penitipan_id" class="mb-1.5 block text-sm font-semibold text-foreground">Kamar</label>
                <select id="kamar_penitipan_id" name="kamar_penitipan_id" required class="<?= e($inputClass) ?>">
                    <?php foreach ($kamarList as $k): ?>
                        <option value="<?= e((string) $k['id']) ?>"><?= e((string) $k['nama_kamar']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="tanggal" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" min="<?= e(date('Y-m-d')) ?>" required class="<?= e($inputClass) ?>">
            </div>
        <?php endif; ?>

        <div>
            <label for="slot_maksimal" class="mb-1.5 block text-sm font-semibold text-foreground">Slot maksimal</label>
            <input type="number" id="slot_maksimal" name="slot_maksimal" min="0" required
                   value="<?= e((string) ($kuota['slot_maksimal'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">Simpan</button>
            <a href="/admin/penitipan/kuota" class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-muted-foreground hover:text-primary">Batal</a>
        </div>
    </form>
</div>
