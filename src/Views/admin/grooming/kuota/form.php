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
$formId = 'kuota-grooming-form';

$inputClass = static function (string $field) use ($errors): string {
    return ui_form_input_class(!empty($errors[$field]));
};
$tabActive = 'bg-card text-primary shadow-sm font-semibold';
$tabInactive = 'text-muted-foreground hover:text-foreground';
?>
<div class="<?= e(design_cn(ui_page_content_shell_classes(), design_page_layout('formSm'), 'pb-24 md:pb-0')) ?>">
    <?php
    ui_breadcrumb([
        ['label' => 'Kuota Grooming', 'href' => '/admin/grooming/kuota'],
        ['label' => $isEdit ? 'Edit' : 'Tambah'],
    ]);
    ui_page_header(
        $isEdit ? 'Edit kuota grooming' : 'Tambah kuota grooming',
        'Grooming · Administrasi',
        $isEdit
            ? 'Perbarui slot maksimal untuk tanggal yang sudah dibuat.'
            : 'Pilih mode penjadwalan: satu hari, rentang tanggal, atau pola berulang.',
    );
    ?>

    <form id="<?= e($formId) ?>" method="POST" action="<?= e($action) ?>" class="<?= e(design_cn(design_surface('panel'), 'space-y-5 p-5 sm:p-6')) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e((string) $kuota['id']) ?>">
        <?php else: ?>
            <input type="hidden" name="mode" id="kuota-mode" value="<?= e((string) $mode) ?>">

            <div class="flex gap-1 rounded-xl bg-muted/60 p-1" role="tablist">
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
                            class="kuota-mode-tab flex-1 rounded-lg px-3 py-2 text-sm transition <?= $isActive ? $tabActive : $tabInactive ?>"
                            data-mode="<?= e($key) ?>"
                            role="tab"
                            aria-selected="<?= $isActive ? 'true' : 'false' ?>">
                        <?= e($label) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="kuota-mode-panel space-y-4" data-mode-panel="single" <?= $mode !== 'single' ? 'hidden' : '' ?>>
                <div>
                    <label class="<?= e(ui_form_label_class()) ?>">Tanggal</label>
                    <input type="date" name="tanggal" value="<?= e((string) old('tanggal', $tanggal)) ?>"
                           min="<?= e(date('Y-m-d')) ?>"
                           class="<?= e($inputClass('tanggal')) ?>">
                    <?php if (!empty($errors['tanggal'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['tanggal']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="kuota-mode-panel space-y-4" data-mode-panel="range" <?= $mode !== 'range' ? 'hidden' : '' ?>>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="<?= e(ui_form_label_class()) ?>">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="<?= e((string) old('tanggal_mulai', $tanggal)) ?>"
                               min="<?= e(date('Y-m-d')) ?>"
                               class="<?= e(ui_form_input_class()) ?>">
                    </div>
                    <div>
                        <label class="<?= e(ui_form_label_class()) ?>">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" value="<?= e((string) old('tanggal_akhir', $tanggal)) ?>"
                               min="<?= e(date('Y-m-d')) ?>"
                               class="<?= e(ui_form_input_class()) ?>">
                    </div>
                </div>
                <?php if (!empty($errors['tanggal_mulai'])): ?>
                    <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['tanggal_mulai']) ?></p>
                <?php endif; ?>
                <p class="text-xs text-muted-foreground">Kuota dibuat untuk setiap hari dalam rentang. Tanggal yang sudah ada akan dilewati.</p>
            </div>

            <div class="kuota-mode-panel space-y-4" data-mode-panel="recurring" <?= $mode !== 'recurring' ? 'hidden' : '' ?>>
                <div>
                    <label class="<?= e(ui_form_label_class()) ?>">Mulai Dari</label>
                    <input type="date" name="tanggal_mulai" value="<?= e((string) old('tanggal_mulai', $tanggal)) ?>"
                           min="<?= e(date('Y-m-d')) ?>"
                           class="<?= e(ui_form_input_class()) ?>">
                </div>
                <div>
                    <label class="<?= e(ui_form_label_class()) ?>">Durasi (minggu)</label>
                    <input type="number" name="minggu" min="1" max="12" value="<?= e((string) old('minggu', '4')) ?>"
                           class="<?= e(design_cn(ui_form_input_class(), 'max-w-xs')) ?>">
                </div>
                <div>
                    <label class="<?= e(ui_form_label_class()) ?>">Hari Aktif</label>
                    <?php
                    $dayLabels = [1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'];
                    $oldDays = old('hari', []);
                    if (!is_array($oldDays)) {
                        $oldDays = [];
                    }
                    ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($dayLabels as $num => $label): ?>
                            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-xl border border-border bg-muted/30 px-3 py-1.5 text-sm text-foreground transition hover:border-primary/30 hover:bg-muted/50">
                                <input type="checkbox" name="hari[]" value="<?= $num ?>"
                                       class="rounded border-border text-primary focus:ring-ring/25"
                                       <?= in_array((string) $num, array_map('strval', $oldDays), true) ? 'checked' : '' ?>>
                                <?= e($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($errors['hari'])): ?>
                        <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['hari']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isEdit): ?>
            <div class="<?= e(design_cn(design_interactive('metricCellOutlined'), 'text-sm')) ?>">
                <p class="text-foreground">
                    Tanggal: <strong class="font-semibold text-primary"><?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?></strong>
                    · Terisi: <strong class="font-semibold text-primary"><?= (int) $kuota['slot_terisi'] ?></strong> slot
                </p>
            </div>
        <?php endif; ?>

        <div>
            <label class="<?= e(ui_form_label_class()) ?>">Slot Maksimal</label>
            <input type="number" name="slot_maksimal" min="1"
                   value="<?= e((string) ($kuota['slot_maksimal'] ?? old('slot_maksimal', '5'))) ?>"
                   class="<?= e($inputClass('slot_maksimal')) ?>">
            <p class="mt-1 text-xs text-muted-foreground">Kapasitas maksimal grooming per hari. Rekomendasi: 3–10 slot.</p>
            <?php if (!empty($errors['slot_maksimal'])): ?>
                <p class="<?= e(ui_field_error_class()) ?>"><?= e((string) $errors['slot_maksimal']) ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-destructive"><?= e((string) $errors['general']) ?></p>
        <?php endif; ?>

        <div class="hidden md:flex flex-wrap gap-3">
            <button type="submit" class="<?= e(ui_btn_primary()) ?> disabled:opacity-60">
                <?= e($submitLabel) ?>
            </button>
            <a href="/admin/grooming/kuota" class="<?= e(ui_btn_secondary()) ?>">
                Batal
            </a>
        </div>
    </form>

    <div class="<?= e(design_cn(design_surface('stickyBar'), 'fixed inset-x-0 bottom-[calc(5.5rem+env(safe-area-inset-bottom))] z-20 safe-bottom md:hidden')) ?>">
        <div class="flex gap-3 px-4 py-3">
            <a href="/admin/grooming/kuota" class="<?= e(design_cn(ui_btn_secondary(), 'flex-1 justify-center')) ?>">Batal</a>
            <button type="submit" form="<?= e($formId) ?>" class="<?= e(design_cn(ui_btn_primary(), 'flex-1 justify-center')) ?> disabled:opacity-60">
                <?= e($submitLabel) ?>
            </button>
        </div>
    </div>
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
                t.classList.toggle('text-primary', active);
                t.classList.toggle('shadow-sm', active);
                t.classList.toggle('font-semibold', active);
                t.classList.toggle('text-muted-foreground', !active);
                t.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            syncPanelInputs(mode);
        });
    });

    syncPanelInputs(modeInput ? modeInput.value : 'single');
})();
</script>
<?php endif; ?>
