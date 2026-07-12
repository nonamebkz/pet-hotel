<?php

declare(strict_types=1);

use App\Core\Csrf;

require __DIR__ . '/../../helpers/navigation.php';

$navName = (string) ($nama ?? 'Pelanggan');
$navInitials = nav_initials($navName);

$layananActive = nav_is_active(['/grooming', '/penitipan', '/pet-care', '/kucing'], true);
$akunActive = nav_is_active(['/profil', '/change-password']);
$badge = nav_badge_classes();
?>
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-xl focus:bg-primary focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
    Lewati ke konten
</a>

<nav class="sticky top-0 z-40 print:hidden bg-card/90 backdrop-blur-md border-b border-white/80 shadow-soft" data-nav>
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16 gap-3">
            <div class="flex items-center gap-4 min-w-0">
                <a href="/dashboard"
                   class="group flex items-center gap-2.5 shrink-0 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-white shadow-soft transition duration-soft group-hover:scale-[1.03]">
                        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4-3.5-7-6.4-7-10a4 4 0 017-2.6A4 4 0 0119 11c0 3.6-3 6.5-7 10z"/>
                        </svg>
                    </span>
                    <span class="font-heading text-lg leading-none text-content-primary">Petshop</span>
                </a>

                <div class="hidden lg:flex items-center gap-0.5 ml-2">
                    <a href="/dashboard" class="<?= nav_link_classes(nav_is_active('/dashboard')) ?>">Dashboard</a>
                    <a href="/notifikasi" class="<?= nav_link_classes(nav_is_active('/notifikasi', true)) ?> gap-1.5">
                        Notifikasi
                        <?php if ($navUnreadCount > 0): ?>
                            <span class="<?= e($badge) ?>"><?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?></span>
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
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div data-nav-dropdown-panel class="<?= nav_dropdown_panel_classes('left') ?>">
                            <p class="px-4 pt-1.5 pb-2 text-[11px] font-semibold uppercase tracking-wider text-content-secondary">Layanan</p>
                            <a href="/grooming" class="<?= nav_dropdown_link_classes(nav_is_active('/grooming', true)) ?>">Grooming</a>
                            <a href="/penitipan" class="<?= nav_dropdown_link_classes(nav_is_active('/penitipan', true)) ?>">Penitipan</a>
                            <a href="/pet-care" class="<?= nav_dropdown_link_classes(nav_is_active('/pet-care', true)) ?>">Pet Care</a>
                            <a href="/kucing" class="<?= nav_dropdown_link_classes(nav_is_active('/kucing', true)) ?>">Kucing Saya</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2">
                <a href="/notifikasi"
                   class="lg:hidden relative cursor-pointer inline-flex items-center justify-center min-h-10 min-w-10 rounded-xl text-content-secondary transition duration-soft hover:bg-primary-soft hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                   aria-label="Notifikasi<?= $navUnreadCount > 0 ? ' (' . $navUnreadCount . ' belum dibaca)' : '' ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <?php if ($navUnreadCount > 0): ?>
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-primary ring-2 ring-card"></span>
                    <?php endif; ?>
                </a>

                <div class="hidden md:block relative" data-nav-dropdown>
                    <button
                        type="button"
                        data-nav-dropdown-trigger
                        aria-expanded="false"
                        aria-haspopup="true"
                        class="<?= nav_dropdown_trigger_classes($akunActive) ?> gap-2 pl-1.5 pr-2.5"
                    >
                        <span class="<?= nav_avatar_classes() ?>"><?= e($navInitials) ?></span>
                        <span class="max-w-[7rem] truncate hidden lg:inline"><?= e($navName) ?></span>
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div data-nav-dropdown-panel class="<?= nav_dropdown_panel_classes('right') ?>">
                        <div class="px-4 py-3 border-b border-border mb-1">
                            <p class="text-sm font-semibold text-content-primary truncate"><?= e($navName) ?></p>
                            <p class="text-xs text-content-secondary mt-0.5">Akun pelanggan</p>
                        </div>
                        <a href="/profil" class="<?= nav_dropdown_link_classes(nav_is_active('/profil', true)) ?>">Profil</a>
                        <a href="/change-password" class="<?= nav_dropdown_link_classes(nav_is_active('/change-password', true)) ?>">Ubah Password</a>
                        <div class="my-1.5 border-t border-border"></div>
                        <form method="POST" action="/logout">
                            <?= Csrf::field() ?>
                            <button type="submit" class="cursor-pointer flex items-center mx-2 px-3 py-2.5 text-sm font-medium text-danger hover:bg-red-50 rounded-xl w-[calc(100%-1rem)] transition duration-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger">
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
                    class="lg:hidden cursor-pointer inline-flex items-center justify-center min-h-10 min-w-10 rounded-xl text-content-secondary transition duration-soft hover:bg-primary-soft hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                >
                    <svg data-nav-icon-open class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                    <svg data-nav-icon-close class="hidden w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="pelanggan-mobile-nav" data-nav-mobile-panel class="hidden lg:hidden border-t border-border/80 pb-4">
            <div class="pt-3 space-y-3">
                <div class="rounded-2xl bg-page/70 p-2 shadow-soft-inset">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-content-secondary">Menu Utama</p>
                    <div class="space-y-0.5">
                        <a href="/dashboard" class="<?= nav_mobile_link_classes(nav_is_active('/dashboard')) ?>">Dashboard</a>
                        <a href="/notifikasi" class="<?= nav_mobile_link_classes(nav_is_active('/notifikasi', true)) ?> justify-between">
                            <span>Notifikasi</span>
                            <?php if ($navUnreadCount > 0): ?>
                                <span class="<?= e($badge) ?>"><?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/bantuan" class="<?= nav_mobile_link_classes(nav_is_active('/bantuan', true)) ?>">Bantuan</a>
                        <a href="/transaksi" class="<?= nav_mobile_link_classes(nav_is_active('/transaksi', true)) ?>">Riwayat</a>
                    </div>
                </div>

                <div class="rounded-2xl bg-page/70 p-2 shadow-soft-inset">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-content-secondary">Layanan</p>
                    <div class="space-y-0.5">
                        <a href="/grooming" class="<?= nav_mobile_link_classes(nav_is_active('/grooming', true)) ?>">Grooming</a>
                        <a href="/penitipan" class="<?= nav_mobile_link_classes(nav_is_active('/penitipan', true)) ?>">Penitipan</a>
                        <a href="/pet-care" class="<?= nav_mobile_link_classes(nav_is_active('/pet-care', true)) ?>">Pet Care</a>
                        <a href="/kucing" class="<?= nav_mobile_link_classes(nav_is_active('/kucing', true)) ?>">Kucing Saya</a>
                    </div>
                </div>

                <div class="rounded-2xl bg-page/70 p-2 shadow-soft-inset md:hidden">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-content-secondary">Akun</p>
                    <div class="px-3 py-2 mb-1 flex items-center gap-3">
                        <span class="<?= nav_avatar_classes() ?>"><?= e($navInitials) ?></span>
                        <span class="text-sm font-semibold text-content-primary truncate"><?= e($navName) ?></span>
                    </div>
                    <div class="space-y-0.5">
                        <a href="/profil" class="<?= nav_mobile_link_classes(nav_is_active('/profil', true)) ?>">Profil</a>
                        <a href="/change-password" class="<?= nav_mobile_link_classes(nav_is_active('/change-password', true)) ?>">Ubah Password</a>
                        <form method="POST" action="/logout">
                            <?= Csrf::field() ?>
                            <button type="submit" class="cursor-pointer flex items-center min-h-11 px-3.5 py-2.5 text-sm font-medium text-danger hover:bg-red-50 rounded-xl w-full transition duration-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-danger">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
