<?php

declare(strict_types=1);

/**
 * @var list<array{label: string, href?: string}> $items
 */
$items = $items ?? [];

if ($items === []) {
    return;
}
?>
<nav class="mb-4 text-sm" aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-1.5">
        <?php foreach ($items as $index => $item): ?>
            <?php
            $isLast = $index === count($items) - 1;
            $label = (string) ($item['label'] ?? '');
            $href = isset($item['href']) ? (string) $item['href'] : '';
            ?>
            <li class="inline-flex items-center gap-1.5">
                <?php if ($index > 0): ?>
                    <span class="text-muted-foreground/60" aria-hidden="true">/</span>
                <?php endif; ?>
                <?php if (!$isLast && $href !== ''): ?>
                    <a href="<?= e($href) ?>" class="text-muted-foreground hover:text-primary transition">
                        <?= e($label) ?>
                    </a>
                <?php else: ?>
                    <span class="<?= e($isLast ? 'font-medium text-foreground' : 'text-muted-foreground') ?>" <?= $isLast ? 'aria-current="page"' : '' ?>>
                        <?= e($label) ?>
                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
