<?php

declare(strict_types=1);

/** @var string $fileUrl */
/** @var string|null $label */
/** @var string|null $maxHeightClass */

$fileUrl = (string) ($fileUrl ?? '');
$label = $label ?? 'Preview';
$maxHeightClass = $maxHeightClass ?? 'max-h-32';

if ($fileUrl === '') {
    return;
}

$isPdf = str_ends_with(strtolower($fileUrl), '.pdf');
?>
<div class="uploaded-file-preview">
    <?php if ($label !== ''): ?>
        <p class="text-xs font-medium text-gray-600 mb-1"><?= e($label) ?></p>
    <?php endif; ?>
    <?php if ($isPdf): ?>
        <a href="<?= e($fileUrl) ?>" target="_blank" rel="noopener noreferrer"
           class="inline-block border rounded-lg px-3 py-2 text-xs text-blue-600 hover:bg-blue-50">
            Buka PDF
        </a>
    <?php else: ?>
        <a href="<?= e($fileUrl) ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= e($fileUrl) ?>" alt="<?= e($label) ?>"
                 class="<?= e($maxHeightClass) ?> max-w-full rounded-lg border object-contain">
        </a>
    <?php endif; ?>
</div>
