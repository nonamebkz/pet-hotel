<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> — Petshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="/dashboard" class="font-bold text-orange-600">Petshop</a>
                <a href="/dashboard" class="text-sm text-gray-600 hover:text-orange-600">Dashboard</a>
                <a href="/profil" class="text-sm text-gray-600 hover:text-orange-600">Profil</a>
                <a href="/kucing" class="text-sm text-gray-600 hover:text-orange-600">Kucing Saya</a>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600"><?= e((string) ($nama ?? '')) ?></span>
                <a href="/change-password" class="text-sm text-gray-600 hover:text-orange-600">Ubah Password</a>
                <form method="POST" action="/logout" class="inline">
                    <?= Csrf::field() ?>
                    <button type="submit" class="text-sm text-red-600 hover:text-red-700">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="max-w-6xl mx-auto px-4 py-8">
        <?php require __DIR__ . '/../partials/flash.php'; ?>
        <?= $content ?? '' ?>
    </main>
</body>
</html>
