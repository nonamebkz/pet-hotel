<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\StatusBookingPetCare;

$bookingList = $bookingList ?? [];
$statusLabels = $statusLabels ?? [];
$filterStatus = $filterStatus ?? '';
$filterTanggal = $filterTanggal ?? '';
$hasFilter = $filterStatus !== '' || $filterTanggal !== '';

$countTerkonfirmasi = 0;
$countProses = 0;
$countSelesai = 0;
foreach ($bookingList as $statRow) {
    $st = (string) ($statRow['status'] ?? '');
    if ($st === StatusBookingPetCare::TERKONFIRMASI->value) {
        $countTerkonfirmasi++;
    } elseif ($st === StatusBookingPetCare::SEDANG_PROSES->value) {
        $countProses++;
    } elseif ($st === StatusBookingPetCare::SELESAI->value) {
        $countSelesai++;
    }
}

$inputClass = design_cn(
    'w-full rounded-xl border border-input bg-background px-3.5 py-2.5 text-base sm:text-sm text-foreground transition',
    'hover:border-primary/30 focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25',
);
$inputClassSm = design_cn($inputClass, 'min-w-[10rem] rounded-lg px-3 py-2 text-xs');
$btnPrimary = design_cn(ui_btn_primary(), 'text-xs px-3.5 py-2 disabled:opacity-60');
$btnPrimaryLg = design_cn(ui_btn_primary(), 'min-h-[42px] w-full px-5 sm:w-auto');
$btnDanger = design_cn(
    ui_btn_secondary(),
    'text-xs px-3 py-2 border-destructive/30 bg-destructive/10 text-destructive hover:bg-destructive/15',
);
$metricCard = design_surface('metric');
$metricCardWarning = design_cn(design_surface('metric'), design_advice_panel_surface('warning'));
$listArticle = design_cn(design_interactive('listArticle'), 'space-y-4 p-5 sm:p-6');
$listArticleWarning = design_cn(design_interactive('listArticle'), design_advice_panel_surface('warning'), 'space-y-4 p-5 sm:p-6');
$metricCell = design_interactive('metricCell');
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_page_header(
        'Booking Pet Care',
        'Pet Care · Administrasi',
        'Lanjutkan proses konsultasi dan kelola pembatalan booking.',
    );
    ?>

    <?php
    $activeTab = 'booking';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if ($bookingList !== [] || $hasFilter): ?>
        <div class="grid sm:grid-cols-3 gap-4">
            <article class="<?= e($metricCard) ?>">
                <p class="text-sm text-muted-foreground">Terkonfirmasi</p>
                <p class="mt-1 font-heading text-2xl tabular-nums text-primary"><?= e((string) $countTerkonfirmasi) ?></p>
                <p class="mt-1 text-xs text-muted-foreground">Siap diproses</p>
            </article>
            <article class="<?= e($countProses > 0 ? $metricCardWarning : $metricCard) ?>">
                <p class="text-sm text-muted-foreground">Sedang Proses</p>
                <p class="mt-1 font-heading text-2xl tabular-nums <?= $countProses > 0 ? 'text-amber-700 dark:text-amber-300' : 'text-primary' ?>"><?= e((string) $countProses) ?></p>
                <p class="mt-1 text-xs text-muted-foreground">Dalam pengerjaan</p>
            </article>
            <article class="<?= e($metricCard) ?>">
                <p class="text-sm text-muted-foreground">Selesai</p>
                <p class="mt-1 font-heading text-2xl tabular-nums text-emerald-700 dark:text-emerald-300"><?= e((string) $countSelesai) ?></p>
                <p class="mt-1 text-xs text-muted-foreground">Pada daftar ini</p>
            </article>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/pet-care/booking" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6')) ?>">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-heading text-lg text-foreground">Filter</h2>
            <?php if ($hasFilter): ?>
                <a href="/admin/pet-care/booking"
                   class="text-xs font-semibold text-muted-foreground transition hover:text-primary focus:outline-none focus-visible:underline">
                    Reset filter
                </a>
            <?php endif; ?>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="tanggal" class="<?= e(ui_form_label_class()) ?>">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= e($filterTanggal) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div>
                <label for="status" class="<?= e(ui_form_label_class()) ?>">Status</label>
                <select id="status" name="status" class="<?= e($inputClass) ?>">
                    <option value="">Semua status</option>
                    <?php foreach ($statusLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= $filterStatus === $value ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end sm:col-span-2">
                <button type="submit" class="<?= e($btnPrimaryLg) ?>">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </form>

    <?php if ($bookingList === []): ?>
        <?php
        if ($hasFilter) {
            $variant = 'filtered';
            $title = 'Tidak ditemukan booking untuk filter ini';
            $description = 'Coba ubah tanggal atau status, atau reset filter untuk melihat semua booking.';
            $ctaLabel = 'Reset Filter';
            $ctaHref = '/admin/pet-care/booking';
            $ctaClass = ui_btn_primary();
        } else {
            $variant = 'empty';
            $title = 'Belum ada booking pet care';
            $description = 'Booking dari pelanggan akan muncul di sini setelah diajukan.';
            $ctaLabel = null;
            $ctaHref = null;
        }
        require __DIR__ . '/../../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($bookingList as $booking): ?>
                <?php
                $statusEnum = StatusBookingPetCare::tryFrom((string) $booking['status']);
                $nextStatus = $statusEnum?->nextStatus();
                $canCancel = $statusEnum?->canCancel() ?? false;
                $isProses = (string) $booking['status'] === StatusBookingPetCare::SEDANG_PROSES->value;
                ?>
                <article class="<?= e($isProses ? $listArticleWarning : $listArticle) ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="font-heading text-lg text-foreground">
                                <?= e((string) $booking['pelanggan_nama']) ?>
                                <span class="text-muted-foreground font-body text-base">· <?= e((string) $booking['kucing_nama']) ?></span>
                            </h2>
                            <p class="mt-1 text-sm text-muted-foreground"><?= e((string) $booking['pelanggan_email']) ?></p>
                        </div>
                        <?php if ($statusEnum): ?>
                            <span class="text-xs px-2.5 py-1 rounded-lg font-medium <?= e($statusEnum->badgeClass()) ?>">
                                <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <dl class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                        <div class="<?= e($metricCell) ?>">
                            <dt class="text-xs text-muted-foreground">Layanan</dt>
                            <dd class="mt-0.5 font-medium text-foreground"><?= e((string) $booking['layanan_nama']) ?></dd>
                        </div>
                        <div class="<?= e($metricCell) ?>">
                            <dt class="text-xs text-muted-foreground">Harga</dt>
                            <dd class="mt-0.5 font-semibold tabular-nums text-primary">
                                Rp <?= e(number_format((float) $booking['harga_layanan'], 0, ',', '.')) ?>
                            </dd>
                        </div>
                        <div class="<?= e($metricCell) ?>">
                            <dt class="text-xs text-muted-foreground">Tanggal</dt>
                            <dd class="mt-0.5 font-medium text-foreground">
                                <?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?>
                            </dd>
                        </div>
                        <div class="<?= e($metricCell) ?>">
                            <dt class="text-xs text-muted-foreground">Slot</dt>
                            <dd class="mt-0.5 font-medium text-foreground">
                                <?= e(substr((string) $booking['slot_waktu'], 0, 5)) ?> WIB
                            </dd>
                        </div>
                    </dl>

                    <?php if (!empty($booking['catatan'])): ?>
                        <p class="rounded-xl bg-muted/50 px-3.5 py-2.5 text-sm text-muted-foreground italic">
                            “<?= e((string) $booking['catatan']) ?>”
                        </p>
                    <?php endif; ?>

                    <div class="flex flex-wrap gap-2 pt-1 border-t border-border/80">
                        <?php if ($nextStatus): ?>
                            <form method="POST" action="/admin/pet-care/booking/status" data-loading-submit>
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <input type="hidden" name="status" value="<?= e($nextStatus->value) ?>">
                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                <button type="submit" class="<?= e($btnPrimary) ?>">
                                    Lanjut → <?= e($statusLabels[$nextStatus->value] ?? $nextStatus->value) ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($canCancel): ?>
                            <form method="POST" action="/admin/pet-care/booking/batalkan"
                                  class="flex flex-wrap items-center gap-2 w-full sm:w-auto"
                                  data-confirm="Batalkan booking ini?">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                                <input type="hidden" name="filter_status" value="<?= e($filterStatus) ?>">
                                <input type="hidden" name="filter_tanggal" value="<?= e($filterTanggal) ?>">
                                <input type="text" name="alasan" placeholder="Alasan (opsional)"
                                       class="<?= e($inputClassSm) ?>">
                                <button type="submit" class="<?= e($btnDanger) ?>">Batalkan</button>
                            </form>
                        <?php endif; ?>

                        <?php if (!$nextStatus && !$canCancel): ?>
                            <span class="text-xs text-muted-foreground/70 py-2">Tidak ada aksi</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
