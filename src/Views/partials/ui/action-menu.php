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
            class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-800"
            data-action-menu-trigger
            aria-haspopup="true"
            aria-expanded="false"
            aria-label="Menu aksi">&#8942;</button>
    <div class="hidden absolute right-0 z-20 mt-1 w-44 origin-top-right rounded-lg border bg-white shadow-lg py-1"
         data-action-menu-panel
         role="menu">
        <?php foreach ($items as $item): ?>
            <?php if (($item['type'] ?? '') === 'link' && !empty($item['href'])): ?>
                <a href="<?= e((string) $item['href']) ?>"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 <?= e((string) ($item['class'] ?? '')) ?>"
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
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 <?= e((string) ($item['class'] ?? 'text-gray-700')) ?>"
                            role="menuitem"><?= e((string) $item['label']) ?></button>
                </form>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
