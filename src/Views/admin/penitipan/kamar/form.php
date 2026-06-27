<?php

declare(strict_types=1);

use App\Core\Csrf;

$kamar = $kamar ?? null;
$action = $action ?? '';
?>
<div>
    <a href="/admin/penitipan/kamar" class="text-sm text-gray-500">&larr; Kembali</a>
    <h1 class="text-2xl font-bold mt-2 mb-6"><?= $kamar ? 'Edit Kamar' : 'Tambah Kamar' ?></h1>
    <form method="POST" action="<?= e($action) ?>" class="bg-white rounded-xl border p-6 max-w-xl space-y-4">
        <?= Csrf::field() ?>
        <?php if ($kamar): ?><input type="hidden" name="id" value="<?= e((string) $kamar['id']) ?>"><?php endif; ?>
        <div><label class="text-sm font-medium">Nama Kamar</label>
            <input type="text" name="nama_kamar" value="<?= e((string) ($kamar['nama_kamar'] ?? '')) ?>" class="w-full border rounded-lg px-3 py-2 text-sm mt-1" required></div>
        <div><label class="text-sm font-medium">Kapasitas</label>
            <input type="number" name="kapasitas" min="1" value="<?= e((string) ($kamar['kapasitas'] ?? '')) ?>" class="w-full border rounded-lg px-3 py-2 text-sm mt-1" required></div>
        <label class="flex items-center gap-2"><input type="checkbox" name="aktif" value="1" <?= !isset($kamar['aktif']) || !empty($kamar['aktif']) ? 'checked' : '' ?>><span class="text-sm">Aktif</span></label>
        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm">Simpan</button>
    </form>
</div>
