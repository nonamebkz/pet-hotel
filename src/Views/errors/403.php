<?php

declare(strict_types=1);

?>
<div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">403</h1>
        <p class="text-gray-600"><?= e($message ?? 'Akses ditolak.') ?></p>
        <a href="/" class="inline-block mt-4 text-orange-600 hover:underline">Kembali ke beranda</a>
    </div>
</div>
