<?php

declare(strict_types=1);

/** @var array<string, mixed>|null $kucing */
/** @var list<array<string, mixed>> $vaksinList */
/** @var array<string, string> $errors */

$index = $index ?? 0;
$row = $vaksinList[$index] ?? ($row ?? []);

$vaksinLabelClass = 'mb-1 block text-xs font-semibold text-foreground';
$vaksinInputClass = design_cn(ui_form_input_class(), 'py-2 text-sm');
?>
<div class="vaksin-row flex flex-wrap gap-3 items-start rounded-lg border border-border bg-muted/30 p-3">
    <div class="flex-1 min-w-[140px]">
        <label class="<?= e($vaksinLabelClass) ?>">Jenis Vaksin</label>
        <input type="text" name="vaksin_jenis[]"
               value="<?= e((string) old("vaksin_jenis.$index", $row['jenis_vaksin'] ?? '')) ?>"
               placeholder="FVRCP, Rabies, ..."
               class="<?= e($vaksinInputClass) ?>">
    </div>
    <div class="w-40">
        <label class="<?= e($vaksinLabelClass) ?>">Tanggal</label>
        <input type="date" name="vaksin_tanggal[]"
               value="<?= e((string) old("vaksin_tanggal.$index", $row['tanggal_vaksin'] ?? '')) ?>"
               class="<?= e($vaksinInputClass) ?>">
    </div>
    <div class="flex-1 min-w-[160px]">
        <label class="<?= e($vaksinLabelClass) ?>">Sertifikat (opsional)</label>
        <?php if (!empty($row['sertifikat_url'])): ?>
            <input type="hidden" name="vaksin_sertifikat_existing[]" value="<?= e((string) $row['sertifikat_url']) ?>">
            <div class="sertifikat-preview-existing mb-2">
                <?php
                $fileUrl = (string) $row['sertifikat_url'];
                $label = 'Sertifikat saat ini';
                require __DIR__ . '/uploaded-file-preview.php';
                ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="vaksin_sertifikat_existing[]" value="">
        <?php endif; ?>
        <input type="file" name="vaksin_sertifikat[]" accept="image/jpeg,image/png,image/webp,application/pdf"
               class="vaksin-sertifikat-input w-full text-xs text-muted-foreground">
        <div class="sertifikat-preview-new hidden mt-2"></div>
    </div>
    <div class="pt-5">
        <button type="button"
                class="remove-vaksin-row px-2 py-1 text-sm text-destructive transition hover:opacity-80"
                title="Hapus baris">
            ✕
        </button>
    </div>
    <?php if (!empty($errors["vaksin_$index"])): ?>
        <p class="<?= e(ui_field_error_class()) ?> w-full"><?= e($errors["vaksin_$index"]) ?></p>
    <?php endif; ?>
</div>
