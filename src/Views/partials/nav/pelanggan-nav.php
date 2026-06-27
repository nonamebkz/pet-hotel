<?php

declare(strict_types=1);

use App\Core\Csrf;

require __DIR__ . '/../../helpers/navigation.php';

$navName = (string) ($nama ?? 'Pelanggan');
$navInitials = nav_initials($navName);

$layananActive = nav_is_active(['/grooming', '/penitipan', '/pet-care', '/kucing'], true);
$akunActive = nav_is_active(['/profil', '/change-password']);
?>
<nav class="bg-card border-b border-border print:hidden" data-nav>
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-14">
            <div class="flex items-center gap-6">
                <a href="/dashboard" class="font-bold text-primary text-base shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 rounded-sm">
                    Petshop
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="/dashboard" class="<?= nav_link_classes(nav_is_active('/dashboard')) ?>">Dashboard</a>
                    <a href="/notifikasi" class="<?= nav_link_classes(nav_is_active('/notifikasi', true)) ?> inline-flex items-center gap-1">
                        Notifikasi
                        <?php if ($navUnreadCount > 0): ?>
                            <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-primary text-white text-xs font-medium">
                                <?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="/bantuan" class="<?= nav_link_classes(nav_is_active('/bantuan', true)) ?>">Bantuan</a>
                    <a href="/transaksi" class="<?= nav_link_classes(nav_is_active('/transaksi', true)) ?>">Riwayat</a>

                    <div class="relative" data-nav-dropdown>
                        <button
                            type="button"
                            data-nav-dropdown-trigger
                            aria-expanded="false"
                            aria-haspopup="true"
                            class="<?= nav_dropdown_trigger_classes($layananActive) ?>"
                        >
                            Layanan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div
                            data-nav-dropdown-panel
                            class="hidden absolute left-0 top-full mt-1 w-48 bg-card border border-border rounded-xl shadow-sm py-1 z-50"
                        >
                            <a href="/grooming" class="<?= nav_mobile_link_classes(nav_is_active('/grooming', true)) ?>">Grooming</a>
                            <a href="/penitipan" class="<?= nav_mobile_link_classes(nav_is_active('/penitipan', true)) ?>">Penitipan</a>
                            <a href="/pet-care" class="<?= nav_mobile_link_classes(nav_is_active('/pet-care', true)) ?>">Pet Care</a>
                            <a href="/kucing" class="<?= nav_mobile_link_classes(nav_is_active('/kucing', true)) ?>">Kucing Saya</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="hidden md:block relative" data-nav-dropdown>
                    <button
                        type="button"
                        data-nav-dropdown-trigger
                        aria-expanded="false"
                        aria-haspopup="true"
                        class="<?= nav_dropdown_trigger_classes($akunActive) ?> gap-2"
                    >
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-primary text-xs font-semibold">
                            <?= e($navInitials) ?>
                        </span>
                        <span class="max-w-[8rem] truncate"><?= e($navName) ?></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div
                        data-nav-dropdown-panel
                        class="hidden absolute right-0 top-full mt-1 w-52 bg-card border border-border rounded-xl shadow-sm py-1 z-50"
                    >
                        <div class="px-3 py-2 border-b border-border">
                            <p class="text-sm font-semibold text-content-primary truncate"><?= e($navName) ?></p>
                        </div>
                        <a href="/profil" class="<?= nav_mobile_link_classes(nav_is_active('/profil', true)) ?>">Profil</a>
                        <a href="/change-password" class="<?= nav_mobile_link_classes(nav_is_active('/change-password', true)) ?>">Ubah Password</a>
                        <form method="POST" action="/logout" class="border-t border-border mt-1 pt-1">
                            <?= Csrf::field() ?>
                            <button type="submit" class="flex items-center min-h-10 px-3 py-2 text-sm text-danger hover:bg-red-50 rounded-lg w-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <button
                    type="button"
                    data-nav-mobile-trigger
                    aria-expanded="false"
                    aria-controls="pelanggan-mobile-nav"
                    aria-label="Buka menu navigasi"
                    class="md:hidden inline-flex items-center justify-center min-h-10 min-w-10 rounded-lg text-content-secondary hover:text-primary hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="pelanggan-mobile-nav" data-nav-mobile-panel class="hidden md:hidden border-t border-border pb-4">
            <div class="pt-3 space-y-4">
                <div>
                    <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wide text-content-secondary">Menu Utama</p>
                    <div class="space-y-0.5">
                        <a href="/dashboard" class="<?= nav_mobile_link_classes(nav_is_active('/dashboard')) ?>">Dashboard</a>
                        <a href="/notifikasi" class="<?= nav_mobile_link_classes(nav_is_active('/notifikasi', true)) ?> justify-between">
                            <span>Notifikasi</span>
                            <?php if ($navUnreadCount > 0): ?>
                                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-primary text-white text-xs font-medium">
                                    <?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <a href="/bantuan" class="<?= nav_mobile_link_classes(nav_is_active('/bantuan', true)) ?>">Bantuan</a>
                        <a href="/transaksi" class="<?= nav_mobile_link_classes(nav_is_active('/transaksi', true)) ?>">Riwayat</a>
                    </div>
                </div>

                <div>
                    <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wide text-content-secondary">Layanan</p>
                    <div class="space-y-0.5">
                        <a href="/grooming" class="<?= nav_mobile_link_classes(nav_is_active('/grooming', true)) ?>">Grooming</a>
                        <a href="/penitipan" class="<?= nav_mobile_link_classes(nav_is_active('/penitipan', true)) ?>">Penitipan</a>
                        <a href="/pet-care" class="<?= nav_mobile_link_classes(nav_is_active('/pet-care', true)) ?>">Pet Care</a>
                        <a href="/kucing" class="<?= nav_mobile_link_classes(nav_is_active('/kucing', true)) ?>">Kucing Saya</a>
                    </div>
                </div>

                <div>
                    <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wide text-content-secondary">Akun</p>
                    <div class="px-3 py-2 mb-1 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-primary text-xs font-semibold">
                            <?= e($navInitials) ?>
                        </span>
                        <span class="text-sm font-semibold text-content-primary truncate"><?= e($navName) ?></span>
                    </div>
                    <div class="space-y-0.5">
                        <a href="/profil" class="<?= nav_mobile_link_classes(nav_is_active('/profil', true)) ?>">Profil</a>
                        <a href="/change-password" class="<?= nav_mobile_link_classes(nav_is_active('/change-password', true)) ?>">Ubah Password</a>
                        <form method="POST" action="/logout">
                            <?= Csrf::field() ?>
                            <button type="submit" class="flex items-center min-h-10 px-3 py-2 text-sm text-danger hover:bg-red-50 rounded-lg w-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
