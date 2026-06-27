<?php

declare(strict_types=1);

use App\Core\Csrf;

$pendingList = $pendingList ?? [];
?>
<div>
    <h1 class="text-2xl font-bold mb-6">Verifikasi Bukti Penitipan</h1>
    <?php require __DIR__ . '/../_nav.php'; ?>
    <?php if ($pendingList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-500">Tidak ada bukti menunggu verifikasi.</div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($pendingList as $item): ?>
                <div class="bg-white rounded-xl border p-4">
                    <div class="font-semibold"><?= e((string) $item['pelanggan_nama']) ?></div>
                    <div class="text-sm text-gray-600 mt-1">
                        <?php if (!empty($item['perpanjangan_penitipan_id'])): ?>
                            Perpanjangan · Check-out baru: <?= e(date('d/m/Y', strtotime((string) ($item['perpanjangan_check_out_baru'] ?? '')))) ?>
                        <?php else: ?>
                            Booking · <?= e(date('d/m/Y', strtotime((string) $item['check_in']))) ?> — <?= e(date('d/m/Y', strtotime((string) $item['check_out']))) ?>
                        <?php endif; ?>
                        · Total: Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?>
                    </div>
                    <?php if (!empty($item['bukti_file_url'])): ?>
                        <a href="<?= e((string) $item['bukti_file_url']) ?>" target="_blank" class="text-sm text-blue-600 underline mt-2 inline-block">Lihat bukti</a>
                    <?php endif; ?>
                    <div class="flex gap-2 mt-3">
                        <form method="POST" action="/admin/penitipan/pembayaran/setujui"><?= Csrf::field() ?>
                            <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                            <button type="submit" class="bg-green-600 text-white rounded-lg px-3 py-1.5 text-sm">Setujui</button>
                        </form>
                        <form method="POST" action="/admin/penitipan/pembayaran/tolak" class="flex gap-2 items-center"><?= Csrf::field() ?>
                            <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                            <input type="text" name="catatan" placeholder="Catatan penolakan" class="border rounded px-2 py-1 text-sm">
                            <button type="submit" class="border border-red-300 text-red-600 rounded-lg px-3 py-1.5 text-sm">Tolak</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
