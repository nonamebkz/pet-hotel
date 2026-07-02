#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Membuat semua direktori upload sesuai FileUploadService::categories().
 * Dipanggil dari scripts/docker-up.sh dan docker/entrypoint.sh.
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$uploadRoot = BASE_PATH . '/public/uploads';

if (!is_dir($uploadRoot) && !mkdir($uploadRoot, 0775, true) && !is_dir($uploadRoot)) {
    fwrite(STDERR, "Gagal membuat direktori: {$uploadRoot}\n");
    exit(1);
}

foreach (\App\Services\FileUploadService::categories() as $category) {
    $dir = $uploadRoot . '/' . $category;

    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fwrite(STDERR, "Gagal membuat direktori: {$dir}\n");
        exit(1);
    }

    $gitkeep = $dir . '/.gitkeep';

    if (!is_file($gitkeep)) {
        touch($gitkeep);
    }
}

$logRoot = BASE_PATH . '/storage/logs';

if (!is_dir($logRoot) && !mkdir($logRoot, 0775, true) && !is_dir($logRoot)) {
    fwrite(STDERR, "Gagal membuat direktori: {$logRoot}\n");
    exit(1);
}

echo 'Upload directories OK: ' . implode(', ', \App\Services\FileUploadService::categories()) . PHP_EOL;
