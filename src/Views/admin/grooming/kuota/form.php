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
?>
<div>
    <div class="mb-6">
        <a href="/admin/grooming/kuota" class="text-sm text-gray-500 hover:text-admin">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= $isEdit ? 'Edit Kuota' : 'Tambah Kuota Grooming' ?></h1>
        <?php if (!$isEdit): ?>
            <p class="text-sm text-gray-500 mt-1">Pilih mode penjadwalan: satu hari, rentang tanggal, atau pola berulang.</p>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?= e($action) ?>" class="bg-white rounded-xl border p-6 max-w-xl space-y-5">
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kuota['id']) ?>">
        <?php else: ?>
            <input type="hidden" name="mode" id="kuota-mode" value="<?= e((string) $mode) ?>">

            <div class="flex gap-1 p-1 bg-gray-100 rounded-lg" role="tablist">
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
                            class="kuota-mode-tab flex-1 rounded-md px-3 py-2 text-sm font-medium transition <?= $isActive ? 'bg-white text-admin shadow-sm' : 'text-gray-500 hover:text-admin' ?>"
                            data-mode="<?= e($key) ?>"
                            role="tab"
                            aria-selected="<?= $isActive ? 'true' : 'false' ?>">
                        <?= e($label) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="kuota-mode-panel space-y-4" data-mode-panel="single" <?= $mode !== 'single' ? 'hidden' : '' ?>>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= e((string) old('tanggal', $tanggal)) ?>"
                           min="<?= e(date('Y-m-d')) ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['tanggal']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['tanggal'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['tanggal']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="kuota-mode-panel space-y-4" data-mode-panel="range" <?= $mode !== 'range' ? 'hidden' : '' ?>>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="<?= e((string) old('tanggal_mulai', $tanggal)) ?>"
                               min="<?= e(date('Y-m-d')) ?>"
                               class="w-full border rounded-lg px-3 py-2 text-sm border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" value="<?= e((string) old('tanggal_akhir', $tanggal)) ?>"
                               min="<?= e(date('Y-m-d')) ?>"
                               class="w-full border rounded-lg px-3 py-2 text-sm border-gray-300">
                    </div>
                </div>
                <?php if (!empty($errors['tanggal_mulai'])): ?>
                    <p class="text-xs text-red-600"><?= e((string) $errors['tanggal_mulai']) ?></p>
                <?php endif; ?>
                <p class="text-xs text-gray-500">Kuota dibuat untuk setiap hari dalam rentang. Tanggal yang sudah ada akan dilewati.</p>
            </div>

            <div class="kuota-mode-panel space-y-4" data-mode-panel="recurring" <?= $mode !== 'recurring' ? 'hidden' : '' ?>>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mulai Dari</label>
                    <input type="date" name="tanggal_mulai" value="<?= e((string) old('tanggal_mulai', $tanggal)) ?>"
                           min="<?= e(date('Y-m-d')) ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (minggu)</label>
                    <input type="number" name="minggu" min="1" max="12" value="<?= e((string) old('minggu', '4')) ?>"
                           class="w-full max-w-xs border rounded-lg px-3 py-2 text-sm border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hari Aktif</label>
                    <?php
                    $dayLabels = [1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'];
                    $oldDays = old('hari', []);
                    if (!is_array($oldDays)) {
                        $oldDays = [];
                    }
                    ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($dayLabels as $num => $label): ?>
                            <label class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-sm cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="hari[]" value="<?= $num ?>"
                                       class="rounded border-gray-300"
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
            <div class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                Tanggal: <strong><?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?></strong>
                · Terisi: <strong><?= (int) $kuota['slot_terisi'] ?></strong> slot
            </div>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slot Maksimal</label>
            <input type="number" name="slot_maksimal" min="1"
                   value="<?= e((string) ($kuota['slot_maksimal'] ?? old('slot_maksimal', '5'))) ?>"
                   class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['slot_maksimal']) ? 'border-red-400' : 'border-gray-300' ?>">
            <p class="text-xs text-gray-500 mt-1">Kapasitas maksimal grooming per hari. Rekomendasi: 3–10 slot.</p>
            <?php if (!empty($errors['slot_maksimal'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['slot_maksimal']) ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-red-600"><?= e((string) $errors['general']) ?></p>
        <?php endif; ?>

        <button type="submit" class="bg-admin text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-admin-hover">
            <?= e($submitLabel) ?>
        </button>
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
                t.classList.toggle('bg-white', active);
                t.classList.toggle('text-admin', active);
                t.classList.toggle('shadow-sm', active);
                t.classList.toggle('text-gray-500', !active);
                t.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            syncPanelInputs(mode);
        });
    });

    syncPanelInputs(modeInput ? modeInput.value : 'single');
})();
</script>
<?php endif; ?>
