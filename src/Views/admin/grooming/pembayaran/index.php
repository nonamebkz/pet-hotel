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
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Grooming</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Verifikasi Bukti Transfer</h1>
                <p class="mt-2 text-sm text-content-secondary">
                    Tinjau bukti transfer booking grooming yang menunggu konfirmasi.
                </p>
            </div>
            <?php if ($pendingList !== []): ?>
                <span class="inline-flex items-center rounded-xl bg-warning-bg px-3 py-1.5 text-sm font-semibold text-amber-800 shadow-soft-inset">
                    <?= e((string) count($pendingList)) ?> menunggu
                </span>
            <?php endif; ?>
        </div>
    </section>

    <?php
    $activeTab = 'pembayaran';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if ($pendingList === []): ?>
        <?php
        $variant = 'success';
        $title = 'Semua bukti transfer sudah diproses';
        $description = 'Tidak ada bukti transfer grooming yang menunggu verifikasi saat ini.';
        $ctaLabel = 'Lihat riwayat transaksi';
        $ctaHref = '/admin/transaksi';
        $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover';
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($pendingList as $item): ?>
                <article class="rounded-2xl border border-amber-200/80 bg-card p-5 sm:p-6 shadow-soft">
                    <div class="grid lg:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h2 class="font-heading text-lg text-content-primary">
                                        <?= e((string) $item['pelanggan_nama']) ?>
                                    </h2>
                                    <p class="mt-1 text-sm text-content-secondary">
                                        <span class="inline-flex rounded-lg bg-warning-bg px-2 py-0.5 text-xs font-semibold text-amber-800 mr-1">Urgent</span>
                                        <?= e((string) $item['jenis_nama']) ?>
                                    </p>
                                </div>
                                <p class="font-heading text-xl text-admin">
                                    Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?>
                                </p>
                            </div>

                            <div class="text-sm text-content-secondary space-y-1">
                                <div>Tanggal grooming: <?= e(date('d/m/Y', strtotime((string) $item['booking_tanggal']))) ?></div>
                                <?php if (!empty($item['jam_grooming'])): ?>
                                    <div>Jam: <?= e(substr((string) $item['jam_grooming'], 0, 5)) ?> WIB</div>
                                <?php endif; ?>
                                <div>Upload: <?= e(date('d/m/Y H:i', strtotime((string) $item['bukti_uploaded_at']))) ?></div>
                            </div>

                            <div class="rounded-xl border border-border bg-admin-soft/40 p-3 text-sm shadow-soft-inset space-y-1">
                                <div class="flex justify-between text-content-secondary">
                                    <span>Subtotal</span>
                                    <span>Rp <?= e(number_format((float) $item['subtotal_layanan'], 0, ',', '.')) ?></span>
                                </div>
                                <div class="flex justify-between text-content-secondary">
                                    <span>Antar-jemput</span>
                                    <span>Rp <?= e(number_format((float) $item['biaya_antar_jemput'], 0, ',', '.')) ?></span>
                                </div>
                                <div class="flex justify-between font-semibold text-content-primary mt-2 pt-2 border-t border-border/80">
                                    <span>Total</span>
                                    <span>Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?></span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 pt-2 border-t border-border/80">
                                <form method="POST" action="/admin/grooming/pembayaran/setujui"
                                      data-confirm="Setujui bukti transfer ini? Booking akan ditandai lunas."
                                      data-loading-submit>
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                                    <button type="submit" class="<?= e($btnSuccess) ?>">
                                        Setujui Bukti
                                    </button>
                                </form>
                                <form method="POST" action="/admin/grooming/pembayaran/tolak"
                                      class="flex flex-wrap items-center gap-2 flex-1 min-w-[16rem]"
                                      data-confirm="Tolak bukti transfer ini? Pelanggan perlu mengunggah ulang.">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                                    <input type="text" name="catatan" required minlength="10"
                                           placeholder="Catatan penolakan (min. 10 karakter)"
                                           class="<?= e($inputClass) ?> flex-1 min-w-[10rem]">
                                    <button type="submit" class="<?= e($btnDanger) ?>">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-content-primary mb-2">Preview Bukti Transfer</p>
                            <?php
                            $fileUrl = (string) $item['bukti_file_url'];
                            $isPdf = str_ends_with(strtolower($fileUrl), '.pdf');
                            ?>
                            <?php if ($isPdf): ?>
                                <a href="<?= e($fileUrl) ?>" target="_blank" rel="noopener noreferrer"
                                   class="cursor-pointer flex items-center justify-center gap-2 rounded-2xl border border-border bg-admin-soft/40 p-6 text-sm font-semibold text-admin shadow-soft-inset transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    Buka PDF Bukti Transfer
                                </a>
                            <?php else: ?>
                                <img src="<?= e($fileUrl) ?>" alt="Bukti transfer"
                                     data-lightbox
                                     class="max-w-full rounded-2xl border border-border shadow-soft cursor-pointer transition duration-soft hover:opacity-90">
                                <p class="text-xs text-content-secondary mt-2">Klik gambar untuk memperbesar</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
