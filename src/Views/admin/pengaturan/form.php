<?php

declare(strict_types=1);

use App\Core\Csrf;

$settings = $settings ?? [];
$errors = $errors ?? [];

$field = static function (string $key) use ($settings): string {
    return e((string) old($key, $settings[$key] ?? ''));
};
?>
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Bisnis Petshop</h1>
        <p class="text-sm text-gray-500 mt-1">
            Kelola lokasi petshop, aturan antar-jemput, rekening pembayaran, promo penitipan, dan kontak WhatsApp.
            Perubahan hanya berlaku untuk booking baru.
        </p>
    </div>

    <form method="POST" action="/admin/pengaturan" class="space-y-6">
        <?= Csrf::field() ?>

        <fieldset class="bg-white rounded-xl border p-6 space-y-4">
            <legend class="text-lg font-semibold text-gray-800 px-1">Lokasi Petshop</legend>
            <?php
            $petshop_lat = old('petshop_lat', $settings['petshop_lat'] ?? null);
            $petshop_lng = old('petshop_lng', $settings['petshop_lng'] ?? null);
            require __DIR__ . '/../../partials/petshop-location-map.php';
            ?>
        </fieldset>

        <fieldset class="bg-white rounded-xl border p-6 space-y-4">
            <legend class="text-lg font-semibold text-gray-800 px-1">Antar-jemput</legend>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Radius gratis (km)</label>
                    <input type="number" name="pickup_free_radius_km" min="0.1" max="50" step="0.1"
                           value="<?= $field('pickup_free_radius_km') ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['pickup_free_radius_km']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['pickup_free_radius_km'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['pickup_free_radius_km']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya per km tambahan (Rp)</label>
                    <input type="number" name="pickup_extra_fee_per_km" min="0" step="500"
                           value="<?= $field('pickup_extra_fee_per_km') ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['pickup_extra_fee_per_km']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['pickup_extra_fee_per_km'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['pickup_extra_fee_per_km']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-xs text-gray-500">Contoh: radius 3 km → gratis ≤ 3 km; di atas 3 km dikenakan biaya per km tambahan.</p>
        </fieldset>

        <fieldset class="bg-white rounded-xl border p-6 space-y-4">
            <legend class="text-lg font-semibold text-gray-800 px-1">Pembayaran</legend>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Batas waktu pembayaran (jam)</label>
                <input type="number" name="payment_deadline_hours" min="1" max="168"
                       value="<?= $field('payment_deadline_hours') ?>"
                       class="w-full max-w-xs border rounded-lg px-3 py-2 text-sm <?= !empty($errors['payment_deadline_hours']) ? 'border-red-400' : 'border-gray-300' ?>">
                <?php if (!empty($errors['payment_deadline_hours'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['payment_deadline_hours']) ?></p>
                <?php endif; ?>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama bank</label>
                    <input type="text" name="bank_name" maxlength="50" value="<?= $field('bank_name') ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['bank_name']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['bank_name'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['bank_name']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. rekening</label>
                    <input type="text" name="bank_account_number" maxlength="30" value="<?= $field('bank_account_number') ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['bank_account_number']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['bank_account_number'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['bank_account_number']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Atas nama</label>
                    <input type="text" name="bank_account_name" maxlength="100" value="<?= $field('bank_account_name') ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['bank_account_name']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['bank_account_name'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['bank_account_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </fieldset>

        <fieldset class="bg-white rounded-xl border p-6 space-y-4">
            <legend class="text-lg font-semibold text-gray-800 px-1">Promo & Lainnya</legend>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimal hari promo</label>
                    <input type="number" name="promo_min_days" min="1" value="<?= $field('promo_min_days') ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['promo_min_days']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['promo_min_days'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['promo_min_days']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diskon promo (%)</label>
                    <input type="number" name="promo_discount_percent" min="1" max="100" value="<?= $field('promo_discount_percent') ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['promo_discount_percent']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['promo_discount_percent'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['promo_discount_percent']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimal vaksin (pet hotel)</label>
                    <input type="number" name="min_vaccination_count" min="0" value="<?= $field('min_vaccination_count') ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['min_vaccination_count']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['min_vaccination_count'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['min_vaccination_count']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp petshop</label>
                    <input type="text" name="petshop_whatsapp" placeholder="6281234567890" value="<?= $field('petshop_whatsapp') ?>"
                           class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['petshop_whatsapp']) ? 'border-red-400' : 'border-gray-300' ?>">
                    <?php if (!empty($errors['petshop_whatsapp'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['petshop_whatsapp']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </fieldset>

        <div class="flex justify-end">
            <button type="submit"
                    class="bg-slate-800 text-white rounded-lg px-6 py-2 text-sm font-medium hover:bg-slate-900">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
