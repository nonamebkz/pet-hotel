#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Memastikan semua pemanggilan FileUploadService::upload() memakai kategori terdaftar.
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$registered = array_fill_keys(\App\Services\FileUploadService::categories(), true);
$used = [];
$srcRoot = BASE_PATH . '/src';

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($srcRoot, FilesystemIterator::SKIP_DOTS),
);

foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $content = file_get_contents($file->getPathname());

    if ($content === false) {
        continue;
    }

    if (preg_match_all("/->upload\\([^,]+,\\s*'([a-z_]+)'\\)/", $content, $matches)) {
        foreach ($matches[1] as $category) {
            $used[$category] = ($used[$category] ?? 0) + 1;
        }
    }
}

$exitCode = 0;

foreach ($used as $category => $count) {
    if (!isset($registered[$category])) {
        fwrite(STDERR, "ERROR: kategori '{$category}' dipakai {$count}x tapi tidak terdaftar di FileUploadService\n");
        $exitCode = 1;
    }
}

foreach (array_keys($registered) as $category) {
    if (!isset($used[$category])) {
        fwrite(STDERR, "WARN: kategori '{$category}' terdaftar tapi belum dipakai di src/\n");
    }
}

if ($exitCode === 0) {
    echo 'Audit upload OK (' . count($used) . ' kategori dipakai, ' . count($registered) . ' terdaftar)' . PHP_EOL;
}

exit($exitCode);
