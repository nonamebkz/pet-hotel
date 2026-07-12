<?php

declare(strict_types=1);

use App\Core\Csrf;

$kuotaList = $kuotaList ?? [];
$kamarList = $kamarList ?? [];
$filterKamarId = $filterKamarId ?? '';
$hasFilter = $filterKamarId !== '';
$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Penitipan</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Kuota Harian</h1>
                <p class="mt-2 text-sm text-content-secondary">Atur slot maksimal per kamar per tanggal.</p>
            </div>
            <a href="/admin/penitipan/kuota/tambah"
               class="cursor-pointer inline-flex items-center gap-2 rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Kuota
            </a>
        </div>
    </section>

    <?php require __DIR__ . '/../_nav.php'; ?>

    <form method="GET" action="/admin/penitipan/kuota" class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label for="kamar_id" class="mb-1.5 block text-sm font-semibold text-content-primary">Kamar</label>
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
                <button type="submit" class="cursor-pointer rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover">Filter</button>
                <?php if ($hasFilter): ?>
                    <a href="/admin/penitipan/kuota" class="cursor-pointer rounded-xl px-3 py-2.5 text-sm font-semibold text-content-secondary hover:text-admin">Reset</a>
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
        $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft hover:bg-admin-hover';
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="hidden md:block rounded-2xl border border-white/80 bg-card shadow-soft overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-admin-soft/40">
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Kamar</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Slot</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Aksi</th>
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
                        <tr class="transition duration-soft hover:bg-admin-soft/30">
                            <td class="px-4 py-3.5 font-semibold text-content-primary"><?= e((string) $q['nama_kamar']) ?></td>
                            <td class="px-4 py-3.5 text-content-primary"><?= e(date('d/m/Y', strtotime((string) $q['tanggal']))) ?></td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="font-medium <?= $penuh ? 'text-amber-700' : 'text-content-primary' ?>"><?= $terisi ?> / <?= $maks ?></span>
                                    <div class="h-1.5 w-20 overflow-hidden rounded-full bg-admin-soft">
                                        <div class="h-full rounded-full <?= $penuh ? 'bg-amber-500' : 'bg-success' ?>" style="width: <?= e((string) $pct) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="/admin/penitipan/kuota/edit?id=<?= e(urlencode((string) $q['id'])) ?>"
                                       class="cursor-pointer rounded-xl border border-border bg-page/60 px-3 py-1.5 text-xs font-semibold text-admin hover:bg-admin-soft">Edit</a>
                                    <form method="POST" action="/admin/penitipan/kuota/hapus" data-confirm="Hapus kuota ini?">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $q['id']) ?>">
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
            <?php foreach ($kuotaList as $q): ?>
                <?php
                $terisi = (int) $q['slot_terisi'];
                $maks = (int) $q['slot_maksimal'];
                ?>
                <article class="rounded-2xl border border-white/80 bg-card p-4 shadow-soft space-y-3">
                    <div>
                        <h2 class="font-semibold text-content-primary"><?= e((string) $q['nama_kamar']) ?></h2>
                        <p class="text-sm text-content-secondary mt-0.5"><?= e(date('d/m/Y', strtotime((string) $q['tanggal']))) ?> · <?= $terisi ?>/<?= $maks ?> slot</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="/admin/penitipan/kuota/edit?id=<?= e(urlencode((string) $q['id'])) ?>"
                           class="flex-1 cursor-pointer text-center rounded-xl border border-border bg-page/60 px-3 py-2 text-xs font-semibold text-admin hover:bg-admin-soft">Edit</a>
                        <form method="POST" action="/admin/penitipan/kuota/hapus" class="flex-1" data-confirm="Hapus kuota ini?">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $q['id']) ?>">
                            <button type="submit" class="w-full cursor-pointer rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
