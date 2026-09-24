<?php

declare(strict_types=1);

use App\Core\Csrf;

$paket = $paket ?? null;
$errors = $errors ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$isEdit = $paket !== null && !empty($paket['id']);

$formPanelClass = design_cn(design_surface('metric'), 'p-5 sm:p-6 space-y-5');
$inputClass = 'w-full rounded-lg border border-input bg-background px-3.5 py-3 text-base sm:text-sm text-foreground transition hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
$checkboxWrapClass = design_cn(design_interactive('metricCellOutlined'), 'flex cursor-pointer items-center gap-3 px-4 py-3');
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground" aria-label="Breadcrumb">
        <a href="/admin/penitipan/paket" class="cursor-pointer hover:text-primary focus:outline-none focus-visible:underline">Paket</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-foreground"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-foreground"><?= $isEdit ? 'Edit paket' : 'Tambah paket' ?></h1>
        <p class="mt-2 text-sm text-muted-foreground">Atur nama, harga harian, dan status paket penitipan.</p>
    </header>

    <form method="POST" action="<?= e($action) ?>" class="<?= e($formPanelClass) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $paket['id']) ?>">
        <?php endif; ?>

        <div>
            <label for="nama" class="mb-1.5 block text-sm font-semibold text-foreground">Nama paket</label>
            <input type="text" id="nama" name="nama" required
                   value="<?= e((string) ($paket['nama'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
            <?php if (!empty($errors['nama'])): ?>
                <p class="mt-1.5 text-xs text-destructive"><?= e((string) $errors['nama']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="harga_per_hari" class="mb-1.5 block text-sm font-semibold text-foreground">Harga per hari</label>
            <input type="number" id="harga_per_hari" name="harga_per_hari" min="0" required
                   value="<?= e((string) ($paket['harga_per_hari'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
            <?php if (!empty($errors['harga_per_hari'])): ?>
                <p class="mt-1.5 text-xs text-destructive"><?= e((string) $errors['harga_per_hari']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-foreground">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="<?= e($inputClass) ?>"><?= e((string) ($paket['deskripsi'] ?? '')) ?></textarea>
        </div>

        <label class="<?= e($checkboxWrapClass) ?>">
            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded border-border text-primary focus:ring-ring"
                   <?= !isset($paket['aktif']) || !empty($paket['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-foreground">Paket aktif (dapat dipilih pelanggan)</span>
        </label>

        <div class="flex flex-wrap gap-3 pt-1">
            <button type="submit"
                    class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">
                <?= e($submitLabel) ?>
            </button>
            <a href="/admin/penitipan/paket" class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-muted-foreground hover:text-primary">Batal</a>
        </div>
    </form>
</div>
