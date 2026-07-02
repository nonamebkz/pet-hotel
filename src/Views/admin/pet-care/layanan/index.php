<?php

declare(strict_types=1);

$layananList = $layananList ?? [];
$statusLabels = $statusLabels ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Layanan Pet Care</h1>
        <a href="/admin/pet-care/layanan/tambah"
           class="bg-admin text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-admin-hover">
            + Tambah Layanan
        </a>
    </div>

    <div class="flex gap-4 mb-6 text-sm border-b border-gray-200">
        <a href="/admin/pet-care/layanan" class="text-admin font-medium border-b-2 border-admin pb-2 -mb-px">Layanan</a>
        <a href="/admin/pet-care/slot" class="text-gray-500 hover:text-admin pb-2">Slot Dokter</a>
        <a href="/admin/pet-care/booking" class="text-gray-500 hover:text-admin pb-2">Booking</a>
    </div>

    <?php if ($layananList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada layanan pet care';
        $description = 'Tambahkan layanan pet care agar pelanggan dapat melakukan booking konsultasi.';
        $ctaLabel = 'Tambah Layanan';
        $ctaHref = '/admin/pet-care/layanan/tambah';
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Harga</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Durasi</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600 w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($layananList as $layanan): ?>
                        <?php $deleted = !empty($layanan['deleted_at']); ?>
                        <tr class="<?= $deleted ? 'opacity-50' : 'hover:bg-gray-50' ?>">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800"><?= e((string) $layanan['nama']) ?></div>
                                <?php if (!empty($layanan['deskripsi'])): ?>
                                    <div class="text-xs text-gray-500 mt-0.5 line-clamp-1"><?= e((string) $layanan['deskripsi']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">Rp <?= e(number_format((float) $layanan['harga'], 0, ',', '.')) ?></td>
                            <td class="px-4 py-3"><?= (int) $layanan['estimasi_durasi_menit'] ?> menit</td>
                            <td class="px-4 py-3">
                                <?php if ($deleted): ?>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Dihapus</span>
                                <?php else: ?>
                                    <span class="text-xs px-2 py-0.5 rounded-full <?= ($layanan['status'] ?? '') === 'AKTIF' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                        <?= e($statusLabels[$layanan['status']] ?? (string) $layanan['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <?php if (!$deleted): ?>
                                    <?php
                                    $items = [
                                        [
                                            'type' => 'link',
                                            'label' => 'Edit',
                                            'href' => '/admin/pet-care/layanan/edit?id=' . urlencode((string) $layanan['id']),
                                        ],
                                        [
                                            'type' => 'form',
                                            'label' => 'Hapus',
                                            'formAction' => '/admin/pet-care/layanan/hapus',
                                            'formFields' => ['id' => (string) $layanan['id']],
                                            'confirm' => 'Nonaktifkan/hapus layanan "' . (string) $layanan['nama'] . '"?',
                                            'class' => 'text-red-600',
                                        ],
                                    ];
                                    require __DIR__ . '/../../../partials/ui/action-menu.php';
                                    ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
