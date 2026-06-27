<?php

declare(strict_types=1);

use App\Enums\JenisNotifikasi;

$notifikasiList = $notifikasiList ?? [];
?>
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
        <p class="text-sm text-gray-500 mt-1">Semua pemberitahuan operasional untuk akun Anda.</p>
    </div>

    <?php if ($notifikasiList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-500">
            Belum ada notifikasi.
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($notifikasiList as $notif): ?>
                <?php
                $jenis = (string) ($notif['jenis'] ?? '');
                $actionUrl = null;

                if ($jenis === JenisNotifikasi::PERPANJANGAN_PENITIPAN_MENUNGGU_KONFIRMASI->value) {
                    $actionUrl = '/admin/penitipan/perpanjangan';
                }
                ?>
                <div class="bg-white rounded-xl border p-4 <?= empty($notif['sudah_dibaca']) ? 'border-slate-300 bg-slate-50/50' : '' ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-medium text-gray-800"><?= e((string) $notif['judul']) ?></div>
                            <p class="text-sm text-gray-600 mt-1"><?= e((string) $notif['pesan']) ?></p>
                            <?php if ($actionUrl !== null): ?>
                                <a href="<?= e($actionUrl) ?>" class="inline-block text-sm text-slate-700 hover:underline mt-2">
                                    Lihat permintaan perpanjangan →
                                </a>
                            <?php endif; ?>
                        </div>
                        <time class="text-xs text-gray-400 shrink-0">
                            <?= e(date('d/m/Y H:i', strtotime((string) $notif['created_at']))) ?>
                        </time>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
