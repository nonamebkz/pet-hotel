<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuotaList = $kuotaList ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Kuota Grooming</h1>
        <a href="/admin/grooming/kuota/tambah"
           class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            + Tambah Kuota
        </a>
    </div>

    <div class="flex gap-4 mb-6 text-sm">
        <a href="/admin/grooming/layanan" class="text-gray-500 hover:text-slate-800">Jenis</a>
        <a href="/admin/grooming/kuota" class="text-slate-800 font-medium border-b-2 border-slate-800 pb-1">Kuota</a>
        <a href="/admin/grooming/booking" class="text-gray-500 hover:text-slate-800">Booking</a>
        <a href="/admin/grooming/pembayaran" class="text-gray-500 hover:text-slate-800">Verifikasi Bukti</a>
    </div>

    <?php if ($kuotaList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">Belum ada kuota grooming.</div>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Slot Maksimal</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Terisi</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Sisa</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($kuotaList as $kuota): ?>
                        <tr>
                            <td class="px-4 py-3"><?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?></td>
                            <td class="px-4 py-3"><?= (int) $kuota['slot_maksimal'] ?></td>
                            <td class="px-4 py-3"><?= (int) $kuota['slot_terisi'] ?></td>
                            <td class="px-4 py-3"><?= (int) $kuota['slot_maksimal'] - (int) $kuota['slot_terisi'] ?></td>
                            <td class="px-4 py-3 text-right">
                                <a href="/admin/grooming/kuota/edit?id=<?= e((string) $kuota['id']) ?>"
                                   class="text-slate-700 hover:underline mr-3">Edit</a>
                                <?php if ((int) $kuota['slot_terisi'] === 0): ?>
                                    <form method="POST" action="/admin/grooming/kuota/hapus" class="inline"
                                          onsubmit="return confirm('Hapus kuota ini?')">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $kuota['id']) ?>">
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
