<?php

declare(strict_types=1);

use App\Core\Csrf;

$booking = $booking ?? [];
$monitoringList = $monitoringList ?? [];
$errors = $errors ?? [];
$inputClass = 'w-full rounded-xl border border-border bg-page/60 px-3.5 py-3 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:bg-white focus:outline-none focus:ring-2 focus:ring-admin/25';
?>
<div class="font-body space-y-6 max-w-2xl">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-content-secondary" aria-label="Breadcrumb">
        <a href="/admin/penitipan/booking" class="cursor-pointer hover:text-admin focus:outline-none focus-visible:underline">Booking</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-content-primary">Monitoring</span>
    </nav>

    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 shadow-soft">
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Monitoring harian</p>
            <h1 class="mt-1 font-heading text-2xl text-content-primary"><?= e((string) ($booking['kucing_nama'] ?? '')) ?></h1>
            <p class="mt-1 text-sm text-content-secondary">Pemilik: <?= e((string) ($booking['pelanggan_nama'] ?? '')) ?></p>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <?php if ($errors !== []): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 space-y-1" role="alert">
            <?php foreach ($errors as $message): ?>
                <p><?= e((string) $message) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/penitipan/monitoring/tambah" enctype="multipart/form-data"
          class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5" data-loading-submit>
        <?= Csrf::field() ?>
        <input type="hidden" name="booking_id" value="<?= e((string) ($booking['id'] ?? '')) ?>">

        <div>
            <label for="tanggal" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" value="<?= e(date('Y-m-d')) ?>" required class="<?= e($inputClass) ?>">
        </div>

        <div>
            <label for="foto" class="mb-1.5 block text-sm font-semibold text-content-primary">Foto <span class="font-normal text-content-secondary">(opsional)</span></label>
            <input type="file" id="foto" name="foto" accept="image/*"
                   class="block w-full text-sm text-content-secondary file:mr-3 file:cursor-pointer file:rounded-xl file:border-0 file:bg-admin-soft file:px-4 file:py-2 file:text-sm file:font-semibold file:text-admin hover:file:bg-admin/10">
        </div>

        <div>
            <label for="catatan_makan" class="mb-1.5 block text-sm font-semibold text-content-primary">Catatan makan</label>
            <textarea id="catatan_makan" name="catatan_makan" rows="2" class="<?= e($inputClass) ?>" placeholder="Contoh: Makan pagi & sore normal"></textarea>
        </div>

        <div>
            <label for="kondisi" class="mb-1.5 block text-sm font-semibold text-content-primary">Kondisi</label>
            <textarea id="kondisi" name="kondisi" rows="2" class="<?= e($inputClass) ?>" placeholder="Kondisi kesehatan / mood"></textarea>
        </div>

        <div>
            <label for="aktivitas_harian" class="mb-1.5 block text-sm font-semibold text-content-primary">Aktivitas harian</label>
            <textarea id="aktivitas_harian" name="aktivitas_harian" rows="2" class="<?= e($inputClass) ?>" placeholder="Bermain, istirahat, dll."></textarea>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-5 py-3 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover disabled:opacity-60">
                Simpan Monitoring
            </button>
            <a href="/admin/penitipan/booking" class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-content-secondary hover:text-admin">Kembali</a>
        </div>
    </form>

    <?php if ($monitoringList !== []): ?>
        <section class="space-y-3">
            <h2 class="font-heading text-lg text-content-primary">Riwayat monitoring</h2>
            <?php foreach ($monitoringList as $m): ?>
                <article class="rounded-2xl border border-white/80 bg-card p-4 shadow-soft space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-semibold text-content-primary"><?= e(date('d/m/Y', strtotime((string) $m['tanggal']))) ?></h3>
                        <?php if (!empty($m['staff_nama'])): ?>
                            <span class="text-xs text-content-secondary"><?= e((string) $m['staff_nama']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($m['foto_url'])): ?>
                        <img src="<?= e((string) $m['foto_url']) ?>" alt="Foto monitoring <?= e(date('d/m/Y', strtotime((string) $m['tanggal']))) ?>"
                             class="mt-1 max-h-40 rounded-xl object-cover shadow-soft" data-lightbox>
                    <?php endif; ?>
                    <?php if (!empty($m['catatan_makan'])): ?>
                        <p class="text-sm text-content-secondary"><span class="font-medium text-content-primary">Makan:</span> <?= e((string) $m['catatan_makan']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($m['kondisi'])): ?>
                        <p class="text-sm text-content-secondary"><span class="font-medium text-content-primary">Kondisi:</span> <?= e((string) $m['kondisi']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($m['aktivitas_harian'])): ?>
                        <p class="text-sm text-content-secondary"><span class="font-medium text-content-primary">Aktivitas:</span> <?= e((string) $m['aktivitas_harian']) ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</div>
