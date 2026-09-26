<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\OpsiPengantaran;

$tanggal = $tanggal ?? '';
$dateOptions = $dateOptions ?? [];
$kuota = $kuota ?? null;
$kucingList = $kucingList ?? [];
$jenisList = $jenisList ?? [];
$opsiLabels = $opsiLabels ?? OpsiPengantaran::labels();
$addressComplete = $addressComplete ?? false;
$errors = $errors ?? [];

$selectedKuota = (string) old('kuota_grooming_id', $kuota['id'] ?? '');
$selectedJenis = (string) old('jenis_grooming_id', '');
$selectedKucing = (string) old('kucing_id', '');
$selectedOpsi = (string) old('opsi_pengantaran', OpsiPengantaran::ANTAR_SENDIRI->value);
$selectedCatatan = (string) old('catatan', '');

$selectedJenisData = null;
foreach ($jenisList as $jenis) {
    if ((string) $jenis['id'] === $selectedJenis) {
        $selectedJenisData = $jenis;
        break;
    }
}
?>
<div class="<?= e(design_cn(ui_page_content_shell_classes(), design_page_layout('formLg'))) ?>">
    <p>
        <a href="/grooming" class="<?= e(ui_back_link_class()) ?>">&larr; Kembali</a>
    </p>
    <?php
    ui_page_header(
        'Booking Grooming',
        'Grooming',
        'Pilih tanggal, jenis grooming, kucing, dan opsi pengantaran.',
    );
    ?>

    <?php if ($dateOptions === []): ?>
        <div class="<?= e(design_cn(design_surface('metric'), 'p-8 text-center')) ?>">
            <p class="text-muted-foreground mb-2">Tidak ada kuota grooming tersedia saat ini.</p>
            <p class="text-sm text-muted-foreground">Silakan coba lagi nanti atau hubungi petshop.</p>
        </div>
    <?php else: ?>
        <form method="GET" action="/grooming/booking" class="mb-6 flex items-end gap-3">
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">Tanggal</label>
                <select name="tanggal" class="<?= e(ui_form_input_class()) ?> text-sm py-2"
                        onchange="this.form.submit()">
                    <?php foreach ($dateOptions as $opt): ?>
                        <option value="<?= e($opt['tanggal']) ?>" <?= $tanggal === $opt['tanggal'] ? 'selected' : '' ?>>
                            <?= e(date('d/m/Y', strtotime($opt['tanggal']))) ?> — sisa <?= (int) $opt['sisa'] ?> slot
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if (!$kuota): ?>
            <div class="<?= e(design_cn(design_surface('metric'), 'p-6 text-center text-muted-foreground')) ?>">
                Kuota tidak tersedia untuk tanggal ini.
            </div>
        <?php else: ?>
            <form method="POST" action="/grooming/booking" class="<?= e(design_cn(design_surface('metric'), 'p-6 space-y-5 max-w-xl')) ?>"
                  id="booking-form">
                <?= Csrf::field() ?>
                <input type="hidden" name="tanggal" value="<?= e($tanggal) ?>">
                <input type="hidden" name="kuota_grooming_id" value="<?= e((string) $kuota['id']) ?>">

                <?php if (!empty($errors['general'])): ?>
                    <p class="text-sm text-destructive"><?= e((string) $errors['general']) ?></p>
                <?php endif; ?>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Jenis Grooming</label>
                    <select name="jenis_grooming_id" id="jenis-select"
                            class="<?= e(ui_form_input_class(!empty($errors['jenis_grooming_id']))) ?> text-sm py-2"
                            required>
                        <option value="">— Pilih jenis —</option>
                        <?php foreach ($jenisList as $jenis): ?>
                            <option value="<?= e((string) $jenis['id']) ?>"
                                    data-harga="<?= e((string) $jenis['harga']) ?>"
                                    <?= $selectedJenis === (string) $jenis['id'] ? 'selected' : '' ?>>
                                <?= e((string) $jenis['nama']) ?> — Rp <?= e(number_format((float) $jenis['harga'], 0, ',', '.')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['jenis_grooming_id'])): ?>
                        <p class="text-xs text-destructive mt-1"><?= e((string) $errors['jenis_grooming_id']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Kucing</label>
                    <select name="kucing_id"
                            class="<?= e(ui_form_input_class(!empty($errors['kucing_id']))) ?> text-sm py-2"
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
                        <p class="text-xs text-destructive mt-1"><?= e((string) $errors['kucing_id']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Opsi Pengantaran</label>
                    <div class="space-y-2">
                        <?php foreach ($opsiLabels as $value => $label): ?>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="opsi_pengantaran" value="<?= e($value) ?>"
                                       class="text-primary opsi-radio"
                                       <?= $selectedOpsi === $value ? 'checked' : '' ?>
                                       required>
                                <span class="text-sm text-foreground"><?= e($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($errors['opsi_pengantaran'])): ?>
                        <p class="text-xs text-destructive mt-1"><?= e((string) $errors['opsi_pengantaran']) ?></p>
                    <?php endif; ?>
                    <?php if (!$addressComplete): ?>
                        <p class="<?= e(design_cn(design_alert_inline('warning'), 'mt-2')) ?>">
                            Untuk antar-jemput, <a href="/profil" class="font-medium underline">lengkapi alamat profil</a> terlebih dahulu.
                        </p>
                    <?php endif; ?>
                    <div id="pickup-estimasi" class="<?= e(design_cn(design_surface('panel'), 'hidden mt-3 p-3 text-sm text-foreground')) ?>"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Catatan Khusus (opsional)</label>
                    <textarea name="catatan" rows="2"
                              class="<?= e(ui_form_input_class()) ?> text-sm py-2"><?= e($selectedCatatan) ?></textarea>
                </div>

                <div class="bg-muted/50 rounded-lg p-4 text-sm">
                    <div class="font-medium text-foreground mb-2">Ringkasan Biaya</div>
                    <div class="text-muted-foreground">Tanggal: <?= e(date('d/m/Y', strtotime($tanggal))) ?></div>
                    <div class="text-muted-foreground" id="harga-layanan">
                        Harga grooming:
                        <?php if ($selectedJenisData): ?>
                            Rp <?= e(number_format((float) $selectedJenisData['harga'], 0, ',', '.')) ?>
                        <?php else: ?>
                            — pilih jenis —
                        <?php endif; ?>
                    </div>
                    <div class="text-muted-foreground" id="harga-pickup">Biaya antar-jemput: Rp 0</div>
                    <div class="font-medium text-foreground mt-2 pt-2 border-t" id="harga-total">Total: —</div>
                </div>

                <button type="submit"
                        class="<?= e(design_cn(ui_btn_primary(), 'hidden w-full md:inline-flex')) ?>">
                    Ajukan Booking
                </button>
            </form>
            <?php ui_form_footer_mobile('Ajukan Booking', 'booking-form'); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
(function () {
    const jenisSelect = document.getElementById('jenis-select');
    const pickupEstimasi = document.getElementById('pickup-estimasi');
    const hargaLayananEl = document.getElementById('harga-layanan');
    const hargaPickupEl = document.getElementById('harga-pickup');
    const hargaTotalEl = document.getElementById('harga-total');
    const addressComplete = <?= $addressComplete ? 'true' : 'false' ?>;

    let hargaLayanan = 0;
    let biayaPickup = 0;

    function formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function updateTotal() {
        if (hargaLayanan > 0) {
            hargaTotalEl.textContent = 'Total: ' + formatRp(hargaLayanan + biayaPickup);
        } else {
            hargaTotalEl.textContent = 'Total: —';
        }
    }

    jenisSelect?.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        hargaLayanan = opt?.dataset?.harga ? Number(opt.dataset.harga) : 0;
        hargaLayananEl.textContent = hargaLayanan > 0 ? 'Harga grooming: ' + formatRp(hargaLayanan) : 'Harga grooming: — pilih jenis —';
        updateTotal();
    });

    async function loadPickupEstimasi() {
        if (!addressComplete) {
            pickupEstimasi.classList.remove('hidden');
            pickupEstimasi.innerHTML = '<span class="text-amber-900 dark:text-amber-200">Alamat profil belum lengkap. <a href="/profil" class="font-medium underline">Lengkapi profil</a> untuk antar-jemput.</span>';
            biayaPickup = 0;
            hargaPickupEl.textContent = 'Biaya antar-jemput: Rp 0';
            updateTotal();
            return;
        }

        pickupEstimasi.classList.remove('hidden');
        pickupEstimasi.textContent = 'Menghitung jarak...';

        try {
            const res = await fetch('/grooming/estimasi-pickup');
            const data = await res.json();

            if (!data.success) {
                pickupEstimasi.innerHTML = '<span class="text-destructive">' + (data.error || 'Gagal menghitung jarak.') + '</span>';
                biayaPickup = 0;
            } else {
                biayaPickup = data.biaya_antar_jemput;
                const jarak = data.jarak_km.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 });
                if (data.gratis) {
                    pickupEstimasi.textContent = 'Jarak: ' + jarak + ' km — Gratis (≤ ' + data.free_radius_km + ' km)';
                } else {
                    pickupEstimasi.textContent = 'Jarak: ' + jarak + ' km — Biaya antar-jemput: ' + formatRp(biayaPickup);
                }
            }
        } catch {
            pickupEstimasi.textContent = 'Gagal menghitung estimasi jarak.';
            biayaPickup = 0;
        }

        hargaPickupEl.textContent = 'Biaya antar-jemput: ' + formatRp(biayaPickup);
        updateTotal();
    }

    function onOpsiChange() {
        const selected = document.querySelector('input[name="opsi_pengantaran"]:checked');
        if (selected?.value === 'ANTAR_JEMPUT') {
            loadPickupEstimasi();
        } else {
            pickupEstimasi.classList.add('hidden');
            biayaPickup = 0;
            hargaPickupEl.textContent = 'Biaya antar-jemput: Rp 0';
            updateTotal();
        }
    }

    document.querySelectorAll('.opsi-radio').forEach(function (el) {
        el.addEventListener('change', onOpsiChange);
    });

    if (document.querySelector('input[name="opsi_pengantaran"]:checked')?.value === 'ANTAR_JEMPUT') {
        onOpsiChange();
    }

    if (jenisSelect?.value) {
        hargaLayanan = Number(jenisSelect.options[jenisSelect.selectedIndex]?.dataset?.harga || 0);
        updateTotal();
    }
})();
</script>
