<?php

declare(strict_types=1);

use App\Core\Csrf;

$kamar = $kamar ?? null;
$action = $action ?? '';
$isEdit = $kamar !== null && !empty($kamar['id']);
$inputClass = 'w-full rounded-xl border border-border bg-page/60 px-3.5 py-3 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:bg-white focus:outline-none focus:ring-2 focus:ring-admin/25';
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-content-secondary" aria-label="Breadcrumb">
        <a href="/admin/penitipan/kamar" class="cursor-pointer hover:text-admin focus:outline-none focus-visible:underline">Kamar</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-content-primary"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-content-primary"><?= $isEdit ? 'Edit kamar' : 'Tambah kamar' ?></h1>
        <p class="mt-2 text-sm text-content-secondary">Tentukan nama kamar dan kapasitas slot harian.</p>
    </header>

    <form method="POST" action="<?= e($action) ?>" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kamar['id']) ?>">
        <?php endif; ?>

        <div>
            <label for="nama_kamar" class="mb-1.5 block text-sm font-semibold text-content-primary">Nama kamar</label>
            <input type="text" id="nama_kamar" name="nama_kamar" required
                   value="<?= e((string) ($kamar['nama_kamar'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
        </div>

        <div>
            <label for="kapasitas" class="mb-1.5 block text-sm font-semibold text-content-primary">Kapasitas</label>
            <input type="number" id="kapasitas" name="kapasitas" min="1" required
                   value="<?= e((string) ($kamar['kapasitas'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
        </div>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-page/50 px-4 py-3 shadow-soft-inset">
            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded border-border text-admin focus:ring-admin"
                   <?= !isset($kamar['aktif']) || !empty($kamar['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-content-primary">Kamar aktif</span>
        </label>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-5 py-3 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover disabled:opacity-60">Simpan</button>
            <a href="/admin/penitipan/kamar" class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-content-secondary hover:text-admin">Batal</a>
        </div>
    </form>
</div>
