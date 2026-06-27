<?php

declare(strict_types=1);

$addressComplete = $addressComplete ?? false;
$kucingCount = $kucingCount ?? 0;
$activeBookings = $activeBookings ?? [];
$pendingPayments = $pendingPayments ?? [];
$promoEligible = $promoEligible ?? false;
$promoConfig = $promoConfig ?? [];
$recentNotifications = $recentNotifications ?? [];
?>
<div class="space-y-6">
    <div class="bg-white rounded-xl border p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Selamat datang, <?= e((string) $nama) ?>!</h1>
        <?php if (!empty($email)): ?>
            <p class="text-sm text-gray-500">Email: <?= e((string) $email) ?></p>
        <?php endif; ?>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Booking Aktif & Mendatang</h2>
            </div>

            <?php if ($activeBookings === []): ?>
                <p class="text-sm text-gray-500 mb-4">Belum ada booking aktif.</p>
                <?php if ($kucingCount >= 1): ?>
                    <div class="flex flex-wrap gap-2 text-sm">
                        <a href="/grooming/booking" class="text-orange-600 hover:underline">Ajukan grooming</a>
                        <span class="text-gray-300">·</span>
                        <a href="/penitipan/booking" class="text-orange-600 hover:underline">Ajukan penitipan</a>
                        <span class="text-gray-300">·</span>
                        <a href="/pet-care/booking" class="text-orange-600 hover:underline">Ajukan pet care</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($activeBookings as $booking): ?>
                        <?php $status = $booking['status'] ?? null; ?>
                        <a href="<?= e((string) $booking['url']) ?>"
                           class="block p-3 rounded-lg border hover:border-orange-300 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs text-gray-500 uppercase tracking-wide">
                                        <?= e((string) $booking['layanan_label']) ?>
                                    </div>
                                    <div class="font-medium text-gray-800"><?= e((string) $booking['label']) ?></div>
                                    <div class="text-sm text-gray-500">Kucing: <?= e((string) $booking['kucing']) ?></div>
                                    <div class="text-sm text-gray-500 mt-0.5"><?= e((string) $booking['tanggal_display']) ?></div>
                                </div>
                                <?php if ($status && method_exists($status, 'badgeClass')): ?>
                                    <span class="text-xs px-2 py-1 rounded-full shrink-0 <?= e($status->badgeClass()) ?>">
                                        <?= e((string) $booking['status_label']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tagihan Menunggu</h2>

            <?php if ($pendingPayments === []): ?>
                <p class="text-sm text-gray-500">Tidak ada tagihan menunggu pembayaran.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($pendingPayments as $payment): ?>
                        <div class="p-3 rounded-lg border border-amber-200 bg-amber-50/50">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div>
                                    <div class="font-medium text-gray-800"><?= e((string) $payment['tagihan_jenis']) ?></div>
                                    <div class="text-sm text-gray-600"><?= e((string) ($payment['layanan_label'] ?? '')) ?></div>
                                </div>
                                <div class="text-sm font-semibold text-orange-700 shrink-0">
                                    Rp <?= e(number_format((float) $payment['total_bayar'], 0, ',', '.')) ?>
                                </div>
                            </div>
                            <?php if (!empty($payment['batas_waktu_bayar'])): ?>
                                <p class="text-xs text-amber-800 mb-2">
                                    Batas bayar: <?= e(date('d/m/Y H:i', strtotime((string) $payment['batas_waktu_bayar']))) ?> WIB
                                </p>
                            <?php endif; ?>
                            <a href="<?= e((string) $payment['payment_url']) ?>"
                               class="text-sm text-orange-600 hover:underline font-medium">
                                Bayar & upload bukti →
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($promoEligible): ?>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-sm text-green-800">
                <span class="font-semibold">Promo penitipan aktif!</span>
                Potongan <?= (int) ($promoConfig['promo_discount_percent'] ?? 10) ?>%
                untuk durasi lebih dari <?= (int) ($promoConfig['promo_min_days'] ?? 7) ?> hari (1× per akun).
            </div>
            <a href="/penitipan/booking"
               class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-green-700 text-white text-sm font-medium hover:bg-green-800 shrink-0">
                Ajukan penitipan
            </a>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Notifikasi Terbaru</h2>
            <a href="/notifikasi" class="text-sm text-orange-600 hover:underline">Lihat semua</a>
        </div>

        <?php if ($recentNotifications === []): ?>
            <p class="text-sm text-gray-500">Belum ada notifikasi.</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($recentNotifications as $notif): ?>
                    <div class="p-3 rounded-lg border <?= empty($notif['sudah_dibaca']) ? 'border-orange-200 bg-orange-50/30' : '' ?>">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-medium text-gray-800 text-sm"><?= e((string) $notif['judul']) ?></div>
                                <p class="text-sm text-gray-600 mt-0.5 line-clamp-2"><?= e((string) $notif['pesan']) ?></p>
                            </div>
                            <time class="text-xs text-gray-400 shrink-0">
                                <?= e(date('d/m H:i', strtotime((string) $notif['created_at']))) ?>
                            </time>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Prasyarat Booking</h2>
        <p class="text-sm text-gray-600 mb-4">Lengkapi data berikut sebelum mengajukan layanan grooming, penitipan, atau pet care.</p>

        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-lg border <?= $addressComplete ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50' ?>">
                <div>
                    <p class="font-medium text-gray-800">Profil & Alamat</p>
                    <p class="text-sm text-gray-600">
                        <?= $addressComplete
                            ? 'Alamat lengkap — siap untuk opsi antar-jemput'
                            : 'Alamat belum lengkap — wajib jika pilih antar-jemput' ?>
                    </p>
                </div>
                <a href="/profil" class="text-sm text-orange-600 hover:underline font-medium shrink-0 ml-4">
                    <?= $addressComplete ? 'Lihat' : 'Lengkapi' ?>
                </a>
            </div>

            <div class="flex items-center justify-between p-3 rounded-lg border <?= $kucingCount >= 1 ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50' ?>">
                <div>
                    <p class="font-medium text-gray-800">Data Kucing</p>
                    <p class="text-sm text-gray-600">
                        <?= $kucingCount >= 1
                            ? "$kucingCount kucing terdaftar"
                            : 'Belum ada kucing — minimal 1 kucing diperlukan' ?>
                    </p>
                </div>
                <a href="/kucing" class="text-sm text-orange-600 hover:underline font-medium shrink-0 ml-4">
                    <?= $kucingCount >= 1 ? 'Kelola' : 'Tambah' ?>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Shortcut</h2>
        <div class="flex flex-wrap gap-3">
            <a href="/kucing/tambah"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">
                + Tambah Kucing
            </a>
            <a href="/profil"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">
                Edit Profil
            </a>
            <?php if ($kucingCount >= 1): ?>
                <a href="/pet-care/booking"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-orange-600 text-white text-sm hover:bg-orange-700">
                    Booking Pet Care
                </a>
                <a href="/grooming/booking"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-orange-600 text-white text-sm hover:bg-orange-700">
                    Booking Grooming
                </a>
                <a href="/penitipan/booking"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-orange-600 text-white text-sm hover:bg-orange-700">
                    Booking Penitipan
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
