<?php

declare(strict_types=1);

use App\Core\Csrf;

$jenis = $jenis ?? null;
$errors = $errors ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$isEdit = $jenis !== null && !empty($jenis['id']);
$inputClass = 'w-full rounded-xl border border-border bg-page/60 px-3.5 py-3 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:bg-white focus:outline-none focus:ring-2 focus:ring-admin/25';
$errorBorder = ' border-red-400 focus:border-red-400 focus:ring-red-200';
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-content-secondary" aria-label="Breadcrumb">
        <a href="/admin/grooming/layanan" class="cursor-pointer hover:text-admin focus:outline-none focus-visible:underline">Jenis</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-content-primary"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-content-primary"><?= $isEdit ? 'Edit jenis grooming' : 'Tambah jenis grooming' ?></h1>
        <p class="mt-2 text-sm text-content-secondary">Atur nama, harga, dan status layanan.</p>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= e((string) $errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= e($action) ?>" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) ($jenis['id'] ?? '')) ?>">
        <?php endif; ?>

        <div>
            <label for="nama" class="mb-1.5 block text-sm font-semibold text-content-primary">Nama</label>
            <input type="text" id="nama" name="nama" value="<?= e((string) ($jenis['nama'] ?? old('nama', ''))) ?>"
                   class="<?= e($inputClass . (!empty($errors['nama']) ? $errorBorder : '')) ?>">
            <?php if (!empty($errors['nama'])): ?><p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['nama']) ?></p><?php endif; ?>
        </div>

        <div>
            <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-content-primary">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="<?= e($inputClass) ?>"><?= e((string) ($jenis['deskripsi'] ?? old('deskripsi', ''))) ?></textarea>
        </div>

        <div>
            <label for="harga" class="mb-1.5 block text-sm font-semibold text-content-primary">Harga (Rp)</label>
            <input type="number" id="harga" name="harga" min="0" step="1000"
                   value="<?= e((string) ($jenis['harga'] ?? old('harga', ''))) ?>"
                   class="<?= e($inputClass . (!empty($errors['harga']) ? $errorBorder : '')) ?>">
            <?php if (!empty($errors['harga'])): ?><p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['harga']) ?></p><?php endif; ?>
        </div>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-page/50 px-4 py-3 shadow-soft-inset">
            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded border-border text-admin focus:ring-admin"
                   <?= !isset($jenis['aktif']) || !empty($jenis['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-content-primary">Aktif (dapat dipilih pelanggan)</span>
        </label>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="cursor-pointer rounded-xl bg-admin px-5 py-3 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover disabled:opacity-60"><?= e($submitLabel) ?></button>
            <a href="/admin/grooming/layanan" class="cursor-pointer px-4 py-3 text-sm font-semibold text-content-secondary hover:text-admin">Batal</a>
        </div>
    </form>
</div>
