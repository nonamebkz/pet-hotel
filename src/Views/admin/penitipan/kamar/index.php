<?php

declare(strict_types=1);

use App\Core\Csrf;

$kamarList = $kamarList ?? [];
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Penitipan</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Kamar Penitipan</h1>
                <p class="mt-2 text-sm text-content-secondary">Kelola kamar dan kapasitas harian penitipan.</p>
            </div>
            <a href="/admin/penitipan/kamar/tambah"
               class="cursor-pointer inline-flex items-center gap-2 rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Kamar
            </a>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <?php if ($kamarList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada kamar';
        $description = 'Tambahkan kamar sebelum mengatur kuota harian.';
        $ctaLabel = 'Tambah Kamar';
        $ctaHref = '/admin/penitipan/kamar/tambah';
        $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover';
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($kamarList as $k): ?>
                <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft space-y-4 transition duration-soft hover:shadow-soft-lg">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-heading text-lg text-content-primary"><?= e((string) $k['nama_kamar']) ?></h2>
                            <p class="mt-1 text-sm text-content-secondary">Kapasitas <?= (int) $k['kapasitas'] ?> slot</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= !empty($k['aktif']) ? 'bg-success-bg text-success' : 'bg-page text-content-secondary' ?>">
                            <?= !empty($k['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                    <div class="flex gap-2 pt-1 border-t border-border/80">
                        <a href="/admin/penitipan/kamar/edit?id=<?= e(urlencode((string) $k['id'])) ?>"
                           class="flex-1 cursor-pointer text-center rounded-xl border border-border bg-page/60 px-3 py-2 text-xs font-semibold text-admin hover:bg-admin-soft">Edit</a>
                        <form method="POST" action="/admin/penitipan/kamar/hapus" class="flex-1" data-confirm="Hapus kamar ini?">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $k['id']) ?>">
                            <button type="submit" class="w-full cursor-pointer rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
