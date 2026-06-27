<?php

declare(strict_types=1);

use App\Core\Csrf;

$staff = $staff ?? null;
$errors = $errors ?? [];
$action = $action ?? '';
?>
<div>
    <div class="mb-6">
        <a href="/admin/staff" class="text-sm text-gray-500 hover:text-slate-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Reset Password Staff</h1>
        <p class="text-sm text-gray-600 mt-1">
            Atur password baru untuk <strong><?= e((string) ($staff['nama'] ?? '')) ?></strong>
            (<?= e((string) ($staff['email'] ?? '')) ?>).
        </p>
    </div>

    <form method="POST" action="<?= e($action) ?>" class="bg-white rounded-xl border p-6 max-w-xl space-y-4">
        <?= Csrf::field() ?>
        <input type="hidden" name="id" value="<?= e((string) ($staff['id'] ?? '')) ?>">

        <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-red-600"><?= e((string) $errors['general']) ?></p>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
            <input type="password" name="password"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['password']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (!empty($errors['password'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['password']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['password_confirmation']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (!empty($errors['password_confirmation'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['password_confirmation']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            Reset Password
        </button>
    </form>
</div>
