<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$slotList = $slotList ?? [];
$tanggal = $tanggal ?? date('Y-m-d');
$statusSlotLabels = $statusSlotLabels ?? [];
$errors = Session::getFlash('errors', []);
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Slot Dokter Pet Care</h1>
        <a href="/admin/pet-care/slot/tambah?tanggal=<?= e($tanggal) ?>"
           class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-900">
            + Tambah Slot
        </a>
    </div>

    <div class="flex gap-4 mb-6 text-sm">
        <a href="/admin/pet-care/layanan" class="text-gray-500 hover:text-slate-800">Layanan</a>
        <a href="/admin/pet-care/slot" class="text-slate-800 font-medium border-b-2 border-slate-800 pb-1">Slot Dokter</a>
        <a href="/admin/pet-care/booking" class="text-gray-500 hover:text-slate-800">Booking</a>
    </div>

    <form method="GET" action="/admin/pet-care/slot" class="mb-6 flex items-end gap-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="<?= e($tanggal) ?>"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm hover:bg-gray-200">
            Tampilkan
        </button>
    </form>

    <?php if ($slotList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">
            Tidak ada slot untuk tanggal ini.
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Waktu</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Terisi</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($slotList as $slot): ?>
                        <tr>
                            <td class="px-4 py-3 font-medium"><?= e(substr((string) $slot['slot_waktu'], 0, 5)) ?></td>
                            <td class="px-4 py-3"><?= (int) $slot['slot_terisi'] ?> / <?= (int) $slot['slot_maksimal'] ?></td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full <?= ($slot['status_slot'] ?? '') === 'TERSEDIA' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' ?>">
                                    <?= e($statusSlotLabels[$slot['status_slot']] ?? (string) $slot['status_slot']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <?php if (($slot['status_slot'] ?? '') === 'TERSEDIA'): ?>
                                    <form method="POST" action="/admin/pet-care/slot/tutup" class="inline">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                        <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                        <button type="submit" class="text-gray-600 hover:underline text-xs">Tutup</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="/admin/pet-care/slot/buka" class="inline">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                        <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                        <button type="submit" class="text-green-700 hover:underline text-xs">Buka</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="/admin/pet-care/slot/hapus" class="inline"
                                      onsubmit="return confirm('Hapus slot ini?')">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                    <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                    <button type="submit" class="text-red-600 hover:underline text-xs">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
