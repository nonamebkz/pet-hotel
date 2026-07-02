<?php

declare(strict_types=1);

use App\Core\Csrf;

$layanan = $layanan ?? null;
$errors = $errors ?? [];
$statusLabels = $statusLabels ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$defaultStatus = $layanan['status'] ?? old('status', 'NONAKTIF');
?>
<div>
    <div class="mb-6">
        <a href="/admin/pet-care/layanan" class="text-sm text-gray-500 hover:text-slate-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $layanan ? 'Edit Layanan' : 'Tambah Layanan' ?></h1>
        <p class="text-sm text-gray-500 mt-1">Layanan baru default nonaktif — aktifkan setelah data lengkap dan siap ditampilkan.</p>
    </div>

    <form method="POST" action="<?= e($action) ?>" class="space-y-6 max-w-2xl">
        <?= Csrf::field() ?>
        <?php if ($layanan): ?>
            <input type="hidden" name="id" value="<?= e((string) ($layanan['id'] ?? '')) ?>">
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-red-600"><?= e((string) $errors['general']) ?></p>
        <?php endif; ?>

        <div class="bg-white rounded-xl border p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-800">Informasi Dasar</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                <input type="text" name="nama" value="<?= e((string) ($layanan['nama'] ?? old('nama', ''))) ?>"
                       class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['nama']) ? 'border-red-400' : 'border-gray-300' ?>">
                <?php if (!empty($errors['nama'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['nama']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                          placeholder="Jelaskan cakupan layanan pet care"><?= e((string) ($layanan['deskripsi'] ?? old('deskripsi', ''))) ?></textarea>
                <p class="text-xs text-gray-500 mt-1">Tampil ke pelanggan saat memilih layanan.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-800">Detail Layanan</h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Estimasi (Rp)</label>
                    <input type="number" name="harga" min="0" step="1000"
                           value="<?= e((string) ($layanan['harga'] ?? old('harga', ''))) ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['harga']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['harga'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['harga']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Durasi (menit)</label>
                    <input type="number" name="estimasi_durasi_menit" min="1"
                           value="<?= e((string) ($layanan['estimasi_durasi_menit'] ?? old('estimasi_durasi_menit', ''))) ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['estimasi_durasi_menit']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['estimasi_durasi_menit'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['estimasi_durasi_menit']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-800">Pengaturan Sistem</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm max-w-xs">
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $defaultStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Nonaktif = draft, belum tampil ke pelanggan.</p>
            </div>

            <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">
                <?= e($submitLabel) ?>
            </button>
        </div>
    </form>
</div>
