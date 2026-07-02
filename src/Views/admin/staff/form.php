<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StaffRole;

$staff = $staff ?? null;
$errors = $errors ?? [];
$statusLabels = $statusLabels ?? [];
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$isEdit = $staff !== null && !empty($staff['id']);
$defaultStatus = $staff['status'] ?? old('status', 'NONAKTIF');
?>
<div>
    <div class="mb-6">
        <a href="/admin/staff" class="text-sm text-gray-500 hover:text-admin">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $isEdit ? 'Edit Staff' : 'Tambah Staff' ?></h1>
        <?php if (!$isEdit): ?>
            <p class="text-sm text-gray-500 mt-1">Akun baru default nonaktif — aktifkan setelah kredensial diserahkan ke staff.</p>
        <?php endif; ?>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm max-w-xs">
                    <option value="<?= e(StaffRole::STAFF->value) ?>" selected>Staff</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Hanya role Staff dapat dibuat dari halaman ini.</p>
                <?php if (!empty($errors['role'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['role']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-gray-700">Password Awal</label>
                    <button type="button"
                            data-generate-password="staff-password"
                            data-generate-password-confirm="staff-password-confirm"
                            class="text-xs text-slate-600 hover:underline">
                        Generate password
                    </button>
                </div>
                <div class="relative">
                    <input type="password" name="password" id="staff-password"
                           data-password-strength="staff-password-meter"
                           class="w-full border rounded-lg px-3 py-2 pr-24 text-sm <?= !empty($errors['password']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <button type="button" data-password-toggle="staff-password"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-gray-800 px-2">
                        Tampilkan
                    </button>
                </div>
                <div id="staff-password-meter" class="mt-2">
                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div data-strength-bar class="h-full rounded-full transition-all" style="width: 0%"></div>
                    </div>
                    <p data-strength-label class="text-xs text-gray-500 mt-1"></p>
                </div>
                <?php if (!empty($errors['password'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="staff-password-confirm"
                           data-password-match="staff-password"
                           class="w-full border rounded-lg px-3 py-2 pr-24 text-sm <?= !empty($errors['password_confirmation']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <button type="button" data-password-toggle="staff-password-confirm"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-gray-800 px-2">
                        Tampilkan
                    </button>
                </div>
                <p data-match-hint class="text-xs mt-1"></p>
                <?php if (!empty($errors['password_confirmation'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['password_confirmation']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm max-w-xs">
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $defaultStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Nonaktif = draft, staff belum bisa login.</p>
                <?php if (!empty($errors['status'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['status']) ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="text-sm text-gray-500">
                Untuk mengubah password, gunakan menu
                <a href="/admin/staff/reset-password?id=<?= e((string) ($staff['id'] ?? '')) ?>" class="text-slate-700 underline">Reset Password</a>.
                Untuk mengaktifkan/menonaktifkan akun, gunakan menu aksi di halaman daftar staff.
            </p>
        <?php endif; ?>

        <button type="submit" class="bg-admin text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-admin-hover">
            <?= e($submitLabel) ?>
        </button>
    </form>
</div>
