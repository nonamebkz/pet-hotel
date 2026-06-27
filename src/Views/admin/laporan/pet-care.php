<?php

declare(strict_types=1);

use App\Enums\StatusBookingPetCare;

$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$filterStatus = $filterStatus ?? '';
$filterLayananId = $filterLayananId ?? '';
$metrics = $metrics ?? [];
$rows = $rows ?? [];
$statusLabels = $statusLabels ?? [];
$layananList = $layananList ?? [];
$activeTab = $activeTab ?? 'pet-care';
?>
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Laporan Data Booking Pet Care</h1>
    <p class="text-sm text-gray-500 mb-6 print:hidden">
        Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
        · Pembayaran di loket (tanpa pendapatan di sistem)
    </p>

    <?php require __DIR__ . '/_subnav.php'; ?>

    <form method="GET" action="/admin/laporan/pet-care" class="mb-6 flex flex-wrap items-end gap-3 print:hidden">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
            <input type="date" name="mulai" value="<?= e($mulai) ?>"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
            <input type="date" name="akhir" value="<?= e($akhir) ?>"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                <?php foreach ($statusLabels as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Layanan</label>
            <select name="layanan_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua</option>
                <?php foreach ($layananList as $layanan): ?>
                    <option value="<?= e((string) $layanan['id']) ?>"
                        <?= $filterLayananId === (string) $layanan['id'] ? 'selected' : '' ?>>
                        <?= e((string) $layanan['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm hover:bg-gray-200">
            Filter
        </button>
    </form>

    <div id="laporan-print" class="space-y-6 print:shadow-none">
        <div class="hidden print:block">
            <h2 class="text-xl font-bold text-gray-800">Laporan Data Booking Pet Care</h2>
            <p class="text-sm text-gray-600">
                Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border p-4 print:border print:shadow-none">
                <div class="text-sm text-gray-500">Jumlah Booking</div>
                <div class="text-2xl font-bold text-slate-800"><?= e((string) ($metrics['jumlah_booking'] ?? 0)) ?></div>
            </div>
            <div class="bg-white rounded-xl border p-4 print:border print:shadow-none">
                <div class="text-sm text-gray-500 mb-2">Per Layanan</div>
                <?php if (($metrics['breakdown_layanan'] ?? []) === []): ?>
                    <div class="text-sm text-gray-400">—</div>
                <?php else: ?>
                    <ul class="text-sm space-y-1">
                        <?php foreach ($metrics['breakdown_layanan'] as $item): ?>
                            <li class="flex justify-between gap-2">
                                <span><?= e($item['layanan_nama']) ?></span>
                                <span class="text-gray-600 shrink-0"><?= e((string) $item['jumlah']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="bg-white rounded-xl border p-4 print:border print:shadow-none">
                <div class="text-sm text-gray-500 mb-2">Per Slot (Top 10)</div>
                <?php if (($metrics['breakdown_slot'] ?? []) === []): ?>
                    <div class="text-sm text-gray-400">—</div>
                <?php else: ?>
                    <ul class="text-sm space-y-1">
                        <?php foreach (array_slice($metrics['breakdown_slot'], 0, 10) as $item): ?>
                            <li class="flex justify-between gap-2">
                                <span><?= e(date('d/m/Y', strtotime($item['tanggal']))) ?> <?= e(substr($item['slot_waktu'], 0, 5)) ?></span>
                                <span class="text-gray-600 shrink-0"><?= e((string) $item['jumlah']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-xl border overflow-hidden print:border print:shadow-none">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h2 class="font-semibold text-gray-800">Detail Booking</h2>
            </div>
            <?php if ($rows === []): ?>
                <div class="p-8 text-center text-gray-600">Tidak ada data pada periode ini.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-2">Tanggal</th>
                                <th class="px-4 py-2">Slot</th>
                                <th class="px-4 py-2">Pelanggan</th>
                                <th class="px-4 py-2">Kucing</th>
                                <th class="px-4 py-2">Layanan</th>
                                <th class="px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($rows as $row): ?>
                                <?php $statusEnum = StatusBookingPetCare::tryFrom((string) $row['status']); ?>
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= e(date('d/m/Y', strtotime((string) $row['tanggal']))) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= e(substr((string) $row['slot_waktu'], 0, 5)) ?> WIB</td>
                                    <td class="px-4 py-2"><?= e((string) $row['pelanggan_nama']) ?></td>
                                    <td class="px-4 py-2"><?= e((string) $row['kucing_nama']) ?></td>
                                    <td class="px-4 py-2"><?= e((string) $row['layanan_nama']) ?></td>
                                    <td class="px-4 py-2">
                                        <?php if ($statusEnum): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                                                <?= e($statusLabels[$row['status']] ?? (string) $row['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-6 print:hidden">
        <button type="button" onclick="window.print()"
                class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">
            Cetak / Simpan PDF
        </button>
    </div>
</div>
