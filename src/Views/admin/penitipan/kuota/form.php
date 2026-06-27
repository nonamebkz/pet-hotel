<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuota = $kuota ?? null;
$kamarList = $kamarList ?? [];
$action = $action ?? '';
?>
<div>
    <a href="/admin/penitipan/kuota" class="text-sm text-gray-500">&larr; Kembali</a>
    <h1 class="text-2xl font-bold mt-2 mb-6"><?= $kuota ? 'Edit Kuota' : 'Tambah Kuota' ?></h1>
    <form method="POST" action="<?= e($action) ?>" class="bg-white rounded-xl border p-6 max-w-xl space-y-4">
        <?= Csrf::field() ?>
        <?php if ($kuota): ?><input type="hidden" name="id" value="<?= e((string) $kuota['id']) ?>"><?php endif; ?>
        <?php if (!$kuota): ?>
            <div><label class="text-sm font-medium">Kamar</label>
                <select name="kamar_penitipan_id" class="w-full border rounded-lg px-3 py-2 text-sm mt-1" required>
                    <?php foreach ($kamarList as $k): ?>
                        <option value="<?= e((string) $k['id']) ?>"><?= e((string) $k['nama_kamar']) ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div><label class="text-sm font-medium">Tanggal</label>
                <input type="date" name="tanggal" min="<?= e(date('Y-m-d')) ?>" class="w-full border rounded-lg px-3 py-2 text-sm mt-1" required></div>
        <?php else: ?>
            <p class="text-sm text-gray-600">Kamar: <?= e((string) ($kuota['nama_kamar'] ?? '')) ?> · <?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?></p>
        <?php endif; ?>
        <div><label class="text-sm font-medium">Slot maksimal</label>
            <input type="number" name="slot_maksimal" min="0" value="<?= e((string) ($kuota['slot_maksimal'] ?? '')) ?>" class="w-full border rounded-lg px-3 py-2 text-sm mt-1" required></div>
        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm">Simpan</button>
    </form>
</div>
