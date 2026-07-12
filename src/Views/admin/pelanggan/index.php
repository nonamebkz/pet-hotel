<?php

declare(strict_types=1);

$pelangganList = $pelangganList ?? [];
$search = $search ?? '';

$segmentBadges = [
    'baru' => 'bg-blue-100 text-blue-800',
    'aktif' => 'bg-success-bg text-success',
    'dormant' => 'bg-page text-content-secondary',
];
$segmentLabels = [
    'baru' => 'Baru',
    'aktif' => 'Aktif',
    'dormant' => 'Dormant',
];
$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-admin/5 blur-2xl" aria-hidden="true"></div>
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">CRM</p>
            <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Manajemen Pelanggan</h1>
            <p class="mt-2 text-sm text-content-secondary max-w-xl">
                Lihat daftar pelanggan terdaftar beserta profil dan data kucing (read-only).
            </p>
        </div>
    </section>

    <form method="GET" action="/admin/pelanggan" class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <label for="q" class="mb-1.5 block text-sm font-semibold text-content-primary">Cari</label>
                <input type="text" id="q" name="q" value="<?= e($search) ?>"
                       placeholder="Nama, email, atau telepon"
                       class="<?= e($inputClass) ?>">
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="w-full cursor-pointer rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
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
            $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover';
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
        <div class="hidden md:block rounded-2xl border border-white/80 bg-card shadow-soft overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-admin-soft/40">
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Nama</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Segment</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Email</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Telepon</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Kucing</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Terdaftar</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/80">
                    <?php foreach ($pelangganList as $pelanggan): ?>
                        <?php
                        $segment = (string) ($pelanggan['segment'] ?? 'aktif');
                        $badgeClass = $segmentBadges[$segment] ?? $segmentBadges['aktif'];
                        $segmentLabel = $segmentLabels[$segment] ?? ucfirst($segment);
                        ?>
                        <tr class="transition duration-soft hover:bg-admin-soft/30">
                            <td class="px-4 py-3.5 font-semibold text-content-primary"><?= e((string) $pelanggan['nama']) ?></td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= e($badgeClass) ?>"><?= e($segmentLabel) ?></span>
                            </td>
                            <td class="px-4 py-3.5 text-content-secondary"><?= e((string) $pelanggan['email']) ?></td>
                            <td class="px-4 py-3.5 text-content-secondary"><?= e((string) ($pelanggan['no_telepon'] ?? '—')) ?></td>
                            <td class="px-4 py-3.5 text-content-primary"><?= (int) ($pelanggan['jumlah_kucing'] ?? 0) ?></td>
                            <td class="px-4 py-3.5 text-content-secondary"><?= e(date('d M Y', strtotime((string) $pelanggan['created_at']))) ?></td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="/admin/pelanggan/detail?id=<?= e(urlencode((string) $pelanggan['id'])) ?>"
                                   class="cursor-pointer inline-flex rounded-xl border border-border bg-page/60 px-3 py-1.5 text-xs font-semibold text-admin transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
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
                <article class="rounded-2xl border border-white/80 bg-card p-4 shadow-soft space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-content-primary"><?= e((string) $pelanggan['nama']) ?></h2>
                            <p class="text-xs text-content-secondary mt-0.5"><?= e((string) $pelanggan['email']) ?></p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= e($badgeClass) ?>"><?= e($segmentLabel) ?></span>
                    </div>
                    <p class="text-sm text-content-secondary">
                        <?= e((string) ($pelanggan['no_telepon'] ?? '—')) ?>
                        · <?= (int) ($pelanggan['jumlah_kucing'] ?? 0) ?> kucing
                    </p>
                    <a href="/admin/pelanggan/detail?id=<?= e(urlencode((string) $pelanggan['id'])) ?>"
                       class="cursor-pointer flex w-full items-center justify-center rounded-xl bg-admin px-3 py-2 text-xs font-semibold text-white shadow-soft hover:bg-admin-hover">
                        Lihat detail
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
