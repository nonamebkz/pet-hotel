<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusBookingGrooming;
use App\Enums\StatusPembayaran;
use App\Enums\StatusRefund;
use App\Enums\StatusVerifikasi;

$booking = $booking ?? [];
$transaksi = $transaksi ?? null;
$transaksiLunas = $transaksiLunas ?? false;
$bukti = $bukti ?? null;
$invoice = $invoice ?? null;
$statusLabels = $statusLabels ?? [];
$opsiLabels = $opsiLabels ?? [];
$bankConfig = $bankConfig ?? [];
$whatsapp = (string) ($bankConfig['petshop_whatsapp'] ?? '');

$statusEnum = StatusBookingGrooming::tryFrom((string) ($booking['status'] ?? ''));
$canCancel = $statusEnum?->canCancelByPelanggan() ?? false;
$statusRefund = $transaksi ? StatusRefund::tryFrom((string) ($transaksi['status_refund'] ?? '')) : null;
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <p>
        <a href="/grooming/riwayat" class="<?= e(ui_back_link_class()) ?>">&larr; Riwayat</a>
    </p>
    <?php
    ui_page_header(
        'Detail Booking',
        'Grooming',
        (string) ($booking['jenis_nama'] ?? '') !== '' ? (string) $booking['jenis_nama'] . ' · ' . (string) ($booking['kucing_nama'] ?? '') : null,
    );
    ?>

    <div class="<?= e(design_cn(design_surface('panel'), 'p-6 space-y-4 max-w-2xl')) ?>">
        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="font-semibold text-lg text-foreground"><?= e((string) $booking['jenis_nama']) ?></div>
                <div class="text-sm text-muted-foreground">Kucing: <?= e((string) $booking['kucing_nama']) ?></div>
            </div>
            <?php if ($statusEnum): ?>
                <span class="text-xs px-2 py-1 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                    <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="grid sm:grid-cols-2 gap-3 text-sm text-muted-foreground">
            <div>Tanggal: <?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?></div>
            <?php if (!empty($booking['jam_grooming'])): ?>
                <div>Jam grooming: <?= e(substr((string) $booking['jam_grooming'], 0, 5)) ?> WIB</div>
            <?php endif; ?>
            <div>Pengantaran: <?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?></div>
            <?php if ($booking['opsi_pengantaran'] === OpsiPengantaran::ANTAR_JEMPUT->value && $booking['jarak_km'] !== null): ?>
                <div>Jarak: <?= e(number_format((float) $booking['jarak_km'], 2, ',', '.')) ?> km</div>
            <?php endif; ?>
        </div>

        <?php if (!empty($booking['catatan'])): ?>
            <p class="text-sm text-muted-foreground">Catatan: <?= e((string) $booking['catatan']) ?></p>
        <?php endif; ?>

        <?php if ($transaksi): ?>
            <div class="bg-muted/50 rounded-lg p-4 text-sm">
                <div class="font-medium text-foreground mb-2">Rincian Tagihan</div>
                <div class="flex justify-between text-muted-foreground">
                    <span>Subtotal layanan</span>
                    <span>Rp <?= e(number_format((float) $transaksi['subtotal_layanan'], 0, ',', '.')) ?></span>
                </div>
                <div class="flex justify-between text-muted-foreground">
                    <span>Biaya antar-jemput</span>
                    <span>Rp <?= e(number_format((float) $transaksi['biaya_antar_jemput'], 0, ',', '.')) ?></span>
                </div>
                <div class="flex justify-between font-medium text-foreground mt-2 pt-2 border-t">
                    <span>Total bayar</span>
                    <span>Rp <?= e(number_format((float) $transaksi['total_bayar'], 0, ',', '.')) ?></span>
                </div>
                <?php if (!empty($transaksi['batas_waktu_bayar']) && (string) $transaksi['status_pembayaran'] === StatusPembayaran::MENUNGGU_PEMBAYARAN->value): ?>
                    <p class="<?= e(design_cn(design_alert_inline('warning'), 'mt-2')) ?>">
                        Batas waktu bayar: <?= e(date('d/m/Y H:i', strtotime((string) $transaksi['batas_waktu_bayar']))) ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php require __DIR__ . '/../partials/refund-status.php'; ?>

        <?php if ($bukti && (string) $bukti['status_verifikasi'] === StatusVerifikasi::DITOLAK->value): ?>
            <div class="<?= e(design_cn(design_advice_panel_surface('danger'), design_surface('panel'), 'p-3 text-sm text-destructive')) ?>">
                Bukti transfer ditolak.
                <?php if (!empty($bukti['catatan_penolakan'])): ?>
                    Catatan: <?= e((string) $bukti['catatan_penolakan']) ?>
                <?php endif; ?>
                Silakan upload ulang bukti transfer.
            </div>
        <?php endif; ?>

        <div class="flex flex-wrap gap-3 pt-2">
            <?php if ((string) ($booking['status'] ?? '') === StatusBookingGrooming::MENUNGGU_PEMBAYARAN->value): ?>
                <a href="/grooming/pembayaran?id=<?= e((string) $booking['id']) ?>"
                   class="<?= e(ui_btn_primary()) ?>">
                    Upload Bukti Transfer
                </a>
            <?php endif; ?>
            <?php if ($invoice && (string) ($transaksi['status_pembayaran'] ?? '') === StatusPembayaran::LUNAS->value): ?>
                <a href="/grooming/invoice?id=<?= e((string) $booking['id']) ?>"
                   class="<?= e(ui_btn_secondary()) ?>">
                    Lihat Invoice
                </a>
            <?php endif; ?>
            <?php if ($canCancel): ?>
                <form method="POST" action="/grooming/booking/batalkan" class="inline"
                      onsubmit="return confirm('Batalkan booking ini?')">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                    <button type="submit" class="text-sm text-destructive hover:underline">Batalkan Booking</button>
                </form>
            <?php elseif ($transaksiLunas && !$canCancel): ?>
                <?php
                $bookingId = (string) $booking['id'];
                require __DIR__ . '/../partials/hubungi-kami.php';
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>
