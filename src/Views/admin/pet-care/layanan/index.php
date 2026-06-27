<?php

declare(strict_types=1);

use App\Core\Csrf;

$layananList = $layananList ?? [];
$statusLabels = $statusLabels ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Layanan Pet Care</h1>
        <a href="/admin/pet-care/layanan/tambah"
           class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            + Tambah Layanan
        </a>
    </div>

    <div class="flex gap-4 mb-6 text-sm">
        <a href="/admin/pet-care/layanan" class="text-slate-800 font-medium border-b-2 border-slate-800 pb-1">Layanan</a>
        <a href="/admin/pet-care/slot" class="text-gray-500 hover:text-slate-800">Slot Dokter</a>
        <a href="/admin/pet-care/booking" class="text-gray-500 hover:text-slate-800">Booking</a>
    </div>

    <?php if ($layananList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">
            Belum ada layanan pet care.
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Nama</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Harga</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Durasi</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($layananList as $layanan): ?>
                        <?php $deleted = !empty($layanan['deleted_at']); ?>
                        <tr class="<?= $deleted ? 'opacity-50' : '' ?>">
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
                                    <a href="/admin/pet-care/layanan/edit?id=<?= e((string) $layanan['id']) ?>"
                                       class="text-slate-700 hover:underline mr-3">Edit</a>
                                    <form method="POST" action="/admin/pet-care/layanan/hapus" class="inline"
                                          onsubmit="return confirm('Nonaktifkan/hapus layanan ini?')">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $layanan['id']) ?>">
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
