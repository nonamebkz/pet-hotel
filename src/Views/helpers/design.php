<?php

declare(strict_types=1);

/**
 * Token kelas Tailwind bersama — port dari lib/design.ts (Pencatatan).
 * Pakai di view: class="<?= e(design_surface('panel')) ?>"
 */

/** @var array<string, string> */
const DESIGN_SURFACE = [
    'panel' => 'rounded-2xl border bg-card/80 shadow-sm backdrop-blur-sm',
    'metric' => 'rounded-2xl border bg-card p-4 shadow-sm',
    'empty' => 'rounded-2xl border border-dashed bg-card/50',
    'stickyBar' => 'border-t bg-background/95 backdrop-blur',
    'chart' => 'overflow-x-auto rounded-2xl border bg-card p-3',
];

/** @var array<string, string> */
const DESIGN_INTERACTIVE = [
    'cardLink' => 'block rounded-2xl border bg-card p-4 shadow-sm transition active:scale-[0.99]',
    'listArticle' => 'rounded-2xl border bg-card p-4 shadow-sm',
    'listArticleHover' => 'transition hover:-translate-y-0.5 hover:shadow-md',
    'listRowHover' => 'hover:border-primary/30',
    'metricCell' => 'rounded-2xl bg-muted/60 p-3',
    'metricCellOutlined' => 'rounded-2xl border bg-background/80 p-3',
];

/** @var array<string, string> */
const DESIGN_WQ_STATUS_SURFACE = [
    'NORMAL' => 'border-emerald-500/20 bg-emerald-500/[0.04]',
    'WARNING' => 'border-amber-500/30 bg-amber-500/[0.06]',
    'DANGER' => 'border-destructive/30 bg-destructive/[0.06]',
];

/** @var array<string, string> */
const DESIGN_ALERT_INLINE = [
    'warning' => 'flex items-center gap-2 rounded-2xl border border-amber-200/80 bg-amber-50 px-3 py-2 text-xs text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/40 dark:text-amber-200',
    'warningLink' => 'flex items-center justify-between gap-3 rounded-2xl border border-amber-200/80 bg-amber-50 px-4 py-4 text-sm text-amber-900 transition active:scale-[0.99] dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-100',
];

/** @var array<string, string> */
const DESIGN_ADVICE_PANEL_SURFACE = [
    'warning' => 'border-amber-500/30 bg-amber-500/5',
    'danger' => 'border-destructive/30 bg-destructive/5',
];

/** @var array<string, string> */
const DESIGN_SKELETON = [
    'block' => 'rounded-2xl',
    'row' => 'h-20 rounded-2xl',
    'card' => 'h-28 rounded-2xl',
];

/** @var array<string, string> */
const DESIGN_PAGE_LAYOUT = [
    'workspace' => 'mx-auto w-full max-w-[90rem]',
    'formSm' => 'mx-auto max-w-2xl pb-24 md:pb-0',
    'formLg' => 'mx-auto max-w-3xl pb-24 md:pb-0',
    'detail' => 'mx-auto max-w-2xl',
    'detailLg' => 'mx-auto max-w-3xl',
];

/** @var array<string, string> */
const DESIGN_STATUS_TONE = [
    'success' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300',
    'muted' => 'bg-muted text-muted-foreground',
    'danger' => 'bg-destructive/10 text-destructive',
];

/** @var array<string, string> */
const DESIGN_ICON_TONE = [
    'default' => 'bg-primary/10 text-primary',
    'success' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300',
    'warning' => 'bg-amber-500/10 text-amber-700 dark:text-amber-300',
    'danger' => 'bg-destructive/10 text-destructive',
];

function design_surface(string $key): string
{
    return design_token(DESIGN_SURFACE, $key, 'surface');
}

function design_interactive(string $key): string
{
    return design_token(DESIGN_INTERACTIVE, $key, 'interactive');
}

function design_wq_status_surface(string $key): string
{
    return design_token(DESIGN_WQ_STATUS_SURFACE, $key, 'wqStatusSurface');
}

function design_alert_inline(string $key): string
{
    return design_token(DESIGN_ALERT_INLINE, $key, 'alertInline');
}

function design_advice_panel_surface(string $key): string
{
    return design_token(DESIGN_ADVICE_PANEL_SURFACE, $key, 'advicePanelSurface');
}

function design_skeleton(string $key): string
{
    return design_token(DESIGN_SKELETON, $key, 'skeleton');
}

function design_page_layout(string $key): string
{
    return design_token(DESIGN_PAGE_LAYOUT, $key, 'pageLayout');
}

function design_status_tone(string $key): string
{
    return design_token(DESIGN_STATUS_TONE, $key, 'statusTone');
}

/**
 * Badge status aktif/nonaktif — gabung dengan `rounded-full px-2.5 py-1 text-xs font-medium`.
 */
function design_status_badge(string $tone = 'muted', ?string $className = null): string
{
    return design_cn(
        'rounded-full px-2.5 py-1 text-xs font-medium',
        design_status_tone($tone),
        $className,
    );
}

function design_icon_badge(string $tone = 'default', ?string $className = null): string
{
    $toneClass = DESIGN_ICON_TONE[$tone] ?? DESIGN_ICON_TONE['default'];

    return design_cn('rounded-xl p-2', $toneClass, $className);
}

function design_cn(?string ...$parts): string
{
    $merged = [];

    foreach ($parts as $part) {
        if ($part === null || $part === '') {
            continue;
        }

        foreach (preg_split('/\s+/', trim($part)) ?: [] as $class) {
            if ($class !== '') {
                $merged[$class] = true;
            }
        }
    }

    return implode(' ', array_keys($merged));
}

/**
 * @param array<string, string> $map
 */
function design_token(array $map, string $key, string $group): string
{
    if (!isset($map[$key])) {
        throw new InvalidArgumentException("Unknown design token {$group}.{$key}");
    }

    return $map[$key];
}
