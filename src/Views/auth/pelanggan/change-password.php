<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<h1 class="text-2xl font-bold text-gray-800 mb-2">Ubah Password</h1>

<?php if (!empty($error)): ?>
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/change-password" class="max-w-md space-y-4 bg-white rounded-xl border p-6">
    <?= Csrf::field() ?>
    <div>
        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
        <input type="password" id="current_password" name="current_password" required
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
        <input type="password" id="password" name="password" required minlength="8"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
    <button type="submit" class="bg-orange-600 text-white rounded-lg px-4 py-2 font-medium hover:bg-orange-700">
        Simpan Password
    </button>
</form>
