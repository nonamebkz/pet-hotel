<?php

declare(strict_types=1);

use App\Core\Csrf;

$settings = $settings ?? [];
$errors = $errors ?? [];

$field = static function (string $key) use ($settings): string {
    return e((string) old($key, $settings[$key] ?? ''));
};

$inputClass = static function (string $fieldName, array $errors): string {
    $base = 'w-full rounded-xl border bg-page/60 px-3.5 py-3 text-sm text-content-primary placeholder:text-content-secondary/60 transition duration-soft focus:bg-white focus:outline-none focus:ring-2';
    if (!empty($errors[$fieldName])) {
        return $base . ' border-red-400 focus:border-red-400 focus:ring-red-200';
    }

    return $base . ' border-border hover:border-admin/30 focus:border-admin focus:ring-admin/25';
};
?>
<div class="font-body space-y-6 pb-24">
    <section class="<?= e(design_cn(design_surface('panel'), 'relative overflow-hidden p-6 sm:p-8')) ?>">
        <div class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Konfigurasi</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Pengaturan Bisnis Petshop</h1>
                <p class="mt-2 text-sm text-content-secondary max-w-xl">
                    Kelola lokasi petshop, aturan antar-jemput, rekening pembayaran, promo penitipan, dan kontak WhatsApp.
                    Perubahan hanya berlaku untuk booking baru.
                </p>
            </div>
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-sm" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.37-.49l-1.296-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
    </section>

    <form method="POST" action="/admin/pengaturan" id="pengaturan-form" class="space-y-6">
        <?= Csrf::field() ?>

        <fieldset class="rounded-2xl border bg-card p-5 sm:p-6 space-y-4">
            <legend class="float-left w-full mb-4">
                <span class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-admin-soft text-admin">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block font-heading text-lg text-content-primary">Lokasi Petshop</span>
                        <span class="block text-xs font-normal text-content-secondary">Memengaruhi perhitungan jarak antar-jemput dan estimasi biaya untuk booking baru.</span>
                    </span>
                </span>
            </legend>
            <div class="clear-both"></div>
            <?php
            $petshop_lat = old('petshop_lat', $settings['petshop_lat'] ?? null);
            $petshop_lng = old('petshop_lng', $settings['petshop_lng'] ?? null);
            require __DIR__ . '/../../partials/petshop-location-map.php';
            ?>
        </fieldset>

        <fieldset class="rounded-2xl border bg-card p-5 sm:p-6 space-y-4">
            <legend class="float-left w-full mb-4">
                <span class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-admin-soft text-admin">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block font-heading text-lg text-content-primary">Antar-jemput</span>
                        <span class="block text-xs font-normal text-content-secondary">Diterapkan saat pelanggan memilih opsi antar-jemput pada booking grooming.</span>
                    </span>
                </span>
            </legend>
            <div class="clear-both"></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="pickup_free_radius_km" class="mb-1.5 block text-sm font-semibold text-content-primary">Radius gratis (km)</label>
                    <input type="number" id="pickup_free_radius_km" name="pickup_free_radius_km" min="0.1" max="50" step="0.1"
                           value="<?= $field('pickup_free_radius_km') ?>"
                           class="<?= e($inputClass('pickup_free_radius_km', $errors)) ?>">
                    <?php if (!empty($errors['pickup_free_radius_km'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['pickup_free_radius_km']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="pickup_extra_fee_per_km" class="mb-1.5 block text-sm font-semibold text-content-primary">Biaya per km tambahan (Rp)</label>
                    <input type="number" id="pickup_extra_fee_per_km" name="pickup_extra_fee_per_km" min="0" step="500"
                           value="<?= $field('pickup_extra_fee_per_km') ?>"
                           class="<?= e($inputClass('pickup_extra_fee_per_km', $errors)) ?>">
                    <?php if (!empty($errors['pickup_extra_fee_per_km'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['pickup_extra_fee_per_km']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-xs text-content-secondary rounded-xl border border-border bg-page/50 px-4 py-3">
                Contoh: radius 3 km → gratis ≤ 3 km; di atas 3 km dikenakan biaya per km tambahan.
            </p>
        </fieldset>

        <fieldset class="rounded-2xl border bg-card p-5 sm:p-6 space-y-4">
            <legend class="float-left w-full mb-4">
                <span class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-admin-soft text-admin">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block font-heading text-lg text-content-primary">Pembayaran</span>
                        <span class="block text-xs font-normal text-content-secondary">Mengatur batas waktu upload bukti dan informasi rekening yang ditampilkan ke pelanggan.</span>
                    </span>
                </span>
            </legend>
            <div class="clear-both"></div>
            <div>
                <label for="payment_deadline_hours" class="mb-1.5 block text-sm font-semibold text-content-primary">Batas waktu pembayaran (jam)</label>
                <input type="number" id="payment_deadline_hours" name="payment_deadline_hours" min="1" max="168"
                       value="<?= $field('payment_deadline_hours') ?>"
                       class="max-w-xs <?= e($inputClass('payment_deadline_hours', $errors)) ?>">
                <?php if (!empty($errors['payment_deadline_hours'])): ?>
                    <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['payment_deadline_hours']) ?></p>
                <?php endif; ?>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label for="bank_name" class="mb-1.5 block text-sm font-semibold text-content-primary">Nama bank</label>
                    <input type="text" id="bank_name" name="bank_name" maxlength="50" value="<?= $field('bank_name') ?>"
                           class="<?= e($inputClass('bank_name', $errors)) ?>">
                    <?php if (!empty($errors['bank_name'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['bank_name']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="bank_account_number" class="mb-1.5 block text-sm font-semibold text-content-primary">No. rekening</label>
                    <input type="text" id="bank_account_number" name="bank_account_number" maxlength="30" value="<?= $field('bank_account_number') ?>"
                           class="<?= e($inputClass('bank_account_number', $errors)) ?>">
                    <?php if (!empty($errors['bank_account_number'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['bank_account_number']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="bank_account_name" class="mb-1.5 block text-sm font-semibold text-content-primary">Atas nama</label>
                    <input type="text" id="bank_account_name" name="bank_account_name" maxlength="100" value="<?= $field('bank_account_name') ?>"
                           class="<?= e($inputClass('bank_account_name', $errors)) ?>">
                    <?php if (!empty($errors['bank_account_name'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['bank_account_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </fieldset>

        <fieldset class="rounded-2xl border bg-card p-5 sm:p-6 space-y-4">
            <legend class="float-left w-full mb-4">
                <span class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-admin-soft text-admin">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block font-heading text-lg text-content-primary">Promo &amp; Lainnya</span>
                        <span class="block text-xs font-normal text-content-secondary">Promo penitipan dan syarat vaksin hanya berlaku untuk booking pet hotel baru.</span>
                    </span>
                </span>
            </legend>
            <div class="clear-both"></div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="promo_min_days" class="mb-1.5 block text-sm font-semibold text-content-primary">Minimal hari promo</label>
                    <input type="number" id="promo_min_days" name="promo_min_days" min="1" value="<?= $field('promo_min_days') ?>"
                           class="<?= e($inputClass('promo_min_days', $errors)) ?>">
                    <?php if (!empty($errors['promo_min_days'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['promo_min_days']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="promo_discount_percent" class="mb-1.5 block text-sm font-semibold text-content-primary">Diskon promo (%)</label>
                    <input type="number" id="promo_discount_percent" name="promo_discount_percent" min="1" max="100" value="<?= $field('promo_discount_percent') ?>"
                           class="<?= e($inputClass('promo_discount_percent', $errors)) ?>">
                    <?php if (!empty($errors['promo_discount_percent'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['promo_discount_percent']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="min_vaccination_count" class="mb-1.5 block text-sm font-semibold text-content-primary">Minimal vaksin (pet hotel)</label>
                    <input type="number" id="min_vaccination_count" name="min_vaccination_count" min="0" value="<?= $field('min_vaccination_count') ?>"
                           class="<?= e($inputClass('min_vaccination_count', $errors)) ?>">
                    <?php if (!empty($errors['min_vaccination_count'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['min_vaccination_count']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="petshop_whatsapp" class="mb-1.5 block text-sm font-semibold text-content-primary">WhatsApp petshop</label>
                    <input type="text" id="petshop_whatsapp" name="petshop_whatsapp" placeholder="6281234567890" value="<?= $field('petshop_whatsapp') ?>"
                           class="<?= e($inputClass('petshop_whatsapp', $errors)) ?>">
                    <?php if (!empty($errors['petshop_whatsapp'])): ?>
                        <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['petshop_whatsapp']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </fieldset>
    </form>

    <div class="fixed bottom-0 left-0 right-0 z-30 border-t border-border bg-card/95 backdrop-blur-sm print:hidden">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <p class="text-sm text-content-secondary hidden sm:block">Perubahan hanya berlaku untuk booking baru setelah disimpan.</p>
            <button type="submit" form="pengaturan-form"
                    class="cursor-pointer ml-auto inline-flex items-center justify-center rounded-xl bg-admin px-6 py-2.5 text-sm font-semibold text-white transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2">
                Simpan Pengaturan
            </button>
        </div>
    </div>
</div>
