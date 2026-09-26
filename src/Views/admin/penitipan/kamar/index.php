<?php

declare(strict_types=1);

use App\Core\Csrf;

$kamarList = $kamarList ?? [];
$listArticleClass = design_cn(design_interactive('listArticle'), design_interactive('listArticleHover'), 'p-5 space-y-4');
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/admin/penitipan/kamar/tambah" class="<?= e(design_cn(ui_btn_primary(), 'gap-2')) ?>">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Kamar
    </a>
    <?php
    ui_page_header(
        'Kamar Penitipan',
        'Penitipan · Administrasi',
        'Kelola kamar dan kapasitas harian penitipan.',
        (string) ob_get_clean(),
    );
    ?>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <?php if ($kamarList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada kamar';
        $description = 'Tambahkan kamar sebelum mengatur kuota harian.';
        $ctaLabel = 'Tambah Kamar';
        $ctaHref = '/admin/penitipan/kamar/tambah';
        $ctaClass = ui_btn_primary();
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($kamarList as $k): ?>
                <article class="<?= e($listArticleClass) ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-heading text-lg text-foreground"><?= e((string) $k['nama_kamar']) ?></h2>
                            <p class="mt-1 text-sm text-muted-foreground">Kapasitas <?= (int) $k['kapasitas'] ?> slot</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= !empty($k['aktif']) ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : 'bg-muted text-muted-foreground' ?>">
                            <?= !empty($k['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                    <div class="flex gap-2 pt-1 border-t border-border/80">
                        <a href="/admin/penitipan/kamar/edit?id=<?= e(urlencode((string) $k['id'])) ?>"
                           class="flex-1 cursor-pointer text-center rounded-lg border border-border bg-muted/60 px-3 py-2 text-xs font-semibold text-primary hover:bg-muted">Edit</a>
                        <form method="POST" action="/admin/penitipan/kamar/hapus" class="flex-1" data-confirm="Hapus kamar ini?">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $k['id']) ?>">
                            <button type="submit" class="w-full cursor-pointer rounded-lg border border-destructive/30 bg-destructive/10 px-3 py-2 text-xs font-semibold text-destructive hover:bg-destructive/15">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
