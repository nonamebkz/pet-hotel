<?php

declare(strict_types=1);

use App\Core\Csrf;

$jenis = $jenis ?? null;
$errors = $errors ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
?>
<div>
    <div class="mb-6">
        <a href="/admin/grooming/layanan" class="text-sm text-gray-500 hover:text-slate-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $jenis ? 'Edit Jenis Grooming' : 'Tambah Jenis Grooming' ?></h1>
    </div>

    <form method="POST" action="<?= e($action) ?>" class="bg-white rounded-xl border p-6 max-w-xl space-y-4">
        <?= Csrf::field() ?>
        <?php if ($jenis): ?>
            <input type="hidden" name="id" value="<?= e((string) ($jenis['id'] ?? '')) ?>">
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-red-600"><?= e((string) $errors['general']) ?></p>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="nama" value="<?= e((string) ($jenis['nama'] ?? old('nama', ''))) ?>"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['nama']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (!empty($errors['nama'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['nama']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><?= e((string) ($jenis['deskripsi'] ?? old('deskripsi', ''))) ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
            <input type="number" name="harga" min="0" step="1000"
                   value="<?= e((string) ($jenis['harga'] ?? old('harga', ''))) ?>"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['harga']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (!empty($errors['harga'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['harga']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="aktif" value="1"
                       <?= !isset($jenis['aktif']) || !empty($jenis['aktif']) ? 'checked' : '' ?>
                       class="rounded border-gray-300">
                <span class="text-sm text-gray-700">Aktif</span>
            </label>
        </div>

        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            <?= e($submitLabel) ?>
        </button>
    </form>
</div>
