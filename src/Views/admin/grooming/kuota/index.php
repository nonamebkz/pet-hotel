<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuotaList = $kuotaList ?? [];
$btnPrimary = design_cn(ui_btn_primary(), 'gap-2');
$btnDanger = design_cn(
    ui_btn_secondary(),
    'border-destructive/30 bg-destructive/10 text-destructive hover:bg-destructive/15 disabled:cursor-not-allowed disabled:opacity-40',
);
?>
<div class="font-body space-y-6">
    <section class="<?= e(design_cn(design_surface('panel'), 'relative overflow-hidden p-6 sm:p-8')) ?>">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Grooming</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-foreground">Kuota Grooming</h1>
                <p class="mt-2 text-sm text-muted-foreground max-w-xl">
                    Atur kapasitas harian agar pelanggan dapat melakukan booking grooming.
                </p>
            </div>
            <a href="/admin/grooming/kuota/tambah" class="<?= e($btnPrimary) ?>">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Kuota
            </a>
        </div>
    </section>

    <?php
    $activeTab = 'kuota';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if ($kuotaList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada kuota grooming';
        $description = 'Atur kapasitas harian agar pelanggan dapat melakukan booking grooming.';
        $ctaLabel = 'Tambah Kuota';
        $ctaHref = '/admin/grooming/kuota/tambah';
        $ctaClass = $btnPrimary;
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <form method="POST" action="/admin/grooming/kuota/hapus-massal" id="kuota-bulk-form"
              data-confirm="Hapus kuota terpilih yang belum terisi?">
            <?= Csrf::field() ?>

            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <label class="inline-flex items-center gap-2 text-sm text-muted-foreground cursor-pointer">
                    <input type="checkbox" id="kuota-select-all"
                           class="rounded border-border text-primary focus:ring-ring/25">
                    Pilih semua yang dapat dihapus
                </label>
                <button type="submit" id="kuota-bulk-delete" disabled
                        class="<?= e($btnDanger) ?>">
                    Hapus Terpilih
                </button>
            </div>

            <div class="<?= e(design_cn(design_surface('panel'), 'overflow-hidden')) ?>">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border bg-muted/40">
                            <th class="w-10 px-4 py-3.5"></th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Tanggal</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Okupansi</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Terisi / Maks</th>
                            <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Sisa</th>
                            <th class="text-right px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/80">
                        <?php foreach ($kuotaList as $kuota): ?>
                            <?php
                            $maks = (int) $kuota['slot_maksimal'];
                            $terisi = (int) $kuota['slot_terisi'];
                            $sisa = $maks - $terisi;
                            $pct = $maks > 0 ? min(100, (int) round(($terisi / $maks) * 100)) : 0;
                            $barColor = $pct >= 90 ? 'bg-destructive' : ($pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                            $canDelete = $terisi === 0;
                            ?>
                            <tr class="<?= e(design_interactive('listRowHover')) ?> transition hover:bg-muted/30">
                                <td class="px-4 py-3.5">
                                    <?php if ($canDelete): ?>
                                        <input type="checkbox" name="ids[]" value="<?= e((string) $kuota['id']) ?>"
                                               class="kuota-row-check rounded border-border text-primary focus:ring-ring/25">
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-foreground">
                                    <?= e(date('d/m/Y', strtotime((string) $kuota['tanggal']))) ?>
                                </td>
                                <td class="px-4 py-3.5 min-w-[140px]">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 bg-muted rounded-full overflow-hidden">
                                            <div class="h-full rounded-full <?= e($barColor) ?>" style="width: <?= e((string) $pct) ?>%"></div>
                                        </div>
                                        <span class="text-xs text-muted-foreground w-8 text-right"><?= e((string) $pct) ?>%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-foreground"><?= $terisi ?> / <?= $maks ?></td>
                                <td class="px-4 py-3.5">
                                    <span class="<?= $sisa === 0 ? 'text-destructive font-semibold' : 'text-foreground' ?>">
                                        <?= $sisa ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <?php
                                    $items = [
                                        [
                                            'type' => 'link',
                                            'label' => 'Edit',
                                            'href' => '/admin/grooming/kuota/edit?id=' . urlencode((string) $kuota['id']),
                                        ],
                                    ];
                                    if ($canDelete) {
                                        $items[] = [
                                            'type' => 'form',
                                            'label' => 'Hapus',
                                            'formAction' => '/admin/grooming/kuota/hapus',
                                            'formFields' => ['id' => (string) $kuota['id']],
                                            'confirm' => 'Hapus kuota tanggal ' . date('d/m/Y', strtotime((string) $kuota['tanggal'])) . '?',
                                            'class' => 'text-destructive',
                                        ];
                                    }
                                    require __DIR__ . '/../../../partials/ui/action-menu.php';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <script>
        (function () {
            var selectAll = document.getElementById('kuota-select-all');
            var bulkBtn = document.getElementById('kuota-bulk-delete');
            var checks = document.querySelectorAll('.kuota-row-check');

            function updateBulkState() {
                var checked = document.querySelectorAll('.kuota-row-check:checked').length;
                if (bulkBtn) {
                    bulkBtn.disabled = checked === 0;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checks.forEach(function (cb) {
                        cb.checked = selectAll.checked;
                    });
                    updateBulkState();
                });
            }

            checks.forEach(function (cb) {
                cb.addEventListener('change', updateBulkState);
            });
        })();
        </script>
    <?php endif; ?>
</div>
