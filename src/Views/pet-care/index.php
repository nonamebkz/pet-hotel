<?php

declare(strict_types=1);

$layananList = $layananList ?? [];
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/pet-care/booking" class="<?= e(ui_btn_primary()) ?>">Ajukan Booking</a>
    <?php
    $actionsHtml = (string) ob_get_clean();
    ui_page_header(
        'Pet Care',
        'Layanan',
        'Layanan kesehatan kucing — booking online, bayar di loket saat kunjungan.',
        $actionsHtml,
    );
    ?>

    <?php if ($layananList === []): ?>
        <div class="<?= e(design_cn(design_surface('metric'), 'p-8 text-center text-muted-foreground')) ?>">
            Belum ada layanan pet care tersedia.
        </div>
    <?php else: ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($layananList as $layanan): ?>
                <div class="<?= e(design_cn(design_surface('metric'), 'p-5 flex flex-col')) ?>">
                    <h2 class="font-semibold text-foreground mb-2"><?= e((string) $layanan['nama']) ?></h2>
                    <?php if (!empty($layanan['deskripsi'])): ?>
                        <p class="text-sm text-muted-foreground mb-3 flex-1"><?= e((string) $layanan['deskripsi']) ?></p>
                    <?php endif; ?>
                    <div class="text-sm text-muted-foreground space-y-1 pt-3 border-t">
                        <div>Estimasi harga: <strong class="text-foreground">Rp <?= e(number_format((float) $layanan['harga'], 0, ',', '.')) ?></strong></div>
                        <div>Durasi: ~<?= (int) $layanan['estimasi_durasi_menit'] ?> menit</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="<?= e(design_cn(design_alert_inline('warning'), 'mt-6')) ?>">
            <strong>Catatan:</strong> Anda membawa kucing sendiri ke petshop (antar sendiri).
            Pembayaran dilakukan langsung di loket — harga di atas hanya estimasi.
        </div>
    <?php endif; ?>
</div>
