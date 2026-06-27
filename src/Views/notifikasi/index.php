<?php

declare(strict_types=1);

$notifikasiList = $notifikasiList ?? [];
?>
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
        <p class="text-sm text-gray-500 mt-1">Semua pemberitahuan untuk akun Anda.</p>
    </div>

    <?php if ($notifikasiList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-500">
            Belum ada notifikasi.
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($notifikasiList as $notif): ?>
                <div class="bg-white rounded-xl border p-4 <?= empty($notif['sudah_dibaca']) ? 'border-orange-200 bg-orange-50/30' : '' ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-medium text-gray-800"><?= e((string) $notif['judul']) ?></div>
                            <p class="text-sm text-gray-600 mt-1"><?= e((string) $notif['pesan']) ?></p>
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
