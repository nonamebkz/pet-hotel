<?php

declare(strict_types=1);

$notifikasiList = $notifikasiList ?? [];
?>
<div>
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-foreground">Notifikasi</h1>
        <p class="text-sm text-muted-foreground mt-1">Semua pemberitahuan untuk akun Anda.</p>
    </div>

    <?php if ($notifikasiList === []): ?>
        <div class="<?= e(design_cn(design_surface('metric'), 'p-8 text-center text-muted-foreground')) ?>">
            Belum ada notifikasi.
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($notifikasiList as $notif): ?>
                <div class="<?= e(design_cn(
                    design_surface('metric'),
                    'p-4',
                    empty($notif['sudah_dibaca']) ? 'border-primary/30 bg-primary/5' : '',
                )) ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-medium text-foreground"><?= e((string) $notif['judul']) ?></div>
                            <p class="text-sm text-muted-foreground mt-1"><?= e((string) $notif['pesan']) ?></p>
                        </div>
                        <time class="text-xs text-muted-foreground/70 shrink-0">
                            <?= e(date('d/m/Y H:i', strtotime((string) $notif['created_at']))) ?>
                        </time>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
