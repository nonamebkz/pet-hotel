<?php

declare(strict_types=1);

use App\Core\Csrf;

$staffList = $staffList ?? [];
$statusLabels = $statusLabels ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Akun Staff</h1>
        <a href="/admin/staff/tambah"
           class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            + Tambah Staff
        </a>
    </div>

    <p class="text-sm text-gray-600 mb-6">
        Kelola akun pegawai petshop. Hanya owner yang dapat menambah, mengedit, mereset password, dan mengaktifkan/menonaktifkan staff.
    </p>

    <?php if ($staffList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">
            Belum ada akun staff terdaftar.
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Username</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Dibuat</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($staffList as $staff): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800"><?= e((string) $staff['nama']) ?></td>
                            <td class="px-4 py-3 text-gray-700"><?= e((string) $staff['email']) ?></td>
                            <td class="px-4 py-3 text-gray-700"><?= e((string) ($staff['username'] ?? '—')) ?></td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full <?= ($staff['status'] ?? '') === 'AKTIF' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= e($statusLabels[$staff['status']] ?? (string) $staff['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?= e(date('d M Y', strtotime((string) $staff['created_at']))) ?>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="/admin/staff/edit?id=<?= e((string) $staff['id']) ?>"
                                   class="text-slate-700 hover:underline mr-3">Edit</a>
                                <a href="/admin/staff/reset-password?id=<?= e((string) $staff['id']) ?>"
                                   class="text-slate-700 hover:underline mr-3">Reset Password</a>
                                <?php $isActive = ($staff['status'] ?? '') === 'AKTIF'; ?>
                                <form method="POST" action="/admin/staff/status" class="inline"
                                      onsubmit="return confirm('<?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?> akun staff ini?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $staff['id']) ?>">
                                    <button type="submit"
                                            class="<?= $isActive ? 'text-red-600' : 'text-green-700' ?> hover:underline">
                                        <?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
