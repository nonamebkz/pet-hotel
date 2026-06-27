<?php

declare(strict_types=1);

$bankConfig = $bankConfig ?? [];
$whatsapp = (string) ($bankConfig['petshop_whatsapp'] ?? '');
?>
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bantuan & Pembatalan</h1>
        <p class="text-sm text-gray-500 mt-1">Informasi pembatalan booking dan refund untuk layanan grooming & penitipan.</p>
    </div>

    <div class="bg-white rounded-xl border p-6 space-y-6 max-w-2xl">
        <section>
            <h2 class="font-semibold text-gray-800 mb-2">Belum terkonfirmasi / belum bayar</h2>
            <p class="text-sm text-gray-600">
                Anda dapat membatalkan booking langsung dari halaman detail atau riwayat booking
                selama status masih menunggu konfirmasi atau menunggu pembayaran.
            </p>
        </section>

        <section>
            <h2 class="font-semibold text-gray-800 mb-2">Sudah terkonfirmasi & sudah bayar</h2>
            <p class="text-sm text-gray-600 mb-3">
                Pembatalan tidak dapat dilakukan otomatis dari aplikasi. Hubungi petshop via WhatsApp,
                sampaikan ID booking dan alasan pembatalan. Refund diproses manual setelah verifikasi staff.
            </p>
            <?php if ($whatsapp !== ''): ?>
                <a href="<?= e(whatsapp_url('Halo, saya butuh bantuan terkait pembatalan booking petshop.')) ?>"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-medium hover:bg-green-700">
                    Hubungi Kami via WhatsApp
                </a>
            <?php else: ?>
                <p class="text-sm text-amber-700">Nomor WhatsApp petshop belum dikonfigurasi.</p>
            <?php endif; ?>
        </section>

        <section class="border-t pt-4">
            <h2 class="font-semibold text-gray-800 mb-2">Pet Care</h2>
            <p class="text-sm text-gray-600">
                Booking pet care dapat dibatalkan langsung dari aplikasi. Tidak ada prepayment,
                sehingga tidak ada alur refund untuk layanan ini.
            </p>
        </section>
    </div>
</div>
