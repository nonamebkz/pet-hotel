<?php

declare(strict_types=1);

use App\Core\Csrf;

$paket = $paket ?? null;
$errors = $errors ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$isEdit = $paket !== null && !empty($paket['id']);

$inputClass = 'w-full rounded-xl border border-border bg-page/60 px-3.5 py-3 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:bg-white focus:outline-none focus:ring-2 focus:ring-admin/25';
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-content-secondary" aria-label="Breadcrumb">
        <a href="/admin/penitipan/paket" class="cursor-pointer hover:text-admin focus:outline-none focus-visible:underline">Paket</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-content-primary"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-content-primary"><?= $isEdit ? 'Edit paket' : 'Tambah paket' ?></h1>
        <p class="mt-2 text-sm text-content-secondary">Atur nama, harga harian, dan status paket penitipan.</p>
    </header>

    <form method="POST" action="<?= e($action) ?>" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $paket['id']) ?>">
        <?php endif; ?>

        <div>
            <label for="nama" class="mb-1.5 block text-sm font-semibold text-content-primary">Nama paket</label>
            <input type="text" id="nama" name="nama" required
                   value="<?= e((string) ($paket['nama'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
            <?php if (!empty($errors['nama'])): ?>
                <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['nama']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="harga_per_hari" class="mb-1.5 block text-sm font-semibold text-content-primary">Harga per hari</label>
            <input type="number" id="harga_per_hari" name="harga_per_hari" min="0" required
                   value="<?= e((string) ($paket['harga_per_hari'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
            <?php if (!empty($errors['harga_per_hari'])): ?>
                <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['harga_per_hari']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-content-primary">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="<?= e($inputClass) ?>"><?= e((string) ($paket['deskripsi'] ?? '')) ?></textarea>
        </div>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-page/50 px-4 py-3 shadow-soft-inset">
            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded border-border text-admin focus:ring-admin"
                   <?= !isset($paket['aktif']) || !empty($paket['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-content-primary">Paket aktif (dapat dipilih pelanggan)</span>
        </label>

        <div class="flex flex-wrap gap-3 pt-1">
            <button type="submit"
                    class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-5 py-3 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin disabled:opacity-60">
                <?= e($submitLabel) ?>
            </button>
            <a href="/admin/penitipan/paket" class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-content-secondary hover:text-admin">Batal</a>
        </div>
    </form>
</div>
