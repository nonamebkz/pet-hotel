<?php

declare(strict_types=1);

use App\Enums\StatusPembayaran;
use App\Enums\StatusRefund;

$rows = $rows ?? [];
$filterStatus = $filterStatus ?? '';
$statusLabels = $statusLabels ?? StatusPembayaran::labels();
$refundLabels = $refundLabels ?? StatusRefund::labels();
$hasActiveFilter = $hasActiveFilter ?? ($filterStatus !== '');
$activeFilters = $activeFilters ?? [];
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Riwayat Transaksi',
        'Akun',
        'Lihat status pembayaran dan unduh bukti untuk booking Anda.',
    );
    ?>

    <div class="<?= e(design_cn(design_surface('panel'), 'p-4 mb-2')) ?>">
        <form method="GET" action="/transaksi" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="status" class="<?= e(ui_form_label_class()) ?>">Status pembayaran</label>
                <select name="status" id="status"
                        class="<?= e(design_cn(ui_form_input_class(), 'text-sm min-w-[200px] py-2')) ?>">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="<?= e(ui_btn_primary()) ?> text-sm">
                Terapkan Filter
            </button>
        </form>
    </div>

    <?php if ($hasActiveFilter): ?>
        <?php
        ui_filter_chips(
            $activeFilters,
            '/transaksi',
            count($rows),
            'transaksi',
        );
        ?>
    <?php endif; ?>

    <?php if ($rows === []): ?>
        <?php
        if ($hasActiveFilter) {
            $variant = 'filtered';
            $title = 'Tidak ditemukan transaksi untuk filter yang dipilih';
            $description = 'Coba status lain atau reset filter untuk melihat semua transaksi.';
            $ctaLabel = 'Reset Filter';
            $ctaHref = '/transaksi';
            $ctaClass = 'bg-primary text-primary-foreground rounded-lg px-4 py-2 text-sm font-medium hover:opacity-90 inline-block';
        } else {
            $variant = 'empty';
            $title = 'Belum ada transaksi';
            $description = 'Transaksi grooming, penitipan, dan perpanjangan penitipan akan muncul di sini.';
            $ctaLabel = 'Ajukan Grooming';
            $ctaHref = '/grooming/booking';
            $ctaClass = 'bg-primary text-primary-foreground rounded-lg px-4 py-2 text-sm font-medium hover:opacity-90 inline-block';
        }
        require __DIR__ . '/../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($rows as $row): ?>
                <?php
                $statusEnum = StatusPembayaran::tryFrom((string) $row['status_pembayaran']);
                $refundEnum = StatusRefund::tryFrom((string) ($row['status_refund'] ?? StatusRefund::TIDAK_ADA->value));
                $statusValue = (string) $row['status_pembayaran'];
                ?>
                <div class="<?= e(design_cn(design_surface('metric'), 'p-4')) ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                        <div>
                            <div class="text-xs text-muted-foreground uppercase tracking-wide mb-0.5">
                                <?= e((string) $row['tagihan_jenis']) ?>
                            </div>
                            <div class="font-semibold text-foreground"><?= e((string) ($row['layanan_label'] ?? '')) ?></div>
                            <div class="text-sm text-muted-foreground mt-0.5">
                                <?= e((string) ($row['tanggal_display'] ?? '')) ?>
                            </div>
                            <div class="text-xs text-muted-foreground/70 mt-1">
                                Dibuat: <?= e(date('d/m/Y H:i', strtotime((string) $row['created_at']))) ?>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-sm font-semibold text-primary-hover mb-2">
                                Rp <?= e(number_format((float) $row['total_bayar'], 0, ',', '.')) ?>
                            </div>
                            <div class="flex flex-wrap justify-end gap-1">
                                <?php if ($statusEnum): ?>
                                    <span class="text-xs px-2 py-1 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                                        <?= e($statusLabels[$statusValue] ?? $statusValue) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($row['bukti_ditolak'])): ?>
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-800">
                                        Bukti Ditolak
                                    </span>
                                <?php endif; ?>
                                <?php if ($refundEnum && $refundEnum !== StatusRefund::TIDAK_ADA): ?>
                                    <span class="text-xs px-2 py-1 rounded-full <?= e($refundEnum->badgeClass()) ?>">
                                        <?= e($refundLabels[$refundEnum->value] ?? $refundEnum->value) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($row['bukti_ditolak']) && !empty($row['bukti_catatan_penolakan'])): ?>
                        <div class="text-sm text-red-700 bg-red-50 border border-red-100 rounded-lg px-3 py-2 mb-3">
                            Catatan penolakan: <?= e((string) $row['bukti_catatan_penolakan']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($row['dibayar_at'])): ?>
                        <div class="text-xs text-muted-foreground mb-3">
                            Dibayar: <?= e(date('d/m/Y H:i', strtotime((string) $row['dibayar_at']))) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($row['nomor_invoice'])): ?>
                        <div class="text-xs text-muted-foreground mb-3">
                            Invoice: <?= e((string) $row['nomor_invoice']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-3 pt-3 border-t text-sm">
                        <a href="<?= e((string) $row['detail_url']) ?>"
                           class="text-primary hover:underline">Detail Booking</a>

                        <?php if ($statusValue === StatusPembayaran::MENUNGGU_PEMBAYARAN->value): ?>
                            <a href="<?= e((string) $row['payment_url']) ?>"
                               class="text-primary hover:underline font-medium">
                                Bayar & Upload Bukti
                            </a>
                        <?php elseif ($statusValue === StatusPembayaran::MENUNGGU_VERIFIKASI->value): ?>
                            <span class="text-blue-600">Menunggu verifikasi staff</span>
                        <?php elseif ($statusValue === StatusPembayaran::LUNAS->value && !empty($row['invoice_url']) && !empty($row['has_invoice'])): ?>
                            <a href="<?= e((string) $row['invoice_url']) ?>"
                               class="text-green-700 hover:underline font-medium">Unduh Invoice</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
