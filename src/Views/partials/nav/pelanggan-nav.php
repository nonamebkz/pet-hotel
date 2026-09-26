<?php

declare(strict_types=1);

use App\Core\Csrf;

require __DIR__ . '/../../helpers/navigation.php';

$navName = (string) ($nama ?? 'Pelanggan');
$navInitials = nav_initials($navName);
$menuSections = pelanggan_nav_menu_sections();

$bookingActive = nav_is_active(['/grooming', '/penitipan', '/pet-care'], true);
$perawatanActive = nav_is_active(['/kucing', '/pet-care/riwayat'], true);
$akunActive = nav_is_active(['/profil', '/change-password']);
$badge = nav_badge_classes();
?>
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-xl focus:bg-primary focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
    Lewati ke konten
</a>

<nav class="sticky top-0 z-40 print:hidden bg-card/90 backdrop-blur-md border-b border-border shadow-sm" data-nav>
    <div class="mx-auto w-full max-w-[90rem] px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14 md:h-16 gap-3 safe-top">
            <div class="flex items-center gap-4 min-w-0">
                <a href="/dashboard"
                   class="group flex items-center gap-2.5 shrink-0 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm transition group-hover:scale-[1.03]">
                        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4-3.5-7-6.4-7-10a4 4 0 017-2.6A4 4 0 0119 11c0 3.6-3 6.5-7 10z"/>
                        </svg>
                    </span>
                    <span class="hidden sm:block font-heading text-lg leading-none text-foreground">Petshop</span>
                </a>

                <div class="hidden lg:flex items-center gap-0.5 ml-2">
                    <a href="/dashboard" class="<?= nav_link_classes(nav_is_active('/dashboard')) ?>">Beranda</a>

                    <div class="relative" data-nav-dropdown>
                        <button
                            type="button"
                            data-nav-dropdown-trigger
                            aria-expanded="false"
                            aria-haspopup="true"
                            class="<?= nav_dropdown_trigger_classes($bookingActive) ?>"
                        >
                            Booking
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div data-nav-dropdown-panel class="<?= nav_dropdown_panel_classes('left') ?>">
                            <p class="px-4 pt-1.5 pb-2 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Ajukan layanan</p>
                            <a href="/grooming" class="<?= nav_dropdown_link_classes(nav_is_active('/grooming', true)) ?>">Grooming</a>
                            <a href="/penitipan" class="<?= nav_dropdown_link_classes(nav_is_active('/penitipan', true)) ?>">Penitipan</a>
                            <a href="/pet-care" class="<?= nav_dropdown_link_classes(nav_is_active('/pet-care', true)) ?>">Pet Care</a>
                        </div>
                    </div>

                    <div class="relative" data-nav-dropdown>
                        <button
                            type="button"
                            data-nav-dropdown-trigger
                            aria-expanded="false"
                            aria-haspopup="true"
                            class="<?= nav_dropdown_trigger_classes($perawatanActive) ?>"
                        >
                            Peliharaan
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div data-nav-dropdown-panel class="<?= nav_dropdown_panel_classes('left') ?>">
                            <a href="/kucing" class="<?= nav_dropdown_link_classes(nav_is_active('/kucing', true)) ?>">Kucing Saya</a>
                            <a href="/pet-care/riwayat" class="<?= nav_dropdown_link_classes(nav_is_active('/pet-care/riwayat', true)) ?>">Riwayat Pet Care</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2">
                <a href="/bantuan"
                   class="hidden md:inline-flex <?= nav_link_classes(nav_is_active('/bantuan', true)) ?>"
                >Bantuan</a>
                <a href="/transaksi"
                   class="hidden xl:inline-flex <?= nav_link_classes(nav_is_active('/transaksi', true)) ?>"
                >Transaksi</a>

                <a href="/notifikasi"
                   class="relative inline-flex min-h-10 min-w-10 cursor-pointer items-center justify-center rounded-xl text-muted-foreground transition hover:bg-muted hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                   aria-label="Notifikasi<?= $navUnreadCount > 0 ? ' (' . $navUnreadCount . ' belum dibaca)' : '' ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <?php if ($navUnreadCount > 0): ?>
                        <span class="absolute -right-0.5 -top-0.5 inline-flex min-w-[1.125rem] items-center justify-center rounded-full bg-primary px-1 text-[10px] font-bold text-primary-foreground">
                            <?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?>
                        </span>
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
                            <p class="text-sm font-semibold text-foreground truncate"><?= e($navName) ?></p>
                            <p class="text-xs text-muted-foreground mt-0.5">Akun pelanggan</p>
                        </div>
                        <a href="/profil" class="<?= nav_dropdown_link_classes(nav_is_active('/profil', true)) ?>">Profil</a>
                        <a href="/change-password" class="<?= nav_dropdown_link_classes(nav_is_active('/change-password', true)) ?>">Ubah Password</a>
                        <a href="/transaksi" class="<?= nav_dropdown_link_classes(nav_is_active('/transaksi', true)) ?>">Riwayat Transaksi</a>
                        <div class="my-1.5 border-t border-border"></div>
                        <form method="POST" action="/logout">
                            <?= Csrf::field() ?>
                            <button type="submit" class="cursor-pointer flex items-center mx-2 px-3 py-2.5 text-sm font-medium text-destructive hover:bg-destructive/10 rounded-xl w-[calc(100%-1rem)] transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-destructive">
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
                    class="lg:hidden cursor-pointer inline-flex items-center justify-center min-h-10 min-w-10 rounded-xl text-muted-foreground transition hover:bg-muted hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
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

        <div id="pelanggan-mobile-nav" data-nav-mobile-panel class="hidden lg:hidden border-t border-border/80 pb-4 max-h-[70vh] overflow-y-auto">
            <div class="pt-3 space-y-3">
                <div class="rounded-2xl bg-muted/40 p-2 ring-1 ring-border">
                    <?php
                    $mobile = true;
                    require __DIR__ . '/_menu-items.php';
                    ?>
                </div>

                <div class="rounded-2xl bg-muted/40 p-2 ring-1 ring-border md:hidden">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Akun</p>
                    <div class="px-3 py-2 mb-1 flex items-center gap-3">
                        <span class="<?= nav_avatar_classes() ?>"><?= e($navInitials) ?></span>
                        <span class="text-sm font-semibold text-foreground truncate"><?= e($navName) ?></span>
                    </div>
                    <div class="space-y-0.5">
                        <a href="/profil" class="<?= nav_mobile_link_classes(nav_is_active('/profil', true)) ?>">Profil</a>
                        <a href="/change-password" class="<?= nav_mobile_link_classes(nav_is_active('/change-password', true)) ?>">Ubah Password</a>
                        <form method="POST" action="/logout">
                            <?= Csrf::field() ?>
                            <button type="submit" class="cursor-pointer flex items-center min-h-11 px-3.5 py-2.5 text-sm font-medium text-destructive hover:bg-destructive/10 rounded-xl w-full transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-destructive">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
