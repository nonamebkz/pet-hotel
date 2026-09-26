<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$tanggal = $tanggal ?? date('Y-m-d');
$errors = Session::getFlash('errors', []);

$inputClass = static function (string $field, array $errors): string {
    $base = design_cn(ui_form_input_class(), 'py-3');
    if (!empty($errors[$field])) {
        return $base . ' border-destructive focus:border-destructive focus:ring-destructive/25';
    }

    return $base;
};

$slotListHref = '/admin/pet-care/slot?tanggal=' . urlencode($tanggal);
?>
<div class="<?= e(design_cn(ui_page_content_shell_classes(), design_page_layout('formSm'))) ?>">
    <?php
    ui_breadcrumb([
        ['label' => 'Slot Dokter', 'href' => $slotListHref],
        ['label' => 'Tambah'],
    ]);
    ui_page_header(
        'Tambah slot dokter',
        'Pet Care · Slot',
        'Buat slot waktu konsultasi. Maksimal 1 booking per slot (1 dokter).',
    );
    ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive" role="alert">
            <?= e((string) $errors['general']) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e($action) ?>"
          class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5')) ?>"
          data-loading-submit>
        <?= Csrf::field() ?>

        <div>
            <label for="tanggal" class="<?= e(ui_form_label_class()) ?>">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" min="<?= e(date('Y-m-d')) ?>"
                   value="<?= e((string) old('tanggal', $tanggal)) ?>"
                   class="<?= e($inputClass('tanggal', $errors)) ?>">
            <?php if (!empty($errors['tanggal'])): ?>
                <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['tanggal']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="slot_waktu" class="<?= e(ui_form_label_class()) ?>">Waktu Slot</label>
            <input type="time" id="slot_waktu" name="slot_waktu"
                   value="<?= e((string) old('slot_waktu', '09:00')) ?>"
                   class="<?= e($inputClass('slot_waktu', $errors)) ?>">
            <?php if (!empty($errors['slot_waktu'])): ?>
                <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['slot_waktu']) ?></p>
            <?php endif; ?>
            <p class="mt-1.5 text-xs text-muted-foreground">Maksimal 1 booking per slot (1 dokter).</p>
        </div>

        <div class="flex flex-wrap gap-3 pt-1">
            <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">
                <?= e($submitLabel) ?>
            </button>
            <a href="<?= e($slotListHref) ?>" class="<?= e(ui_btn_secondary()) ?>">Batal</a>
        </div>
    </form>
</div>
