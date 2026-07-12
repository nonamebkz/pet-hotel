<?php

declare(strict_types=1);

use App\Core\Csrf;

$paketList = $paketList ?? [];
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Penitipan</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Paket Penitipan</h1>
                <p class="mt-2 text-sm text-content-secondary">Kelola paket harga per hari untuk layanan penitipan.</p>
            </div>
            <a href="/admin/penitipan/paket/tambah"
               class="cursor-pointer inline-flex items-center gap-2 rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Paket
            </a>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <?php if ($paketList === []): ?>
        <?php
        $variant = 'empty';
        $title = 'Belum ada paket penitipan';
        $description = 'Tambahkan paket agar pelanggan dapat memilih layanan penitipan.';
        $ctaLabel = 'Tambah Paket';
        $ctaHref = '/admin/penitipan/paket/tambah';
        $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover';
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="hidden md:block rounded-2xl border border-white/80 bg-card shadow-soft overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-admin-soft/40">
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Nama</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Harga/hari</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/80">
                    <?php foreach ($paketList as $p): ?>
                        <tr class="transition duration-soft hover:bg-admin-soft/30">
                            <td class="px-4 py-3.5 font-semibold text-content-primary"><?= e((string) $p['nama']) ?></td>
                            <td class="px-4 py-3.5 text-admin font-medium">Rp <?= e(number_format((float) $p['harga_per_hari'], 0, ',', '.')) ?></td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex rounded-lg px-2 py-0.5 text-xs font-semibold <?= !empty($p['aktif']) ? 'bg-success-bg text-success' : 'bg-page text-content-secondary' ?>">
                                    <?= !empty($p['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="/admin/penitipan/paket/edit?id=<?= e(urlencode((string) $p['id'])) ?>"
                                       class="cursor-pointer rounded-xl border border-border bg-page/60 px-3 py-1.5 text-xs font-semibold text-admin transition duration-soft hover:bg-admin-soft">Edit</a>
                                    <form method="POST" action="/admin/penitipan/paket/hapus" data-confirm="Hapus paket ini?">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $p['id']) ?>">
                                        <button type="submit" class="cursor-pointer rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">Hapus</button>
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
                <article class="rounded-2xl border border-white/80 bg-card p-4 shadow-soft space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-content-primary"><?= e((string) $p['nama']) ?></h2>
                            <p class="mt-1 font-heading text-admin">Rp <?= e(number_format((float) $p['harga_per_hari'], 0, ',', '.')) ?><span class="text-xs font-body text-content-secondary"> /hari</span></p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= !empty($p['aktif']) ? 'bg-success-bg text-success' : 'bg-page text-content-secondary' ?>">
                            <?= !empty($p['aktif']) ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <a href="/admin/penitipan/paket/edit?id=<?= e(urlencode((string) $p['id'])) ?>"
                           class="flex-1 cursor-pointer text-center rounded-xl border border-border bg-page/60 px-3 py-2 text-xs font-semibold text-admin hover:bg-admin-soft">Edit</a>
                        <form method="POST" action="/admin/penitipan/paket/hapus" class="flex-1" data-confirm="Hapus paket ini?">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $p['id']) ?>">
                            <button type="submit" class="w-full cursor-pointer rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
