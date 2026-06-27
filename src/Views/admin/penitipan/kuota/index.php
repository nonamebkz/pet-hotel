<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuotaList = $kuotaList ?? [];
$kamarList = $kamarList ?? [];
$filterKamarId = $filterKamarId ?? '';
?>
<div>
    <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kuota Penitipan</h1>
        <a href="/admin/penitipan/kuota/tambah" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm">+ Tambah</a>
    </div>
    <?php require __DIR__ . '/../_nav.php'; ?>
    <form method="GET" class="mb-4 flex gap-2 items-end">
        <div><label class="text-sm">Kamar</label>
            <select name="kamar_id" class="border rounded-lg px-3 py-2 text-sm block mt-1">
                <option value="">Semua</option>
                <?php foreach ($kamarList as $k): ?>
                    <option value="<?= e((string) $k['id']) ?>" <?= $filterKamarId === (string) $k['id'] ? 'selected' : '' ?>><?= e((string) $k['nama_kamar']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 border rounded-lg px-3 py-2 text-sm">Filter</button>
    </form>
    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b"><tr><th class="p-3 text-left">Kamar</th><th class="p-3 text-left">Tanggal</th><th class="p-3 text-left">Slot</th><th class="p-3 text-right">Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($kuotaList as $q): ?>
                    <tr class="border-b">
                        <td class="p-3"><?= e((string) $q['nama_kamar']) ?></td>
                        <td class="p-3"><?= e(date('d/m/Y', strtotime((string) $q['tanggal']))) ?></td>
                        <td class="p-3"><?= (int) $q['slot_terisi'] ?> / <?= (int) $q['slot_maksimal'] ?></td>
                        <td class="p-3 text-right">
                            <a href="/admin/penitipan/kuota/edit?id=<?= e((string) $q['id']) ?>" class="text-blue-600">Edit</a>
                            <form method="POST" action="/admin/penitipan/kuota/hapus" class="inline" onsubmit="return confirm('Hapus?')">
                                <?= Csrf::field() ?><input type="hidden" name="id" value="<?= e((string) $q['id']) ?>">
                                <button type="submit" class="text-red-600 ml-2">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
