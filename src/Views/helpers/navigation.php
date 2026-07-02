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
    $accentText = $adminTheme ? 'text-admin' : 'text-primary';
    $accentHover = $adminTheme ? 'hover:text-admin' : 'hover:text-primary';
    $accentBorder = $adminTheme ? 'border-admin' : 'border-primary';
    $accentRing = $adminTheme ? 'focus-visible:ring-admin' : 'focus-visible:ring-primary';

    $base = 'text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 ' . $accentRing . ' focus-visible:ring-offset-2 rounded-sm';

    if ($inline) {
        $base .= ' inline-flex items-center min-h-10 px-1';
    } else {
        $base .= ' flex items-center min-h-10 px-3 py-2 rounded-lg w-full';
    }

    if ($active) {
        return $base . ' ' . $accentText . ' font-semibold border-b-2 ' . $accentBorder . ' pb-0.5';
    }

    return $base . ' text-content-secondary ' . $accentHover . ' border-b-2 border-transparent pb-0.5';
}

function nav_dropdown_trigger_classes(bool $active, bool $adminTheme = false): string
{
    $accentText = $adminTheme ? 'text-admin' : 'text-primary';
    $accentHover = $adminTheme ? 'hover:text-admin' : 'hover:text-primary';
    $accentBg = $adminTheme ? 'bg-admin-soft' : 'bg-primary-soft';
    $accentBgHover = $adminTheme ? 'hover:bg-admin-soft/80' : 'hover:bg-primary-soft/50';
    $accentRing = $adminTheme ? 'focus-visible:ring-admin' : 'focus-visible:ring-primary';

    $base = 'inline-flex items-center gap-1 min-h-10 px-2.5 text-sm font-medium transition-colors rounded-lg focus-visible:outline-none focus-visible:ring-2 ' . $accentRing . ' focus-visible:ring-offset-2';

    if ($active) {
        return $base . ' ' . $accentText . ' ' . $accentBg;
    }

    return $base . ' text-content-secondary ' . $accentHover . ' ' . $accentBgHover;
}

function nav_dropdown_panel_classes(string $align = 'left'): string
{
    $position = $align === 'right' ? 'right-0' : 'left-0';

    return 'hidden absolute ' . $position . ' top-full mt-2 min-w-[13rem] bg-card border border-border rounded-xl shadow-dropdown py-2 z-50 ring-1 ring-black/5';
}

function nav_dropdown_link_classes(bool $active, bool $adminTheme = false): string
{
    $base = 'flex items-center mx-2 px-3 py-2.5 text-sm rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset';
    $accentText = $adminTheme ? 'text-admin' : 'text-primary';
    $accentHover = $adminTheme ? 'hover:text-admin' : 'hover:text-primary';
    $accentBg = $adminTheme ? 'bg-admin-soft' : 'bg-primary-soft';
    $accentBgHover = $adminTheme ? 'hover:bg-admin-soft/80' : 'hover:bg-primary-soft/70';
    $accentRing = $adminTheme ? 'focus-visible:ring-admin' : 'focus-visible:ring-primary';

    $base .= ' ' . $accentRing;

    if ($active) {
        return $base . ' ' . $accentText . ' font-semibold ' . $accentBg;
    }

    return $base . ' text-content-primary ' . $accentBgHover . ' ' . $accentHover;
}

function nav_mobile_link_classes(bool $active, bool $adminTheme = false): string
{
    $base = 'flex items-center min-h-10 px-3 py-2 text-sm rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset';
    $accentText = $adminTheme ? 'text-admin' : 'text-primary';
    $accentHover = $adminTheme ? 'hover:text-admin' : 'hover:text-primary';
    $accentBg = $adminTheme ? 'bg-admin-soft' : 'bg-primary-soft';
    $accentBgHover = $adminTheme ? 'hover:bg-admin-soft/80' : 'hover:bg-primary-soft/50';
    $accentRing = $adminTheme ? 'focus-visible:ring-admin' : 'focus-visible:ring-primary';

    $base .= ' ' . $accentRing;

    if ($active) {
        return $base . ' ' . $accentText . ' font-semibold ' . $accentBg;
    }

    return $base . ' text-content-secondary ' . $accentHover . ' ' . $accentBgHover;
}

function nav_avatar_classes(bool $adminTheme = false): string
{
    if ($adminTheme) {
        return 'inline-flex items-center justify-center w-8 h-8 rounded-full bg-admin-soft text-admin text-xs font-semibold shrink-0';
    }

    return 'inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-soft text-primary text-xs font-semibold shrink-0';
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
