<?php

declare(strict_types=1);

$notifikasiList = $notifikasiList ?? [];
$unreadCount = count(array_filter(
    $notifikasiList,
    static fn (array $notif): bool => empty($notif['sudah_dibaca']),
));
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Notifikasi',
        'Akun',
        'Semua pemberitahuan booking, pembayaran, dan operasional untuk akun Anda.',
        null,
        '<span class="' . e(design_status_badge($unreadCount > 0 ? 'muted' : 'success')) . '">Perlu dibaca: ' . e((string) $unreadCount) . '</span>',
    );
    ?>

    <?php if ($notifikasiList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada notifikasi';
        $description = 'Update booking dan pembayaran akan muncul di sini secara otomatis.';
        $ctaLabel = 'Kembali ke Beranda';
        $ctaHref = '/dashboard';
        $ctaClass = ui_btn_secondary();
        require __DIR__ . '/../partials/ui/empty-state.php';
        ?>
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
                        <time class="text-xs text-muted-foreground shrink-0 tabular-nums">
                            <?= e(date('d/m/Y H:i', strtotime((string) $notif['created_at']))) ?>
                        </time>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
