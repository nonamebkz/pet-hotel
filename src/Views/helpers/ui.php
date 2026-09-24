<?php

declare(strict_types=1);

function ui_empty_state(
    string $variant,
    string $title,
    ?string $description = null,
    ?string $ctaLabel = null,
    ?string $ctaHref = null,
    ?string $ctaClass = null,
): void {
    require BASE_PATH . '/src/Views/partials/ui/empty-state.php';
}

/** @param list<array{label: string, removeHref?: string}> $chips */
function ui_filter_chips(
    array $chips,
    ?string $resetHref = null,
    ?int $resultCount = null,
    string $resultLabel = 'item',
): void {
    require BASE_PATH . '/src/Views/partials/ui/filter-chips.php';
}

function ui_form_footer_mobile(string $submitLabel, ?string $formId = null, ?string $buttonClass = null): void
{
    require BASE_PATH . '/src/Views/partials/ui/form-footer-mobile.php';
}
