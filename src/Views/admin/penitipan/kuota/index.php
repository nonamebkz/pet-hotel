<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuotaList = $kuotaList ?? [];
$kamarList = $kamarList ?? [];
$filterKamarId = $filterKamarId ?? '';
$hasFilter = $filterKamarId !== '';
$filterFormClass = design_cn(design_surface('metric'), 'p-5');
$tableWrapClass = design_cn(design_surface('panel'), 'hidden md:block overflow-hidden');
$listArticleClass = design_cn(design_interactive('listArticle'), design_interactive('listArticleHover'), 'p-4 space-y-3');
$inputClass = 'w-full rounded-lg border border-input bg-background px-3.5 py-2.5 text-base sm:text-sm text-foreground transition hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/admin/penitipan/kuota/tambah" class="<?= e(design_cn(ui_btn_primary(), 'gap-2')) ?>">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Kuota
    </a>
    <?php
    $actionsHtml = (string) ob_get_clean();
    ui_page_header(
        'Kuota Harian',
        'Penitipan · Administrasi',
        'Atur slot maksimal per kamar per tanggal.',
        $actionsHtml,
    );
    ?>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <form method="GET" action="/admin/penitipan/kuota" class="<?= e($filterFormClass) ?>">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="kamar_id" class="mb-1.5 block text-sm font-semibold text-foreground">Kamar</label>
                <select id="kamar_id" name="kamar_id" class="<?= e($inputClass) ?>">
                    <option value="">Semua kamar</option>
                    <?php foreach ($kamarList as $k): ?>
                        <option value="<?= e((string) $k['id']) ?>" <?= $filterKamarId === (string) $k['id'] ? 'selected' : '' ?>>
                            <?= e((string) $k['nama_kamar']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="<?= e(ui_btn_primary()) ?>">Filter</button>
                <?php if ($hasFilter): ?>
                    <a href="/admin/penitipan/kuota" class="cursor-pointer rounded-lg px-3 py-2.5 text-sm font-semibold text-muted-foreground hover:text-primary">Reset</a>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <?php if ($kuotaList === []): ?>
        <?php
        $variant = $hasFilter ? 'filtered' : 'empty';
        $title = $hasFilter ? 'Tidak ada kuota untuk kamar ini' : 'Belum ada kuota';
        $description = $hasFilter ? 'Coba pilih kamar lain atau reset filter.' : 'Tambahkan kuota harian agar pelanggan bisa booking.';
        $ctaLabel = $hasFilter ? 'Reset Filter' : 'Tambah Kuota';
        $ctaHref = $hasFilter ? '/admin/penitipan/kuota' : '/admin/penitipan/kuota/tambah';
        $ctaClass = ui_btn_primary();
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="<?= e($tableWrapClass) ?>">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Kamar</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Slot</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/80">
                    <?php foreach ($kuotaList as $q): ?>
                        <?php
                        $terisi = (int) $q['slot_terisi'];
                        $maks = (int) $q['slot_maksimal'];
                        $penuh = $maks > 0 && $terisi >= $maks;
                        $pct = $maks > 0 ? min(100, (int) round(($terisi / $maks) * 100)) : 0;
                        ?>
                        <tr class="transition hover:bg-muted/50">
                            <td class="px-4 py-3.5 font-semibold text-foreground"><?= e((string) $q['nama_kamar']) ?></td>
                            <td class="px-4 py-3.5 text-foreground"><?= e(date('d/m/Y', strtotime((string) $q['tanggal']))) ?></td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="font-medium <?= $penuh ? 'text-amber-700 dark:text-amber-300' : 'text-foreground' ?>"><?= $terisi ?> / <?= $maks ?></span>
                                    <div class="h-1.5 w-20 overflow-hidden rounded-full bg-muted">
                                        <div class="h-full rounded-full <?= $penuh ? 'bg-amber-500' : 'bg-emerald-500' ?>" style="width: <?= e((string) $pct) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="/admin/penitipan/kuota/edit?id=<?= e(urlencode((string) $q['id'])) ?>"
                                       class="cursor-pointer rounded-lg border border-border bg-muted/60 px-3 py-1.5 text-xs font-semibold text-primary hover:bg-muted">Edit</a>
                                    <form method="POST" action="/admin/penitipan/kuota/hapus" data-confirm="Hapus kuota ini?">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $q['id']) ?>">
                                        <button type="submit" class="cursor-pointer rounded-lg border border-destructive/30 bg-destructive/10 px-3 py-1.5 text-xs font-semibold text-destructive hover:bg-destructive/15">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            <?php foreach ($kuotaList as $q): ?>
                <?php
                $terisi = (int) $q['slot_terisi'];
                $maks = (int) $q['slot_maksimal'];
                ?>
                <article class="<?= e($listArticleClass) ?>">
                    <div>
                        <h2 class="font-semibold text-foreground"><?= e((string) $q['nama_kamar']) ?></h2>
                        <p class="text-sm text-muted-foreground mt-0.5"><?= e(date('d/m/Y', strtotime((string) $q['tanggal']))) ?> · <?= $terisi ?>/<?= $maks ?> slot</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="/admin/penitipan/kuota/edit?id=<?= e(urlencode((string) $q['id'])) ?>"
                           class="flex-1 cursor-pointer text-center rounded-lg border border-border bg-muted/60 px-3 py-2 text-xs font-semibold text-primary hover:bg-muted">Edit</a>
                        <form method="POST" action="/admin/penitipan/kuota/hapus" class="flex-1" data-confirm="Hapus kuota ini?">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $q['id']) ?>">
                            <button type="submit" class="w-full cursor-pointer rounded-lg border border-destructive/30 bg-destructive/10 px-3 py-2 text-xs font-semibold text-destructive">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
