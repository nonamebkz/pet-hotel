<?php

declare(strict_types=1);

/**
 * @var string|null $eyebrow
 * @var string $title
 * @var string|null $description
 * @var string|null $headerClass
 * @var string|null $actionsHtml raw HTML for primary/secondary actions (already escaped by caller)
 * @var string|null $metaHtml badges / status row (already escaped)
 */

$eyebrow = $eyebrow ?? null;
$description = $description ?? null;
$headerClass = $headerClass ?? design_cn(design_surface('metric'), 'p-5 sm:p-6');
$actionsHtml = $actionsHtml ?? null;
$metaHtml = $metaHtml ?? null;
?>
<header class="<?= e($headerClass) ?>">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="min-w-0 flex-1">
            <?php if ($eyebrow !== null && $eyebrow !== ''): ?>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground"><?= e($eyebrow) ?></p>
            <?php endif; ?>
            <h1 class="mt-1 font-heading text-xl sm:text-2xl font-bold tracking-tight text-foreground">
                <?= e($title) ?>
            </h1>
            <?php if ($description !== null && $description !== ''): ?>
                <p class="mt-2 text-sm text-muted-foreground max-w-2xl"><?= e($description) ?></p>
            <?php endif; ?>
            <?php if ($metaHtml !== null && $metaHtml !== ''): ?>
                <div class="mt-3 flex flex-wrap gap-2 text-xs"><?= $metaHtml ?></div>
            <?php endif; ?>
        </div>
        <?php if ($actionsHtml !== null && $actionsHtml !== ''): ?>
            <div class="flex flex-wrap items-center gap-2 shrink-0"><?= $actionsHtml ?></div>
        <?php endif; ?>
    </div>
</header>
