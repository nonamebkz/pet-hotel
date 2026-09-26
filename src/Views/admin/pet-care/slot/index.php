<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$slotList = $slotList ?? [];
$tanggal = $tanggal ?? date('Y-m-d');
$statusSlotLabels = $statusSlotLabels ?? [];
$errors = Session::getFlash('errors', []);

$inputClass = design_cn(
    'w-full rounded-xl border border-input bg-background px-3.5 py-2.5 text-base sm:text-sm text-foreground transition',
    'hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25',
);
$btnPrimary = design_cn(ui_btn_primary(), 'gap-2');
$btnSecondary = design_cn(ui_btn_secondary(), 'text-xs px-3 py-1.5');
$btnSuccess = design_cn(ui_btn_primary(), 'text-xs px-3 py-1.5 bg-emerald-600 hover:opacity-90');
$btnDanger = design_cn(
    ui_btn_secondary(),
    'text-xs px-3 py-1.5 border-destructive/30 bg-destructive/10 text-destructive hover:bg-destructive/15',
);
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/admin/pet-care/slot/tambah?tanggal=<?= e(urlencode($tanggal)) ?>" class="<?= e($btnPrimary) ?>">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Slot
    </a>
    <?php
    ui_page_header(
        'Slot Dokter',
        'Pet Care · Administrasi',
        'Atur ketersediaan slot konsultasi per tanggal (maks. 1 booking per slot).',
        (string) ob_get_clean(),
    );
    ?>

    <?php
    $activeTab = 'slot';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if (!empty($errors['general'])): ?>
        <div class="rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive" role="alert">
            <?= e((string) $errors['general']) ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/pet-care/slot" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6')) ?>">
        <div class="flex flex-wrap items-end gap-4">
            <div class="min-w-[12rem] flex-1 sm:flex-none">
                <label for="tanggal" class="<?= e(ui_form_label_class()) ?>">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= e($tanggal) ?>" class="<?= e($inputClass) ?>">
            </div>
            <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'min-h-[42px]')) ?>">Tampilkan</button>
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
        <div class="<?= e(design_cn(design_surface('panel'), 'hidden overflow-hidden md:block')) ?>">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/40">
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Waktu</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Terisi</th>
                        <th class="text-left px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-4 py-3.5 font-semibold text-muted-foreground text-xs uppercase tracking-wider">Aksi</th>
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
                        <tr class="<?= e(design_interactive('listRowHover')) ?> transition hover:bg-muted/30">
                            <td class="px-4 py-3.5 font-semibold text-foreground whitespace-nowrap">
                                <?= e(substr((string) $slot['slot_waktu'], 0, 5)) ?> WIB
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-medium tabular-nums <?= $penuh ? 'text-amber-700 dark:text-amber-300' : 'text-foreground' ?>">
                                    <?= $terisi ?> / <?= $maks ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="<?= e(design_status_badge($tersedia ? 'success' : 'muted')) ?>">
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
                                            <button type="submit" class="<?= e($btnSecondary) ?>">Tutup</button>
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
                <article class="<?= e(design_cn(design_interactive('listArticle'), 'space-y-3')) ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-heading text-lg text-foreground">
                                <?= e(substr((string) $slot['slot_waktu'], 0, 5)) ?> WIB
                            </h2>
                            <p class="mt-0.5 text-sm text-muted-foreground tabular-nums"><?= $terisi ?> / <?= $maks ?> terisi</p>
                        </div>
                        <span class="<?= e(design_status_badge($tersedia ? 'success' : 'muted')) ?> shrink-0">
                            <?= e($statusSlotLabels[$slot['status_slot']] ?? (string) $slot['status_slot']) ?>
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <?php if ($tersedia): ?>
                            <form method="POST" action="/admin/pet-care/slot/tutup" class="flex-1" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                <button type="submit" class="<?= e(design_cn($btnSecondary, 'w-full')) ?>">Tutup</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="/admin/pet-care/slot/buka" class="flex-1" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                                <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                                <button type="submit" class="<?= e(design_cn($btnSuccess, 'w-full')) ?>">Buka</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" action="/admin/pet-care/slot/hapus" class="flex-1"
                              data-confirm="Hapus slot ini?">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $slot['id']) ?>">
                            <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                            <button type="submit" class="<?= e(design_cn($btnDanger, 'w-full')) ?>">Hapus</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
