<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuota = $kuota ?? null;
$kamarList = $kamarList ?? [];
$action = $action ?? '';
$isEdit = $kuota !== null && !empty($kuota['id']);
$inputClass = 'w-full rounded-xl border border-border bg-page/60 px-3.5 py-3 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:bg-white focus:outline-none focus:ring-2 focus:ring-admin/25';
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-content-secondary" aria-label="Breadcrumb">
        <a href="/admin/penitipan/kuota" class="cursor-pointer hover:text-admin focus:outline-none focus-visible:underline">Kuota</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-content-primary"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-content-primary"><?= $isEdit ? 'Edit kuota' : 'Tambah kuota' ?></h1>
        <p class="mt-2 text-sm text-content-secondary">
            <?= $isEdit ? 'Perbarui slot maksimal untuk tanggal yang sudah dibuat.' : 'Buat slot harian untuk kamar tertentu.' ?>
        </p>
    </header>

    <form method="POST" action="<?= e($action) ?>" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kuota['id']) ?>">
            <div class="rounded-xl border border-border bg-admin-soft/50 px-4 py-3 text-sm shadow-soft-inset">
                <p class="font-semibold text-admin"><?= e((string) ($kuota['nama_kamar'] ?? '')) ?></p>
                <p class="text-content-secondary mt-0.5"><?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?></p>
            </div>
        <?php else: ?>
            <div>
                <label for="kamar_penitipan_id" class="mb-1.5 block text-sm font-semibold text-content-primary">Kamar</label>
                <select id="kamar_penitipan_id" name="kamar_penitipan_id" required class="<?= e($inputClass) ?>">
                    <?php foreach ($kamarList as $k): ?>
                        <option value="<?= e((string) $k['id']) ?>"><?= e((string) $k['nama_kamar']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="tanggal" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" min="<?= e(date('Y-m-d')) ?>" required class="<?= e($inputClass) ?>">
            </div>
        <?php endif; ?>

        <div>
            <label for="slot_maksimal" class="mb-1.5 block text-sm font-semibold text-content-primary">Slot maksimal</label>
            <input type="number" id="slot_maksimal" name="slot_maksimal" min="0" required
                   value="<?= e((string) ($kuota['slot_maksimal'] ?? '')) ?>"
                   class="<?= e($inputClass) ?>">
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-5 py-3 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover disabled:opacity-60">Simpan</button>
            <a href="/admin/penitipan/kuota" class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-content-secondary hover:text-admin">Batal</a>
        </div>
    </form>
</div>
