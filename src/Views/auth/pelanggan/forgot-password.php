<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<h2 class="text-xl font-semibold text-gray-800 mb-4">Lupa Password</h2>

<form method="POST" action="/forgot-password" class="space-y-4">
    <?= Csrf::field() ?>
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
    </div>
    <button type="submit" class="w-full bg-orange-600 text-white rounded-lg py-2 font-medium hover:bg-orange-700">
        Kirim Link Reset
    </button>
</form>

<?php if (!empty($reset_url)): ?>
    <div class="mt-4 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm">
        <p class="font-medium text-amber-800 mb-1">Link reset (mode dev):</p>
        <a href="<?= e($reset_url) ?>" class="text-orange-600 break-all hover:underline"><?= e($reset_url) ?></a>
    </div>
<?php endif; ?>

<p class="mt-4 text-center text-sm text-gray-600">
    <a href="/login" class="text-orange-600 hover:underline">Kembali ke login</a>
</p>
