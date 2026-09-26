<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuota = $kuota ?? null;
$kamarList = $kamarList ?? [];
$action = $action ?? '';
$isEdit = $kuota !== null && !empty($kuota['id']);
$formPanelClass = design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5');
$infoBoxClass = design_cn(design_interactive('metricCellOutlined'), 'px-4 py-3 text-sm');
?>
<div class="<?= e(design_cn(ui_page_content_shell_classes(), design_page_layout('formSm'))) ?>">
    <?php
    ui_breadcrumb([
        ['label' => 'Kuota', 'href' => '/admin/penitipan/kuota'],
        ['label' => $isEdit ? 'Edit' : 'Tambah'],
    ]);
    ui_page_header(
        $isEdit ? 'Edit kuota' : 'Tambah kuota',
        'Penitipan · Administrasi',
        $isEdit ? 'Perbarui slot maksimal untuk tanggal yang sudah dibuat.' : 'Buat slot harian untuk kamar tertentu.',
    );
    ?>

    <form method="POST" action="<?= e($action) ?>" class="<?= e($formPanelClass) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kuota['id']) ?>">
            <div class="<?= e($infoBoxClass) ?>">
                <p class="font-semibold text-primary"><?= e((string) ($kuota['nama_kamar'] ?? '')) ?></p>
                <p class="text-muted-foreground mt-0.5"><?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?></p>
            </div>
        <?php else: ?>
            <div>
                <label for="kamar_penitipan_id" class="<?= e(ui_form_label_class()) ?>">Kamar</label>
                <select id="kamar_penitipan_id" name="kamar_penitipan_id" required class="<?= e(ui_form_input_class()) ?>">
                    <?php foreach ($kamarList as $k): ?>
                        <option value="<?= e((string) $k['id']) ?>"><?= e((string) $k['nama_kamar']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="tanggal" class="<?= e(ui_form_label_class()) ?>">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" min="<?= e(date('Y-m-d')) ?>" required class="<?= e(ui_form_input_class()) ?>">
            </div>
        <?php endif; ?>

        <div>
            <label for="slot_maksimal" class="<?= e(ui_form_label_class()) ?>">Slot maksimal</label>
            <input type="number" id="slot_maksimal" name="slot_maksimal" min="0" required
                   value="<?= e((string) ($kuota['slot_maksimal'] ?? '')) ?>"
                   class="<?= e(ui_form_input_class()) ?>">
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">Simpan</button>
            <a href="/admin/penitipan/kuota" class="<?= e(ui_btn_secondary()) ?>">Batal</a>
        </div>
    </form>
</div>
