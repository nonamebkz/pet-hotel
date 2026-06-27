<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Petshop') ?> — Petshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-orange-50 to-amber-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-orange-600">Petshop</h1>
            <p class="text-gray-600 text-sm mt-1">Portal Pelanggan</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <?php require __DIR__ . '/../partials/flash.php'; ?>
            <?= $content ?? '' ?>
        </div>
    </div>
</body>
</html>
