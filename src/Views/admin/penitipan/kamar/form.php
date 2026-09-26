<?php

declare(strict_types=1);

use App\Core\Csrf;

$kamar = $kamar ?? null;
$action = $action ?? '';
$isEdit = $kamar !== null && !empty($kamar['id']);
$formPanelClass = design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5');
$checkboxWrapClass = design_cn(design_interactive('metricCellOutlined'), 'flex cursor-pointer items-center gap-3 px-4 py-3');
?>
<div class="<?= e(design_cn(ui_page_content_shell_classes(), design_page_layout('formSm'))) ?>">
    <?php
    ui_breadcrumb([
        ['label' => 'Kamar', 'href' => '/admin/penitipan/kamar'],
        ['label' => $isEdit ? 'Edit' : 'Tambah'],
    ]);
    ui_page_header(
        $isEdit ? 'Edit kamar' : 'Tambah kamar',
        'Penitipan · Administrasi',
        'Tentukan nama kamar dan kapasitas slot harian.',
    );
    ?>

    <form method="POST" action="<?= e($action) ?>" class="<?= e($formPanelClass) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kamar['id']) ?>">
        <?php endif; ?>

        <div>
            <label for="nama_kamar" class="<?= e(ui_form_label_class()) ?>">Nama kamar</label>
            <input type="text" id="nama_kamar" name="nama_kamar" required
                   value="<?= e((string) ($kamar['nama_kamar'] ?? '')) ?>"
                   class="<?= e(ui_form_input_class()) ?>">
        </div>

        <div>
            <label for="kapasitas" class="<?= e(ui_form_label_class()) ?>">Kapasitas</label>
            <input type="number" id="kapasitas" name="kapasitas" min="1" required
                   value="<?= e((string) ($kamar['kapasitas'] ?? '')) ?>"
                   class="<?= e(ui_form_input_class()) ?>">
        </div>

        <label class="<?= e($checkboxWrapClass) ?>">
            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded border-border text-primary focus:ring-ring"
                   <?= !isset($kamar['aktif']) || !empty($kamar['aktif']) ? 'checked' : '' ?>>
            <span class="text-sm font-medium text-foreground">Kamar aktif</span>
        </label>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">Simpan</button>
            <a href="/admin/penitipan/kamar" class="<?= e(ui_btn_secondary()) ?>">Batal</a>
        </div>
    </form>
</div>
