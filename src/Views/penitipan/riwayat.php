<?php

declare(strict_types=1);

$bookingList = $bookingList ?? [];
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/penitipan/booking" class="<?= e(ui_btn_primary()) ?>">+ Ajukan Penitipan</a>
    <?php
    ui_page_header(
        'Riwayat Penitipan',
        'Penitipan',
        'Semua booking pet hotel Anda.',
        (string) ob_get_clean(),
    );
    ?>

    <?php if ($bookingList === []): ?>
        <?php
        ui_empty_state(
            'empty',
            'Belum ada riwayat penitipan',
            'Ajukan penitipan pertama untuk kucing Anda.',
            'Ajukan Penitipan',
            '/penitipan/booking',
        );
        ?>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($bookingList as $booking): ?>
                <a href="/penitipan/detail?id=<?= e((string) $booking['id']) ?>"
                   class="<?= e(design_cn(design_interactive('listArticle'), design_interactive('listRowHover'), design_surface('panel'), 'block p-4 transition')) ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-medium text-foreground"><?= e((string) $booking['kucing_nama']) ?></div>
                            <div class="text-sm text-muted-foreground">
                                <?= e((string) $booking['paket_nama']) ?> · <?= e((string) $booking['nama_kamar']) ?>
                            </div>
                            <div class="text-sm text-muted-foreground mt-1">
                                <?= e(date('d/m/Y', strtotime((string) $booking['check_in']))) ?>
                                — <?= e(date('d/m/Y', strtotime((string) $booking['check_out']))) ?>
                                (<?= (int) $booking['lama_hari'] ?> hari)
                            </div>
                        </div>
                        <span class="<?= e(design_status_badge('muted')) ?> shrink-0">
                            <?= e((string) ($booking['status_label'] ?? $booking['status'])) ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
