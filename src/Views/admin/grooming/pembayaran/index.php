<?php

declare(strict_types=1);

use App\Core\Csrf;

$pendingList = $pendingList ?? [];
?>
<div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Verifikasi Bukti Transfer</h1>

    <div class="flex gap-4 mb-6 text-sm">
        <a href="/admin/grooming/layanan" class="text-gray-500 hover:text-slate-800">Jenis</a>
        <a href="/admin/grooming/kuota" class="text-gray-500 hover:text-slate-800">Kuota</a>
        <a href="/admin/grooming/booking" class="text-gray-500 hover:text-slate-800">Booking</a>
        <a href="/admin/grooming/pembayaran" class="text-slate-800 font-medium border-b-2 border-slate-800 pb-1">Verifikasi Bukti</a>
    </div>

    <?php if ($pendingList === []): ?>
        <div class="bg-white rounded-xl border p-8 text-center text-gray-600">
            Tidak ada bukti transfer menunggu verifikasi.
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($pendingList as $item): ?>
                <div class="bg-white rounded-xl border p-6">
                    <div class="grid lg:grid-cols-2 gap-6">
                        <div>
                            <h2 class="font-semibold text-gray-800 mb-2">
                                <?= e((string) $item['pelanggan_nama']) ?> — <?= e((string) $item['jenis_nama']) ?>
                            </h2>
                            <div class="text-sm text-gray-600 space-y-1 mb-4">
                                <div>Tanggal grooming: <?= e(date('d/m/Y', strtotime((string) $item['booking_tanggal']))) ?></div>
                                <?php if (!empty($item['jam_grooming'])): ?>
                                    <div>Jam: <?= e(substr((string) $item['jam_grooming'], 0, 5)) ?> WIB</div>
                                <?php endif; ?>
                                <div>Upload: <?= e(date('d/m/Y H:i', strtotime((string) $item['bukti_uploaded_at']))) ?></div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-3 text-sm mb-4">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal</span>
                                    <span>Rp <?= e(number_format((float) $item['subtotal_layanan'], 0, ',', '.')) ?></span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Antar-jemput</span>
                                    <span>Rp <?= e(number_format((float) $item['biaya_antar_jemput'], 0, ',', '.')) ?></span>
                                </div>
                                <div class="flex justify-between font-medium text-gray-800 mt-2 pt-2 border-t">
                                    <span>Total</span>
                                    <span>Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?></span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="/admin/grooming/pembayaran/setujui">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                                    <button type="submit" class="text-sm bg-green-600 text-white rounded-lg px-4 py-2 hover:bg-green-700">
                                        Setujui Bukti
                                    </button>
                                </form>
                                <form method="POST" action="/admin/grooming/pembayaran/tolak" class="flex items-center gap-2">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="bukti_id" value="<?= e((string) $item['bukti_id']) ?>">
                                    <input type="text" name="catatan" placeholder="Catatan penolakan"
                                           class="text-sm border border-gray-300 rounded-lg px-2 py-1.5 w-48">
                                    <button type="submit" class="text-sm border border-red-300 text-red-600 rounded-lg px-4 py-2 hover:bg-red-50">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Preview Bukti Transfer</p>
                            <?php
                            $fileUrl = (string) $item['bukti_file_url'];
                            $isPdf = str_ends_with(strtolower($fileUrl), '.pdf');
                            ?>
                            <?php if ($isPdf): ?>
                                <a href="<?= e($fileUrl) ?>" target="_blank"
                                   class="block border rounded-lg p-4 text-center text-sm text-blue-600 hover:bg-blue-50">
                                    Buka PDF Bukti Transfer
                                </a>
                            <?php else: ?>
                                <a href="<?= e($fileUrl) ?>" target="_blank">
                                    <img src="<?= e($fileUrl) ?>" alt="Bukti transfer"
                                         class="max-w-full rounded-lg border">
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
