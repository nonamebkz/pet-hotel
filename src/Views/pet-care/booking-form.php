<?php

declare(strict_types=1);

use App\Core\Csrf;

$tanggal = $tanggal ?? '';
$availableDates = $availableDates ?? [];
$slots = $slots ?? [];
$kucingList = $kucingList ?? [];
$layananList = $layananList ?? [];
$errors = $errors ?? [];

$selectedKuota = (string) old('kuota_pet_care_id', '');
$selectedLayanan = (string) old('layanan_pet_care_id', '');
$selectedKucing = (string) old('kucing_id', '');
$selectedCatatan = (string) old('catatan', '');

$selectedLayananData = null;

foreach ($layananList as $layanan) {
    if ((string) $layanan['id'] === $selectedLayanan) {
        $selectedLayananData = $layanan;
        break;
    }
}
?>
<div>
    <div class="mb-6">
        <a href="/pet-care" class="text-sm text-gray-500 hover:text-orange-600">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Booking Pet Care</h1>
        <p class="text-sm text-gray-500 mt-1">Pilih tanggal, slot waktu, layanan, dan kucing Anda.</p>
    </div>

    <?php if ($availableDates === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center">
            <p class="text-gray-600 mb-2">Tidak ada jadwal slot tersedia saat ini.</p>
            <p class="text-sm text-gray-500">Silakan coba lagi nanti atau hubungi petshop.</p>
        </div>
    <?php else: ?>
        <form method="GET" action="/pet-care/booking" class="mb-6 flex items-end gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <select name="tanggal" class="border border-gray-300 rounded-lg px-3 py-2 text-sm"
                        onchange="this.form.submit()">
                    <?php foreach ($availableDates as $date): ?>
                        <option value="<?= e($date) ?>" <?= $tanggal === $date ? 'selected' : '' ?>>
                            <?= e(date('d/m/Y', strtotime($date))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if ($slots === []): ?>
            <div class="bg-white rounded-xl border p-6 text-center text-gray-600">
                Tidak ada slot kosong untuk tanggal ini. Pilih tanggal lain.
            </div>
        <?php else: ?>
            <form method="POST" action="/pet-care/booking" class="bg-white rounded-xl border p-6 space-y-5 max-w-xl">
                <?= Csrf::field() ?>
                <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">

                <?php if (!empty($errors['general'])): ?>
                    <p class="text-sm text-red-600"><?= e((string) $errors['general']) ?></p>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slot Waktu</label>
                    <div class="grid grid-cols-3 gap-2">
                        <?php foreach ($slots as $slot): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="kuota_pet_care_id" value="<?= e((string) $slot['id']) ?>"
                                       class="peer sr-only"
                                       <?= $selectedKuota === (string) $slot['id'] ? 'checked' : '' ?>
                                       required>
                                <span class="block text-center border rounded-lg py-2 text-sm peer-checked:border-orange-600 peer-checked:bg-orange-50 peer-checked:text-orange-700 hover:border-orange-300">
                                    <?= e(substr((string) $slot['slot_waktu'], 0, 5)) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($errors['kuota_pet_care_id'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['kuota_pet_care_id']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Layanan</label>
                    <select name="layanan_pet_care_id" id="layanan-select"
                            class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['layanan_pet_care_id']) ? 'border-red-400' : 'border-gray-300' ?>"
                            required>
                        <option value="">— Pilih layanan —</option>
                        <?php foreach ($layananList as $layanan): ?>
                            <option value="<?= e((string) $layanan['id']) ?>"
                                    data-harga="<?= e((string) $layanan['harga']) ?>"
                                    <?= $selectedLayanan === (string) $layanan['id'] ? 'selected' : '' ?>>
                                <?= e((string) $layanan['nama']) ?> — Rp <?= e(number_format((float) $layanan['harga'], 0, ',', '.')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['layanan_pet_care_id'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['layanan_pet_care_id']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kucing</label>
                    <select name="kucing_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['kucing_id']) ? 'border-red-400' : 'border-gray-300' ?>"
                            required>
                        <option value="">— Pilih kucing —</option>
                        <?php foreach ($kucingList as $kucing): ?>
                            <option value="<?= e((string) $kucing['id']) ?>"
                                    <?= $selectedKucing === (string) $kucing['id'] ? 'selected' : '' ?>>
                                <?= e((string) $kucing['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['kucing_id'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['kucing_id']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Khusus (opsional)</label>
                    <textarea name="catatan" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><?= e($selectedCatatan) ?></textarea>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 text-sm">
                    <div class="font-medium text-gray-800 mb-1">Ringkasan</div>
                    <div class="text-gray-600">Tanggal: <?= e(date('d/m/Y', strtotime($tanggal))) ?></div>
                    <div class="text-gray-600" id="harga-ringkasan">
                        Estimasi biaya:
                        <?php if ($selectedLayananData): ?>
                            Rp <?= e(number_format((float) $selectedLayananData['harga'], 0, ',', '.')) ?>
                        <?php else: ?>
                            — pilih layanan —
                        <?php endif; ?>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">
                        Pengantaran: antar sendiri · Pembayaran di loket saat kunjungan
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-orange-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-orange-700">
                    Konfirmasi Booking
                </button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
document.getElementById('layanan-select')?.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const harga = opt?.dataset?.harga;
    const el = document.getElementById('harga-ringkasan');
    if (el && harga) {
        el.textContent = 'Estimasi biaya: Rp ' + Number(harga).toLocaleString('id-ID');
    }
});
</script>
