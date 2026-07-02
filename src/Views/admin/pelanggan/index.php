<?php

declare(strict_types=1);

$pelangganList = $pelangganList ?? [];
$search = $search ?? '';

$segmentBadges = [
    'baru' => 'bg-blue-100 text-blue-800',
    'aktif' => 'bg-green-100 text-green-800',
    'dormant' => 'bg-gray-100 text-gray-600',
];
$segmentLabels = [
    'baru' => 'Baru',
    'aktif' => 'Aktif',
    'dormant' => 'Dormant',
];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Pelanggan</h1>
    </div>

    <p class="text-sm text-gray-600 mb-6">
        Lihat daftar pelanggan terdaftar beserta profil dan data kucing miliknya (read-only).
    </p>

    <form method="GET" action="/admin/pelanggan" class="mb-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
            <input type="text" id="q" name="q" value="<?= e($search) ?>"
                   placeholder="Nama, email, atau telepon"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">
            Cari
        </button>
    </form>

    <?php if ($search !== ''): ?>
        <?php
        ui_filter_chips(
            [['label' => 'Pencarian: ' . $search, 'removeHref' => '/admin/pelanggan']],
            '/admin/pelanggan',
            count($pelangganList),
            'pelanggan',
        );
        ?>
    <?php endif; ?>

    <?php if ($pelangganList === []): ?>
        <?php
        if ($search !== '') {
            $variant = 'filtered';
            $title = 'Tidak ditemukan pelanggan untuk pencarian ini';
            $description = 'Coba kata kunci lain atau reset filter.';
            $ctaLabel = 'Reset Filter';
            $ctaHref = '/admin/pelanggan';
        } else {
            $variant = 'empty';
            $title = 'Belum ada pelanggan terdaftar';
            $description = 'Pelanggan yang mendaftar akan muncul di halaman ini.';
            $ctaLabel = null;
            $ctaHref = null;
        }
        require __DIR__ . '/../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Segment</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Telepon</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Kucing</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Terdaftar</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($pelangganList as $pelanggan): ?>
                        <?php
                        $segment = (string) ($pelanggan['segment'] ?? 'aktif');
                        $badgeClass = $segmentBadges[$segment] ?? $segmentBadges['aktif'];
                        $segmentLabel = $segmentLabels[$segment] ?? ucfirst($segment);
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800"><?= e((string) $pelanggan['nama']) ?></td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full <?= e($badgeClass) ?>">
                                    <?= e($segmentLabel) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700"><?= e((string) $pelanggan['email']) ?></td>
                            <td class="px-4 py-3 text-gray-700"><?= e((string) ($pelanggan['no_telepon'] ?? '—')) ?></td>
                            <td class="px-4 py-3 text-gray-700"><?= (int) ($pelanggan['jumlah_kucing'] ?? 0) ?></td>
                            <td class="px-4 py-3 text-gray-600">
                                <?= e(date('d M Y', strtotime((string) $pelanggan['created_at']))) ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="/admin/pelanggan/detail?id=<?= e((string) $pelanggan['id']) ?>"
                                   class="text-slate-700 hover:underline">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
