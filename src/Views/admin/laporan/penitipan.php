<?php

declare(strict_types=1);

use App\Enums\StatusPenitipan;

$mulai = $mulai ?? date('Y-m-01');
$akhir = $akhir ?? date('Y-m-t');
$filterStatus = $filterStatus ?? '';
$metrics = $metrics ?? [];
$rows = $rows ?? [];
$statusLabels = $statusLabels ?? [];
$opsiLabels = $opsiLabels ?? [];
$statusPembayaranLunas = $statusPembayaranLunas ?? 'LUNAS';
$activeTab = $activeTab ?? 'penitipan';
$totalPendapatan = (float) ($metrics['pendapatan_awal'] ?? 0) + (float) ($metrics['pendapatan_perpanjangan'] ?? 0);
?>
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Laporan Data Pet Hotel</h1>
    <p class="text-sm text-gray-500 mb-6 print:hidden">
        Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
    </p>

    <?php require __DIR__ . '/_subnav.php'; ?>

    <form method="GET" action="/admin/laporan/penitipan" class="mb-6 flex flex-wrap items-end gap-3 print:hidden">
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
        <button type="submit" class="bg-gray-100 border border-gray-300 rounded-lg px-4 py-2 text-sm hover:bg-gray-200">
            Filter
        </button>
    </form>

    <div id="laporan-print" class="space-y-6 print:shadow-none">
        <div class="hidden print:block">
            <h2 class="text-xl font-bold text-gray-800">Laporan Data Pet Hotel</h2>
            <p class="text-sm text-gray-600">
                Periode: <?= e(date('d/m/Y', strtotime($mulai))) ?> — <?= e(date('d/m/Y', strtotime($akhir))) ?>
                <?php if ($filterStatus !== ''): ?>
                    · Status: <?= e($statusLabels[$filterStatus] ?? $filterStatus) ?>
                <?php endif; ?>
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border p-4 print:border print:shadow-none">
                <div class="text-sm text-gray-500">Jumlah Booking</div>
                <div class="text-2xl font-bold text-slate-800"><?= e((string) ($metrics['jumlah_booking'] ?? 0)) ?></div>
            </div>
            <div class="bg-white rounded-xl border p-4 print:border print:shadow-none">
                <div class="text-sm text-gray-500">Total Hari Dititipkan</div>
                <div class="text-2xl font-bold text-slate-800"><?= e((string) ($metrics['total_hari'] ?? 0)) ?> hari</div>
            </div>
            <div class="bg-white rounded-xl border p-4 print:border print:shadow-none">
                <div class="text-sm text-gray-500">Total Pendapatan (Lunas)</div>
                <div class="text-2xl font-bold text-green-700">
                    Rp <?= e(number_format($totalPendapatan, 0, ',', '.')) ?>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Awal: Rp <?= e(number_format((float) ($metrics['pendapatan_awal'] ?? 0), 0, ',', '.')) ?>
                    · Perpanjangan: Rp <?= e(number_format((float) ($metrics['pendapatan_perpanjangan'] ?? 0), 0, ',', '.')) ?>
                </div>
            </div>
            <div class="bg-white rounded-xl border p-4 print:border print:shadow-none">
                <div class="text-sm text-gray-500">Promo & Antar-jemput</div>
                <div class="text-sm mt-1">
                    Promo: <?= e((string) ($metrics['promo_jumlah'] ?? 0)) ?> booking
                    (Rp <?= e(number_format((float) ($metrics['promo_total_potongan'] ?? 0), 0, ',', '.')) ?> potongan)
                </div>
                <div class="text-sm mt-1">
                    Antar-jemput: <?= e((string) ($metrics['antar_jemput_jumlah'] ?? 0)) ?> booking
                    (Rp <?= e(number_format((float) ($metrics['antar_jemput_pendapatan'] ?? 0), 0, ',', '.')) ?>)
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border overflow-hidden print:border print:shadow-none">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h2 class="font-semibold text-gray-800">Detail Penitipan</h2>
            </div>
            <?php if ($rows === []): ?>
                <div class="p-8 text-center text-gray-600">Tidak ada data pada periode ini.</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-2">Check-in</th>
                                <th class="px-4 py-2">Check-out</th>
                                <th class="px-4 py-2">Pelanggan</th>
                                <th class="px-4 py-2">Kucing</th>
                                <th class="px-4 py-2">Paket</th>
                                <th class="px-4 py-2">Hari</th>
                                <th class="px-4 py-2">Pengantaran</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2 text-right">Bayar Awal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php foreach ($rows as $row): ?>
                                <?php $statusEnum = StatusPenitipan::tryFrom((string) $row['status']); ?>
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= e(date('d/m/Y', strtotime((string) $row['check_in']))) ?></td>
                                    <td class="px-4 py-2 whitespace-nowrap"><?= e(date('d/m/Y', strtotime((string) $row['check_out']))) ?></td>
                                    <td class="px-4 py-2"><?= e((string) $row['pelanggan_nama']) ?></td>
                                    <td class="px-4 py-2"><?= e((string) $row['kucing_nama']) ?></td>
                                    <td class="px-4 py-2"><?= e((string) $row['paket_nama']) ?></td>
                                    <td class="px-4 py-2"><?= e((string) $row['lama_hari']) ?></td>
                                    <td class="px-4 py-2"><?= e($opsiLabels[$row['opsi_pengantaran']] ?? (string) $row['opsi_pengantaran']) ?></td>
                                    <td class="px-4 py-2">
                                        <?php if ($statusEnum): ?>
                                            <span class="text-xs px-2 py-0.5 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                                                <?= e($statusLabels[$row['status']] ?? (string) $row['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2 text-right whitespace-nowrap">
                                        <?php if ((string) ($row['status_pembayaran_awal'] ?? '') === $statusPembayaranLunas): ?>
                                            Rp <?= e(number_format((float) $row['total_bayar_awal'], 0, ',', '.')) ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">—</span>
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
