<?php

declare(strict_types=1);

use App\Core\Csrf;

$paket = $paket ?? null;
$errors = $errors ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$isEdit = $paket !== null && !empty($paket['id']);

$formPanelClass = design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5');
$checkboxWrapClass = design_cn(design_interactive('metricCellOutlined'), 'flex cursor-pointer items-center gap-3 px-4 py-3');
?>
<div class="<?= e(design_cn(ui_page_content_shell_classes(), design_page_layout('formSm'))) ?>">
    <?php
    ui_breadcrumb([
        ['label' => 'Paket', 'href' => '/admin/penitipan/paket'],
        ['label' => $isEdit ? 'Edit' : 'Tambah'],
    ]);
    ui_page_header(
        $isEdit ? 'Edit paket' : 'Tambah paket',
        'Penitipan · Administrasi',
        'Atur nama, harga harian, dan status paket penitipan.',
    );
    ?>

    <form method="POST" action="<?= e($action) ?>" class="<?= e($formPanelClass) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $paket['id']) ?>">
        <?php endif; ?>

        <div>
            <label for="nama" class="<?= e(ui_form_label_class()) ?>">Nama paket</label>
            <input type="text" id="nama" name="nama" required
                   value="<?= e((string) ($paket['nama'] ?? '')) ?>"
                   class="<?= e(ui_form_input_class(!empty($errors['nama']))) ?>">
            <?php if (!empty($errors['nama'])): ?>
                <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['nama']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="harga_per_hari" class="<?= e(ui_form_label_class()) ?>">Harga per hari</label>
            <input type="number" id="harga_per_hari" name="harga_per_hari" min="0" required
                   value="<?= e((string) ($paket['harga_per_hari'] ?? '')) ?>"
                   class="<?= e(ui_form_input_class(!empty($errors['harga_per_hari']))) ?>">
            <?php if (!empty($errors['harga_per_hari'])): ?>
                <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['harga_per_hari']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="deskripsi" class="<?= e(ui_form_label_class()) ?>">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" class="<?= e(ui_form_input_class()) ?>"><?= e((string) ($paket['deskripsi'] ?? '')) ?></textarea>
        </div>

        <label class="<?= e($checkboxWrapClass) ?>">
            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded border-border text-primary focus:ring-ring"
                   <?= !isset($paket['aktif']) || !empty($paket['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-foreground">Paket aktif (dapat dipilih pelanggan)</span>
        </label>

        <div class="flex flex-wrap gap-3 pt-1">
            <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">
                <?= e($submitLabel) ?>
            </button>
            <a href="/admin/penitipan/paket" class="<?= e(ui_btn_secondary()) ?>">Batal</a>
        </div>
    </form>
</div>
