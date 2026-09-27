<?php

declare(strict_types=1);

$jenisList = $jenisList ?? [];
$pickupSettings = $pickupSettings ?? [];
$freeRadiusKm = (float) ($pickupSettings['pickup_free_radius_km'] ?? 3);
$feePerKm = (int) ($pickupSettings['pickup_extra_fee_per_km'] ?? 5000);
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Grooming',
        'Booking',
        'Layanan perawatan kucing — ajukan online dengan opsi antar-jemput.',
    );
    ?>

    <?php if ($jenisList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada jenis grooming tersedia';
        $description = 'Hubungi petshop jika layanan grooming belum tampil.';
        $ctaLabel = null;
        $ctaHref = null;
        $ctaClass = ui_btn_primary();
        require __DIR__ . '/../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($jenisList as $index => $jenis): ?>
                <?php $isRecommended = $index === 0; ?>
                <div class="<?= e(design_cn(
                    design_surface('metric'),
                    'relative flex flex-col p-5',
                    $isRecommended ? 'ring-2 ring-primary/20 border-primary/30' : '',
                )) ?>">
                    <?php if ($isRecommended): ?>
                        <span class="absolute -top-2.5 left-4 text-xs font-semibold bg-primary text-primary-foreground px-2 py-0.5 rounded-full">
                            Rekomendasi
                        </span>
                    <?php endif; ?>
                    <h2 class="font-semibold text-foreground mb-2 mt-1"><?= e((string) $jenis['nama']) ?></h2>
                    <?php if (!empty($jenis['deskripsi'])): ?>
                        <p class="text-sm text-muted-foreground mb-3 flex-1 line-clamp-3"><?= e((string) $jenis['deskripsi']) ?></p>
                    <?php else: ?>
                        <div class="flex-1"></div>
                    <?php endif; ?>
                    <div class="text-sm text-muted-foreground pt-3 border-t mb-4">
                        Harga: <strong class="text-foreground">Rp <?= e(number_format((float) $jenis['harga'], 0, ',', '.')) ?></strong>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="/grooming/booking?jenis_grooming_id=<?= e(urlencode((string) $jenis['id'])) ?>"
                           class="<?= e(design_cn(ui_btn_primary(), 'flex-1 text-center text-sm')) ?>">
                            Ajukan
                        </a>
                        <a href="/grooming/booking?jenis_grooming_id=<?= e(urlencode((string) $jenis['id'])) ?>#detail"
                           class="<?= e(design_cn(ui_btn_secondary(), 'flex-1 text-center text-sm')) ?>">
                            Detail
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="<?= e(design_cn(design_alert_inline('warning'), 'mt-6 text-sm')) ?>">
            <strong>Antar-jemput:</strong> Gratis jika jarak ≤ <?= e(number_format($freeRadiusKm, 1, ',', '.')) ?> km dari petshop.
            Di atas <?= e(number_format($freeRadiusKm, 1, ',', '.')) ?> km dikenakan biaya
            Rp <?= e(number_format($feePerKm, 0, ',', '.')) ?> per km tambahan.
        </div>
    <?php endif; ?>
</div>
