<?php

declare(strict_types=1);

use App\Core\Csrf;

$jenis = $jenis ?? null;
$errors = $errors ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$isEdit = $jenis !== null && !empty($jenis['id']);
$inputClass = design_cn(
    'w-full rounded-xl border border-input bg-background px-3.5 py-3 text-base sm:text-sm text-foreground transition',
    'hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25',
);
$errorBorder = ' border-destructive/50 focus:border-destructive focus:ring-destructive/25';
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground" aria-label="Breadcrumb">
        <a href="/admin/grooming/layanan" class="hover:text-primary focus:outline-none focus-visible:underline">Jenis</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-foreground"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-foreground"><?= $isEdit ? 'Edit jenis grooming' : 'Tambah jenis grooming' ?></h1>
        <p class="mt-2 text-sm text-muted-foreground">Atur nama, harga, dan status layanan.</p>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <div class="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive" role="alert"><?= e((string) $errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= e($action) ?>" class="<?= e(design_cn(design_surface('panel'), 'space-y-5 p-5 sm:p-6')) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) ($jenis['id'] ?? '')) ?>">
        <?php endif; ?>

        <div>
            <label for="nama" class="mb-1.5 block text-sm font-semibold text-foreground">Nama</label>
            <input type="text" id="nama" name="nama" value="<?= e((string) ($jenis['nama'] ?? old('nama', ''))) ?>"
                   class="<?= e($inputClass . (!empty($errors['nama']) ? $errorBorder : '')) ?>">
            <?php if (!empty($errors['nama'])): ?><p class="mt-1.5 text-xs text-destructive"><?= e((string) $errors['nama']) ?></p><?php endif; ?>
        </div>

        <div>
            <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-foreground">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="<?= e($inputClass) ?>"><?= e((string) ($jenis['deskripsi'] ?? old('deskripsi', ''))) ?></textarea>
        </div>

        <div>
            <label for="harga" class="mb-1.5 block text-sm font-semibold text-foreground">Harga (Rp)</label>
            <input type="number" id="harga" name="harga" min="0" step="1000"
                   value="<?= e((string) ($jenis['harga'] ?? old('harga', ''))) ?>"
                   class="<?= e($inputClass . (!empty($errors['harga']) ? $errorBorder : '')) ?>">
            <?php if (!empty($errors['harga'])): ?><p class="mt-1.5 text-xs text-destructive"><?= e((string) $errors['harga']) ?></p><?php endif; ?>
        </div>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-muted/30 px-4 py-3">
            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded border-border text-primary focus:ring-ring"
                   <?= !isset($jenis['aktif']) || !empty($jenis['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-foreground">Aktif (dapat dipilih pelanggan)</span>
        </label>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="<?= e(ui_btn_primary()) ?> disabled:opacity-60"><?= e($submitLabel) ?></button>
            <a href="/admin/grooming/layanan" class="<?= e(ui_btn_secondary()) ?>">Batal</a>
        </div>
    </form>
</div>
