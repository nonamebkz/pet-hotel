<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<h2 class="text-xl font-semibold text-gray-800 mb-4">Login Pelanggan</h2>

<?php if (!empty($error)): ?>
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/login" class="space-y-4">
    <?= Csrf::field() ?>
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" id="password" name="password" required
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
    <button type="submit" class="w-full bg-orange-600 text-white rounded-lg py-2 font-medium hover:bg-orange-700">
        Login
    </button>
</form>

<p class="mt-4 text-center text-sm text-gray-600">
    <a href="/forgot-password" class="text-orange-600 hover:underline">Lupa password?</a>
</p>
<p class="mt-2 text-center text-sm text-gray-600">
    Belum punya akun? <a href="/register" class="text-orange-600 hover:underline">Daftar</a>
</p>
