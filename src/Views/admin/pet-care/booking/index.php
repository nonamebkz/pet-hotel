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

$inputClass = 'w-full rounded-xl border border-border bg-white px-3.5 py-2.5 text-sm text-content-primary shadow-soft-inset transition duration-soft hover:border-admin/30 focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25';
$btnPrimary = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-3.5 py-2 text-xs font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin disabled:opacity-60';
$btnDanger = 'cursor-pointer inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-semibold text-red-700 transition duration-soft hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400';
?>
<div class="font-body space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Pet Care</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Booking Pet Care</h1>
                <p class="mt-2 text-sm text-content-secondary max-w-xl">
                    Lanjutkan proses konsultasi dan kelola pembatalan booking.
                </p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-admin text-white shadow-soft" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
            </div>
        </div>
    </section>

    <?php
    $activeTab = 'booking';
    require __DIR__ . '/../_subnav.php';
    ?>

    <?php if ($bookingList !== [] || $hasFilter): ?>
        <div class="grid sm:grid-cols-3 gap-4">
            <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Terkonfirmasi</p>
                <p class="mt-1 font-heading text-2xl text-admin"><?= e((string) $countTerkonfirmasi) ?></p>
                <p class="mt-1 text-xs text-content-secondary">Siap diproses</p>
            </article>
            <article class="rounded-2xl border <?= $countProses > 0 ? 'border-amber-200 bg-warning-bg/40' : 'border-white/80 bg-card' ?> p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Sedang Proses</p>
                <p class="mt-1 font-heading text-2xl <?= $countProses > 0 ? 'text-amber-700' : 'text-admin' ?>"><?= e((string) $countProses) ?></p>
                <p class="mt-1 text-xs text-content-secondary">Dalam pengerjaan</p>
            </article>
            <article class="rounded-2xl border border-white/80 bg-card p-5 shadow-soft">
                <p class="text-sm text-content-secondary">Selesai</p>
                <p class="mt-1 font-heading text-2xl text-success"><?= e((string) $countSelesai) ?></p>
                <p class="mt-1 text-xs text-content-secondary">Pada daftar ini</p>
            </article>
        </div>
    <?php endif; ?>

    <form method="GET" action="/admin/pet-care/booking" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="font-heading text-lg text-content-primary">Filter</h2>
            <?php if ($hasFilter): ?>
                <a href="/admin/pet-care/booking"
                   class="cursor-pointer text-xs font-semibold text-content-secondary transition duration-soft hover:text-admin focus:outline-none focus-visible:underline">
                    Reset filter
                </a>
            <?php endif; ?>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="tanggal" class="mb-1.5 block text-sm font-semibold text-content-primary">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= e($filterTanggal) ?>" class="<?= e($inputClass) ?>">
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-semibold text-content-primary">Status</label>
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
                <button type="submit" class="<?= e($btnPrimary) ?> min-h-[42px] px-5">
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
            $ctaClass = 'cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin';
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
                <article class="rounded-2xl border <?= $isProses ? 'border-amber-200 bg-warning-bg/25' : 'border-white/80 bg-card' ?> p-5 sm:p-6 shadow-soft space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="font-heading text-lg text-content-primary">
                                <?= e((string) $booking['pelanggan_nama']) ?>
                                <span class="text-content-secondary font-body text-base">· <?= e((string) $booking['kucing_nama']) ?></span>
                            </h2>
                            <p class="mt-1 text-sm text-content-secondary"><?= e((string) $booking['pelanggan_email']) ?></p>
                        </div>
                        <?php if ($statusEnum): ?>
                            <span class="text-xs px-2.5 py-1 rounded-lg font-medium <?= e($statusEnum->badgeClass()) ?>">
                                <?= e($statusLabels[$booking['status']] ?? (string) $booking['status']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <dl class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                        <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset">
                            <dt class="text-xs text-content-secondary">Layanan</dt>
                            <dd class="mt-0.5 font-medium text-content-primary"><?= e((string) $booking['layanan_nama']) ?></dd>
                        </div>
                        <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset">
                            <dt class="text-xs text-content-secondary">Harga</dt>
                            <dd class="mt-0.5 font-semibold text-admin">
                                Rp <?= e(number_format((float) $booking['harga_layanan'], 0, ',', '.')) ?>
                            </dd>
                        </div>
                        <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset">
                            <dt class="text-xs text-content-secondary">Tanggal</dt>
                            <dd class="mt-0.5 font-medium text-content-primary">
                                <?= e(date('d/m/Y', strtotime((string) $booking['tanggal']))) ?>
                            </dd>
                        </div>
                        <div class="rounded-xl bg-page/60 px-3.5 py-2.5 shadow-soft-inset">
                            <dt class="text-xs text-content-secondary">Slot</dt>
                            <dd class="mt-0.5 font-medium text-content-primary">
                                <?= e(substr((string) $booking['slot_waktu'], 0, 5)) ?> WIB
                            </dd>
                        </div>
                    </dl>

                    <?php if (!empty($booking['catatan'])): ?>
                        <p class="rounded-xl bg-page/80 px-3.5 py-2.5 text-sm text-content-secondary italic">
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
                                       class="rounded-xl border border-border bg-white px-3 py-2 text-xs shadow-soft-inset focus:border-admin focus:outline-none focus:ring-2 focus:ring-admin/25 min-w-[10rem]">
                                <button type="submit" class="<?= e($btnDanger) ?>">Batalkan</button>
                            </form>
                        <?php endif; ?>

                        <?php if (!$nextStatus && !$canCancel): ?>
                            <span class="text-xs text-content-secondary/70 py-2">Tidak ada aksi</span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
