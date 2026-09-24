<?php

declare(strict_types=1);

$bookingList = $bookingList ?? [];
?>
<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-foreground">Riwayat Penitipan</h1>
            <p class="text-sm text-muted-foreground mt-1">Semua booking pet hotel Anda.</p>
        </div>
        <a href="/penitipan/booking" class="<?= e(ui_btn_primary()) ?>">
            + Ajukan Penitipan
        </a>
    </div>

    <?php if ($bookingList === []): ?>
        <div class="<?= e(design_cn(design_surface('metric'), 'p-8 text-center text-muted-foreground')) ?>">
            Belum ada riwayat penitipan.
        </div>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($bookingList as $booking): ?>
                <a href="/penitipan/detail?id=<?= e((string) $booking['id']) ?>"
                   class="<?= e(design_cn(design_interactive('listArticle'), design_interactive('listRowHover'), 'block transition')) ?>">
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
                        <span class="text-xs px-2 py-1 rounded-full bg-muted text-foreground shrink-0">
                            <?= e((string) ($booking['status_label'] ?? $booking['status'])) ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
