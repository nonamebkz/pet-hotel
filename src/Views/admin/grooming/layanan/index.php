<?php

declare(strict_types=1);

$jenisList = $jenisList ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jenis Grooming</h1>
        <a href="/admin/grooming/layanan/tambah"
           class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">
            + Tambah Jenis
        </a>
    </div>

    <?php
    $activeTab = 'layanan';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if ($jenisList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada jenis grooming';
        $description = 'Tambahkan jenis layanan grooming agar pelanggan dapat melakukan booking.';
        $ctaLabel = 'Tambah Jenis';
        $ctaHref = '/admin/grooming/layanan/tambah';
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Harga</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600 w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($jenisList as $jenis): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800"><?= e((string) $jenis['nama']) ?></div>
                                <?php if (!empty($jenis['deskripsi'])): ?>
                                    <div class="text-xs text-gray-500 mt-0.5 line-clamp-1"><?= e((string) $jenis['deskripsi']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">Rp <?= e(number_format((float) $jenis['harga'], 0, ',', '.')) ?></td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full <?= !empty($jenis['aktif']) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= !empty($jenis['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <?php
                                $items = [
                                    [
                                        'type' => 'link',
                                        'label' => 'Edit',
                                        'href' => '/admin/grooming/layanan/edit?id=' . urlencode((string) $jenis['id']),
                                    ],
                                    [
                                        'type' => 'form',
                                        'label' => 'Hapus',
                                        'formAction' => '/admin/grooming/layanan/hapus',
                                        'formFields' => ['id' => (string) $jenis['id']],
                                        'confirm' => 'Hapus jenis grooming "' . (string) $jenis['nama'] . '"?',
                                        'class' => 'text-red-600',
                                    ],
                                ];
                                require __DIR__ . '/../../../partials/ui/action-menu.php';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
