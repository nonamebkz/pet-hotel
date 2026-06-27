<?php

declare(strict_types=1);

/** @var array<string, mixed>|null $kucing */
/** @var list<array<string, mixed>> $vaksinList */
/** @var array<string, string> $errors */

$index = $index ?? 0;
$row = $vaksinList[$index] ?? [];
?>
<div class="vaksin-row flex flex-wrap gap-3 items-start border border-gray-100 rounded-lg p-3 bg-gray-50">
    <div class="flex-1 min-w-[140px]">
        <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Vaksin</label>
        <input type="text" name="vaksin_jenis[]"
               value="<?= e((string) old("vaksin_jenis.$index", $row['jenis_vaksin'] ?? '')) ?>"
               placeholder="FVRCP, Rabies, ..."
               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
    <div class="w-40">
        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
        <input type="date" name="vaksin_tanggal[]"
               value="<?= e((string) old("vaksin_tanggal.$index", $row['tanggal_vaksin'] ?? '')) ?>"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs font-medium text-gray-600 mb-1">Sertifikat (opsional)</label>
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
               class="vaksin-sertifikat-input w-full text-xs text-gray-600">
        <div class="sertifikat-preview-new hidden mt-2"></div>
    </div>
    <div class="pt-5">
        <button type="button"
                class="remove-vaksin-row text-sm text-red-600 hover:text-red-700 px-2 py-1"
                title="Hapus baris">
            ✕
        </button>
    </div>
    <?php if (!empty($errors["vaksin_$index"])): ?>
        <p class="w-full text-red-600 text-xs"><?= e($errors["vaksin_$index"]) ?></p>
    <?php endif; ?>
</div>
