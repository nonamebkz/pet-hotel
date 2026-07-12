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
<div class="flex flex-wrap items-center gap-2 mb-5">
    <?php if ($resultCount !== null): ?>
        <span class="text-sm text-content-secondary mr-1">
            Menampilkan <strong class="font-semibold text-content-primary"><?= e((string) $resultCount) ?></strong> <?= e($resultLabel) ?>
        </span>
    <?php endif; ?>

    <?php foreach ($chips as $chip): ?>
        <span class="inline-flex items-center gap-1 rounded-xl bg-admin-soft text-admin px-3 py-1.5 text-xs font-semibold shadow-soft-inset">
            <?= e((string) $chip['label']) ?>
            <?php if (!empty($chip['removeHref'])): ?>
                <a href="<?= e((string) $chip['removeHref']) ?>"
                   class="ml-0.5 cursor-pointer inline-flex h-4 w-4 items-center justify-center rounded-md text-admin/70 transition duration-soft hover:bg-white hover:text-admin focus:outline-none focus-visible:ring-2 focus-visible:ring-admin"
                   aria-label="Hapus filter">×</a>
            <?php endif; ?>
        </span>
    <?php endforeach; ?>

    <?php if ($resetHref !== null && ($chips !== [] || $resultCount === 0)): ?>
        <a href="<?= e($resetHref) ?>"
           class="cursor-pointer text-sm font-semibold text-content-secondary transition duration-soft hover:text-admin focus:outline-none focus-visible:underline ml-1">
            Reset Filter
        </a>
    <?php endif; ?>
</div>
