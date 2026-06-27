<?php

declare(strict_types=1);

use App\Core\Csrf;

$paketList = $paketList ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Paket Penitipan</h1>
        <a href="/admin/penitipan/paket/tambah" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm">+ Tambah</a>
    </div>
    <?php require __DIR__ . '/../_nav.php'; ?>
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left p-3">Nama</th>
                    <th class="text-left p-3">Harga/hari</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-right p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paketList as $p): ?>
                    <tr class="border-b">
                        <td class="p-3"><?= e((string) $p['nama']) ?></td>
                        <td class="p-3">Rp <?= e(number_format((float) $p['harga_per_hari'], 0, ',', '.')) ?></td>
                        <td class="p-3"><?= !empty($p['aktif']) ? 'Aktif' : 'Nonaktif' ?></td>
                        <td class="p-3 text-right space-x-2">
                            <a href="/admin/penitipan/paket/edit?id=<?= e((string) $p['id']) ?>" class="text-blue-600">Edit</a>
                            <form method="POST" action="/admin/penitipan/paket/hapus" class="inline" onsubmit="return confirm('Hapus paket?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $p['id']) ?>">
                                <button type="submit" class="text-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
