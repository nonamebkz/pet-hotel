<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$slotList = $slotList ?? [];
$tanggal = $tanggal ?? date('Y-m-d');
$statusSlotLabels = $statusSlotLabels ?? [];
$errors = Session::getFlash('errors', []);

$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
$btnPrimary = 'cursor-pointer inline-flex items-center gap-2 rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';
$btnGhost = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-border bg-page/60 px-3 py-1.5 text-xs font-semibold text-admin transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';
$btnSuccess = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-green-200 bg-success-bg px-3 py-1.5 text-xs font-semibold text-success transition duration-soft hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-success';
$btnDanger = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition duration-soft hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400';
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Pet Care</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Slot Dokter</h1>
                <p class="mt-2 text-sm text-content-secondary max-w-xl">
                    Atur ketersediaan slot konsultasi per tanggal (maks. 1 booking per slot).
                </p>
            </div>
            <a href="/admin/pet-care/slot/tambah?tanggal=<?= e(urlencode($tanggal)) ?>" class="<?= e($btnPrimary) ?>">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Slot
            </a>
        </div>
    </section>

    <?php
    $activeTab = 'slot';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <?= e((string) $errors['general']) ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/pet-care/slot" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft">
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[12rem] flex-1 sm:flex-none">
                <label for="tanggal" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= e($tanggal) ?>" class="<?= e($inputClass) ?>">
            </div>
            <button type="submit" class="<?= e($btnPrimary) ?> min-h-[42px]">Tampilkan</button>
        </div>
    </form>

    <?php if ($slotList === []): ?>
        <?php
        $variant = 'filtered';
        $title = 'Tidak ada slot untuk tanggal ini';
        $description = 'Tambahkan slot dokter atau pilih tanggal lain.';
        $ctaLabel = 'Tambah Slot';
        $ctaHref = '/admin/pet-care/slot/tambah?tanggal=' . urlencode($tanggal);
        $ctaClass = $btnPrimary;
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="hidden md:block rounded-2xl border border-white/80 bg-card shadow-soft overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-admin-soft/40">
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Waktu</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Terisi</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-content-secondary text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/80">
                    <?php foreach ($slotList as $slot): ?>
                        <?php
                        $tersedia = ($slot['status_slot'] ?? '') === 'TERSEDIA';
                        $terisi = (int) $slot['slot_terisi'];
                        $maks = (int) $slot['slot_maksimal'];
                        $penuh = $maks > 0 && $terisi >= $maks;
                        ?>
                        <tr class="transition duration-soft hover:bg-admin-soft/30">
                            <td class="px-4 py-3.5 font-semibold text-content-primary whitespace-nowrap">
                                <?= e(substr((string) $slot['slot_waktu'], 0, 5)) ?> WIB
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-medium <?= $penuh ? 'text-amber-700' : 'text-content-primary' ?>">
                                    <?= $terisi ?> / <?= $maks ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="text-xs px-2 py-0.5 rounded-lg font-semibold <?= $tersedia ? 'bg-success-bg text-success' : 'bg-page text-content-secondary' ?>">
                                    <?= e($statusSlotLabels[$slot['status_slot']] ?? (string) $slot['status_slot']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                    <?php if ($tersedia): ?>
                                        <form method="POST" action="/admin/pet-care/slot/tutup" data-loading-submit>
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                            <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                            <button type="submit" class="<?= e($btnGhost) ?>">Tutup</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="/admin/pet-care/slot/buka" data-loading-submit>
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                            <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                            <button type="submit" class="<?= e($btnSuccess) ?>">Buka</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="/admin/pet-care/slot/hapus"
                                          data-confirm="Hapus slot ini?">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                        <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                        <button type="submit" class="<?= e($btnDanger) ?>">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3">
            <?php foreach ($slotList as $slot): ?>
                <?php
                $tersedia = ($slot['status_slot'] ?? '') === 'TERSEDIA';
                $terisi = (int) $slot['slot_terisi'];
                $maks = (int) $slot['slot_maksimal'];
                ?>
                <article class="rounded-2xl border border-white/80 bg-card p-4 shadow-soft space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-heading text-lg text-content-primary">
                                <?= e(substr((string) $slot['slot_waktu'], 0, 5)) ?> WIB
                            </h2>
                            <p class="mt-0.5 text-sm text-content-secondary"><?= $terisi ?> / <?= $maks ?> terisi</p>
                        </div>
                        <span class="shrink-0 text-xs px-2 py-0.5 rounded-lg font-semibold <?= $tersedia ? 'bg-success-bg text-success' : 'bg-page text-content-secondary' ?>">
                            <?= e($statusSlotLabels[$slot['status_slot']] ?? (string) $slot['status_slot']) ?>
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <?php if ($tersedia): ?>
                            <form method="POST" action="/admin/pet-care/slot/tutup" class="flex-1" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                <button type="submit" class="<?= e($btnGhost) ?> w-full">Tutup</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="/admin/pet-care/slot/buka" class="flex-1" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                <button type="submit" class="<?= e($btnSuccess) ?> w-full">Buka</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" action="/admin/pet-care/slot/hapus" class="flex-1"
                              data-confirm="Hapus slot ini?">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                            <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                            <button type="submit" class="<?= e($btnDanger) ?> w-full">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
