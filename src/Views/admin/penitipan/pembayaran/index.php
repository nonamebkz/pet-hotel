<?php

declare(strict_types=1);

use App\Core\Csrf;

$pendingList = $pendingList ?? [];
$inputClass = 'rounded-xl border border-border bg-white px-3 py-2 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
$btnSuccess = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-success px-4 py-2 text-sm font-semibold text-white shadow-soft transition duration-soft hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-success';
$btnDanger = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition duration-soft hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400';
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-amber-400/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Penitipan</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Verifikasi Bukti</h1>
                <p class="mt-2 text-sm text-content-secondary">
                    Tinjau bukti transfer booking &amp; perpanjangan penitipan.
                </p>
            </div>
            <?php if ($pendingList !== []): ?>
                <span class="inline-flex items-center rounded-xl bg-warning-bg px-3 py-1.5 text-sm font-semibold text-amber-800 shadow-soft-inset">
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
        $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover';
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($pendingList as $item): ?>
                <article class="rounded-2xl border border-amber-200/80 bg-card p-5 sm:p-6 shadow-soft space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-heading text-lg text-content-primary"><?= e((string) $item['pelanggan_nama']) ?></h2>
                            <p class="mt-1 text-sm text-content-secondary">
                                <?php if (!empty($item['perpanjangan_penitipan_id'])): ?>
                                    <span class="inline-flex rounded-lg bg-warning-bg px-2 py-0.5 text-xs font-semibold text-amber-800 mr-1">Perpanjangan</span>
                                    Check-out baru: <?= e(date('d/m/Y', strtotime((string) ($item['perpanjangan_check_out_baru'] ?? '')))) ?>
                                <?php else: ?>
                                    <span class="inline-flex rounded-lg bg-admin-soft px-2 py-0.5 text-xs font-semibold text-admin mr-1">Booking</span>
                                    <?= e(date('d/m/Y', strtotime((string) $item['check_in']))) ?>
                                    — <?= e(date('d/m/Y', strtotime((string) $item['check_out']))) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <p class="font-heading text-xl text-admin">
                            Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?>
                        </p>
                    </div>

                    <?php if (!empty($item['bukti_file_url'])): ?>
                        <a href="<?= e((string) $item['bukti_file_url']) ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="cursor-pointer inline-flex items-center gap-1.5 text-sm font-semibold text-admin transition duration-soft hover:underline focus:outline-none focus-visible:underline">
                            Lihat bukti transfer
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        </a>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-2 pt-2 border-t border-border/80">
                        <form method="POST" action="/admin/penitipan/pembayaran/setujui" data-loading-submit>
                            <?= Csrf::field() ?>
                            <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                            <button type="submit" class="<?= e($btnSuccess) ?>">Setujui</button>
                        </form>
                        <form method="POST" action="/admin/penitipan/pembayaran/tolak" class="flex flex-wrap items-center gap-2 flex-1 min-w-[16rem]" data-confirm="Tolak bukti transfer ini?">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                            <input type="text" name="catatan" placeholder="Catatan penolakan" class="<?= e($inputClass) ?> flex-1 min-w-[10rem]">
                            <button type="submit" class="<?= e($btnDanger) ?>">Tolak</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
