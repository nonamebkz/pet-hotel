<?php

declare(strict_types=1);

/**
 * @var string|null $searchName
 * @var string|null $searchValue
 * @var string|null $searchPlaceholder
 * @var string|null $formAction
 * @var list<array{label: string, removeHref?: string}>|null $chips
 * @var string|null $resetHref
 * @var int|null $resultCount
 * @var string|null $resultLabel
 */
$searchName = $searchName ?? 'q';
$searchValue = $searchValue ?? '';
$searchPlaceholder = $searchPlaceholder ?? 'Cari…';
$formAction = $formAction ?? '';
$chips = $chips ?? [];
$resetHref = $resetHref ?? null;
$resultCount = $resultCount ?? null;
$resultLabel = $resultLabel ?? 'item';
?>
<div class="<?= e(design_cn(design_surface('panel'), 'mb-4 flex flex-col gap-3 p-4 sm:flex-row sm:flex-wrap sm:items-center')) ?>">
    <?php if ($formAction !== ''): ?>
        <form method="get" action="<?= e($formAction) ?>" class="flex min-w-0 flex-1 items-center gap-2 sm:max-w-md" role="search">
            <label for="list-toolbar-search" class="sr-only">Pencarian lokal</label>
            <input
                type="search"
                id="list-toolbar-search"
                name="<?= e($searchName) ?>"
                value="<?= e($searchValue) ?>"
                placeholder="<?= e($searchPlaceholder) ?>"
                class="<?= e(ui_form_input_class()) ?> py-2 text-sm"
            >
            <button type="submit" class="<?= e(ui_btn_secondary()) ?> shrink-0 text-sm px-3 py-2">
                Cari
            </button>
        </form>
    <?php endif; ?>

    <?php if ($chips !== [] || $resetHref !== null || $resultCount !== null): ?>
        <div class="w-full sm:flex-1 sm:min-w-[12rem]">
            <?php
            ui_filter_chips($chips, $resetHref, $resultCount, $resultLabel);
            ?>
        </div>
    <?php endif; ?>
</div>
