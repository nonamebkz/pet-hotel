<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$kuota = $kuota ?? null;
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$tanggal = $tanggal ?? date('Y-m-d');
$errors = Session::getFlash('errors', []);
?>
<div>
    <div class="mb-6">
        <a href="/admin/grooming/kuota" class="text-sm text-gray-500 hover:text-slate-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $kuota ? 'Edit Kuota' : 'Tambah Kuota Grooming' ?></h1>
    </div>

    <form method="POST" action="<?= e($action) ?>" class="bg-white rounded-xl border p-6 max-w-xl space-y-4">
        <?= Csrf::field() ?>
        <?php if ($kuota): ?>
            <input type="hidden" name="id" value="<?= e((string) $kuota['id']) ?>">
        <?php endif; ?>

        <?php if (!$kuota): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="<?= e((string) old('tanggal', $tanggal)) ?>"
                       min="<?= e(date('Y-m-d')) ?>"
                       class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['tanggal']) ? 'border-red-400' : 'border-gray-300' ?>">
                <?php if (!empty($errors['tanggal'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['tanggal']) ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-sm text-gray-600">
                Tanggal: <strong><?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?></strong>
                (terisi: <?= (int) $kuota['slot_terisi'] ?>)
            </div>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slot Maksimal</label>
            <input type="number" name="slot_maksimal" min="1"
                   value="<?= e((string) ($kuota['slot_maksimal'] ?? old('slot_maksimal', '5'))) ?>"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['slot_maksimal']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (!empty($errors['slot_maksimal'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['slot_maksimal']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            <?= e($submitLabel) ?>
        </button>
    </form>
</div>
