<?php

declare(strict_types=1);

use App\Core\Csrf;

$pendingList = $pendingList ?? [];
$heroClass = design_cn(design_surface('panel'), 'relative overflow-hidden p-6 sm:p-8');
$listArticleClass = design_cn(design_interactive('listArticle'), 'scroll-mt-24 p-5 sm:p-6 border-amber-200/80');
$summaryCellClass = design_cn(design_interactive('metricCellOutlined'), 'p-3 text-sm space-y-1');
$inputClass = 'rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground transition hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
$btnSuccess = 'cursor-pointer inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium touch-target bg-emerald-600 text-white hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500';
$btnDanger = 'cursor-pointer inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/10 px-4 py-2 text-sm font-semibold text-destructive transition hover:bg-destructive/15 focus:outline-none focus-visible:ring-2 focus-visible:ring-destructive/40';

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
?>
<div class="font-body space-y-6">
    <section class="<?= e($heroClass) ?>">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-amber-400/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Penitipan</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-foreground">Verifikasi Bukti</h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Tinjau bukti transfer booking &amp; perpanjangan penitipan.
                </p>
            </div>
            <?php if ($pendingList !== []): ?>
                <span class="<?= e(design_status_badge('warning')) ?>">
                    <?= e((string) count($pendingList)) ?> menunggu
                </span>
            <?php endif; ?>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <?php if ($pendingList === []): ?>
        <?php
        $variant = 'success';
        $title = 'Semua bukti sudah diverifikasi';
        $description = 'Tidak ada bukti transfer penitipan yang menunggu tindakan.';
        $ctaLabel = 'Lihat booking';
        $ctaHref = '/admin/penitipan/booking';
        $ctaClass = ui_btn_primary();
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <section class="space-y-3">
            <h2 class="font-heading text-lg text-foreground">
                Perlu Verifikasi
                <span class="ml-1 text-sm font-semibold text-amber-700 dark:text-amber-300">(<?= count($pendingList) ?>)</span>
            </h2>
            <div class="space-y-4">
                <?php foreach ($pendingList as $item): ?>
                    <?php
                    $isPerpanjangan = !empty($item['perpanjangan_penitipan_id']);
                    $checkIn = (string) ($item['check_in'] ?? '');
                    $isCheckInUrgent = !$isPerpanjangan && $checkIn !== '' && $checkIn <= $tomorrow;
                    $uploadedAt = (string) ($item['bukti_uploaded_at'] ?? '');
                    $waitingHours = $uploadedAt !== '' ? max(0, (int) floor((time() - strtotime($uploadedAt)) / 3600)) : 0;
                    $isOverdueReview = $waitingHours >= 24;
                    ?>
                    <article id="bukti-<?= e((string) $item['bukti_id']) ?>"
                             class="<?= e($listArticleClass) ?>">
                        <div class="grid lg:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h3 class="font-heading text-lg text-foreground">
                                            <?= e((string) $item['pelanggan_nama']) ?>
                                            · <?= e((string) ($item['kucing_nama'] ?? '')) ?>
                                        </h3>
                                        <p class="mt-1 text-sm text-muted-foreground">
                                            <?php if ($isPerpanjangan): ?>
                                                <span class="inline-flex rounded-lg bg-warning-bg px-2 py-0.5 text-xs font-semibold text-amber-800 dark:text-amber-200 mr-1">Perpanjangan</span>
                                                <?= e((string) ($item['paket_nama'] ?? '')) ?>
                                                · +<?= e((string) ($item['perpanjangan_tambah_hari'] ?? $item['lama_hari'] ?? '')) ?> hari
                                            <?php else: ?>
                                                <span class="inline-flex rounded-lg bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary mr-1">Booking</span>
                                                <?= e((string) ($item['paket_nama'] ?? '')) ?>
                                                · <?= e((string) ($item['lama_hari'] ?? '')) ?> hari
                                            <?php endif; ?>
                                        </p>
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            <?php if ($isCheckInUrgent): ?>
                                                <span class="inline-flex rounded-lg bg-destructive/10 px-2 py-0.5 text-xs font-semibold text-destructive">
                                                    Check-in <?= $checkIn === $today ? 'hari ini' : 'besok' ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($isOverdueReview): ?>
                                                <span class="inline-flex rounded-lg bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-900 dark:bg-amber-950/50 dark:text-amber-200">
                                                    Menunggu <?= e((string) $waitingHours) ?> jam
                                                </span>
                                            <?php elseif ($waitingHours > 0): ?>
                                                <span class="inline-flex rounded-lg bg-muted px-2 py-0.5 text-xs font-semibold text-muted-foreground">
                                                    Upload <?= e((string) $waitingHours) ?> jam lalu
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <p class="font-heading text-xl text-primary">
                                        Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?>
                                    </p>
                                </div>

                                <div class="text-sm text-muted-foreground space-y-1">
                                    <?php if (!$isPerpanjangan): ?>
                                        <div>Check-in: <?= e(date('d/m/Y', strtotime($checkIn))) ?> — <?= e(date('d/m/Y', strtotime((string) $item['check_out']))) ?></div>
                                    <?php else: ?>
                                        <div>Check-out baru: <?= e(date('d/m/Y', strtotime((string) ($item['perpanjangan_check_out_baru'] ?? '')))) ?></div>
                                    <?php endif; ?>
                                    <?php if ($uploadedAt !== ''): ?>
                                        <div>Upload bukti: <?= e(date('d/m/Y H:i', strtotime($uploadedAt))) ?> WIB</div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['batas_waktu_bayar'])): ?>
                                        <div>Batas bayar: <?= e(date('d/m/Y H:i', strtotime((string) $item['batas_waktu_bayar']))) ?> WIB</div>
                                    <?php endif; ?>
                                </div>

                                <div class="<?= e($summaryCellClass) ?>">
                                    <div class="flex justify-between text-muted-foreground">
                                        <span>Subtotal layanan</span>
                                        <span>Rp <?= e(number_format((float) ($item['subtotal_layanan'] ?? 0), 0, ',', '.')) ?></span>
                                    </div>
                                    <?php if ((float) ($item['potongan_promo'] ?? 0) > 0): ?>
                                        <div class="flex justify-between text-muted-foreground">
                                            <span>Potongan promo</span>
                                            <span>- Rp <?= e(number_format((float) $item['potongan_promo'], 0, ',', '.')) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex justify-between text-muted-foreground">
                                        <span>Antar-jemput</span>
                                        <span>Rp <?= e(number_format((float) ($item['biaya_antar_jemput'] ?? 0), 0, ',', '.')) ?></span>
                                    </div>
                                    <div class="flex justify-between font-semibold text-foreground mt-2 pt-2 border-t border-border/80">
                                        <span>Total tagihan</span>
                                        <span>Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?></span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2 pt-2 border-t border-border/80">
                                    <form method="POST" action="/admin/penitipan/pembayaran/setujui"
                                          data-confirm="Setujui bukti transfer ini? Pembayaran akan ditandai lunas."
                                          data-loading-submit>
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                                        <button type="submit" class="<?= e($btnSuccess) ?>">Setujui Bukti</button>
                                    </form>
                                    <form method="POST" action="/admin/penitipan/pembayaran/tolak"
                                          class="flex flex-wrap items-center gap-2 flex-1 min-w-[16rem]"
                                          data-confirm="Tolak bukti transfer ini? Pelanggan perlu mengunggah ulang.">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                                        <input type="text" name="catatan" required minlength="10"
                                               placeholder="Catatan penolakan (min. 10 karakter)"
                                               class="<?= e($inputClass) ?> flex-1 min-w-[10rem]">
                                        <button type="submit" class="<?= e($btnDanger) ?>">Tolak</button>
                                    </form>
                                </div>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-foreground mb-2">Preview Bukti Transfer</p>
                                <?php
                                $fileUrl = (string) ($item['bukti_file_url'] ?? '');
                                $isPdf = str_ends_with(strtolower($fileUrl), '.pdf');
                                ?>
                                <?php if ($fileUrl === ''): ?>
                                    <p class="text-sm text-muted-foreground">File bukti tidak tersedia.</p>
                                <?php elseif ($isPdf): ?>
                                    <a href="<?= e($fileUrl) ?>" target="_blank" rel="noopener noreferrer"
                                       class="<?= e(design_cn(design_interactive('metricCellOutlined'), 'cursor-pointer flex items-center justify-center gap-2 p-6 text-sm font-semibold text-primary transition hover:bg-muted/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring')) ?>">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                        Buka PDF Bukti Transfer
                                    </a>
                                <?php else: ?>
                                    <img src="<?= e($fileUrl) ?>" alt="Bukti transfer"
                                         data-lightbox
                                         class="max-w-full cursor-pointer rounded-2xl border border-border shadow-sm transition hover:opacity-90">
                                    <p class="text-xs text-muted-foreground mt-2">Klik gambar untuk memperbesar</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
