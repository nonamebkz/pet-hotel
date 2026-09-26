<?php

declare(strict_types=1);

use App\Core\Csrf;

$paketList = $paketList ?? [];
$tableWrapClass = design_cn(design_surface('panel'), 'hidden md:block overflow-hidden');
$listArticleClass = design_cn(design_interactive('listArticle'), design_interactive('listArticleHover'), 'p-4 space-y-3');
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/admin/penitipan/paket/tambah" class="<?= e(design_cn(ui_btn_primary(), 'gap-2')) ?>">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Paket
    </a>
    <?php
    ui_page_header(
        'Paket Penitipan',
        'Penitipan · Administrasi',
        'Kelola paket harga per hari untuk layanan penitipan.',
        (string) ob_get_clean(),
    );
    ?>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <?php if ($paketList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada paket penitipan';
        $description = 'Tambahkan paket agar pelanggan dapat memilih layanan penitipan.';
        $ctaLabel = 'Tambah Paket';
        $ctaHref = '/admin/penitipan/paket/tambah';
        $ctaClass = ui_btn_primary();
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="<?= e($tableWrapClass) ?>">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Nama</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Harga/hari</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/80">
                    <?php foreach ($paketList as $p): ?>
                        <tr class="transition hover:bg-muted/50">
                            <td class="px-4 py-3.5 font-semibold text-foreground"><?= e((string) $p['nama']) ?></td>
                            <td class="px-4 py-3.5 font-medium text-primary">Rp <?= e(number_format((float) $p['harga_per_hari'], 0, ',', '.')) ?></td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex rounded-lg px-2 py-0.5 text-xs font-semibold <?= !empty($p['aktif']) ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : 'bg-muted text-muted-foreground' ?>">
                                    <?= !empty($p['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="/admin/penitipan/paket/edit?id=<?= e(urlencode((string) $p['id'])) ?>"
                                       class="cursor-pointer rounded-lg border border-border bg-muted/60 px-3 py-1.5 text-xs font-semibold text-primary transition hover:bg-muted">Edit</a>
                                    <form method="POST" action="/admin/penitipan/paket/hapus" data-confirm="Hapus paket ini?">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $p['id']) ?>">
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
            <?php foreach ($paketList as $p): ?>
                <article class="<?= e($listArticleClass) ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-foreground"><?= e((string) $p['nama']) ?></h2>
                            <p class="mt-1 font-heading text-primary">Rp <?= e(number_format((float) $p['harga_per_hari'], 0, ',', '.')) ?><span class="text-xs font-body text-muted-foreground"> /hari</span></p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= !empty($p['aktif']) ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : 'bg-muted text-muted-foreground' ?>">
                            <?= !empty($p['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <a href="/admin/penitipan/paket/edit?id=<?= e(urlencode((string) $p['id'])) ?>"
                           class="flex-1 cursor-pointer text-center rounded-lg border border-border bg-muted/60 px-3 py-2 text-xs font-semibold text-primary hover:bg-muted">Edit</a>
                        <form method="POST" action="/admin/penitipan/paket/hapus" class="flex-1" data-confirm="Hapus paket ini?">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $p['id']) ?>">
                            <button type="submit" class="w-full cursor-pointer rounded-lg border border-destructive/30 bg-destructive/10 px-3 py-2 text-xs font-semibold text-destructive">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
