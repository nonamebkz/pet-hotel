<?php

declare(strict_types=1);

use App\Core\Csrf;

$errors = $errors ?? [];
$pelanggan = $pelanggan ?? [];
$addressComplete = $addressComplete ?? false;
?>
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
        <?php if ($addressComplete): ?>
            <span class="text-xs font-medium bg-green-100 text-green-800 px-3 py-1 rounded-full">
                Alamat lengkap untuk antar-jemput
            </span>
        <?php else: ?>
            <span class="text-xs font-medium bg-amber-100 text-amber-800 px-3 py-1 rounded-full">
                Alamat belum lengkap
            </span>
        <?php endif; ?>
    </div>

    <p class="text-sm text-gray-600 mb-2">
        Alamat wajib lengkap hanya jika Anda memilih opsi antar-jemput saat booking grooming atau penitipan.
    </p>
    <p class="text-xs text-gray-500 mb-6">
        Ketik alamat lengkap — peta akan mencari lokasi otomatis. Anda juga bisa klik atau geser penanda di peta.
    </p>

    <form method="POST" action="/profil" enctype="multipart/form-data" class="bg-white rounded-xl border p-6 space-y-4">
        <?= Csrf::field() ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-red-600 text-sm"><?= e($errors['general']) ?></p>
        <?php endif; ?>

        <div class="flex items-center gap-4">
            <?php if (!empty($pelanggan['foto_profil_url'])): ?>
                <img src="<?= e((string) $pelanggan['foto_profil_url']) ?>" alt="Foto profil"
                     class="w-16 h-16 rounded-full object-cover border">
            <?php else: ?>
                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xl">
                    <?= e(mb_substr((string) ($pelanggan['nama'] ?? 'P'), 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div class="flex-1">
                <label for="foto_profil" class="block text-sm font-medium text-gray-700 mb-1">Foto Profil (opsional)</label>
                <input type="file" id="foto_profil" name="foto_profil" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-600">
                <?php if (!empty($errors['foto_profil'])): ?>
                    <p class="text-red-600 text-xs mt-1"><?= e($errors['foto_profil']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" required
                   value="<?= e((string) old('nama', $pelanggan['nama'] ?? '')) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            <?php if (!empty($errors['nama'])): ?>
                <p class="text-red-600 text-xs mt-1"><?= e($errors['nama']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" value="<?= e((string) ($pelanggan['email'] ?? '')) ?>" disabled
                   class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-gray-500">
        </div>

        <div>
            <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
            <input type="text" id="no_telepon" name="no_telepon"
                   value="<?= e((string) old('no_telepon', $pelanggan['no_telepon'] ?? '')) ?>"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
                      placeholder="Contoh: Jl. Merdeka No. 10, Jakarta Pusat"><?= e((string) old('alamat_lengkap', $pelanggan['alamat_lengkap'] ?? '')) ?></textarea>
            <?php if (!empty($errors['alamat_lengkap'])): ?>
                <p class="text-red-600 text-xs mt-1"><?= e($errors['alamat_lengkap']) ?></p>
            <?php endif; ?>
        </div>

        <?php
        $latitude = old('latitude', $pelanggan['latitude'] ?? null);
        $longitude = old('longitude', $pelanggan['longitude'] ?? null);
        require __DIR__ . '/../partials/address-map.php';
        ?>

        <button type="submit" class="bg-orange-600 text-white rounded-lg px-6 py-2 font-medium hover:bg-orange-700">
            Simpan Profil
        </button>
    </form>
</div>
