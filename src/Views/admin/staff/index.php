<?php

declare(strict_types=1);

$staffList = $staffList ?? [];
$statusLabels = $statusLabels ?? [];

$roleLabels = [
    'STAFF' => 'Staff',
    'OWNER' => 'Owner',
];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Akun Staff</h1>
        <a href="/admin/staff/tambah"
           class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">
            + Tambah Staff
        </a>
    </div>

    <p class="text-sm text-gray-600 mb-6">
        Kelola akun pegawai petshop. Hanya owner yang dapat menambah, mengedit, mereset password, dan mengaktifkan/menonaktifkan staff.
    </p>

    <?php if ($staffList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada akun staff terdaftar';
        $description = 'Tambahkan akun staff agar tim dapat mengakses dashboard operasional.';
        $ctaLabel = 'Tambah Staff';
        $ctaHref = '/admin/staff/tambah';
        require __DIR__ . '/../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Username</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Role</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Dibuat</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600 w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($staffList as $staff): ?>
                        <?php $isActive = ($staff['status'] ?? '') === 'AKTIF'; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800"><?= e((string) $staff['nama']) ?></td>
                            <td class="px-4 py-3 text-gray-700"><?= e((string) $staff['email']) ?></td>
                            <td class="px-4 py-3 text-gray-700"><?= e((string) ($staff['username'] ?? '—')) ?></td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-800">
                                    <?= e($roleLabels[$staff['role'] ?? ''] ?? (string) ($staff['role'] ?? '—')) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full <?= $isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= e($statusLabels[$staff['status']] ?? (string) $staff['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                <?= e(date('d M Y', strtotime((string) $staff['created_at']))) ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <?php
                                $items = [
                                    [
                                        'type' => 'link',
                                        'label' => 'Edit',
                                        'href' => '/admin/staff/edit?id=' . urlencode((string) $staff['id']),
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Reset Password',
                                        'href' => '/admin/staff/reset-password?id=' . urlencode((string) $staff['id']),
                                    ],
                                    [
                                        'type' => 'form',
                                        'label' => $isActive ? 'Nonaktifkan' : 'Aktifkan',
                                        'formAction' => '/admin/staff/status',
                                        'formFields' => ['id' => (string) $staff['id']],
                                        'confirm' => ($isActive ? 'Nonaktifkan' : 'Aktifkan') . ' akun staff "' . (string) $staff['nama'] . '"?',
                                        'class' => $isActive ? 'text-red-600' : 'text-green-700',
                                    ],
                                ];
                                require __DIR__ . '/../../partials/ui/action-menu.php';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
