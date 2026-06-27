<?php

declare(strict_types=1);

$pelanggan = $pelanggan ?? [];
$kucingList = $kucingList ?? [];
$minVaksin = $minVaksin ?? 1;
$jenisKelaminLabels = $jenisKelaminLabels ?? [];
$addressComplete = $addressComplete ?? false;
$promoUsed = !empty($pelanggan['pernah_pakai_promo_penitipan']);
?>
<div>
    <nav class="text-sm text-gray-500 mb-4">
        <a href="/admin/pelanggan" class="hover:text-slate-700 hover:underline">Pelanggan</a>
        <span class="mx-1">/</span>
        <span class="text-gray-800">Detail</span>
    </nav>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detail Pelanggan</h1>
        <a href="/admin/pelanggan" class="text-sm text-slate-700 hover:underline">← Kembali</a>
    </div>

    <div class="bg-white rounded-xl border p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Profil</h2>

        <div class="flex flex-wrap gap-6">
            <div class="flex-shrink-0">
                <?php if (!empty($pelanggan['foto_profil_url'])): ?>
                    <img src="<?= e((string) $pelanggan['foto_profil_url']) ?>" alt="Foto profil"
                         class="w-20 h-20 rounded-full object-cover border">
                <?php else: ?>
                    <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-2xl font-medium">
                        <?= e(mb_substr((string) ($pelanggan['nama'] ?? 'P'), 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex-1 min-w-[240px] grid gap-3 sm:grid-cols-2 text-sm">
                <div>
                    <div class="text-xs font-medium text-gray-500 mb-0.5">Nama</div>
                    <div class="text-gray-800"><?= e((string) ($pelanggan['nama'] ?? '')) ?></div>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 mb-0.5">Email</div>
                    <div class="text-gray-800"><?= e((string) ($pelanggan['email'] ?? '')) ?></div>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 mb-0.5">Telepon</div>
                    <div class="text-gray-800"><?= e((string) ($pelanggan['no_telepon'] ?? '—')) ?></div>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 mb-0.5">Terdaftar</div>
                    <div class="text-gray-800">
                        <?= !empty($pelanggan['created_at'])
                            ? e(date('d M Y H:i', strtotime((string) $pelanggan['created_at'])))
                            : '—' ?>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <div class="text-xs font-medium text-gray-500 mb-0.5">Alamat</div>
                    <div class="text-gray-800 whitespace-pre-wrap"><?= e((string) ($pelanggan['alamat_lengkap'] ?? '—')) ?></div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t">
            <?php if ($addressComplete): ?>
                <span class="text-xs font-medium bg-green-100 text-green-800 px-3 py-1 rounded-full">
                    Alamat lengkap untuk antar-jemput
                </span>
            <?php else: ?>
                <span class="text-xs font-medium bg-amber-100 text-amber-800 px-3 py-1 rounded-full">
                    Alamat belum lengkap
                </span>
            <?php endif; ?>
            <?php if ($promoUsed): ?>
                <span class="text-xs font-medium bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                    Sudah pernah pakai promo penitipan
                </span>
            <?php else: ?>
                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                    Belum pernah pakai promo penitipan
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            Kucing Milik Pelanggan
            <span class="text-sm font-normal text-gray-500">(<?= count($kucingList) ?>)</span>
        </h2>

        <?php if ($kucingList === []): ?>
            <div class="bg-white rounded-xl border p-8 text-center text-gray-600">
                Pelanggan ini belum mendaftarkan kucing.
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($kucingList as $kucing): ?>
                    <?php
                    $vaksinCount = (int) ($kucing['vaksin_count'] ?? 0);
                    $eligible = !empty($kucing['eligible_pet_hotel']);
                    ?>
                    <div class="bg-white rounded-xl border p-5">
                        <div class="flex flex-wrap gap-4 mb-4">
                            <?php if (!empty($kucing['foto_url'])): ?>
                                <img src="<?= e((string) $kucing['foto_url']) ?>" alt="<?= e((string) $kucing['nama']) ?>"
                                     class="w-16 h-16 rounded-lg object-cover border">
                            <?php else: ?>
                                <div class="w-16 h-16 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 text-lg font-bold">
                                    <?= e(mb_substr((string) $kucing['nama'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>

                            <div class="flex-1 min-w-[200px]">
                                <h3 class="font-semibold text-gray-800 text-lg"><?= e((string) $kucing['nama']) ?></h3>
                                <p class="text-sm text-gray-600">
                                    <?= e($jenisKelaminLabels[$kucing['jenis_kelamin']] ?? (string) $kucing['jenis_kelamin']) ?>
                                    <?php if (!empty($kucing['ras'])): ?>
                                        · <?= e((string) $kucing['ras']) ?>
                                    <?php endif; ?>
                                </p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <?php if ($eligible): ?>
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">
                                            Eligible pet hotel
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">
                                            Vaksin belum memenuhi syarat (min. <?= (int) $minVaksin ?>)
                                        </span>
                                    <?php endif; ?>
                                    <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                        <?= $vaksinCount ?> entri vaksin lengkap
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-3 text-sm mb-4">
                            <?php if (!empty($kucing['tanggal_lahir'])): ?>
                                <div>
                                    <span class="text-xs text-gray-500">Tanggal lahir</span>
                                    <div class="text-gray-800">
                                        <?= e(date('d/m/Y', strtotime((string) $kucing['tanggal_lahir']))) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($kucing['berat_badan'])): ?>
                                <div>
                                    <span class="text-xs text-gray-500">Berat badan</span>
                                    <div class="text-gray-800"><?= e((string) $kucing['berat_badan']) ?> kg</div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($kucing['catatan_kesehatan'])): ?>
                                <div class="sm:col-span-3">
                                    <span class="text-xs text-gray-500">Catatan kesehatan</span>
                                    <div class="text-gray-800 whitespace-pre-wrap"><?= e((string) $kucing['catatan_kesehatan']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="font-medium text-gray-700 mb-2 text-sm">Riwayat Vaksin</div>
                            <?php
                            $vaksinList = $kucing['vaksin_list'] ?? [];
                            require __DIR__ . '/../../partials/vaksin-readonly-list.php';
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
