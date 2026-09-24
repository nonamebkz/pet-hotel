<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$tanggal = $tanggal ?? date('Y-m-d');
$errors = Session::getFlash('errors', []);

$inputClass = design_cn(
    'w-full rounded-xl border border-input bg-background px-3.5 py-3 text-base sm:text-sm text-foreground transition',
    'hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25',
);
$errorBorder = ' border-destructive/50 focus:border-destructive focus:ring-destructive/25';
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground" aria-label="Breadcrumb">
        <a href="/admin/pet-care/slot?tanggal=<?= e(urlencode($tanggal)) ?>"
           class="hover:text-primary focus:outline-none focus-visible:underline">Slot Dokter</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-foreground">Tambah</span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-foreground">Tambah slot dokter</h1>
        <p class="mt-2 text-sm text-muted-foreground">
            Buat slot waktu konsultasi. Maksimal 1 booking per slot (1 dokter).
        </p>
    </header>

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
                   class="<?= e($inputClass . (!empty($errors['tanggal']) ? $errorBorder : '')) ?>">
            <?php if (!empty($errors['tanggal'])): ?>
                <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['tanggal']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="slot_waktu" class="<?= e(ui_form_label_class()) ?>">Waktu Slot</label>
            <input type="time" id="slot_waktu" name="slot_waktu"
                   value="<?= e((string) old('slot_waktu', '09:00')) ?>"
                   class="<?= e($inputClass . (!empty($errors['slot_waktu']) ? $errorBorder : '')) ?>">
            <?php if (!empty($errors['slot_waktu'])): ?>
                <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['slot_waktu']) ?></p>
            <?php endif; ?>
            <p class="mt-1.5 text-xs text-muted-foreground">Maksimal 1 booking per slot (1 dokter).</p>
        </div>

        <div class="flex flex-wrap gap-3 pt-1">
            <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">
                <?= e($submitLabel) ?>
            </button>
            <a href="/admin/pet-care/slot?tanggal=<?= e(urlencode($tanggal)) ?>" class="<?= e(ui_btn_secondary()) ?>">Batal</a>
        </div>
    </form>
</div>
