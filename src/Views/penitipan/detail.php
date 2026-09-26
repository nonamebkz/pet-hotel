<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Enums\OpsiPengantaran;
use App\Enums\StatusPenitipan;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPerpanjanganPenitipan;
use App\Enums\StatusRefund;
use App\Enums\StatusVerifikasi;

$booking = $booking ?? [];
$transaksi = $transaksi ?? null;
$transaksiLunas = $transaksiLunas ?? false;
$bukti = $bukti ?? null;
$invoice = $invoice ?? null;
$statusEnum = $statusEnum ?? null;
$statusLabel = $statusLabel ?? '';
$vaksinList = $vaksinList ?? [];
$monitoringList = $monitoringList ?? [];
$perpanjanganList = $perpanjanganList ?? [];
$opsiLabels = $opsiLabels ?? [];
$perpanjanganLabels = $perpanjanganLabels ?? [];
$canCancel = $canCancel ?? false;
$canPerpanjang = $canPerpanjang ?? false;
$bankConfig = $bankConfig ?? [];
$whatsapp = (string) ($bankConfig['petshop_whatsapp'] ?? '');
$statusRefund = $transaksi ? StatusRefund::tryFrom((string) ($transaksi['status_refund'] ?? '')) : null;
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <p>
        <a href="/penitipan/riwayat" class="<?= e(ui_back_link_class()) ?>">&larr; Riwayat</a>
    </p>
    <?php
    ui_page_header(
        'Detail Penitipan',
        'Penitipan',
        (string) ($booking['kucing_nama'] ?? '') !== '' ? (string) $booking['kucing_nama'] . ' · ' . (string) ($booking['paket_nama'] ?? '') : null,
    );
    ?>

    <div class="grid gap-6 lg:grid-cols-2 max-w-5xl">
        <div class="<?= e(design_cn(design_surface('metric'), 'p-6 space-y-4')) ?>">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="font-semibold text-lg text-foreground"><?= e((string) $booking['kucing_nama']) ?></div>
                    <div class="text-sm text-muted-foreground"><?= e((string) $booking['paket_nama']) ?> · <?= e((string) $booking['nama_kamar']) ?></div>
                </div>
                <?php if ($statusEnum): ?>
                    <span class="text-xs px-2 py-1 rounded-full <?= e($statusEnum->badgeClass()) ?>">
                        <?= e($statusLabel) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="grid sm:grid-cols-2 gap-2 text-sm text-muted-foreground">
                <div>Check-in: <?= e(date('d/m/Y', strtotime((string) $booking['check_in']))) ?></div>
                <div>Check-out: <?= e(date('d/m/Y', strtotime((string) $booking['check_out']))) ?></div>
                <div>Lama: <?= (int) $booking['lama_hari'] ?> hari</div>
                <div>Pengantaran: <?= e($opsiLabels[$booking['opsi_pengantaran']] ?? (string) $booking['opsi_pengantaran']) ?></div>
            </div>

            <?php if (!empty($booking['catatan_makan'])): ?>
                <p class="text-sm text-muted-foreground">Catatan makan: <?= e((string) $booking['catatan_makan']) ?></p>
            <?php endif; ?>

            <?php if ($vaksinList !== []): ?>
                <div class="bg-muted/50 rounded-lg p-3 text-sm">
                    <div class="font-medium text-foreground mb-1">Riwayat Vaksin</div>
                    <ul class="text-muted-foreground space-y-1">
                        <?php foreach ($vaksinList as $v): ?>
                            <li><?= e((string) $v['jenis_vaksin']) ?> — <?= e(date('d/m/Y', strtotime((string) $v['tanggal_vaksin']))) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($transaksi): ?>
                <div class="bg-muted/50 rounded-lg p-4 text-sm">
                    <div class="font-medium text-foreground mb-2">Rincian Tagihan Awal</div>
                    <div class="flex justify-between text-muted-foreground">
                        <span>Subtotal</span>
                        <span>Rp <?= e(number_format((float) $transaksi['subtotal_layanan'], 0, ',', '.')) ?></span>
                    </div>
                    <?php if ((float) $transaksi['potongan_promo'] > 0): ?>
                        <div class="flex justify-between text-emerald-700 dark:text-emerald-300">
                            <span>Potongan promo</span>
                            <span>- Rp <?= e(number_format((float) $transaksi['potongan_promo'], 0, ',', '.')) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex justify-between text-muted-foreground">
                        <span>Antar-jemput</span>
                        <span>Rp <?= e(number_format((float) $transaksi['biaya_antar_jemput'], 0, ',', '.')) ?></span>
                    </div>
                    <div class="flex justify-between font-medium text-foreground mt-2 pt-2 border-t">
                        <span>Total</span>
                        <span>Rp <?= e(number_format((float) $transaksi['total_bayar'], 0, ',', '.')) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php require __DIR__ . '/../partials/refund-status.php'; ?>

            <div class="flex flex-wrap gap-3">
                <?php if ((string) ($booking['status'] ?? '') === StatusPenitipan::MENUNGGU_PEMBAYARAN->value): ?>
                    <a href="/penitipan/pembayaran?id=<?= e((string) $booking['id']) ?>"
                       class="<?= e(ui_btn_primary()) ?>">
                        Upload Bukti Transfer
                    </a>
                <?php endif; ?>
                <?php if ($invoice && $transaksiLunas): ?>
                    <a href="/penitipan/invoice?id=<?= e((string) $booking['id']) ?>"
                       class="<?= e(ui_btn_secondary()) ?>">
                        Lihat Invoice
                    </a>
                <?php endif; ?>
                <?php if ($canCancel): ?>
                    <form method="POST" action="/penitipan/booking/batalkan" class="inline"
                          onsubmit="return confirm('Batalkan penitipan ini?')">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="id" value="<?= e((string) $booking['id']) ?>">
                        <button type="submit" class="text-sm text-destructive hover:underline">Batalkan</button>
                    </form>
                <?php elseif ($transaksiLunas && !$canCancel): ?>
                    <?php
                    $bookingId = (string) $booking['id'];
                    require __DIR__ . '/../partials/hubungi-kami.php';
                    ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-6">
            <?php if ($canPerpanjang): ?>
                <div class="<?= e(design_cn(design_surface('metric'), 'p-6')) ?>">
                    <h2 class="font-semibold text-foreground mb-3">Perpanjang Penitipan</h2>
                    <form method="POST" action="/penitipan/perpanjangan" class="space-y-3" id="form-perpanjangan">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="booking_id" value="<?= e((string) $booking['id']) ?>">
                        <div>
                            <label class="block text-sm text-foreground mb-1">Check-out baru</label>
                            <input type="date" name="check_out_baru" id="check-out-baru"
                                   min="<?= e(date('Y-m-d', strtotime((string) $booking['check_out'] . ' +1 day'))) ?>"
                                   class="<?= e(ui_form_input_class()) ?> text-sm py-2" required>
                        </div>
                        <div id="estimasi-perpanjangan" class="text-sm text-muted-foreground hidden"></div>
                        <button type="submit"
                                class="<?= e(ui_btn_primary()) ?>">
                            Ajukan Perpanjangan
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($perpanjanganList !== []): ?>
                <div class="<?= e(design_cn(design_surface('metric'), 'p-6')) ?>">
                    <h2 class="font-semibold text-foreground mb-3">Riwayat Perpanjangan</h2>
                    <div class="space-y-3">
                        <?php foreach ($perpanjanganList as $pp): ?>
                            <div class="border rounded-lg p-3 text-sm">
                                <div class="flex justify-between">
                                    <span><?= e(date('d/m/Y', strtotime((string) $pp['check_out_sebelum']))) ?>
                                        → <?= e(date('d/m/Y', strtotime((string) $pp['check_out_baru']))) ?></span>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-muted">
                                        <?= e($perpanjanganLabels[$pp['status']] ?? (string) $pp['status']) ?>
                                    </span>
                                </div>
                                <div class="text-muted-foreground mt-1">
                                    +<?= (int) $pp['tambah_hari'] ?> hari ·
                                    Rp <?= e(number_format((float) $pp['subtotal_tambahan'], 0, ',', '.')) ?>
                                </div>
                                <?php if ((string) $pp['status'] === StatusPerpanjanganPenitipan::MENUNGGU_PEMBAYARAN->value): ?>
                                    <a href="/penitipan/perpanjangan/pembayaran?id=<?= e((string) $pp['id']) ?>"
                                       class="inline-block mt-2 text-primary underline text-xs">Bayar perpanjangan</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($monitoringList !== []): ?>
                <div class="<?= e(design_cn(design_surface('metric'), 'p-6')) ?>">
                    <h2 class="font-semibold text-foreground mb-3">Monitoring Harian</h2>
                    <div class="space-y-4">
                        <?php foreach ($monitoringList as $mon): ?>
                            <div class="border rounded-lg p-3 text-sm">
                                <div class="font-medium text-foreground">
                                    <?= e(date('d/m/Y', strtotime((string) $mon['tanggal']))) ?>
                                </div>
                                <?php if (!empty($mon['foto_url'])): ?>
                                    <img src="<?= e((string) $mon['foto_url']) ?>" alt="Foto kucing"
                                         class="mt-2 rounded-lg max-h-40 object-cover">
                                <?php endif; ?>
                                <?php if (!empty($mon['catatan_makan'])): ?>
                                    <p class="text-muted-foreground mt-1">Makan: <?= e((string) $mon['catatan_makan']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($mon['kondisi'])): ?>
                                    <p class="text-muted-foreground">Kondisi: <?= e((string) $mon['kondisi']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($mon['aktivitas_harian'])): ?>
                                    <p class="text-muted-foreground">Aktivitas: <?= e((string) $mon['aktivitas_harian']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($canPerpanjang): ?>
<script>
(function () {
    const input = document.getElementById('check-out-baru');
    const estimasi = document.getElementById('estimasi-perpanjangan');
    const bookingId = '<?= e((string) $booking['id']) ?>';

    input?.addEventListener('change', async function () {
        if (!this.value) return;
        estimasi.classList.remove('hidden');
        estimasi.textContent = 'Menghitung...';
        try {
            const params = new URLSearchParams({ booking_id: bookingId, check_out_baru: this.value });
            const res = await fetch('/penitipan/perpanjangan/estimasi?' + params.toString());
            const json = await res.json();
            if (json.success) {
                estimasi.textContent = '+' + json.data.tambah_hari + ' hari · Total: Rp '
                    + Number(json.data.total_bayar).toLocaleString('id-ID') + ' (tanpa promo)';
            } else {
                estimasi.textContent = json.errors?.check_out_baru || 'Estimasi gagal.';
            }
        } catch {
            estimasi.textContent = 'Gagal menghitung estimasi.';
        }
    });
})();
</script>
<?php endif; ?>
