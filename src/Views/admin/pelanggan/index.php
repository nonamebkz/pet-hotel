<?php

declare(strict_types=1);

$pelangganList = $pelangganList ?? [];
$search = $search ?? '';
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Pelanggan</h1>
    </div>

    <p class="text-sm text-gray-600 mb-6">
        Lihat daftar pelanggan terdaftar beserta profil dan data kucing miliknya (read-only).
    </p>

    <form method="GET" action="/admin/pelanggan" class="mb-6 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
            <input type="text" id="q" name="q" value="<?= e($search) ?>"
                   placeholder="Nama, email, atau telepon"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            Cari
        </button>
        <?php if ($search !== ''): ?>
            <a href="/admin/pelanggan" class="text-sm text-gray-600 hover:underline py-2">Reset</a>
        <?php endif; ?>
    </form>

    <?php if ($pelangganList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">
            <?= $search !== ''
                ? 'Tidak ada pelanggan yang cocok dengan pencarian.'
                : 'Belum ada pelanggan terdaftar.' ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Telepon</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Jumlah Kucing</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Terdaftar</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($pelangganList as $pelanggan): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800"><?= e((string) $pelanggan['nama']) ?></td>
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
