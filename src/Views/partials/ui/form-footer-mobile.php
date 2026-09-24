<?php

declare(strict_types=1);

/**
 * Sticky submit bar for long forms on mobile (above pelanggan bottom nav).
 *
 * @var string $submitLabel
 * @var string|null $formId HTML form id for form="" attribute on button
 * @var string|null $buttonClass extra classes on submit button
 */
$submitLabel = $submitLabel ?? 'Simpan';
$formId = $formId ?? null;
$buttonClass = $buttonClass ?? null;
?>
<div class="<?= e(design_cn(
    design_surface('stickyBar'),
    'safe-bottom fixed inset-x-0 bottom-[calc(5.5rem+env(safe-area-inset-bottom))] z-20 p-4 md:hidden',
)) ?>">
    <button type="submit"
            <?= $formId !== null && $formId !== '' ? 'form="' . e($formId) . '"' : '' ?>
            class="<?= e(design_cn(ui_btn_primary(), 'w-full active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60', $buttonClass)) ?>">
        <?= e($submitLabel) ?>
    </button>
</div>
