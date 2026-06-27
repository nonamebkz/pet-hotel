<?php

declare(strict_types=1);

use App\Enums\JenisLayanan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusRefund;

$rows = $rows ?? [];
$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$filterStatus = $filterStatus ?? '';
$filterJenis = $filterJenis ?? '';
$filterQ = $filterQ ?? '';
$statusLabels = $statusLabels ?? StatusPembayaran::labels();
$refundLabels = $refundLabels ?? StatusRefund::labels();
?>
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Riwayat Transaksi</h1>
    <p class="text-sm text-gray-500 mb-6">
        Arsip pembayaran grooming, penitipan, dan perpanjangan penitipan.
        Periode transaksi: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
    </p>

    <div class="flex flex-wrap gap-4 mb-6 text-sm">
        <a href="/admin/grooming/pembayaran" class="text-gray-500 hover:text-slate-800">Verifikasi Grooming</a>
        <a href="/admin/penitipan/pembayaran" class="text-gray-500 hover:text-slate-800">Verifikasi Penitipan</a>
        <span class="text-slate-800 font-medium border-b-2 border-slate-800 pb-1">Riwayat Transaksi</span>
    </div>

    <form method="GET" action="/admin/transaksi" class="mb-6 flex flex-wrap items-end gap-3">
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
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm min-w-[180px]">
                <option value="">Semua</option>
                <?php foreach ($statusLabels as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Layanan</label>
            <select name="jenis" class="border border-gray-300 rounded-lg px-3 py-2 text-sm min-w-[160px]">
                <option value="">Semua</option>
                <option value="<?= e(JenisLayanan::GROOMING->value) ?>"
                    <?= $filterJenis === JenisLayanan::GROOMING->value ? 'selected' : '' ?>>
                    Grooming
                </option>
                <option value="<?= e(JenisLayanan::PENITIPAN->value) ?>"
                    <?= $filterJenis === JenisLayanan::PENITIPAN->value ? 'selected' : '' ?>>
                    Penitipan
                </option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
            <input type="search" name="q" value="<?= e($filterQ) ?>"
                   placeholder="Cari nama..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm min-w-[180px]">
        </div>
        <button type="submit"
                class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm hover:bg-gray-200">
            Terapkan Filter
        </button>
    </form>

    <?php if ($rows === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">
            Tidak ada transaksi untuk filter yang dipilih.
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Tanggal</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Pelanggan</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Jenis</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Layanan</th>
                            <th class="text-right px-4 py-3 font-medium text-gray-700">Total</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Status</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Bukti</th>
                            <th class="text-left px-4 py-3 font-medium text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($rows as $row): ?>
                            <?php
                            $statusEnum = StatusPembayaran::tryFrom((string) $row['status_pembayaran']);
                            $refundEnum = StatusRefund::tryFrom((string) ($row['status_refund'] ?? StatusRefund::TIDAK_ADA->value));
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div><?= e(date('d/m/Y', strtotime((string) $row['created_at']))) ?></div>
                                    <div class="text-xs text-gray-400">
                                        <?= e(date('H:i', strtotime((string) $row['created_at']))) ?>
                                    </div>
                                    <?php if (!empty($row['dibayar_at'])): ?>
                                        <div class="text-xs text-green-700 mt-1">
                                            Lunas: <?= e(date('d/m/Y H:i', strtotime((string) $row['dibayar_at']))) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800"><?= e((string) $row['pelanggan_nama']) ?></div>
                                    <?php if (!empty($row['pelanggan_telepon'])): ?>
                                        <div class="text-xs text-gray-500"><?= e((string) $row['pelanggan_telepon']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap"><?= e((string) $row['tagihan_jenis']) ?></td>
                                <td class="px-4 py-3">
                                    <div><?= e((string) ($row['layanan_label'] ?? '')) ?></div>
                                    <div class="text-xs text-gray-500"><?= e((string) ($row['tanggal_display'] ?? '')) ?></div>
                                </td>
                                <td class="px-4 py-3 text-right font-medium whitespace-nowrap">
                                    Rp <?= e(number_format((float) $row['total_bayar'], 0, ',', '.')) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <?php if ($statusEnum): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                                                <?= e($statusLabels[$statusEnum->value] ?? $statusEnum->value) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($row['bukti_ditolak'])): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-800">
                                                Bukti Ditolak
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($refundEnum && $refundEnum !== StatusRefund::TIDAK_ADA): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-full <?= e($refundEnum->badgeClass()) ?>">
                                                <?= e($refundLabels[$refundEnum->value] ?? $refundEnum->value) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($row['nomor_invoice'])): ?>
                                        <div class="text-xs text-gray-500 mt-1"><?= e((string) $row['nomor_invoice']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if (!empty($row['bukti_file_url'])): ?>
                                        <a href="<?= e((string) $row['bukti_file_url']) ?>"
                                           target="_blank"
                                           class="text-blue-600 hover:underline text-xs">
                                            Lihat bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="<?= e((string) $row['admin_booking_url']) ?>"
                                       class="text-slate-600 hover:underline text-xs">
                                        Booking →
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-3"><?= count($rows) ?> transaksi ditampilkan</p>
    <?php endif; ?>
</div>
