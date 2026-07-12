<?php

declare(strict_types=1);

$layananList = $layananList ?? [];
$statusLabels = $statusLabels ?? [];
$btnPrimary = 'cursor-pointer inline-flex items-center gap-2 rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Pet Care</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Layanan Pet Care</h1>
                <p class="mt-2 text-sm text-content-secondary max-w-xl">
                    Kelola layanan konsultasi yang ditawarkan ke pelanggan.
                </p>
            </div>
            <a href="/admin/pet-care/layanan/tambah" class="<?= e($btnPrimary) ?>">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Layanan
            </a>
        </div>
    </section>

    <?php
    $activeTab = 'layanan';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if ($layananList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada layanan pet care';
        $description = 'Tambahkan layanan pet care agar pelanggan dapat melakukan booking konsultasi.';
        $ctaLabel = 'Tambah Layanan';
        $ctaHref = '/admin/pet-care/layanan/tambah';
        $ctaClass = $btnPrimary;
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="hidden md:block rounded-2xl border border-white/80 bg-card shadow-soft overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-admin-soft/40">
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Nama</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Harga</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Durasi</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/80">
                    <?php foreach ($layananList as $layanan): ?>
                        <?php $deleted = !empty($layanan['deleted_at']); ?>
                        <tr class="transition duration-soft hover:bg-admin-soft/30 <?= $deleted ? 'opacity-50' : '' ?>">
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-content-primary"><?= e((string) $layanan['nama']) ?></div>
                                <?php if (!empty($layanan['deskripsi'])): ?>
                                    <div class="text-xs text-content-secondary mt-0.5 line-clamp-1"><?= e((string) $layanan['deskripsi']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 font-medium text-admin whitespace-nowrap">
                                Rp <?= e(number_format((float) $layanan['harga'], 0, ',', '.')) ?>
                            </td>
                            <td class="px-4 py-3.5 text-content-primary"><?= (int) $layanan['estimasi_durasi_menit'] ?> menit</td>
                            <td class="px-4 py-3.5">
                                <?php if ($deleted): ?>
                                    <span class="text-xs px-2 py-0.5 rounded-lg font-semibold bg-page text-content-secondary">Dihapus</span>
                                <?php else: ?>
                                    <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= ($layanan['status'] ?? '') === 'AKTIF' ? 'bg-success-bg text-success' : 'bg-page text-content-secondary' ?>">
                                        <?= e($statusLabels[$layanan['status']] ?? (string) $layanan['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <?php if (!$deleted): ?>
                                    <?php
                                    $items = [
                                        [
                                            'type' => 'link',
                                            'label' => 'Edit',
                                            'href' => '/admin/pet-care/layanan/edit?id=' . urlencode((string) $layanan['id']),
                                        ],
                                        [
                                            'type' => 'form',
                                            'label' => 'Hapus',
                                            'formAction' => '/admin/pet-care/layanan/hapus',
                                            'formFields' => ['id' => (string) $layanan['id']],
                                            'confirm' => 'Nonaktifkan/hapus layanan "' . (string) $layanan['nama'] . '"?',
                                            'class' => 'text-red-600',
                                        ],
                                    ];
                                    require __DIR__ . '/../../../partials/ui/action-menu.php';
                                    ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            <?php foreach ($layananList as $layanan): ?>
                <?php $deleted = !empty($layanan['deleted_at']); ?>
                <article class="rounded-2xl border border-white/80 bg-card p-4 shadow-soft space-y-3 <?= $deleted ? 'opacity-50' : '' ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="font-semibold text-content-primary"><?= e((string) $layanan['nama']) ?></h2>
                            <p class="mt-1 font-heading text-admin">
                                Rp <?= e(number_format((float) $layanan['harga'], 0, ',', '.')) ?>
                            </p>
                            <p class="mt-0.5 text-xs text-content-secondary"><?= (int) $layanan['estimasi_durasi_menit'] ?> menit</p>
                        </div>
                        <?php if ($deleted): ?>
                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-lg font-semibold bg-page text-content-secondary">Dihapus</span>
                        <?php else: ?>
                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-lg font-semibold <?= ($layanan['status'] ?? '') === 'AKTIF' ? 'bg-success-bg text-success' : 'bg-page text-content-secondary' ?>">
                                <?= e($statusLabels[$layanan['status']] ?? (string) $layanan['status']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if (!$deleted): ?>
                        <div class="flex justify-end">
                            <?php
                            $items = [
                                [
                                    'type' => 'link',
                                    'label' => 'Edit',
                                    'href' => '/admin/pet-care/layanan/edit?id=' . urlencode((string) $layanan['id']),
                                ],
                                [
                                    'type' => 'form',
                                    'label' => 'Hapus',
                                    'formAction' => '/admin/pet-care/layanan/hapus',
                                    'formFields' => ['id' => (string) $layanan['id']],
                                    'confirm' => 'Nonaktifkan/hapus layanan "' . (string) $layanan['nama'] . '"?',
                                    'class' => 'text-red-600',
                                ],
                            ];
                            require __DIR__ . '/../../../partials/ui/action-menu.php';
                            ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
