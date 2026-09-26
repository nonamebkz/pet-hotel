<?php

declare(strict_types=1);

use App\Core\Session;
use App\Repositories\NotifikasiRepository;

$navUnreadCount = $unreadNotificationCount ?? null;

if ($navUnreadCount === null && Session::get('auth.staff_id')) {
    $navUnreadCount = (new NotifikasiRepository())->countUnreadByStaff(
        (string) Session::get('auth.staff_id'),
    );
}

$navUnreadCount = (int) ($navUnreadCount ?? 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> — Admin Petshop</title>
    <?php require __DIR__ . '/../partials/head/tailwind-config.php'; ?>
</head>
<body class="min-h-screen bg-background font-body text-foreground antialiased lg:flex">
    <?php require __DIR__ . '/../partials/nav/admin-nav.php'; ?>
    <main id="main-content" class="<?= e(ui_layout_main_classes()) ?> flex-1 print:max-w-none print:px-0 lg:pb-8">
        <div class="print:hidden">
            <?php require __DIR__ . '/../partials/flash.php'; ?>
        </div>
        <?= $content ?? '' ?>
    </main>
    </div>
    <?php require __DIR__ . '/../partials/ui/confirm-modal.php'; ?>
    <script src="/js/nav.js" defer></script>
    <script src="/js/ui.js" defer></script>
</body>
</html>
