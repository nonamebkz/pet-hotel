<?php

declare(strict_types=1);

/**
 * Render link / group menu items (sidebar atau mobile drawer).
 *
 * @var list<array{label: string, items: list<array<string, mixed>>}> $menuSections
 * @var bool $mobile
 */

$mobile = $mobile ?? false;
$linkClassFn = $mobile ? 'nav_mobile_link_classes' : 'nav_sidebar_link_classes';
$sublinkClassFn = $mobile ? 'nav_mobile_link_classes' : 'nav_sidebar_sublink_classes';

foreach ($menuSections as $section):
    ?>
    <div>
        <p class="<?= e($mobile ? 'px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground' : nav_section_heading_classes()) ?>">
            <?= e($section['label']) ?>
        </p>
        <div class="<?= e($mobile ? 'space-y-0.5' : 'space-y-0.5 px-2') ?>">
            <?php foreach ($section['items'] as $item): ?>
                <?php
                $type = (string) ($item['type'] ?? 'link');
                if ($type === 'group'):
                    $children = $item['children'] ?? [];
                    $groupActive = nav_menu_group_active(
                        $children,
                        $item['paths'] ?? [],
                        (bool) ($item['prefix'] ?? true),
                    );
                    if ($mobile):
                        ?>
                        <p class="px-3 pt-2 pb-1 text-xs font-semibold text-foreground"><?= e((string) $item['label']) ?></p>
                        <?php foreach ($children as $child): ?>
                            <a href="<?= e((string) $child['href']) ?>" class="<?= e(call_user_func($sublinkClassFn, nav_menu_item_active($child))) ?>">
                                <?= e((string) $child['label']) ?>
                            </a>
                        <?php endforeach;
                    else:
                        ?>
                        <details class="group/nav" <?= $groupActive ? 'open' : '' ?>>
                            <summary class="<?= e(nav_sidebar_link_classes($groupActive)) ?> cursor-pointer list-none [&::-webkit-details-marker]:hidden">
                                <span class="flex-1"><?= e((string) $item['label']) ?></span>
                                <svg class="h-4 w-4 opacity-60 transition group-open/nav:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>
                            <div class="mt-0.5 space-y-0.5 pb-1">
                                <?php foreach ($children as $child): ?>
                                    <a href="<?= e((string) $child['href']) ?>" class="<?= e($sublinkClassFn(nav_menu_item_active($child))) ?>">
                                        <?= e((string) $child['label']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </details>
                    <?php
                    endif;
                else:
                    $active = nav_menu_item_active($item);
                    ?>
                    <a href="<?= e((string) $item['href']) ?>" class="<?= e(call_user_func($linkClassFn, $active)) ?>">
                        <?= e((string) $item['label']) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
