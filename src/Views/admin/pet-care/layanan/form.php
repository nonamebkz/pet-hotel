<?php

declare(strict_types=1);

use App\Core\Csrf;

$layanan = $layanan ?? null;
$errors = $errors ?? [];
$statusLabels = $statusLabels ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$defaultStatus = $layanan['status'] ?? old('status', 'NONAKTIF');
$isEdit = $layanan !== null && !empty($layanan['id']);

$inputClass = 'w-full rounded-xl border border-border bg-page/60 px-3.5 py-3 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:bg-white focus:outline-none focus:ring-2 focus:ring-admin/25';
$errorBorder = ' border-red-400 focus:border-red-400 focus:ring-red-200';
?>
<div class="font-body max-w-2xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-content-secondary" aria-label="Breadcrumb">
        <a href="/admin/pet-care/layanan" class="cursor-pointer hover:text-admin focus:outline-none focus-visible:underline">Layanan</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-content-primary"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-content-primary">
            <?= $isEdit ? 'Edit layanan' : 'Tambah layanan' ?>
        </h1>
        <p class="mt-2 text-sm text-content-secondary">
            Layanan baru default nonaktif — aktifkan setelah data lengkap dan siap ditampilkan.
        </p>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <?= e((string) $errors['general']) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e($action) ?>" class="space-y-5" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($layanan): ?>
            <input type="hidden" name="id" value="<?= e((string) ($layanan['id'] ?? '')) ?>">
        <?php endif; ?>

        <section class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5">
            <h2 class="font-heading text-lg text-content-primary">Informasi Dasar</h2>

            <div>
                <label for="nama" class="mb-1.5 block text-sm font-semibold text-content-primary">Nama Layanan</label>
                <input type="text" id="nama" name="nama"
                       value="<?= e((string) ($layanan['nama'] ?? old('nama', ''))) ?>"
                       class="<?= e($inputClass . (!empty($errors['nama']) ? $errorBorder : '')) ?>">
                <?php if (!empty($errors['nama'])): ?>
                    <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['nama']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-content-primary">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                          class="<?= e($inputClass) ?>"
                          placeholder="Jelaskan cakupan layanan pet care"><?= e((string) ($layanan['deskripsi'] ?? old('deskripsi', ''))) ?></textarea>
                <p class="mt-1.5 text-xs text-content-secondary">Tampil ke pelanggan saat memilih layanan.</p>
            </div>
        </section>

        <section class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5">
            <h2 class="font-heading text-lg text-content-primary">Detail Layanan</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="harga" class="mb-1.5 block text-sm font-semibold text-content-primary">Harga Estimasi (Rp)</label>
                    <input type="number" id="harga" name="harga" min="0" step="1000"
                           value="<?= e((string) ($layanan['harga'] ?? old('harga', ''))) ?>"
                           class="<?= e($inputClass . (!empty($errors['harga']) ? $errorBorder : '')) ?>">
                    <?php if (!empty($errors['harga'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['harga']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="estimasi_durasi_menit" class="mb-1.5 block text-sm font-semibold text-content-primary">Estimasi Durasi (menit)</label>
                    <input type="number" id="estimasi_durasi_menit" name="estimasi_durasi_menit" min="1"
                           value="<?= e((string) ($layanan['estimasi_durasi_menit'] ?? old('estimasi_durasi_menit', ''))) ?>"
                           class="<?= e($inputClass . (!empty($errors['estimasi_durasi_menit']) ? $errorBorder : '')) ?>">
                    <?php if (!empty($errors['estimasi_durasi_menit'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['estimasi_durasi_menit']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5">
            <h2 class="font-heading text-lg text-content-primary">Pengaturan Sistem</h2>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-content-primary">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?> max-w-xs">
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $defaultStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="mt-1.5 text-xs text-content-secondary">Nonaktif = draft, belum tampil ke pelanggan.</p>
            </div>

            <div class="flex flex-wrap gap-3 pt-1">
                <button type="submit"
                        class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-5 py-3 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin disabled:opacity-60">
                    <?= e($submitLabel) ?>
                </button>
                <a href="/admin/pet-care/layanan"
                   class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-content-secondary transition duration-soft hover:text-admin focus:outline-none focus-visible:underline">
                    Batal
                </a>
            </div>
        </section>
    </form>
</div>
