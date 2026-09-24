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
        <span class="text-sm text-muted-foreground mr-1">
            Menampilkan <strong class="font-semibold text-foreground"><?= e((string) $resultCount) ?></strong> <?= e($resultLabel) ?>
        </span>
    <?php endif; ?>

    <?php foreach ($chips as $chip): ?>
        <span class="inline-flex items-center gap-1 rounded-xl border border-border bg-muted/50 text-foreground px-3 py-1.5 text-xs font-semibold">
            <?= e((string) $chip['label']) ?>
            <?php if (!empty($chip['removeHref'])): ?>
                <a href="<?= e((string) $chip['removeHref']) ?>"
                   class="ml-0.5 cursor-pointer inline-flex h-4 w-4 items-center justify-center rounded-md text-muted-foreground transition hover:bg-background hover:text-foreground focus:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                   aria-label="Hapus filter">×</a>
            <?php endif; ?>
        </span>
    <?php endforeach; ?>

    <?php if ($resetHref !== null && ($chips !== [] || $resultCount === 0)): ?>
        <a href="<?= e($resetHref) ?>"
           class="<?= e(design_cn(ui_btn_secondary(), 'text-sm px-3 py-1.5 ml-1')) ?>">
            Reset Filter
        </a>
    <?php endif; ?>
</div>
