<?php

declare(strict_types=1);

use App\Core\Csrf;

$kamar = $kamar ?? null;
$action = $action ?? '';
$isEdit = $kamar !== null && !empty($kamar['id']);
$formPanelClass = design_cn(design_surface('metric'), 'p-5 sm:p-6 space-y-5');
$inputClass = 'w-full rounded-lg border border-input bg-background px-3.5 py-3 text-base sm:text-sm text-foreground transition hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
$checkboxWrapClass = design_cn(design_interactive('metricCellOutlined'), 'flex cursor-pointer items-center gap-3 px-4 py-3');
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground" aria-label="Breadcrumb">
        <a href="/admin/penitipan/kamar" class="cursor-pointer hover:text-primary focus:outline-none focus-visible:underline">Kamar</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-foreground"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-foreground"><?= $isEdit ? 'Edit kamar' : 'Tambah kamar' ?></h1>
        <p class="mt-2 text-sm text-muted-foreground">Tentukan nama kamar dan kapasitas slot harian.</p>
    </header>

    <form method="POST" action="<?= e($action) ?>" class="<?= e($formPanelClass) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kamar['id']) ?>">
        <?php endif; ?>

        <div>
            <label for="nama_kamar" class="mb-1.5 block text-sm font-semibold text-foreground">Nama kamar</label>
            <input type="text" id="nama_kamar" name="nama_kamar" required
                   value="<?= e((string) ($kamar['nama_kamar'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
        </div>

        <div>
            <label for="kapasitas" class="mb-1.5 block text-sm font-semibold text-foreground">Kapasitas</label>
            <input type="number" id="kapasitas" name="kapasitas" min="1" required
                   value="<?= e((string) ($kamar['kapasitas'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
        </div>

        <label class="<?= e($checkboxWrapClass) ?>">
            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded border-border text-primary focus:ring-ring"
                   <?= !isset($kamar['aktif']) || !empty($kamar['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-foreground">Kamar aktif</span>
        </label>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">Simpan</button>
            <a href="/admin/penitipan/kamar" class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-muted-foreground hover:text-primary">Batal</a>
        </div>
    </form>
</div>
