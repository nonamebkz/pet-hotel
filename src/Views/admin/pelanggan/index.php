<?php

declare(strict_types=1);

$pelangganList = $pelangganList ?? [];
$search = $search ?? '';

$btnPrimary = design_cn(ui_btn_primary(), 'gap-2');
$btnSecondary = ui_btn_secondary();
$inputClass = design_cn(
    'w-full rounded-xl border border-input bg-background px-3.5 py-2.5 text-base sm:text-sm text-foreground transition',
    'hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25',
);

$segmentBadges = [
    'baru' => design_status_badge('muted') . ' !bg-primary/10 !text-primary',
    'aktif' => design_status_badge('success'),
    'dormant' => design_status_badge('muted'),
];
$segmentLabels = [
    'baru' => 'Baru',
    'aktif' => 'Aktif',
    'dormant' => 'Dormant',
];
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Manajemen Pelanggan',
        'Administrasi · CRM',
        'Daftar pelanggan terdaftar beserta profil dan data kucing (read-only).',
    );
    ?>

    <form method="GET" action="/admin/pelanggan" class="<?= e(design_surface('metric')) ?>">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <label for="q" class="mb-1.5 block text-sm font-semibold text-foreground">Cari</label>
                <input type="text" id="q" name="q" value="<?= e($search) ?>"
                       placeholder="Nama, email, atau telepon"
                       class="<?= e($inputClass) ?>">
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="<?= e(design_cn($btnPrimary, 'w-full')) ?>">
                    Cari
                </button>
            </div>
        </div>
    </form>

    <?php if ($search !== ''): ?>
        <?php
        ui_filter_chips(
            [['label' => 'Pencarian: ' . $search, 'removeHref' => '/admin/pelanggan']],
            '/admin/pelanggan',
            count($pelangganList),
            'pelanggan',
        );
        ?>
    <?php endif; ?>

    <?php if ($pelangganList === []): ?>
        <?php
        if ($search !== '') {
            $variant = 'filtered';
            $title = 'Tidak ditemukan pelanggan untuk pencarian ini';
            $description = 'Coba kata kunci lain atau reset filter.';
            $ctaLabel = 'Reset Filter';
            $ctaHref = '/admin/pelanggan';
            $ctaClass = $btnPrimary;
        } else {
            $variant = 'empty';
            $title = 'Belum ada pelanggan terdaftar';
            $description = 'Pelanggan yang mendaftar akan muncul di halaman ini.';
            $ctaLabel = null;
            $ctaHref = null;
        }
        require __DIR__ . '/../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="<?= e(design_cn(design_surface('panel'), 'hidden overflow-hidden md:block')) ?>">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Nama</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Segment</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Email</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Telepon</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Kucing</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Terdaftar</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/80">
                    <?php foreach ($pelangganList as $pelanggan): ?>
                        <?php
                        $segment = (string) ($pelanggan['segment'] ?? 'aktif');
                        $badgeClass = $segmentBadges[$segment] ?? $segmentBadges['aktif'];
                        $segmentLabel = $segmentLabels[$segment] ?? ucfirst($segment);
                        ?>
                        <tr class="transition transition hover:bg-muted/50">
                            <td class="px-4 py-3.5 font-semibold text-foreground"><?= e((string) $pelanggan['nama']) ?></td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= e($badgeClass) ?>"><?= e($segmentLabel) ?></span>
                            </td>
                            <td class="px-4 py-3.5 text-muted-foreground"><?= e((string) $pelanggan['email']) ?></td>
                            <td class="px-4 py-3.5 text-muted-foreground"><?= e((string) ($pelanggan['no_telepon'] ?? '—')) ?></td>
                            <td class="px-4 py-3.5 text-foreground"><?= (int) ($pelanggan['jumlah_kucing'] ?? 0) ?></td>
                            <td class="px-4 py-3.5 text-muted-foreground"><?= e(date('d M Y', strtotime((string) $pelanggan['created_at']))) ?></td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="/admin/pelanggan/detail?id=<?= e(urlencode((string) $pelanggan['id'])) ?>"
                                   class="<?= e(design_cn(ui_btn_secondary(), 'px-3 py-1.5 text-xs')) ?>">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            <?php foreach ($pelangganList as $pelanggan): ?>
                <?php
                $segment = (string) ($pelanggan['segment'] ?? 'aktif');
                $badgeClass = $segmentBadges[$segment] ?? $segmentBadges['aktif'];
                $segmentLabel = $segmentLabels[$segment] ?? ucfirst($segment);
                ?>
                <article class="<?= e(design_cn(design_interactive('listArticle'), 'space-y-3')) ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-foreground"><?= e((string) $pelanggan['nama']) ?></h2>
                            <p class="text-xs text-muted-foreground mt-0.5"><?= e((string) $pelanggan['email']) ?></p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= e($badgeClass) ?>"><?= e($segmentLabel) ?></span>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        <?= e((string) ($pelanggan['no_telepon'] ?? '—')) ?>
                        · <?= (int) ($pelanggan['jumlah_kucing'] ?? 0) ?> kucing
                    </p>
                    <a href="/admin/pelanggan/detail?id=<?= e(urlencode((string) $pelanggan['id'])) ?>"
                       class="<?= e(design_cn($btnPrimary, 'flex w-full text-xs')) ?>">
                        Lihat detail
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
