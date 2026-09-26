<?php

declare(strict_types=1);

function ui_btn_primary(): string
{
    return 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium touch-target bg-primary text-primary-foreground hover:opacity-90 transition';
}

function ui_btn_secondary(): string
{
    return 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium touch-target border border-border bg-background text-foreground hover:bg-muted transition';
}

function ui_btn_destructive(): string
{
    return 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium touch-target bg-destructive text-primary-foreground hover:opacity-90 transition';
}

function ui_btn_tertiary(): string
{
    return 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium touch-target text-primary hover:bg-muted transition';
}

function ui_page_shell_classes(): string
{
    return design_cn('space-y-6 md:space-y-8', design_page_layout('formSm'));
}

function ui_page_content_shell_classes(): string
{
    return 'space-y-6 md:space-y-8';
}

function ui_layout_main_classes(): string
{
    return design_cn(
        design_page_layout('workspace'),
        'px-4 py-4 pb-mobile-nav md:px-6 md:py-8 lg:px-8 md:pb-0',
    );
}

function ui_form_input_class(bool $invalid = false): string
{
    return design_cn(
        'w-full rounded-lg border border-border bg-background px-3 py-2.5 text-base text-foreground',
        'placeholder:text-muted-foreground transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25',
        $invalid ? 'border-destructive focus:border-destructive focus:ring-destructive/25' : '',
    );
}

function ui_form_label_class(): string
{
    return 'mb-1.5 block text-sm font-semibold text-foreground';
}

function ui_field_error_class(): string
{
    return 'mt-1 text-xs text-destructive';
}

function ui_back_link_class(): string
{
    return 'text-sm text-muted-foreground transition hover:text-primary';
}
