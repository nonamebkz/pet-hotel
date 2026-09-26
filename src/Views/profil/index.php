<?php

declare(strict_types=1);

use App\Core\Csrf;

$errors = $errors ?? [];
$pelanggan = $pelanggan ?? [];
$addressComplete = $addressComplete ?? false;

$headerMeta = $addressComplete
    ? '<span class="' . e(design_status_badge('success')) . '">Alamat lengkap untuk antar-jemput</span>'
    : '<span class="' . e(design_cn('rounded-full px-2.5 py-1 text-xs font-medium', 'bg-amber-500/10 text-amber-800 dark:text-amber-200')) . '">Alamat belum lengkap</span>';
?>
<div class="<?= e(design_cn('max-w-2xl', ui_page_content_shell_classes(), design_page_layout('formSm'))) ?>">
    <?php
    ui_page_header(
        'Profil Saya',
        'Akun',
        'Alamat wajib lengkap hanya jika Anda memilih opsi antar-jemput saat booking grooming atau penitipan.',
        null,
        $headerMeta,
    );
    ?>

    <p class="text-xs text-muted-foreground">
        Ketik alamat lengkap — peta akan mencari lokasi otomatis. Anda juga bisa klik atau geser penanda di peta.
    </p>

    <form method="POST" action="/profil" enctype="multipart/form-data" id="profil-form" class="<?= e(design_cn(design_surface('panel'), 'p-6 space-y-4')) ?>">
        <?= Csrf::field() ?>

        <?php if (!empty($errors['general'])): ?>
            <p class="text-destructive text-sm"><?= e($errors['general']) ?></p>
        <?php endif; ?>

        <div class="flex items-center gap-4">
            <?php if (!empty($pelanggan['foto_profil_url'])): ?>
                <img src="<?= e((string) $pelanggan['foto_profil_url']) ?>" alt="Foto profil"
                     class="w-16 h-16 rounded-full object-cover border">
            <?php else: ?>
                <div class="w-16 h-16 rounded-full bg-muted flex items-center justify-center text-muted-foreground text-xl">
                    <?= e(mb_substr((string) ($pelanggan['nama'] ?? 'P'), 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div class="flex-1">
                <label for="foto_profil" class="block text-sm font-medium text-foreground mb-1">Foto Profil (opsional)</label>
                <input type="file" id="foto_profil" name="foto_profil" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-muted-foreground">
                <?php if (!empty($errors['foto_profil'])): ?>
                    <p class="text-destructive text-xs mt-1"><?= e($errors['foto_profil']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label for="nama" class="block text-sm font-medium text-foreground mb-1">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" required
                   value="<?= e((string) old('nama', $pelanggan['nama'] ?? '')) ?>"
                   class="<?= e(ui_form_input_class(!empty($errors['nama']))) ?>">
            <?php if (!empty($errors['nama'])): ?>
                <p class="text-destructive text-xs mt-1"><?= e($errors['nama']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-foreground mb-1">Email</label>
            <input type="email" id="email" value="<?= e((string) ($pelanggan['email'] ?? '')) ?>" disabled
                   class="w-full rounded-lg border border-border bg-muted/50 px-3 py-2 text-muted-foreground">
        </div>

        <div>
            <label for="no_telepon" class="block text-sm font-medium text-foreground mb-1">No. Telepon</label>
            <input type="text" id="no_telepon" name="no_telepon"
                   value="<?= e((string) old('no_telepon', $pelanggan['no_telepon'] ?? '')) ?>"
                   class="<?= e(ui_form_input_class(!empty($errors['no_telepon']))) ?>">
        </div>

        <div>
            <label for="alamat_lengkap" class="block text-sm font-medium text-foreground mb-1">Alamat Lengkap</label>
            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3"
                      class="<?= e(ui_form_input_class(!empty($errors['alamat_lengkap']))) ?>"
                      placeholder="Contoh: Jl. Merdeka No. 10, Jakarta Pusat"><?= e((string) old('alamat_lengkap', $pelanggan['alamat_lengkap'] ?? '')) ?></textarea>
            <?php if (!empty($errors['alamat_lengkap'])): ?>
                <p class="text-destructive text-xs mt-1"><?= e($errors['alamat_lengkap']) ?></p>
            <?php endif; ?>
        </div>

        <?php
        $latitude = old('latitude', $pelanggan['latitude'] ?? null);
        $longitude = old('longitude', $pelanggan['longitude'] ?? null);
        require __DIR__ . '/../partials/address-map.php';
        ?>

        <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'hidden md:inline-flex')) ?>">
            Simpan Profil
        </button>
    </form>
    <?php ui_form_footer_mobile('Simpan Profil', 'profil-form'); ?>
</div>
