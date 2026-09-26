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

function ui_page_header(
    string $title,
    ?string $eyebrow = null,
    ?string $description = null,
    ?string $actionsHtml = null,
    ?string $metaHtml = null,
    ?string $headerClass = null,
): void {
    require BASE_PATH . '/src/Views/partials/ui/page-header.php';
}

/** @param list<array{label: string, href?: string}> $items */
function ui_breadcrumb(array $items): void
{
    require BASE_PATH . '/src/Views/partials/ui/breadcrumb.php';
}

/** @param list<array{label: string, removeHref?: string}>|null $chips */
function ui_list_toolbar(
    ?string $formAction = null,
    string $searchName = 'q',
    string $searchValue = '',
    string $searchPlaceholder = 'Cari…',
    array $chips = [],
    ?string $resetHref = null,
    ?int $resultCount = null,
    string $resultLabel = 'item',
): void {
    require BASE_PATH . '/src/Views/partials/ui/list-toolbar.php';
}
