<?php

declare(strict_types=1);

/**
 * @var list<array{label: string, removeHref?: string}> $chips
 * @var string|null $resetHref
 * @var int|null $resultCount
 * @var string|null $resultLabel
 */
$chips = $chips ?? [];
$resetHref = $resetHref ?? null;
$resultCount = $resultCount ?? null;
$resultLabel = $resultLabel ?? 'item';
?>
<div class="flex flex-wrap items-center gap-2 mb-4">
    <?php if ($resultCount !== null): ?>
        <span class="text-sm text-gray-600 mr-2">
            Menampilkan <strong><?= e((string) $resultCount) ?></strong> <?= e($resultLabel) ?>
        </span>
    <?php endif; ?>

    <?php foreach ($chips as $chip): ?>
        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-admin px-3 py-1 text-xs font-medium">
            <?= e((string) $chip['label']) ?>
            <?php if (!empty($chip['removeHref'])): ?>
                <a href="<?= e((string) $chip['removeHref']) ?>"
                   class="ml-0.5 text-slate-500 hover:text-admin"
                   aria-label="Hapus filter">×</a>
            <?php endif; ?>
        </span>
    <?php endforeach; ?>

    <?php if ($resetHref !== null && ($chips !== [] || $resultCount === 0)): ?>
        <a href="<?= e($resetHref) ?>"
           class="text-sm text-slate-600 hover:underline ml-1">Reset Filter</a>
    <?php endif; ?>
</div>
