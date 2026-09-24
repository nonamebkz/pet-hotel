<?php

declare(strict_types=1);

use App\Core\Csrf;

$booking = $booking ?? [];
$monitoringList = $monitoringList ?? [];
$lastMonitoring = $lastMonitoring ?? null;
$sisaHari = $sisaHari ?? null;
$canInput = $canInput ?? false;
$activeTab = $activeTab ?? ($canInput ? 'input' : 'riwayat');
$errors = $errors ?? [];
$bookingId = (string) ($booking['id'] ?? '');
$tabBaseUrl = '/admin/penitipan/monitoring/tambah?booking_id=' . urlencode($bookingId);
$heroClass = design_cn(design_surface('panel'), 'relative overflow-hidden p-6');
$contextAsideClass = design_cn(design_surface('panel'), design_advice_panel_surface('warning'), 'p-4 sm:p-5 space-y-3');
$formPanelClass = design_cn(design_surface('metric'), 'p-5 sm:p-6 space-y-5');
$listArticleClass = design_cn(design_interactive('listArticle'), 'p-4 space-y-2');
$emptyBoxClass = design_cn(design_surface('empty'), 'p-6 text-center text-sm text-muted-foreground');
$inputClass = 'w-full rounded-lg border border-input bg-background px-3.5 py-3 text-base sm:text-sm text-foreground transition hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
$tabActive = 'rounded-lg bg-card text-primary shadow-sm';
$tabInactive = 'text-muted-foreground hover:text-primary';
?>
<div class="font-body space-y-6 max-w-2xl">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-muted-foreground" aria-label="Breadcrumb">
        <a href="/admin/penitipan/booking" class="cursor-pointer hover:text-primary focus:outline-none focus-visible:underline">Booking</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-foreground">Monitoring</span>
    </nav>

    <section class="<?= e($heroClass) ?>">
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Monitoring harian</p>
            <h1 class="mt-1 font-heading text-2xl text-foreground"><?= e((string) ($booking['kucing_nama'] ?? '')) ?></h1>
            <p class="mt-1 text-sm text-muted-foreground">Pemilik: <?= e((string) ($booking['pelanggan_nama'] ?? '')) ?></p>
            <?php if (!$canInput): ?>
                <p class="mt-2 text-xs font-medium text-muted-foreground">Mode baca saja — booking sudah check-out.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <aside class="<?= e($contextAsideClass) ?>">
        <h2 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Konteks penitipan</h2>
        <dl class="grid gap-3 sm:grid-cols-2 text-sm">
            <div>
                <dt class="text-muted-foreground">Check-in / Check-out</dt>
                <dd class="font-semibold text-foreground">
                    <?= e(date('d/m/Y', strtotime((string) ($booking['check_in'] ?? '')))) ?>
                    → <?= e(date('d/m/Y', strtotime((string) ($booking['check_out'] ?? '')))) ?>
                </dd>
            </div>
            <?php if ($sisaHari !== null): ?>
                <div>
                    <dt class="text-muted-foreground">Sisa hari</dt>
                    <dd class="font-semibold text-foreground"><?= e((string) $sisaHari) ?> hari</dd>
                </div>
            <?php endif; ?>
            <?php if (!empty($booking['catatan_makan'])): ?>
                <div class="sm:col-span-2">
                    <dt class="text-muted-foreground">Catatan makan (booking)</dt>
                    <dd class="mt-0.5 text-foreground"><?= e((string) $booking['catatan_makan']) ?></dd>
                </div>
            <?php endif; ?>
            <?php if ($lastMonitoring !== null): ?>
                <div class="sm:col-span-2">
                    <dt class="text-muted-foreground">Monitoring terakhir</dt>
                    <dd class="mt-0.5 text-foreground">
                        <?= e(date('d/m/Y', strtotime((string) $lastMonitoring['tanggal']))) ?>
                        <?php if (!empty($lastMonitoring['kondisi'])): ?>
                            · <?= e((string) $lastMonitoring['kondisi']) ?>
                        <?php elseif (!empty($lastMonitoring['catatan_makan'])): ?>
                            · <?= e((string) $lastMonitoring['catatan_makan']) ?>
                        <?php endif; ?>
                    </dd>
                </div>
            <?php else: ?>
                <div class="sm:col-span-2">
                    <dt class="text-muted-foreground">Monitoring terakhir</dt>
                    <dd class="mt-0.5 text-muted-foreground">Belum ada laporan</dd>
                </div>
            <?php endif; ?>
        </dl>
    </aside>

    <?php if ($errors !== []): ?>
        <div class="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive space-y-1" role="alert">
            <?php foreach ($errors as $message): ?>
                <p><?= e((string) $message) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($canInput): ?>
        <div class="flex gap-1 p-1 bg-muted/60 rounded-xl" role="tablist" aria-label="Tab monitoring">
            <a href="<?= e($tabBaseUrl . '&tab=input') ?>"
               class="flex-1 px-3 py-2.5 text-sm font-semibold text-center transition <?= $activeTab === 'input' ? $tabActive : $tabInactive ?>"
               role="tab"
               aria-selected="<?= $activeTab === 'input' ? 'true' : 'false' ?>">
                Input Baru
            </a>
            <a href="<?= e($tabBaseUrl . '&tab=riwayat') ?>"
               class="flex-1 px-3 py-2.5 text-sm font-semibold text-center transition <?= $activeTab === 'riwayat' ? $tabActive : $tabInactive ?>"
               role="tab"
               aria-selected="<?= $activeTab === 'riwayat' ? 'true' : 'false' ?>">
                Riwayat<?= $monitoringList !== [] ? ' (' . count($monitoringList) . ')' : '' ?>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($canInput && $activeTab === 'input'): ?>
        <form method="POST" action="/admin/penitipan/monitoring/tambah" enctype="multipart/form-data"
              class="<?= e($formPanelClass) ?>" data-loading-submit>
            <?= Csrf::field() ?>
            <input type="hidden" name="booking_id" value="<?= e($bookingId) ?>">

            <div>
                <label for="tanggal" class="mb-1.5 block text-sm font-semibold text-foreground">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= e(date('Y-m-d')) ?>" required class="<?= e($inputClass) ?>">
            </div>

            <div>
                <label for="foto" class="mb-1.5 block text-sm font-semibold text-foreground">Foto <span class="font-normal text-muted-foreground">(opsional)</span></label>
                <input type="file" id="foto" name="foto" accept="image/*"
                       class="block w-full text-sm text-muted-foreground file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary hover:file:bg-primary/15">
            </div>

            <div>
                <label for="catatan_makan" class="mb-1.5 block text-sm font-semibold text-foreground">Catatan makan</label>
                <textarea id="catatan_makan" name="catatan_makan" rows="2" class="<?= e($inputClass) ?>" placeholder="Contoh: Makan pagi & sore normal"></textarea>
                <p class="mt-1 text-xs text-muted-foreground">Minimal isi satu field teks (makan, kondisi, atau aktivitas).</p>
            </div>

            <div>
                <label for="kondisi" class="mb-1.5 block text-sm font-semibold text-foreground">Kondisi</label>
                <textarea id="kondisi" name="kondisi" rows="2" class="<?= e($inputClass) ?>" placeholder="Kondisi kesehatan / mood"></textarea>
            </div>

            <div>
                <label for="aktivitas_harian" class="mb-1.5 block text-sm font-semibold text-foreground">Aktivitas harian</label>
                <textarea id="aktivitas_harian" name="aktivitas_harian" rows="2" class="<?= e($inputClass) ?>" placeholder="Bermain, istirahat, dll."></textarea>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'disabled:opacity-60')) ?>">
                    Simpan Monitoring
                </button>
                <a href="/admin/penitipan/booking" class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-muted-foreground hover:text-primary">Kembali</a>
            </div>
        </form>
    <?php endif; ?>

    <?php if (!$canInput || $activeTab === 'riwayat'): ?>
        <section class="space-y-3">
            <h2 class="font-heading text-lg text-foreground">Riwayat monitoring</h2>
            <?php if ($monitoringList === []): ?>
                <div class="<?= e($emptyBoxClass) ?>">
                    Belum ada laporan monitoring untuk booking ini.
                </div>
            <?php else: ?>
                <?php foreach ($monitoringList as $m): ?>
                    <article class="<?= e($listArticleClass) ?>">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-semibold text-foreground"><?= e(date('d/m/Y', strtotime((string) $m['tanggal']))) ?></h3>
                            <?php if (!empty($m['staff_nama'])): ?>
                                <span class="text-xs text-muted-foreground"><?= e((string) $m['staff_nama']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($m['foto_url'])): ?>
                            <img src="<?= e((string) $m['foto_url']) ?>" alt="Foto monitoring <?= e(date('d/m/Y', strtotime((string) $m['tanggal']))) ?>"
                                 class="mt-1 max-h-40 rounded-xl object-cover shadow-sm" data-lightbox>
                        <?php endif; ?>
                        <?php if (!empty($m['catatan_makan'])): ?>
                            <p class="text-sm text-muted-foreground"><span class="font-medium text-foreground">Makan:</span> <?= e((string) $m['catatan_makan']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($m['kondisi'])): ?>
                            <p class="text-sm text-muted-foreground"><span class="font-medium text-foreground">Kondisi:</span> <?= e((string) $m['kondisi']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($m['aktivitas_harian'])): ?>
                            <p class="text-sm text-muted-foreground"><span class="font-medium text-foreground">Aktivitas:</span> <?= e((string) $m['aktivitas_harian']) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!$canInput || $activeTab === 'riwayat'): ?>
                <a href="/admin/penitipan/booking" class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-muted-foreground hover:text-primary">Kembali ke Booking</a>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</div>
