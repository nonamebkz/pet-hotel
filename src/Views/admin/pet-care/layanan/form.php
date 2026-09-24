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

$inputClass = design_cn(
    'w-full rounded-xl border border-input bg-background px-3.5 py-3 text-base sm:text-sm text-foreground transition',
    'hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25',
);
$errorBorder = ' border-destructive/50 focus:border-destructive focus:ring-destructive/25';
?>
<div class="font-body max-w-2xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground" aria-label="Breadcrumb">
        <a href="/admin/pet-care/layanan" class="hover:text-primary focus:outline-none focus-visible:underline">Layanan</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-foreground"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-foreground">
            <?= $isEdit ? 'Edit layanan' : 'Tambah layanan' ?>
        </h1>
        <p class="mt-2 text-sm text-muted-foreground">
            Layanan baru default nonaktif — aktifkan setelah data lengkap dan siap ditampilkan.
        </p>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <div class="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive" role="alert">
            <?= e((string) $errors['general']) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e($action) ?>" class="space-y-5" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($layanan): ?>
            <input type="hidden" name="id" value="<?= e((string) ($layanan['id'] ?? '')) ?>">
        <?php endif; ?>

        <section class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5')) ?>">
            <h2 class="font-heading text-lg text-foreground">Informasi Dasar</h2>

            <div>
                <label for="nama" class="<?= e(ui_form_label_class()) ?>">Nama Layanan</label>
                <input type="text" id="nama" name="nama"
                       value="<?= e((string) ($layanan['nama'] ?? old('nama', ''))) ?>"
                       class="<?= e($inputClass . (!empty($errors['nama']) ? $errorBorder : '')) ?>">
                <?php if (!empty($errors['nama'])): ?>
                    <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['nama']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="deskripsi" class="<?= e(ui_form_label_class()) ?>">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                          class="<?= e($inputClass) ?>"
                          placeholder="Jelaskan cakupan layanan pet care"><?= e((string) ($layanan['deskripsi'] ?? old('deskripsi', ''))) ?></textarea>
                <p class="mt-1.5 text-xs text-muted-foreground">Tampil ke pelanggan saat memilih layanan.</p>
            </div>
        </section>

        <section class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5')) ?>">
            <h2 class="font-heading text-lg text-foreground">Detail Layanan</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="harga" class="<?= e(ui_form_label_class()) ?>">Harga Estimasi (Rp)</label>
                    <input type="number" id="harga" name="harga" min="0" step="1000"
                           value="<?= e((string) ($layanan['harga'] ?? old('harga', ''))) ?>"
                           class="<?= e($inputClass . (!empty($errors['harga']) ? $errorBorder : '')) ?>">
                    <?php if (!empty($errors['harga'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['harga']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="estimasi_durasi_menit" class="<?= e(ui_form_label_class()) ?>">Estimasi Durasi (menit)</label>
                    <input type="number" id="estimasi_durasi_menit" name="estimasi_durasi_menit" min="1"
                           value="<?= e((string) ($layanan['estimasi_durasi_menit'] ?? old('estimasi_durasi_menit', ''))) ?>"
                           class="<?= e($inputClass . (!empty($errors['estimasi_durasi_menit']) ? $errorBorder : '')) ?>">
                    <?php if (!empty($errors['estimasi_durasi_menit'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['estimasi_durasi_menit']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5')) ?>">
            <h2 class="font-heading text-lg text-foreground">Pengaturan Sistem</h2>

            <div>
                <label for="status" class="<?= e(ui_form_label_class()) ?>">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?> max-w-xs">
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $defaultStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="mt-1.5 text-xs text-muted-foreground">Nonaktif = draft, belum tampil ke pelanggan.</p>
            </div>

            <div class="flex flex-wrap gap-3 pt-1">
                <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">
                    <?= e($submitLabel) ?>
                </button>
                <a href="/admin/pet-care/layanan" class="<?= e(ui_btn_secondary()) ?>">Batal</a>
            </div>
        </section>
    </form>
</div>
