<?php

declare(strict_types=1);

use App\Core\Csrf;

$staff = $staff ?? null;
$errors = $errors ?? [];
$statusLabels = $statusLabels ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$isEdit = $staff !== null && !empty($staff['id']);
?>
<div>
    <div class="mb-6">
        <a href="/admin/staff" class="text-sm text-gray-500 hover:text-slate-800">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $isEdit ? 'Edit Staff' : 'Tambah Staff' ?></h1>
    </div>

    <form method="POST" action="<?= e($action) ?>" class="bg-white rounded-xl border p-6 max-w-xl space-y-4">
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) ($staff['id'] ?? '')) ?>">
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-red-600"><?= e((string) $errors['general']) ?></p>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="nama" value="<?= e((string) ($staff['nama'] ?? old('nama', ''))) ?>"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['nama']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (!empty($errors['nama'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['nama']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="<?= e((string) ($staff['email'] ?? old('email', ''))) ?>"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['email']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (!empty($errors['email'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" name="username" value="<?= e((string) ($staff['username'] ?? old('username', ''))) ?>"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['username']) ? 'border-red-400' : 'border-gray-300' ?>"
                   placeholder="Opsional — untuk login alternatif">
            <?php if (!empty($errors['username'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['username']) ?></p>
            <?php endif; ?>
        </div>

        <?php if (!$isEdit): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Awal</label>
                <input type="password" name="password"
                       class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['password']) ? 'border-red-400' : 'border-gray-300' ?>">
                <?php if (!empty($errors['password'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['password_confirmation']) ? 'border-red-400' : 'border-gray-300' ?>">
                <?php if (!empty($errors['password_confirmation'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['password_confirmation']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= (($staff['status'] ?? old('status', 'AKTIF')) === $value) ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['status'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['status']) ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-gray-500">
                Untuk mengubah password, gunakan menu
                <a href="/admin/staff/reset-password?id=<?= e((string) ($staff['id'] ?? '')) ?>" class="text-slate-700 underline">Reset Password</a>.
                Untuk mengaktifkan/menonaktifkan akun, gunakan tombol di halaman daftar staff.
            </p>
        <?php endif; ?>

        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            <?= e($submitLabel) ?>
        </button>
    </form>
</div>
