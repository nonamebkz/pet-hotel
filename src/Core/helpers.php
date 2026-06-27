<?php

declare(strict_types=1);

function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key);

    if ($value === false || $value === null) {
        return $default;
    }

    return match (strtolower((string) $value)) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'null', '(null)' => null,
        default => $value,
    };
}

function config(string $file): array
{
    static $cache = [];

    if (!isset($cache[$file])) {
        $cache[$file] = require BASE_PATH . '/config/' . $file . '.php';
    }

    return $cache[$file];
}

function uuid(): string
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function url(string $path = ''): string
{
    $base = config('app')['url'];

    if ($path === '') {
        return $base;
    }

    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function old(string $key, mixed $default = ''): mixed
{
    return \App\Core\Session::getFlash('_old.' . $key, $default);
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
