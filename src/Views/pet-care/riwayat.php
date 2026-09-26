<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusBookingPetCare;

$bookingList = $bookingList ?? [];
$statusLabels = $statusLabels ?? [];
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/pet-care/booking" class="<?= e(ui_btn_primary()) ?>">+ Booking Baru</a>
    <?php
    ui_page_header(
        'Riwayat Pet Care',
        'Pet Care',
        'Semua booking pet care Anda.',
        (string) ob_get_clean(),
    );
    ?>

    <?php if ($bookingList === []): ?>
        <?php
        ui_empty_state(
            'empty',
            'Belum ada booking pet care',
            'Ajukan kunjungan pet care pertama untuk kucing Anda.',
            'Ajukan Booking',
            '/pet-care/booking',
        );
        ?>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($bookingList as $booking): ?>
                <?php
                $statusEnum = StatusBookingPetCare::tryFrom((string) $booking['status']);
                $canCancel = $statusEnum?->canCancel() ?? false;
                ?>
                <div class="<?= e(design_cn(design_surface('panel'), 'p-4')) ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                        <div>
                            <div class="font-semibold text-foreground"><?= e((string) $booking['layanan_nama']) ?></div>
                            <div class="text-sm text-muted-foreground">Kucing: <?= e((string) $booking['kucing_nama']) ?></div>
                        </div>
                        <?php if ($statusEnum): ?>
                            <span class="text-xs px-2 py-1 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                                <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-2 text-sm text-muted-foreground mb-3">
                        <div>Tanggal: <?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></div>
                        <div>Slot: <?= e(substr((string) $booking['slot_waktu'], 0, 5)) ?> WIB</div>
                        <div>Estimasi: Rp <?= e(number_format((float) $booking['harga_layanan'], 0, ',', '.')) ?></div>
                        <div>Dibuat: <?= e(date('d/m/Y H:i', strtotime((string) $booking['created_at']))) ?></div>
                    </div>

                    <?php if (!empty($booking['catatan'])): ?>
                        <p class="text-sm text-muted-foreground mb-3">Catatan: <?= e((string) $booking['catatan']) ?></p>
                    <?php endif; ?>

                    <?php if ($canCancel): ?>
                        <form method="POST" action="/pet-care/booking/batalkan" class="pt-3 border-t border-border"
                              onsubmit="return confirm('Batalkan booking ini?')">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                            <button type="submit" class="text-sm text-destructive hover:underline">
                                Batalkan Booking
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
