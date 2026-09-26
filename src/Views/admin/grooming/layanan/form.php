<?php

declare(strict_types=1);

use App\Core\Csrf;

$jenis = $jenis ?? null;
$errors = $errors ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$isEdit = $jenis !== null && !empty($jenis['id']);

$inputClass = static function (string $field) use ($errors): string {
    return ui_form_input_class(!empty($errors[$field]));
};
?>
<div class="<?= e(design_cn(ui_page_content_shell_classes(), design_page_layout('formSm'))) ?>">
    <?php
    ui_breadcrumb([
        ['label' => 'Jenis Layanan', 'href' => '/admin/grooming/layanan'],
        ['label' => $isEdit ? 'Edit' : 'Tambah'],
    ]);
    ui_page_header(
        $isEdit ? 'Edit jenis grooming' : 'Tambah jenis grooming',
        'Grooming · Administrasi',
        'Atur nama, harga, dan status layanan.',
    );
    ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive" role="alert"><?= e((string) $errors['general']) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= e($action) ?>" class="<?= e(design_cn(design_surface('panel'), 'space-y-5 p-5 sm:p-6')) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) ($jenis['id'] ?? '')) ?>">
        <?php endif; ?>

        <div>
            <label for="nama" class="<?= e(ui_form_label_class()) ?>">Nama</label>
            <input type="text" id="nama" name="nama" value="<?= e((string) ($jenis['nama'] ?? old('nama', ''))) ?>"
                   class="<?= e($inputClass('nama')) ?>">
            <?php if (!empty($errors['nama'])): ?><p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['nama']) ?></p><?php endif; ?>
        </div>

        <div>
            <label for="deskripsi" class="<?= e(ui_form_label_class()) ?>">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="<?= e(ui_form_input_class()) ?>"><?= e((string) ($jenis['deskripsi'] ?? old('deskripsi', ''))) ?></textarea>
        </div>

        <div>
            <label for="harga" class="<?= e(ui_form_label_class()) ?>">Harga (Rp)</label>
            <input type="number" id="harga" name="harga" min="0" step="1000"
                   value="<?= e((string) ($jenis['harga'] ?? old('harga', ''))) ?>"
                   class="<?= e($inputClass('harga')) ?>">
            <?php if (!empty($errors['harga'])): ?><p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['harga']) ?></p><?php endif; ?>
        </div>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-muted/30 px-4 py-3">
            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded border-border text-primary focus:ring-ring"
                   <?= !isset($jenis['aktif']) || !empty($jenis['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-foreground">Aktif (dapat dipilih pelanggan)</span>
        </label>

        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'w-full sm:w-auto')) ?> disabled:opacity-60"><?= e($submitLabel) ?></button>
            <a href="/admin/grooming/layanan" class="<?= e(design_cn(ui_btn_secondary(), 'w-full sm:w-auto text-center')) ?>">Batal</a>
        </div>
    </form>
</div>
