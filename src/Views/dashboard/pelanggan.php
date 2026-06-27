<?php

declare(strict_types=1);

$addressComplete = $addressComplete ?? false;
$kucingCount = $kucingCount ?? 0;
?>
<div class="space-y-6">
    <div class="bg-white rounded-xl border p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Selamat datang, <?= e((string) $nama) ?>!</h1>
        <?php if (!empty($email)): ?>
            <p class="text-sm text-gray-500">Email: <?= e((string) $email) ?></p>
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
        </div>
        <p class="text-xs text-gray-500 mt-3">Fitur booking grooming, penitipan, dan pet care akan ditambahkan di fase berikutnya.</p>
    </div>
</div>
