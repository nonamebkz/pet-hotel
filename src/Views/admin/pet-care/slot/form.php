<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$tanggal = $tanggal ?? date('Y-m-d');
$errors = Session::getFlash('errors', []);

$inputClass = 'w-full rounded-xl border border-border bg-page/60 px-3.5 py-3 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:bg-white focus:outline-none focus:ring-2 focus:ring-admin/25';
$errorBorder = ' border-red-400 focus:border-red-400 focus:ring-red-200';
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-content-secondary" aria-label="Breadcrumb">
        <a href="/admin/pet-care/slot?tanggal=<?= e(urlencode($tanggal)) ?>"
           class="cursor-pointer hover:text-admin focus:outline-none focus-visible:underline">Slot Dokter</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-content-primary">Tambah</span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-content-primary">Tambah slot dokter</h1>
        <p class="mt-2 text-sm text-content-secondary">
            Buat slot waktu konsultasi. Maksimal 1 booking per slot (1 dokter).
        </p>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <?= e((string) $errors['general']) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e($action) ?>"
          class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5"
          data-loading-submit>
        <?= Csrf::field() ?>

        <div>
            <label for="tanggal" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" min="<?= e(date('Y-m-d')) ?>"
                   value="<?= e((string) old('tanggal', $tanggal)) ?>"
                   class="<?= e($inputClass . (!empty($errors['tanggal']) ? $errorBorder : '')) ?>">
            <?php if (!empty($errors['tanggal'])): ?>
                <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['tanggal']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="slot_waktu" class="mb-1.5 block text-sm font-semibold text-content-primary">Waktu Slot</label>
            <input type="time" id="slot_waktu" name="slot_waktu"
                   value="<?= e((string) old('slot_waktu', '09:00')) ?>"
                   class="<?= e($inputClass . (!empty($errors['slot_waktu']) ? $errorBorder : '')) ?>">
            <?php if (!empty($errors['slot_waktu'])): ?>
                <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['slot_waktu']) ?></p>
            <?php endif; ?>
            <p class="mt-1.5 text-xs text-content-secondary">Maksimal 1 booking per slot (1 dokter).</p>
        </div>

        <div class="flex flex-wrap gap-3 pt-1">
            <button type="submit"
                    class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-5 py-3 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin disabled:opacity-60">
                <?= e($submitLabel) ?>
            </button>
            <a href="/admin/pet-care/slot?tanggal=<?= e(urlencode($tanggal)) ?>"
               class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-content-secondary transition duration-soft hover:text-admin focus:outline-none focus-visible:underline">
                Batal
            </a>
        </div>
    </form>
</div>
