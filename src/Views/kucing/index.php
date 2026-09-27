<?php

declare(strict_types=1);

use App\Core\Csrf;

$kucingList = $kucingList ?? [];
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ob_start();
    ?>
    <a href="/kucing/tambah" class="<?= e(ui_btn_primary()) ?>">+ Tambah Kucing</a>
    <?php
    ui_page_header(
        'Kucing Saya',
        'Profil hewan',
        'Daftar kucing terdaftar untuk booking grooming, penitipan, dan pet care.',
        (string) ob_get_clean(),
    );
    ?>

    <?php if ($kucingList === []): ?>
        <div class="<?= e(design_cn(design_surface('metric'), 'p-8 text-center')) ?>">
            <p class="text-muted-foreground mb-4">Belum ada kucing terdaftar.</p>
            <p class="text-sm text-muted-foreground mb-4">Minimal 1 kucing diperlukan sebelum booking layanan.</p>
            <a href="/kucing/tambah" class="text-primary hover:underline font-medium">Tambah kucing pertama</a>
        </div>
    <?php else: ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($kucingList as $kucing): ?>
                <div class="<?= e(design_cn(design_surface('metric'), 'p-4 flex flex-col')) ?>">
                    <div class="flex items-start gap-3 mb-3">
                        <?php if (!empty($kucing['foto_url'])): ?>
                            <img src="<?= e((string) $kucing['foto_url']) ?>" alt="<?= e((string) $kucing['nama']) ?>"
                                 class="w-14 h-14 rounded-lg object-cover border">
                        <?php else: ?>
                            <div class="w-14 h-14 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-sm font-bold">
                                <?= e(mb_substr((string) $kucing['nama'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="flex-1 min-w-0">
                            <h2 class="font-semibold text-foreground truncate"><?= e((string) $kucing['nama']) ?></h2>
                            <p class="text-sm text-muted-foreground">
                                <?= e(($jenisKelaminLabels ?? [])[$kucing['jenis_kelamin']] ?? (string) $kucing['jenis_kelamin']) ?>
                                <?php if (!empty($kucing['ras'])): ?>
                                    · <?= e((string) $kucing['ras']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-3">
                        <?php if (!empty($kucing['eligible_pet_hotel'])): ?>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">
                                Eligible pet hotel
                            </span>
                        <?php else: ?>
                            <span class="text-xs bg-muted text-muted-foreground px-2 py-0.5 rounded-full">
                                Vaksin belum lengkap
                            </span>
                        <?php endif; ?>
                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                            <?= (int) ($kucing['vaksin_count'] ?? 0) ?> riwayat vaksin
                        </span>
                    </div>

                    <?php if (!empty($kucing['berat_badan'])): ?>
                        <p class="text-xs text-muted-foreground mb-3">Berat: <?= e((string) $kucing['berat_badan']) ?> kg</p>
                    <?php endif; ?>

                    <div class="mt-auto flex gap-2 pt-2 border-t">
                        <a href="/kucing/edit?id=<?= e((string) $kucing['id']) ?>"
                           class="flex-1 text-center text-sm border border-border rounded-lg py-1.5 hover:bg-muted">
                            Edit
                        </a>
                        <form method="POST" action="/kucing/hapus" class="flex-1"
                              onsubmit="return confirm('Hapus kucing ini?')">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= e((string) $kucing['id']) ?>">
                            <button type="submit"
                                    class="<?= e(design_cn(ui_btn_secondary(), 'w-full border-destructive/30 text-destructive hover:bg-destructive/10')) ?>">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
