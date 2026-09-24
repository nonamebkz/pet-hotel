<?php

declare(strict_types=1);

/**
 * @var string $variant empty|filtered|success
 * @var string $title
 * @var string|null $description
 * @var string|null $ctaLabel
 * @var string|null $ctaHref
 * @var string|null $ctaClass
 */
$variant = $variant ?? 'empty';
$title = $title ?? 'Belum ada data';
$description = $description ?? null;
$ctaLabel = $ctaLabel ?? null;
$ctaHref = $ctaHref ?? null;
$ctaClass = $ctaClass ?? ui_btn_primary();

$iconPaths = [
    'empty' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
    'filtered' => 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z',
    'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
];
$iconTone = [
    'empty' => 'muted',
    'filtered' => 'warning',
    'success' => 'success',
];
$tone = $iconTone[$variant] ?? 'muted';
?>
<div class="<?= e(design_cn(design_surface('empty'), 'p-8 text-center')) ?>">
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center <?= e(design_icon_badge($tone, 'h-14 w-14 rounded-2xl p-0')) ?>">
        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= e($iconPaths[$variant] ?? $iconPaths['empty']) ?>"></path>
        </svg>
    </div>
    <h3 class="font-heading text-lg text-foreground mb-1"><?= e($title) ?></h3>
    <?php if ($description !== null && $description !== ''): ?>
        <p class="text-sm text-muted-foreground mb-4 max-w-md mx-auto"><?= e($description) ?></p>
    <?php endif; ?>
    <?php if ($ctaLabel !== null && $ctaHref !== null): ?>
        <a href="<?= e($ctaHref) ?>" class="<?= e($ctaClass) ?>"><?= e($ctaLabel) ?></a>
    <?php endif; ?>
</div>
