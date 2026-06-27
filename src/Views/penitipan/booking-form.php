<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\OpsiPengantaran;

$paketList = $paketList ?? [];
$kucingList = $kucingList ?? [];
$opsiLabels = $opsiLabels ?? [];
$addressComplete = $addressComplete ?? false;
$promoEligible = $promoEligible ?? false;
$promoConfig = $promoConfig ?? [];
$errors = $errors ?? [];

$selectedPaket = (string) old('paket_penitipan_id', '');
$selectedKucing = (string) old('kucing_id', '');
$selectedCheckIn = (string) old('check_in', '');
$selectedCheckOut = (string) old('check_out', '');
$selectedOpsi = (string) old('opsi_pengantaran', OpsiPengantaran::ANTAR_SENDIRI->value);
$selectedCatatan = (string) old('catatan_makan', '');
?>
<div>
    <div class="mb-6">
        <a href="/penitipan" class="text-sm text-gray-500 hover:text-orange-600">&larr; Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Booking Penitipan</h1>
        <p class="text-sm text-gray-500 mt-1">Pilih kucing eligible, tanggal, paket, dan opsi pengantaran.</p>
    </div>

    <form method="POST" action="/penitipan/booking" class="bg-white rounded-xl border p-6 space-y-5 max-w-xl" id="booking-form">
        <?= Csrf::field() ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-red-600"><?= e((string) $errors['general']) ?></p>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Paket Penitipan</label>
            <select name="paket_penitipan_id" id="paket-select"
                    class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['paket_penitipan_id']) ? 'border-red-400' : 'border-gray-300' ?>"
                    required>
                <option value="">— Pilih paket —</option>
                <?php foreach ($paketList as $paket): ?>
                    <option value="<?= e((string) $paket['id']) ?>"
                            data-harga="<?= e((string) $paket['harga_per_hari']) ?>"
                            <?= $selectedPaket === (string) $paket['id'] ? 'selected' : '' ?>>
                        <?= e((string) $paket['nama']) ?> — Rp <?= e(number_format((float) $paket['harga_per_hari'], 0, ',', '.')) ?>/hari
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['paket_penitipan_id'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['paket_penitipan_id']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kucing</label>
            <select name="kucing_id" id="kucing-select"
                    class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['kucing_id']) ? 'border-red-400' : 'border-gray-300' ?>"
                    required>
                <option value="">— Pilih kucing —</option>
                <?php foreach ($kucingList as $kucing): ?>
                    <option value="<?= e((string) $kucing['id']) ?>"
                            data-eligible="<?= !empty($kucing['eligible_pet_hotel']) ? '1' : '0' ?>"
                            <?= $selectedKucing === (string) $kucing['id'] ? 'selected' : '' ?>
                            <?= empty($kucing['eligible_pet_hotel']) ? 'disabled' : '' ?>>
                        <?= e((string) $kucing['nama']) ?>
                        <?= empty($kucing['eligible_pet_hotel']) ? ' (belum eligible — lengkapi vaksin)' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['kucing_id'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['kucing_id']) ?></p>
            <?php endif; ?>
            <p class="text-xs text-gray-500 mt-1">
                Kucing belum eligible? <a href="/kucing" class="text-orange-600 underline">Lengkapi riwayat vaksin</a>
            </p>
        </div>

        <div id="vaksin-panel" class="hidden bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm">
            <div class="font-medium text-blue-900 mb-2">Riwayat Vaksin (read-only)</div>
            <ul id="vaksin-list" class="text-blue-800 space-y-1"></ul>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in</label>
                <input type="date" name="check_in" id="check-in"
                       value="<?= e($selectedCheckIn) ?>"
                       min="<?= e(date('Y-m-d')) ?>"
                       class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['check_in']) ? 'border-red-400' : 'border-gray-300' ?>"
                       required>
                <?php if (!empty($errors['check_in'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['check_in']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Check-out</label>
                <input type="date" name="check_out" id="check-out"
                       value="<?= e($selectedCheckOut) ?>"
                       class="w-full border rounded-lg px-3 py-2 text-sm <?= !empty($errors['check_out']) ? 'border-red-400' : 'border-gray-300' ?>"
                       required>
                <?php if (!empty($errors['check_out'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= e((string) $errors['check_out']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Makan & Kebiasaan (opsional)</label>
            <textarea name="catatan_makan" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><?= e($selectedCatatan) ?></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Opsi Pengantaran</label>
            <div class="space-y-2">
                <?php foreach ($opsiLabels as $value => $label): ?>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="opsi_pengantaran" value="<?= e($value) ?>"
                               class="text-orange-600 opsi-radio"
                               <?= $selectedOpsi === $value ? 'checked' : '' ?> required>
                        <span class="text-sm text-gray-700"><?= e($label) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (!$addressComplete): ?>
                <p class="text-xs text-amber-700 mt-2">
                    Untuk antar-jemput, <a href="/profil" class="underline">lengkapi alamat profil</a>.
                </p>
            <?php endif; ?>
            <div id="pickup-estimasi" class="hidden mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-900"></div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 text-sm" id="ringkasan-biaya">
            <div class="font-medium text-gray-800 mb-2">Ringkasan Biaya</div>
            <div class="text-gray-600" id="line-lama">Lama penitipan: —</div>
            <div class="text-gray-600" id="line-subtotal">Subtotal penitipan: —</div>
            <div class="text-green-700 hidden" id="line-promo">Potongan promo: —</div>
            <div class="text-gray-600" id="line-pickup">Biaya antar-jemput: Rp 0</div>
            <div class="font-medium text-gray-800 mt-2 pt-2 border-t" id="line-total">Total: —</div>
        </div>

        <button type="submit"
                class="w-full bg-orange-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-orange-700">
            Ajukan Penitipan
        </button>
    </form>
</div>

<script>
(function () {
    const kucingData = <?= json_encode(array_map(static fn ($k) => [
        'id' => $k['id'],
        'vaksin' => array_map(static fn ($v) => [
            'jenis' => $v['jenis_vaksin'],
            'tanggal' => $v['tanggal_vaksin'],
        ], $k['vaksin_list'] ?? []),
    ], $kucingList), JSON_HEX_TAG | JSON_HEX_AMP) ?>;

    const promoEligible = <?= $promoEligible ? 'true' : 'false' ?>;
    const promoMinDays = <?= (int) ($promoConfig['promo_min_days'] ?? 7) ?>;
    const promoPercent = <?= (int) ($promoConfig['promo_discount_percent'] ?? 10) ?>;
    const addressComplete = <?= $addressComplete ? 'true' : 'false' ?>;

    const paketSelect = document.getElementById('paket-select');
    const kucingSelect = document.getElementById('kucing-select');
    const checkIn = document.getElementById('check-in');
    const checkOut = document.getElementById('check-out');
    const vaksinPanel = document.getElementById('vaksin-panel');
    const vaksinList = document.getElementById('vaksin-list');
    const pickupEstimasi = document.getElementById('pickup-estimasi');

    function formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function showVaksin(kucingId) {
        const k = kucingData.find(x => x.id === kucingId);
        if (!k || k.vaksin.length === 0) {
            vaksinPanel.classList.add('hidden');
            return;
        }
        vaksinPanel.classList.remove('hidden');
        vaksinList.innerHTML = k.vaksin.map(v =>
            '<li>' + v.jenis + ' — ' + new Date(v.tanggal).toLocaleDateString('id-ID') + '</li>'
        ).join('');
    }

    async function updateEstimasi() {
        const paketId = paketSelect?.value;
        const kucingId = kucingSelect?.value;
        const ci = checkIn?.value;
        const co = checkOut?.value;
        const opsi = document.querySelector('input[name="opsi_pengantaran"]:checked')?.value;

        if (!paketId || !kucingId || !ci || !co || !opsi) return;

        try {
            const params = new URLSearchParams({
                paket_penitipan_id: paketId,
                kucing_id: kucingId,
                check_in: ci,
                check_out: co,
                opsi_pengantaran: opsi,
            });
            const res = await fetch('/penitipan/estimasi-biaya?' + params.toString());
            const json = await res.json();

            if (!json.success) return;

            const d = json.data;
            document.getElementById('line-lama').textContent = 'Lama penitipan: ' + d.lama_hari + ' hari';
            document.getElementById('line-subtotal').textContent = 'Subtotal penitipan: ' + formatRp(d.subtotal);

            const promoEl = document.getElementById('line-promo');
            if (d.potongan_promo > 0) {
                promoEl.classList.remove('hidden');
                promoEl.textContent = 'Potongan promo (' + promoPercent + '%): -' + formatRp(d.potongan_promo);
            } else {
                promoEl.classList.add('hidden');
            }

            document.getElementById('line-pickup').textContent = 'Biaya antar-jemput: ' + formatRp(d.biaya_antar_jemput);
            document.getElementById('line-total').textContent = 'Total: ' + formatRp(d.total_bayar);
        } catch (e) {}
    }

    async function loadPickup() {
        if (!addressComplete) {
            pickupEstimasi.classList.remove('hidden');
            pickupEstimasi.innerHTML = '<span class="text-amber-800">Alamat profil belum lengkap.</span>';
            return;
        }
        pickupEstimasi.classList.remove('hidden');
        pickupEstimasi.textContent = 'Menghitung jarak...';
        try {
            const res = await fetch('/penitipan/estimasi-pickup');
            const data = await res.json();
            if (data.success) {
                pickupEstimasi.textContent = data.gratis
                    ? 'Jarak: ' + data.jarak_km.toFixed(2) + ' km — Gratis'
                    : 'Jarak: ' + data.jarak_km.toFixed(2) + ' km — ' + formatRp(data.biaya_antar_jemput);
            }
        } catch {}
        updateEstimasi();
    }

    kucingSelect?.addEventListener('change', function () {
        showVaksin(this.value);
        updateEstimasi();
    });

    [paketSelect, checkIn, checkOut].forEach(el => el?.addEventListener('change', updateEstimasi));

    document.querySelectorAll('.opsi-radio').forEach(el => {
        el.addEventListener('change', function () {
            if (this.value === 'ANTAR_JEMPUT') loadPickup();
            else {
                pickupEstimasi.classList.add('hidden');
                updateEstimasi();
            }
        });
    });

    if (kucingSelect?.value) showVaksin(kucingSelect.value);
    if (document.querySelector('input[name="opsi_pengantaran"]:checked')?.value === 'ANTAR_JEMPUT') loadPickup();
    updateEstimasi();
})();
</script>
