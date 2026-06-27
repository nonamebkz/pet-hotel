<?php

declare(strict_types=1);

use App\Core\Csrf;

$errors = $errors ?? [];
?>
<h2 class="text-xl font-semibold text-gray-800 mb-4">Daftar Akun</h2>

<form method="POST" action="/register" class="space-y-4">
    <?= Csrf::field() ?>
    <div>
        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" value="<?= e((string) old('nama')) ?>" required
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
        <?php if (!empty($errors['nama'])): ?>
            <p class="text-red-600 text-xs mt-1"><?= e($errors['nama']) ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
        <?php if (!empty($errors['email'])): ?>
            <p class="text-red-600 text-xs mt-1"><?= e($errors['email']) ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" name="password" required minlength="8"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
        <?php if (!empty($errors['password'])): ?>
            <p class="text-red-600 text-xs mt-1"><?= e($errors['password']) ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
        <?php if (!empty($errors['password_confirmation'])): ?>
            <p class="text-red-600 text-xs mt-1"><?= e($errors['password_confirmation']) ?></p>
        <?php endif; ?>
    </div>
    <button type="submit" class="w-full bg-orange-600 text-white rounded-lg py-2 font-medium hover:bg-orange-700">
        Daftar
    </button>
</form>

<p class="mt-4 text-center text-sm text-gray-600">
    Sudah punya akun? <a href="/login" class="text-orange-600 hover:underline">Login</a>
</p>
