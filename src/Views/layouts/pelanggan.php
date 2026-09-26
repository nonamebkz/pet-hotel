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
<body class="min-h-screen bg-background font-body text-foreground antialiased">
    <?php require __DIR__ . '/../partials/nav/pelanggan-nav.php'; ?>
    <main id="main-content" class="<?= e(ui_layout_main_classes()) ?>">
        <?php require __DIR__ . '/../partials/flash.php'; ?>
        <?= $content ?? '' ?>
    </main>
    <?php require __DIR__ . '/../partials/nav/pelanggan-bottom-nav.php'; ?>
    <?php require __DIR__ . '/../partials/ui/confirm-modal.php'; ?>
    <script src="/js/nav.js" defer></script>
    <script src="/js/ui.js" defer></script>
</body>
</html>
