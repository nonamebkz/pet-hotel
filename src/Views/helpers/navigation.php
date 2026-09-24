<?php

declare(strict_types=1);

function nav_current_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    return rtrim($uri, '/') ?: '/';
}

/**
 * @param string|list<string> $paths
 */
function nav_is_active(string|array $paths, bool $prefixMatch = false): bool
{
    $current = nav_current_path();
    $paths = is_array($paths) ? $paths : [$paths];

    foreach ($paths as $path) {
        $normalized = rtrim($path, '/') ?: '/';

        if ($prefixMatch) {
            if ($current === $normalized || str_starts_with($current, $normalized . '/')) {
                return true;
            }

            continue;
        }

        if ($current === $normalized) {
            return true;
        }
    }

    return false;
}

function nav_link_classes(bool $active, bool $inline = true, bool $adminTheme = false): string
{
    $accentText = 'text-primary';
    $accentBg = 'bg-primary/10';
    $accentHover = 'hover:text-primary hover:bg-muted';
    $accentRing = 'focus-visible:ring-ring';

    $base = 'cursor-pointer text-sm font-medium rounded-xl transition focus-visible:outline-none focus-visible:ring-2 '
        . $accentRing . ' focus-visible:ring-offset-2';

    if ($inline) {
        $base .= ' inline-flex items-center min-h-10 px-3';
    } else {
        $base .= ' flex items-center min-h-10 px-3 py-2 w-full';
    }

    if ($active) {
        return $base . ' ' . $accentText . ' ' . $accentBg . ' font-semibold';
    }

    return $base . ' text-muted-foreground ' . $accentHover;
}

function nav_dropdown_trigger_classes(bool $active, bool $adminTheme = false): string
{
    return nav_link_classes($active, true, $adminTheme);
}

function nav_dropdown_panel_classes(string $align = 'left'): string
{
    $position = $align === 'right' ? 'right-0' : 'left-0';

    return 'hidden absolute ' . $position . ' top-full mt-2 min-w-[14rem] bg-card/95 backdrop-blur-md border border-border rounded-2xl shadow-md py-2 z-50';
}

function nav_dropdown_link_classes(bool $active, bool $adminTheme = false): string
{
    $base = 'cursor-pointer flex items-center mx-2 px-3 py-2.5 text-sm rounded-xl transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-ring';

    if ($active) {
        return $base . ' text-primary font-semibold bg-primary/10';
    }

    return $base . ' text-foreground hover:bg-muted hover:text-primary';
}

function nav_mobile_link_classes(bool $active, bool $adminTheme = false): string
{
    $base = 'cursor-pointer flex items-center min-h-11 px-3.5 py-2.5 text-sm rounded-xl transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-ring';

    if ($active) {
        return $base . ' text-primary font-semibold bg-primary/10';
    }

    return $base . ' text-muted-foreground hover:text-primary hover:bg-muted';
}

function nav_avatar_classes(bool $adminTheme = false): string
{
    return 'inline-flex items-center justify-center w-8 h-8 rounded-xl bg-primary text-primary-foreground text-xs font-semibold shrink-0 shadow-sm';
}

function nav_badge_classes(bool $adminTheme = false): string
{
    return 'inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-lg bg-primary text-primary-foreground text-[10px] font-bold leading-none';
}

function nav_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];

    if ($parts === []) {
        return '?';
    }

    if (count($parts) === 1) {
        return strtoupper(substr($parts[0], 0, 1));
    }

    return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
}
