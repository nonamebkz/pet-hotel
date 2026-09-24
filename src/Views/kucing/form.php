<?php

declare(strict_types=1);

use App\Core\Csrf;

$errors = $errors ?? [];
$kucing = $kucing ?? null;
$vaksinList = $vaksinList ?? [];
$action = $action ?? '/kucing';
$submitLabel = $submitLabel ?? 'Simpan';
$jenisKelaminLabels = $jenisKelaminLabels ?? [];
$isEdit = $kucing !== null && !empty($kucing['id']);
?>
<div class="<?= e(design_cn('max-w-3xl', ui_page_content_shell_classes(), design_page_layout('formLg'))) ?>">
    <div class="mb-6">
        <a href="/kucing" class="<?= e(ui_back_link_class()) ?>">&larr; Kembali ke daftar</a>
        <h1 class="text-2xl font-bold text-foreground mt-2"><?= $isEdit ? 'Edit Kucing' : 'Tambah Kucing' ?></h1>
    </div>

    <form method="POST" action="<?= e($action) ?>" enctype="multipart/form-data" data-stepper>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kucing['id']) ?>">
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-destructive text-sm mb-4"><?= e($errors['general']) ?></p>
        <?php endif; ?>

        <div class="flex items-center gap-2 mb-6 text-sm">
            <span data-step-indicator class="text-primary font-semibold">1. Identitas</span>
            <span class="text-border">→</span>
            <span data-step-indicator class="text-muted-foreground/70">2. Kesehatan</span>
            <span class="text-border">→</span>
            <span data-step-indicator class="text-muted-foreground/70">3. Vaksin</span>
        </div>

        <div class="<?= e(design_cn(design_surface('metric'), 'p-6 space-y-4')) ?>" data-step-panel>
            <h2 class="text-base font-semibold text-foreground">Identitas Kucing</h2>

            <div class="flex items-center gap-4">
                <?php if (!empty($kucing['foto_url'])): ?>
                    <img src="<?= e((string) $kucing['foto_url']) ?>" alt="Foto kucing"
                         class="w-16 h-16 rounded-lg object-cover border">
                <?php endif; ?>
                <div class="flex-1">
                    <label for="foto" class="block text-sm font-medium text-foreground mb-1">Foto Kucing (opsional)</label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                           class="w-full text-sm text-muted-foreground">
                    <?php if (!empty($errors['foto'])): ?>
                        <p class="text-destructive text-xs mt-1"><?= e($errors['foto']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block text-sm font-medium text-foreground mb-1">Nama Kucing</label>
                    <input type="text" id="nama" name="nama" required
                           value="<?= e((string) old('nama', $kucing['nama'] ?? '')) ?>"
                           class="w-full rounded-lg border border-border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                    <?php if (!empty($errors['nama'])): ?>
                        <p class="text-destructive text-xs mt-1"><?= e($errors['nama']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="jenis_kelamin" class="block text-sm font-medium text-foreground mb-1">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="w-full rounded-lg border border-border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">— Pilih —</option>
                        <?php foreach ($jenisKelaminLabels as $value => $label): ?>
                            <option value="<?= e($value) ?>"
                                <?= (string) old('jenis_kelamin', $kucing['jenis_kelamin'] ?? '') === $value ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['jenis_kelamin'])): ?>
                        <p class="text-destructive text-xs mt-1"><?= e($errors['jenis_kelamin']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="ras" class="block text-sm font-medium text-foreground mb-1">Ras</label>
                    <input type="text" id="ras" name="ras"
                           value="<?= e((string) old('ras', $kucing['ras'] ?? '')) ?>"
                           class="w-full rounded-lg border border-border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="tanggal_lahir" class="block text-sm font-medium text-foreground mb-1">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                           value="<?= e((string) old('tanggal_lahir', $kucing['tanggal_lahir'] ?? '')) ?>"
                           class="w-full rounded-lg border border-border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                    <?php if (!empty($errors['tanggal_lahir'])): ?>
                        <p class="text-destructive text-xs mt-1"><?= e($errors['tanggal_lahir']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" data-step-next
                        class="bg-primary text-primary-foreground rounded-lg px-4 py-2 text-sm font-medium hover:opacity-90">
                    Lanjut →
                </button>
            </div>
        </div>

        <div class="<?= e(design_cn(design_surface('metric'), 'p-6 space-y-4 hidden')) ?>" data-step-panel>
            <h2 class="text-base font-semibold text-foreground">Data Kesehatan</h2>

            <div>
                <label for="berat_badan" class="block text-sm font-medium text-foreground mb-1">Berat Badan (kg)</label>
                <input type="number" id="berat_badan" name="berat_badan" step="0.01" min="0"
                       value="<?= e((string) old('berat_badan', $kucing['berat_badan'] ?? '')) ?>"
                       class="w-full rounded-lg border border-border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                <?php if (!empty($errors['berat_badan'])): ?>
                    <p class="text-destructive text-xs mt-1"><?= e($errors['berat_badan']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="catatan_kesehatan" class="block text-sm font-medium text-foreground mb-1">Catatan Kesehatan / Alergi</label>
                <textarea id="catatan_kesehatan" name="catatan_kesehatan" rows="3"
                          class="w-full rounded-lg border border-border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary"
                          placeholder="Alergi makanan, kondisi khusus, dll."><?= e((string) old('catatan_kesehatan', $kucing['catatan_kesehatan'] ?? '')) ?></textarea>
            </div>

            <div class="flex justify-between pt-2">
                <button type="button" data-step-prev
                        class="border border-border text-foreground rounded-lg px-4 py-2 text-sm hover:bg-muted">
                    ← Kembali
                </button>
                <button type="button" data-step-next
                        class="bg-primary text-primary-foreground rounded-lg px-4 py-2 text-sm font-medium hover:opacity-90">
                    Lanjut →
                </button>
            </div>
        </div>

        <div class="<?= e(design_cn(design_surface('metric'), 'p-6 space-y-4 hidden')) ?>" data-step-panel>
            <button type="button"
                    data-collapsible-trigger="vaksin-section"
                    class="flex w-full items-center justify-between text-left">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Riwayat Vaksin (opsional)</h2>
                    <p class="text-xs text-muted-foreground mt-0.5">Syarat vaksin hanya divalidasi saat booking pet hotel.</p>
                </div>
                <span class="text-sm text-primary shrink-0 ml-4">Tampilkan/Sembunyikan</span>
            </button>

            <div id="vaksin-section">
                <div class="flex items-center justify-end mb-3">
                    <button type="button" id="add-vaksin-row"
                            class="text-sm text-primary hover:text-primary-hover font-medium">
                        + Tambah baris
                    </button>
                </div>
                <div id="vaksin-rows" class="space-y-2">
                    <?php if ($vaksinList === []): ?>
                        <?php $index = 0; require __DIR__ . '/../partials/vaksin-row.php'; ?>
                    <?php else: ?>
                        <?php foreach ($vaksinList as $index => $row): ?>
                            <?php require __DIR__ . '/../partials/vaksin-row.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex justify-between pt-2 border-t">
                <button type="button" data-step-prev
                        class="border border-border text-foreground rounded-lg px-4 py-2 text-sm hover:bg-muted">
                    ← Kembali
                </button>
                <button type="submit" class="bg-primary text-primary-foreground rounded-lg px-6 py-2 font-medium hover:opacity-90">
                    <?= e($submitLabel) ?>
                </button>
            </div>
        </div>
    </form>
</div>

<template id="vaksin-row-template">
    <?php $index = '__INDEX__'; $row = []; require __DIR__ . '/../partials/vaksin-row.php'; ?>
</template>

<script>
(function () {
    const container = document.getElementById('vaksin-rows');
    const template = document.getElementById('vaksin-row-template');
    const addBtn = document.getElementById('add-vaksin-row');
    let rowIndex = container.querySelectorAll('.vaksin-row').length;

    addBtn.addEventListener('click', function () {
        const html = template.innerHTML.replace(/__INDEX__/g, String(rowIndex++));
        container.insertAdjacentHTML('beforeend', html);
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-vaksin-row')) {
            const rows = container.querySelectorAll('.vaksin-row');
            if (rows.length > 1) {
                const row = e.target.closest('.vaksin-row');
                revokePreviewUrl(row);
                row.remove();
            }
        }
    });

    container.addEventListener('change', function (e) {
        if (!e.target.classList.contains('vaksin-sertifikat-input')) {
            return;
        }

        const row = e.target.closest('.vaksin-row');
        const previewEl = row?.querySelector('.sertifikat-preview-new');

        if (!previewEl) {
            return;
        }

        revokePreviewUrl(row);
        previewEl.innerHTML = '';
        previewEl.classList.add('hidden');

        const file = e.target.files?.[0];

        if (!file) {
            return;
        }

        const objectUrl = URL.createObjectURL(file);
        row.dataset.previewObjectUrl = objectUrl;

        if (file.type === 'application/pdf') {
            previewEl.innerHTML =
                '<p class="text-xs font-medium text-muted-foreground mb-1">Preview file baru</p>' +
                '<div class="flex items-center gap-2 rounded-lg border border-border bg-card px-3 py-2">' +
                '<span class="text-xs text-foreground truncate">' + escapeHtml(file.name) + '</span>' +
                '<a href="' + objectUrl + '" target="_blank" rel="noopener noreferrer" ' +
                'class="text-xs text-blue-600 hover:underline shrink-0">Buka PDF</a>' +
                '</div>';
        } else {
            previewEl.innerHTML =
                '<p class="text-xs font-medium text-muted-foreground mb-1">Preview file baru</p>' +
                '<a href="' + objectUrl + '" target="_blank" rel="noopener noreferrer">' +
                '<img src="' + objectUrl + '" alt="Preview sertifikat" ' +
                'class="max-h-32 max-w-full rounded-lg border object-contain">' +
                '</a>';
        }

        previewEl.classList.remove('hidden');
    });

    function revokePreviewUrl(row) {
        const url = row?.dataset.previewObjectUrl;

        if (url) {
            URL.revokeObjectURL(url);
            delete row.dataset.previewObjectUrl;
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;

        return div.innerHTML;
    }
})();
</script>
