<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$kuota = $kuota ?? null;
$action = $action ?? '';
$submitLabel = $submitLabel ?? 'Simpan';
$tanggal = $tanggal ?? date('Y-m-d');
$errors = Session::getFlash('errors', []);
$mode = old('mode', 'single');
$isEdit = $kuota !== null;
$inputClass = 'w-full rounded-xl border border-border bg-page/60 px-3.5 py-3 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:bg-white focus:outline-none focus:ring-2 focus:ring-admin/25';
$inputError = 'w-full rounded-xl border border-red-300 bg-page/60 px-3.5 py-3 text-sm text-content-primary shadow-soft-inset transition duration-soft focus:border-red-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-200';
$tabActive = 'bg-card text-admin shadow-soft';
$tabInactive = 'text-content-secondary hover:text-admin';
?>
<div class="font-body max-w-xl space-y-6">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-content-secondary" aria-label="Breadcrumb">
        <a href="/admin/grooming/kuota" class="cursor-pointer hover:text-admin focus:outline-none focus-visible:underline">Kuota</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-content-primary"><?= $isEdit ? 'Edit' : 'Tambah' ?></span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-content-primary"><?= $isEdit ? 'Edit Kuota' : 'Tambah Kuota Grooming' ?></h1>
        <?php if (!$isEdit): ?>
            <p class="mt-2 text-sm text-content-secondary">Pilih mode penjadwalan: satu hari, rentang tanggal, atau pola berulang.</p>
        <?php else: ?>
            <p class="mt-2 text-sm text-content-secondary">Perbarui slot maksimal untuk tanggal yang sudah dibuat.</p>
        <?php endif; ?>
    </header>

    <form method="POST" action="<?= e($action) ?>" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kuota['id']) ?>">
        <?php else: ?>
            <input type="hidden" name="mode" id="kuota-mode" value="<?= e((string) $mode) ?>">

            <div class="flex gap-1 p-1 bg-admin-soft/60 rounded-xl" role="tablist">
                <?php
                $modes = [
                    'single' => 'Satu Hari',
                    'range' => 'Rentang',
                    'recurring' => 'Berulang',
                ];
                foreach ($modes as $key => $label):
                    $isActive = $mode === $key;
                ?>
                    <button type="button"
                            class="kuota-mode-tab flex-1 rounded-lg px-3 py-2 text-sm font-semibold transition duration-soft <?= $isActive ? $tabActive : $tabInactive ?>"
                            data-mode="<?= e($key) ?>"
                            role="tab"
                            aria-selected="<?= $isActive ? 'true' : 'false' ?>">
                        <?= e($label) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="kuota-mode-panel space-y-4" data-mode-panel="single" <?= $mode !== 'single' ? 'hidden' : '' ?>>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= e((string) old('tanggal', $tanggal)) ?>"
                           min="<?= e(date('Y-m-d')) ?>"
                           class="<?= !empty($errors['tanggal']) ? e($inputError) : e($inputClass) ?>">
                    <?php if (!empty($errors['tanggal'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['tanggal']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="kuota-mode-panel space-y-4" data-mode-panel="range" <?= $mode !== 'range' ? 'hidden' : '' ?>>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="<?= e((string) old('tanggal_mulai', $tanggal)) ?>"
                               min="<?= e(date('Y-m-d')) ?>"
                               class="<?= e($inputClass) ?>">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" value="<?= e((string) old('tanggal_akhir', $tanggal)) ?>"
                               min="<?= e(date('Y-m-d')) ?>"
                               class="<?= e($inputClass) ?>">
                    </div>
                </div>
                <?php if (!empty($errors['tanggal_mulai'])): ?>
                    <p class="text-xs text-red-600"><?= e((string) $errors['tanggal_mulai']) ?></p>
                <?php endif; ?>
                <p class="text-xs text-content-secondary">Kuota dibuat untuk setiap hari dalam rentang. Tanggal yang sudah ada akan dilewati.</p>
            </div>

            <div class="kuota-mode-panel space-y-4" data-mode-panel="recurring" <?= $mode !== 'recurring' ? 'hidden' : '' ?>>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-content-primary">Mulai Dari</label>
                    <input type="date" name="tanggal_mulai" value="<?= e((string) old('tanggal_mulai', $tanggal)) ?>"
                           min="<?= e(date('Y-m-d')) ?>"
                           class="<?= e($inputClass) ?>">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-content-primary">Durasi (minggu)</label>
                    <input type="number" name="minggu" min="1" max="12" value="<?= e((string) old('minggu', '4')) ?>"
                           class="<?= e($inputClass) ?> max-w-xs">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-content-primary">Hari Aktif</label>
                    <?php
                    $dayLabels = [1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'];
                    $oldDays = old('hari', []);
                    if (!is_array($oldDays)) {
                        $oldDays = [];
                    }
                    ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($dayLabels as $num => $label): ?>
                            <label class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-page/60 px-3 py-1.5 text-sm text-content-primary cursor-pointer shadow-soft-inset transition duration-soft hover:bg-admin-soft hover:border-admin/30">
                                <input type="checkbox" name="hari[]" value="<?= $num ?>"
                                       class="rounded border-border text-admin focus:ring-admin/25"
                                       <?= in_array((string) $num, array_map('strval', $oldDays), true) ? 'checked' : '' ?>>
                                <?= e($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($errors['hari'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['hari']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isEdit): ?>
            <div class="rounded-xl border border-border bg-admin-soft/50 px-4 py-3 text-sm shadow-soft-inset">
                <p class="text-content-primary">
                    Tanggal: <strong class="font-semibold text-admin"><?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?></strong>
                    · Terisi: <strong class="font-semibold text-admin"><?= (int) $kuota['slot_terisi'] ?></strong> slot
                </p>
            </div>
        <?php endif; ?>

        <div>
            <label class="mb-1.5 block text-sm font-semibold text-content-primary">Slot Maksimal</label>
            <input type="number" name="slot_maksimal" min="1"
                   value="<?= e((string) ($kuota['slot_maksimal'] ?? old('slot_maksimal', '5'))) ?>"
                   class="<?= !empty($errors['slot_maksimal']) ? e($inputError) : e($inputClass) ?>">
            <p class="text-xs text-content-secondary mt-1">Kapasitas maksimal grooming per hari. Rekomendasi: 3–10 slot.</p>
            <?php if (!empty($errors['slot_maksimal'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['slot_maksimal']) ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-red-600"><?= e((string) $errors['general']) ?></p>
        <?php endif; ?>

        <div class="flex flex-wrap gap-3">
            <button type="submit"
                    class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-5 py-3 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin disabled:opacity-60">
                <?= e($submitLabel) ?>
            </button>
            <a href="/admin/grooming/kuota"
               class="cursor-pointer inline-flex items-center px-4 py-3 text-sm font-semibold text-content-secondary transition duration-soft hover:text-admin focus:outline-none focus-visible:underline">
                Batal
            </a>
        </div>
    </form>
</div>

<?php if (!$isEdit): ?>
<script>
(function () {
    var modeInput = document.getElementById('kuota-mode');
    var tabs = document.querySelectorAll('.kuota-mode-tab');
    var panels = document.querySelectorAll('.kuota-mode-panel');

    function syncPanelInputs(mode) {
        panels.forEach(function (panel) {
            var isActive = panel.getAttribute('data-mode-panel') === mode;
            panel.hidden = !isActive;
            panel.querySelectorAll('input, select, textarea').forEach(function (input) {
                input.disabled = !isActive;
            });
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var mode = tab.getAttribute('data-mode');
            if (!mode || !modeInput) {
                return;
            }
            modeInput.value = mode;
            tabs.forEach(function (t) {
                var active = t === tab;
                t.classList.toggle('bg-card', active);
                t.classList.toggle('text-admin', active);
                t.classList.toggle('shadow-soft', active);
                t.classList.toggle('text-content-secondary', !active);
                t.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            syncPanelInputs(mode);
        });
    });

    syncPanelInputs(modeInput ? modeInput.value : 'single');
})();
</script>
<?php endif; ?>
