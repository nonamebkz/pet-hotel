<?php

declare(strict_types=1);

use App\Core\Csrf;

/**
 * @var list<array{type: string, label: string, href?: string, formAction?: string, formFields?: array<string, string>, confirm?: string, class?: string}> $items
 */
$items = $items ?? [];
?>
<div class="relative inline-block text-left" data-action-menu>
    <button type="button"
            class="cursor-pointer p-2 rounded-xl text-muted-foreground transition hover:bg-muted hover:text-foreground focus:outline-none focus-visible:ring-2 focus-visible:ring-ring touch-target"
            data-action-menu-trigger
            aria-haspopup="true"
            aria-expanded="false"
            aria-label="Menu aksi">
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 6.75a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6.75a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 6.75a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
        </svg>
    </button>
    <div class="hidden absolute right-0 z-20 mt-1 w-48 origin-top-right rounded-xl border border-border bg-card shadow-md py-1"
         data-action-menu-panel
         role="menu">
        <?php foreach ($items as $item): ?>
            <?php if (($item['type'] ?? '') === 'link' && !empty($item['href'])): ?>
                <a href="<?= e((string) $item['href']) ?>"
                   class="block cursor-pointer px-4 py-2.5 text-sm text-foreground transition hover:bg-muted <?= e((string) ($item['class'] ?? '')) ?>"
                   role="menuitem"><?= e((string) $item['label']) ?></a>
            <?php elseif (($item['type'] ?? '') === 'form' && !empty($item['formAction'])): ?>
                <form method="POST" action="<?= e((string) $item['formAction']) ?>"
                      class="block"
                      <?php if (!empty($item['confirm'])): ?>
                          data-confirm="<?= e((string) $item['confirm']) ?>"
                      <?php endif; ?>>
                    <?= Csrf::field() ?>
                    <?php foreach (($item['formFields'] ?? []) as $name => $value): ?>
                        <input type="hidden" name="<?= e((string) $name) ?>" value="<?= e((string) $value) ?>">
                    <?php endforeach; ?>
                    <button type="submit"
                            class="w-full cursor-pointer text-left px-4 py-2.5 text-sm transition hover:bg-muted <?= e((string) ($item['class'] ?? 'text-foreground')) ?>"
                            role="menuitem"><?= e((string) $item['label']) ?></button>
                </form>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
