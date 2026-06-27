<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Petshop') ?> — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-white">Petshop Admin</h1>
            <p class="text-slate-400 text-sm mt-1">Portal Staff & Owner</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <?php require __DIR__ . '/../partials/flash.php'; ?>
            <?= $content ?? '' ?>
        </div>
    </div>
</body>
</html>
