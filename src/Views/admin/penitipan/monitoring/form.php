<?php

declare(strict_types=1);

use App\Core\Csrf;

$booking = $booking ?? [];
$monitoringList = $monitoringList ?? [];
$errors = $errors ?? [];
?>
<div>
    <a href="/admin/penitipan/booking" class="text-sm text-gray-500">&larr; Booking</a>
    <h1 class="text-2xl font-bold mt-2 mb-2">Monitoring Harian</h1>
    <p class="text-sm text-gray-500 mb-6"><?= e((string) $booking['kucing_nama']) ?> · <?= e((string) $booking['pelanggan_nama']) ?></p>

    <form method="POST" action="/admin/penitipan/monitoring/tambah" enctype="multipart/form-data" class="bg-white rounded-xl border p-6 max-w-xl space-y-4 mb-8">
        <?= Csrf::field() ?>
        <input type="hidden" name="booking_id" value="<?= e((string) $booking['id']) ?>">
        <div><label class="text-sm font-medium">Tanggal</label>
            <input type="date" name="tanggal" value="<?= e(date('Y-m-d')) ?>" class="w-full border rounded-lg px-3 py-2 text-sm mt-1" required></div>
        <div><label class="text-sm font-medium">Foto (opsional)</label>
            <input type="file" name="foto" accept="image/*" class="w-full text-sm mt-1"></div>
        <div><label class="text-sm font-medium">Catatan makan</label>
            <textarea name="catatan_makan" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm mt-1"></textarea></div>
        <div><label class="text-sm font-medium">Kondisi</label>
            <textarea name="kondisi" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm mt-1"></textarea></div>
        <div><label class="text-sm font-medium">Aktivitas harian</label>
            <textarea name="aktivitas_harian" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm mt-1"></textarea></div>
        <button type="submit" class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm">Simpan Monitoring</button>
    </form>

    <?php if ($monitoringList !== []): ?>
        <h2 class="font-semibold mb-3">Riwayat Monitoring</h2>
        <div class="space-y-3">
            <?php foreach ($monitoringList as $m): ?>
                <div class="bg-white border rounded-lg p-3 text-sm">
                    <div class="font-medium"><?= e(date('d/m/Y', strtotime((string) $m['tanggal']))) ?></div>
                    <?php if (!empty($m['foto_url'])): ?>
                        <img src="<?= e((string) $m['foto_url']) ?>" class="mt-2 max-h-32 rounded" alt="">
                    <?php endif; ?>
                    <?php if (!empty($m['catatan_makan'])): ?><p class="text-gray-600 mt-1">Makan: <?= e((string) $m['catatan_makan']) ?></p><?php endif; ?>
                    <?php if (!empty($m['kondisi'])): ?><p class="text-gray-600">Kondisi: <?= e((string) $m['kondisi']) ?></p><?php endif; ?>
                    <?php if (!empty($m['aktivitas_harian'])): ?><p class="text-gray-600">Aktivitas: <?= e((string) $m['aktivitas_harian']) ?></p><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
