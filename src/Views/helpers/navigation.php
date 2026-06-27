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

function nav_link_classes(bool $active, bool $inline = true): string
{
    $base = 'text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 rounded-sm';

    if ($inline) {
        $base .= ' inline-flex items-center min-h-10 px-1';
    } else {
        $base .= ' flex items-center min-h-10 px-3 py-2 rounded-lg w-full';
    }

    if ($active) {
        return $base . ' text-content-primary font-semibold border-b-2 border-primary pb-0.5';
    }

    return $base . ' text-content-secondary hover:text-primary border-b-2 border-transparent pb-0.5';
}

function nav_dropdown_trigger_classes(bool $active): string
{
    $base = 'inline-flex items-center gap-1 min-h-10 px-2 text-sm font-medium transition-colors rounded-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2';

    if ($active) {
        return $base . ' text-primary';
    }

    return $base . ' text-content-secondary hover:text-primary';
}

function nav_mobile_link_classes(bool $active): string
{
    $base = 'flex items-center min-h-10 px-3 py-2 text-sm rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary';

    if ($active) {
        return $base . ' text-primary font-semibold bg-orange-50';
    }

    return $base . ' text-content-secondary hover:text-primary hover:bg-gray-50';
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
