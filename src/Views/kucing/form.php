<?php

declare(strict_types=1);

use App\Core\Csrf;

$errors = $errors ?? [];
$kucing = $kucing ?? null;
$vaksinList = $vaksinList ?? [];
$action = $action ?? '/kucing';
$submitLabel = $submitLabel ?? 'Simpan';
$jenisKelaminLabels = $jenisKelaminLabels ?? [];
$isEdit = $kucing !== null && !empty($kucing['id']);
?>
<div class="max-w-3xl">
    <div class="mb-6">
        <a href="/kucing" class="text-sm text-gray-600 hover:text-orange-600">&larr; Kembali ke daftar</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $isEdit ? 'Edit Kucing' : 'Tambah Kucing' ?></h1>
    </div>

    <form method="POST" action="<?= e($action) ?>" enctype="multipart/form-data"
          class="bg-white rounded-xl border p-6 space-y-4">
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kucing['id']) ?>">
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-red-600 text-sm"><?= e($errors['general']) ?></p>
        <?php endif; ?>

        <div class="flex items-center gap-4">
            <?php if (!empty($kucing['foto_url'])): ?>
                <img src="<?= e((string) $kucing['foto_url']) ?>" alt="Foto kucing"
                     class="w-16 h-16 rounded-lg object-cover border">
            <?php endif; ?>
            <div class="flex-1">
                <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto Kucing (opsional)</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-600">
                <?php if (!empty($errors['foto'])): ?>
                    <p class="text-red-600 text-xs mt-1"><?= e($errors['foto']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Kucing</label>
                <input type="text" id="nama" name="nama" required
                       value="<?= e((string) old('nama', $kucing['nama'] ?? '')) ?>"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <?php if (!empty($errors['nama'])): ?>
                    <p class="text-red-600 text-xs mt-1"><?= e($errors['nama']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">— Pilih —</option>
                    <?php foreach ($jenisKelaminLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>"
                            <?= (string) old('jenis_kelamin', $kucing['jenis_kelamin'] ?? '') === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['jenis_kelamin'])): ?>
                    <p class="text-red-600 text-xs mt-1"><?= e($errors['jenis_kelamin']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="ras" class="block text-sm font-medium text-gray-700 mb-1">Ras</label>
                <input type="text" id="ras" name="ras"
                       value="<?= e((string) old('ras', $kucing['ras'] ?? '')) ?>"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                       value="<?= e((string) old('tanggal_lahir', $kucing['tanggal_lahir'] ?? '')) ?>"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <?php if (!empty($errors['tanggal_lahir'])): ?>
                    <p class="text-red-600 text-xs mt-1"><?= e($errors['tanggal_lahir']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label for="berat_badan" class="block text-sm font-medium text-gray-700 mb-1">Berat Badan (kg)</label>
            <input type="number" id="berat_badan" name="berat_badan" step="0.01" min="0"
                   value="<?= e((string) old('berat_badan', $kucing['berat_badan'] ?? '')) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            <?php if (!empty($errors['berat_badan'])): ?>
                <p class="text-red-600 text-xs mt-1"><?= e($errors['berat_badan']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="catatan_kesehatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Kesehatan / Alergi</label>
            <textarea id="catatan_kesehatan" name="catatan_kesehatan" rows="2"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"><?= e((string) old('catatan_kesehatan', $kucing['catatan_kesehatan'] ?? '')) ?></textarea>
        </div>

        <div class="border-t pt-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Riwayat Vaksin (opsional)</h2>
                    <p class="text-xs text-gray-500">Syarat vaksin hanya divalidasi saat booking pet hotel.</p>
                </div>
                <button type="button" id="add-vaksin-row"
                        class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                    + Tambah baris
                </button>
            </div>
            <div id="vaksin-rows" class="space-y-2">
                <?php if ($vaksinList === []): ?>
                    <?php $index = 0; require __DIR__ . '/../partials/vaksin-row.php'; ?>
                <?php else: ?>
                    <?php foreach ($vaksinList as $index => $row): ?>
                        <?php require __DIR__ . '/../partials/vaksin-row.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="bg-orange-600 text-white rounded-lg px-6 py-2 font-medium hover:bg-orange-700">
            <?= e($submitLabel) ?>
        </button>
    </form>
</div>

<template id="vaksin-row-template">
    <?php $index = '__INDEX__'; $row = []; require __DIR__ . '/../partials/vaksin-row.php'; ?>
</template>

<script>
(function () {
    const container = document.getElementById('vaksin-rows');
    const template = document.getElementById('vaksin-row-template');
    const addBtn = document.getElementById('add-vaksin-row');
    let rowIndex = container.querySelectorAll('.vaksin-row').length;

    addBtn.addEventListener('click', function () {
        const html = template.innerHTML.replace(/__INDEX__/g, String(rowIndex++));
        container.insertAdjacentHTML('beforeend', html);
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-vaksin-row')) {
            const rows = container.querySelectorAll('.vaksin-row');
            if (rows.length > 1) {
                e.target.closest('.vaksin-row').remove();
            }
        }
    });
})();
</script>
