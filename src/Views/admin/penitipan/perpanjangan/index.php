<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusPerpanjanganPenitipan;

$perpanjanganList = $perpanjanganList ?? [];
$statusLabels = $statusLabels ?? [];
$filterStatus = $filterStatus ?? '';
?>
<div>
    <h1 class="text-2xl font-bold mb-6">Perpanjangan Penitipan</h1>
    <?php require __DIR__ . '/../_nav.php'; ?>
    <form method="GET" class="mb-4">
        <select name="status" class="border rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="">Semua status</option>
            <?php foreach ($statusLabels as $v => $l): ?>
                <option value="<?= e($v) ?>" <?= $filterStatus === $v ? 'selected' : '' ?>><?= e($l) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <div class="space-y-3">
        <?php foreach ($perpanjanganList as $pp): ?>
            <div class="bg-white border rounded-xl p-4 text-sm">
                <div class="flex justify-between">
                    <span><?= e((string) $pp['pelanggan_nama']) ?> · <?= e((string) $pp['kucing_nama']) ?></span>
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded"><?= e($statusLabels[$pp['status']] ?? (string) $pp['status']) ?></span>
                </div>
                <div class="text-gray-600 mt-1">
                    <?= e(date('d/m/Y', strtotime((string) $pp['check_out_sebelum']))) ?>
                    → <?= e(date('d/m/Y', strtotime((string) $pp['check_out_baru']))) ?>
                    · +<?= (int) $pp['tambah_hari'] ?> hari · Rp <?= e(number_format((float) $pp['subtotal_tambahan'], 0, ',', '.')) ?>
                </div>
                <?php if ((string) $pp['status'] === StatusPerpanjanganPenitipan::MENUNGGU_KONFIRMASI->value): ?>
                    <div class="flex gap-2 mt-3">
                        <form method="POST" action="/admin/penitipan/perpanjangan/konfirmasi"><?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $pp['id']) ?>">
                            <button type="submit" class="bg-green-600 text-white rounded px-3 py-1 text-xs">Konfirmasi</button>
                        </form>
                        <form method="POST" action="/admin/penitipan/perpanjangan/tolak"><?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $pp['id']) ?>">
                            <input type="text" name="catatan" placeholder="Alasan" class="border rounded px-2 py-1 text-xs">
                            <button type="submit" class="border border-red-300 text-red-600 rounded px-3 py-1 text-xs">Tolak</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
