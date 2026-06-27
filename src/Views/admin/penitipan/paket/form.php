<?php

declare(strict_types=1);

use App\Core\Csrf;

$paket = $paket ?? null;
$errors = $errors ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
?>
<div>
    <div class="mb-6">
        <a href="/admin/penitipan/paket" class="text-sm text-gray-500 hover:text-slate-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $paket ? 'Edit Paket' : 'Tambah Paket' ?></h1>
    </div>
    <form method="POST" action="<?= e($action) ?>" class="bg-white rounded-xl border p-6 max-w-xl space-y-4">
        <?= Csrf::field() ?>
        <?php if ($paket && !empty($paket['id'])): ?>
            <input type="hidden" name="id" value="<?= e((string) $paket['id']) ?>">
        <?php endif; ?>
        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="nama" value="<?= e((string) ($paket['nama'] ?? '')) ?>" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Harga per hari</label>
            <input type="number" name="harga_per_hari" min="0" value="<?= e((string) ($paket['harga_per_hari'] ?? '')) ?>" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"><?= e((string) ($paket['deskripsi'] ?? '')) ?></textarea>
        </div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="aktif" value="1" <?= !isset($paket['aktif']) || !empty($paket['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm">Aktif</span>
        </label>
        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm"><?= e($submitLabel) ?></button>
    </form>
</div>
