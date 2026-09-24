<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusBookingGrooming;

$bookingList = $bookingList ?? [];
$statusLabels = $statusLabels ?? [];
?>
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-foreground">Riwayat Grooming</h1>
        <a href="/grooming/booking" class="<?= e(ui_btn_primary()) ?>">
            + Booking Baru
        </a>
    </div>

    <?php if ($bookingList === []): ?>
        <div class="<?= e(design_cn(design_surface('metric'), 'p-8 text-center')) ?>">
            <p class="text-muted-foreground mb-4">Belum ada booking grooming.</p>
            <a href="/grooming/booking" class="text-primary hover:underline font-medium">Ajukan booking pertama</a>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($bookingList as $booking): ?>
                <?php
                $statusEnum = StatusBookingGrooming::tryFrom((string) $booking['status']);
                $canCancel = $statusEnum?->canCancelByPelanggan() ?? false;
                ?>
                <div class="<?= e(design_cn(design_surface('metric'), 'p-4')) ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                        <div>
                            <div class="font-semibold text-foreground"><?= e((string) $booking['jenis_nama']) ?></div>
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
                        <?php if (!empty($booking['jam_grooming'])): ?>
                            <div>Jam: <?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?> WIB</div>
                        <?php endif; ?>
                        <div>Total: Rp <?= e(number_format((float) $booking['harga_layanan'] + (float) $booking['biaya_antar_jemput'], 0, ',', '.')) ?></div>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-3 border-t text-sm">
                        <a href="/grooming/detail?id=<?= e((string) $booking['id']) ?>"
                           class="text-primary hover:underline">Detail</a>
                        <?php if ((string) $booking['status'] === StatusBookingGrooming::MENUNGGU_PEMBAYARAN->value): ?>
                            <a href="/grooming/pembayaran?id=<?= e((string) $booking['id']) ?>"
                               class="text-primary hover:underline font-medium">Bayar & Upload Bukti</a>
                        <?php endif; ?>
                        <?php if ($canCancel): ?>
                            <form method="POST" action="/grooming/booking/batalkan" class="inline"
                                  onsubmit="return confirm('Batalkan booking ini?')">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <button type="submit" class="text-destructive hover:underline">Batalkan</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
