<?php

declare(strict_types=1);

$pelanggan = $pelanggan ?? [];
$kucingList = $kucingList ?? [];
$minVaksin = $minVaksin ?? 1;
$jenisKelaminLabels = $jenisKelaminLabels ?? [];
$addressComplete = $addressComplete ?? false;
$promoUsed = !empty($pelanggan['pernah_pakai_promo_penitipan']);

$namaPelanggan = (string) ($pelanggan['nama'] ?? '');
$initial = mb_substr($namaPelanggan !== '' ? $namaPelanggan : 'P', 0, 1);
?>
<div class="<?= e(ui_page_content_shell_classes()) ?>">
    <?php
    ui_breadcrumb([
        ['label' => 'Pelanggan', 'href' => '/admin/pelanggan'],
        ['label' => 'Detail'],
    ]);
    ?>

    <section class="<?= e(design_cn(design_surface('metric'), 'p-5 sm:p-6')) ?>">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <?php if (!empty($pelanggan['foto_profil_url'])): ?>
                    <img src="<?= e((string) $pelanggan['foto_profil_url']) ?>" alt="Foto profil"
                         class="h-16 w-16 sm:h-20 sm:w-20 shrink-0 rounded-2xl object-cover border border-border">
                <?php else: ?>
                    <div class="<?= e(design_cn(design_icon_badge('default'), 'flex h-16 w-16 sm:h-20 sm:w-20 shrink-0 items-center justify-center rounded-2xl font-heading text-2xl font-semibold')) ?>" aria-hidden="true">
                        <?= e(mb_strtoupper($initial)) ?>
                    </div>
                <?php endif; ?>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">CRM · Pelanggan</p>
                    <h1 class="mt-1 font-heading text-xl sm:text-2xl font-bold tracking-tight text-foreground truncate">
                        <?= e($namaPelanggan !== '' ? $namaPelanggan : 'Detail Pelanggan') ?>
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground truncate">
                        <?= e((string) ($pelanggan['email'] ?? '')) ?>
                    </p>
                </div>
            </div>
            <a href="/admin/pelanggan"
               class="<?= e(design_cn(ui_btn_secondary(), 'gap-2')) ?>">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </section>

    <section class="<?= e(design_cn(design_surface('panel'), 'p-6 sm:p-8')) ?>">
        <h2 class="font-heading text-lg text-foreground mb-5">Profil</h2>

        <div class="grid gap-4 sm:grid-cols-2 text-sm">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-1">Nama</div>
                <div class="font-medium text-foreground"><?= e((string) ($pelanggan['nama'] ?? '')) ?></div>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-1">Email</div>
                <div class="font-medium text-foreground"><?= e((string) ($pelanggan['email'] ?? '')) ?></div>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-1">Telepon</div>
                <div class="font-medium text-foreground"><?= e((string) ($pelanggan['no_telepon'] ?? '—')) ?></div>
            </div>
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-1">Terdaftar</div>
                <div class="font-medium text-foreground">
                    <?= !empty($pelanggan['created_at'])
                        ? e(date('d M Y H:i', strtotime((string) $pelanggan['created_at'])))
                        : '—' ?>
                </div>
            </div>
            <div class="sm:col-span-2">
                <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-1">Alamat</div>
                <div class="font-medium text-foreground whitespace-pre-wrap"><?= e((string) ($pelanggan['alamat_lengkap'] ?? '—')) ?></div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-5 pt-5 border-t border-border">
            <?php if ($addressComplete): ?>
                <span class="inline-flex items-center rounded-lg bg-success-bg px-2.5 py-1 text-xs font-semibold text-success">
                    Alamat lengkap untuk antar-jemput
                </span>
            <?php else: ?>
                <span class="inline-flex items-center rounded-lg bg-warning-bg px-2.5 py-1 text-xs font-semibold text-amber-800">
                    Alamat belum lengkap
                </span>
            <?php endif; ?>
            <?php if ($promoUsed): ?>
                <span class="inline-flex items-center rounded-lg bg-primary-soft px-2.5 py-1 text-xs font-semibold text-primary">
                    Sudah pernah pakai promo penitipan
                </span>
            <?php else: ?>
                <span class="inline-flex items-center rounded-lg bg-muted px-2.5 py-1 text-xs font-semibold text-muted-foreground">
                    Belum pernah pakai promo penitipan
                </span>
            <?php endif; ?>
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h2 class="font-heading text-lg text-foreground">
                Kucing Milik Pelanggan
            </h2>
            <span class="text-sm font-medium text-muted-foreground"><?= count($kucingList) ?> kucing</span>
        </div>

        <?php if ($kucingList === []): ?>
            <?php
            $variant = 'empty';
            $title = 'Belum ada kucing terdaftar';
            $description = 'Pelanggan ini belum mendaftarkan kucing.';
            $ctaLabel = null;
            $ctaHref = null;
            require __DIR__ . '/../../partials/ui/empty-state.php';
            ?>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($kucingList as $kucing): ?>
                    <?php
                    $vaksinCount = (int) ($kucing['vaksin_count'] ?? 0);
                    $eligible = !empty($kucing['eligible_pet_hotel']);
                    ?>
                    <article class="<?= e(design_cn(design_interactive('listArticle'), design_interactive('listArticleHover'), 'p-5 sm:p-6')) ?>">
                        <div class="flex flex-wrap gap-4 mb-4">
                            <?php if (!empty($kucing['foto_url'])): ?>
                                <img src="<?= e((string) $kucing['foto_url']) ?>" alt="<?= e((string) $kucing['nama']) ?>"
                                     class="h-16 w-16 shrink-0 rounded-2xl object-cover border border-border">
                            <?php else: ?>
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-primary-soft font-heading text-lg font-semibold text-primary" aria-hidden="true">
                                    <?= e(mb_substr((string) $kucing['nama'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>

                            <div class="flex-1 min-w-[200px]">
                                <h3 class="font-heading text-lg text-foreground"><?= e((string) $kucing['nama']) ?></h3>
                                <p class="mt-0.5 text-sm text-muted-foreground">
                                    <?= e($jenisKelaminLabels[$kucing['jenis_kelamin']] ?? (string) $kucing['jenis_kelamin']) ?>
                                    <?php if (!empty($kucing['ras'])): ?>
                                        · <?= e((string) $kucing['ras']) ?>
                                    <?php endif; ?>
                                </p>
                                <div class="flex flex-wrap gap-2 mt-2.5">
                                    <?php if ($eligible): ?>
                                        <span class="inline-flex items-center rounded-lg bg-success-bg px-2 py-0.5 text-xs font-semibold text-success">
                                            Eligible pet hotel
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center rounded-lg bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">
                                            Vaksin belum memenuhi syarat (min. <?= (int) $minVaksin ?>)
                                        </span>
                                    <?php endif; ?>
                                    <span class="inline-flex items-center rounded-lg bg-primary-soft px-2 py-0.5 text-xs font-semibold text-primary">
                                        <?= $vaksinCount ?> entri vaksin lengkap
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3 text-sm mb-4">
                            <?php if (!empty($kucing['tanggal_lahir'])): ?>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-0.5">Tanggal lahir</div>
                                    <div class="font-medium text-foreground">
                                        <?= e(date('d/m/Y', strtotime((string) $kucing['tanggal_lahir']))) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($kucing['berat_badan'])): ?>
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-0.5">Berat badan</div>
                                    <div class="font-medium text-foreground"><?= e((string) $kucing['berat_badan']) ?> kg</div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($kucing['catatan_kesehatan'])): ?>
                                <div class="sm:col-span-3">
                                    <div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-0.5">Catatan kesehatan</div>
                                    <div class="font-medium text-foreground whitespace-pre-wrap"><?= e((string) $kucing['catatan_kesehatan']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="rounded-2xl border border-border bg-muted/60 p-4">
                            <div class="mb-2 text-sm font-semibold text-foreground">Riwayat Vaksin</div>
                            <?php
                            $vaksinList = $kucing['vaksin_list'] ?? [];
                            require __DIR__ . '/../../partials/vaksin-readonly-list.php';
                            ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
