<?php

declare(strict_types=1);

use App\Core\Csrf;

$kamarList = $kamarList ?? [];
?>
<div>
    <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kamar Penitipan</h1>
        <a href="/admin/penitipan/kamar/tambah" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm">+ Tambah</a>
    </div>
    <?php require __DIR__ . '/../_nav.php'; ?>
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b"><tr><th class="p-3 text-left">Nama</th><th class="p-3 text-left">Kapasitas</th><th class="p-3 text-left">Status</th><th class="p-3 text-right">Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($kamarList as $k): ?>
                    <tr class="border-b">
                        <td class="p-3"><?= e((string) $k['nama_kamar']) ?></td>
                        <td class="p-3"><?= (int) $k['kapasitas'] ?></td>
                        <td class="p-3"><?= !empty($k['aktif']) ? 'Aktif' : 'Nonaktif' ?></td>
                        <td class="p-3 text-right">
                            <a href="/admin/penitipan/kamar/edit?id=<?= e((string) $k['id']) ?>" class="text-blue-600">Edit</a>
                            <form method="POST" action="/admin/penitipan/kamar/hapus" class="inline" onsubmit="return confirm('Hapus?')">
                                <?= Csrf::field() ?><input type="hidden" name="id" value="<?= e((string) $k['id']) ?>">
                                <button type="submit" class="text-red-600 ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
