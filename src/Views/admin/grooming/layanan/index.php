<?php

declare(strict_types=1);

use App\Core\Csrf;

$jenisList = $jenisList ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jenis Grooming</h1>
        <a href="/admin/grooming/layanan/tambah"
           class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            + Tambah Jenis
        </a>
    </div>

    <div class="flex gap-4 mb-6 text-sm">
        <a href="/admin/grooming/layanan" class="text-slate-800 font-medium border-b-2 border-slate-800 pb-1">Jenis</a>
        <a href="/admin/grooming/kuota" class="text-gray-500 hover:text-slate-800">Kuota</a>
        <a href="/admin/grooming/booking" class="text-gray-500 hover:text-slate-800">Booking</a>
        <a href="/admin/grooming/pembayaran" class="text-gray-500 hover:text-slate-800">Verifikasi Bukti</a>
    </div>

    <?php if ($jenisList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">Belum ada jenis grooming.</div>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Harga</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($jenisList as $jenis): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800"><?= e((string) $jenis['nama']) ?></div>
                                <?php if (!empty($jenis['deskripsi'])): ?>
                                    <div class="text-xs text-gray-500 mt-0.5"><?= e((string) $jenis['deskripsi']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">Rp <?= e(number_format((float) $jenis['harga'], 0, ',', '.')) ?></td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full <?= !empty($jenis['aktif']) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= !empty($jenis['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="/admin/grooming/layanan/edit?id=<?= e((string) $jenis['id']) ?>"
                                   class="text-slate-700 hover:underline mr-3">Edit</a>
                                <form method="POST" action="/admin/grooming/layanan/hapus" class="inline"
                                      onsubmit="return confirm('Hapus jenis grooming ini?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $jenis['id']) ?>">
                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
