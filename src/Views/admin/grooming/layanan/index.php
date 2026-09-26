<?php

declare(strict_types=1);

$jenisList = $jenisList ?? [];
$btnPrimary = design_cn(ui_btn_primary(), 'gap-2');
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/admin/grooming/layanan/tambah" class="<?= e($btnPrimary) ?>">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Jenis
    </a>
    <?php
    ui_page_header(
        'Jenis Layanan',
        'Grooming · Administrasi',
        'Kelola paket grooming yang ditawarkan ke pelanggan.',
        (string) ob_get_clean(),
    );
    ?>

    <?php $activeTab = 'layanan'; require __DIR__ . '/../_subnav.php'; ?>

    <?php if ($jenisList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada jenis grooming';
        $description = 'Tambahkan jenis layanan grooming agar pelanggan dapat melakukan booking.';
        $ctaLabel = 'Tambah Jenis';
        $ctaHref = '/admin/grooming/layanan/tambah';
        $ctaClass = $btnPrimary;
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="<?= e(design_cn(design_surface('panel'), 'hidden overflow-hidden md:block')) ?>">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/40">
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Nama</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Harga</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/80">
                    <?php foreach ($jenisList as $jenis): ?>
                        <tr class="<?= e(design_interactive('listRowHover')) ?> transition hover:bg-muted/30">
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-foreground"><?= e((string) $jenis['nama']) ?></div>
                                <?php if (!empty($jenis['deskripsi'])): ?>
                                    <div class="text-xs text-muted-foreground mt-0.5 line-clamp-1"><?= e((string) $jenis['deskripsi']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 font-medium text-primary">Rp <?= e(number_format((float) $jenis['harga'], 0, ',', '.')) ?></td>
                            <td class="px-4 py-3.5">
                                <span class="<?= e(design_status_badge(!empty($jenis['aktif']) ? 'success' : 'muted')) ?>">
                                    <?= !empty($jenis['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <?php
                                $items = [
                                    ['type' => 'link', 'label' => 'Edit', 'href' => '/admin/grooming/layanan/edit?id=' . urlencode((string) $jenis['id'])],
                                    ['type' => 'form', 'label' => 'Hapus', 'formAction' => '/admin/grooming/layanan/hapus', 'formFields' => ['id' => (string) $jenis['id']], 'confirm' => 'Hapus jenis grooming "' . (string) $jenis['nama'] . '"?', 'class' => 'text-destructive'],
                                ];
                                require __DIR__ . '/../../../partials/ui/action-menu.php';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            <?php foreach ($jenisList as $jenis): ?>
                <article class="<?= e(design_cn(design_interactive('listArticle'), 'space-y-3')) ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-foreground"><?= e((string) $jenis['nama']) ?></h2>
                            <p class="mt-1 font-heading text-primary">Rp <?= e(number_format((float) $jenis['harga'], 0, ',', '.')) ?></p>
                        </div>
                        <span class="<?= e(design_status_badge(!empty($jenis['aktif']) ? 'success' : 'muted')) ?>">
                            <?= !empty($jenis['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                    <div class="flex justify-end">
                        <?php
                        $items = [
                            ['type' => 'link', 'label' => 'Edit', 'href' => '/admin/grooming/layanan/edit?id=' . urlencode((string) $jenis['id'])],
                            ['type' => 'form', 'label' => 'Hapus', 'formAction' => '/admin/grooming/layanan/hapus', 'formFields' => ['id' => (string) $jenis['id']], 'confirm' => 'Hapus jenis grooming "' . (string) $jenis['nama'] . '"?', 'class' => 'text-destructive'],
                        ];
                        require __DIR__ . '/../../../partials/ui/action-menu.php';
                        ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
