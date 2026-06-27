<?php

declare(strict_types=1);

use App\Core\Session;
use App\Repositories\NotifikasiRepository;

$navUnreadCount = $unreadNotificationCount ?? null;

if ($navUnreadCount === null && Session::get('auth.pelanggan_id')) {
    $navUnreadCount = (new NotifikasiRepository())->countUnreadByPelanggan(
        (string) Session::get('auth.pelanggan_id'),
    );
}

$navUnreadCount = (int) ($navUnreadCount ?? 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> — Petshop</title>
    <?php require __DIR__ . '/../partials/head/tailwind-config.php'; ?>
</head>
<body class="min-h-screen bg-page">
    <?php require __DIR__ . '/../partials/nav/pelanggan-nav.php'; ?>
    <main class="max-w-7xl mx-auto px-4 py-8">
        <?php require __DIR__ . '/../partials/flash.php'; ?>
        <?= $content ?? '' ?>
    </main>
    <script src="/js/nav.js" defer></script>
</body>
</html>
