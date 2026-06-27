<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$tanggal = $tanggal ?? date('Y-m-d');
$errors = Session::getFlash('errors', []);
?>
<div>
    <div class="mb-6">
        <a href="/admin/pet-care/slot?tanggal=<?= e($tanggal) ?>" class="text-sm text-gray-500 hover:text-slate-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Tambah Slot Dokter</h1>
    </div>

    <form method="POST" action="<?= e($action) ?>" class="bg-white rounded-xl border p-6 max-w-xl space-y-4">
        <?= Csrf::field() ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-red-600"><?= e((string) $errors['general']) ?></p>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" min="<?= e(date('Y-m-d')) ?>"
                   value="<?= e((string) old('tanggal', $tanggal)) ?>"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['tanggal']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (!empty($errors['tanggal'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['tanggal']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Slot</label>
            <input type="time" name="slot_waktu" value="<?= e((string) old('slot_waktu', '09:00')) ?>"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['slot_waktu']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (!empty($errors['slot_waktu'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['slot_waktu']) ?></p>
            <?php endif; ?>
            <p class="text-xs text-gray-500 mt-1">Maksimal 1 booking per slot (1 dokter).</p>
        </div>

        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            <?= e($submitLabel) ?>
        </button>
    </form>
</div>
