<?php

declare(strict_types=1);

use App\Core\Csrf;

require __DIR__ . '/../../helpers/navigation.php';

$navName = (string) ($nama ?? 'Staff');
$navInitials = nav_initials($navName);
$isOwner = ($role ?? null)?->value === 'OWNER';

$layananActive = nav_is_active([
    '/admin/pet-care',
    '/admin/grooming',
    '/admin/penitipan',
], true);

$akunActive = nav_is_active([
    '/admin/staff',
    '/admin/pengaturan',
    '/admin/change-password',
], true);

$badge = nav_badge_classes(true);
?>
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-xl focus:bg-admin focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
    Lewati ke konten
</a>

<nav class="sticky top-0 z-40 print:hidden bg-card/90 backdrop-blur-md border-b border-white/80 shadow-soft" data-nav>
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16 gap-3">
            <div class="flex items-center gap-4 min-w-0">
                <a href="/admin/dashboard"
                   class="group flex items-center gap-2.5 shrink-0 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-admin text-white shadow-soft transition duration-soft group-hover:scale-[1.03]">
                        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                    </span>
                    <span class="hidden sm:block">
                        <span class="block font-heading text-base leading-tight text-admin">Petshop</span>
                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-content-secondary">Admin</span>
                    </span>
                </a>

                <div class="hidden lg:flex items-center gap-0.5 ml-2">
                    <a href="/admin/dashboard" class="<?= nav_link_classes(nav_is_active('/admin/dashboard'), true, true) ?>">Dashboard</a>
                    <a href="/admin/notifikasi" class="<?= nav_link_classes(nav_is_active('/admin/notifikasi', true), true, true) ?> gap-1.5">
                        Notifikasi
                        <?php if ($navUnreadCount > 0): ?>
                            <span class="<?= e($badge) ?>"><?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/admin/pelanggan" class="<?= nav_link_classes(nav_is_active('/admin/pelanggan', true), true, true) ?>">Pelanggan</a>
                    <a href="/admin/laporan" class="<?= nav_link_classes(nav_is_active('/admin/laporan', true), true, true) ?>">Laporan</a>
                    <a href="/admin/transaksi" class="<?= nav_link_classes(nav_is_active('/admin/transaksi', true), true, true) ?>">Riwayat</a>

                    <div class="relative" data-nav-dropdown>
                        <button
                            type="button"
                            data-nav-dropdown-trigger
                            aria-expanded="false"
                            aria-haspopup="true"
                            class="<?= nav_dropdown_trigger_classes($layananActive, true) ?>"
                        >
                            Layanan
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div data-nav-dropdown-panel class="<?= nav_dropdown_panel_classes('left') ?> min-w-[15rem]">
                            <p class="px-4 pt-1.5 pb-2 text-[11px] font-semibold uppercase tracking-wider text-content-secondary">Kelola Layanan</p>
                            <a href="/admin/pet-care/layanan" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/pet-care', true), true) ?>">Pet Care</a>
                            <a href="/admin/grooming/layanan" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/grooming', true), true) ?>">Grooming</a>
                            <a href="/admin/penitipan/booking" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/penitipan', true), true) ?>">Penitipan</a>
                            <div class="my-1.5 mx-3 border-t border-border"></div>
                            <p class="px-4 pt-1 pb-2 text-[11px] font-semibold uppercase tracking-wider text-content-secondary">Verifikasi</p>
                            <a href="/admin/grooming/pembayaran" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/grooming/pembayaran', true), true) ?>">Verifikasi Grooming</a>
                            <a href="/admin/penitipan/pembayaran" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/penitipan/pembayaran', true), true) ?>">Verifikasi Penitipan</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2">
                <a href="/admin/notifikasi"
                   class="lg:hidden relative cursor-pointer inline-flex items-center justify-center min-h-10 min-w-10 rounded-xl text-content-secondary transition duration-soft hover:bg-admin-soft hover:text-admin focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-admin"
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
                        class="<?= nav_dropdown_trigger_classes($akunActive, true) ?> gap-2 pl-1.5 pr-2.5"
                    >
                        <span class="<?= nav_avatar_classes(true) ?>"><?= e($navInitials) ?></span>
                        <span class="max-w-[7rem] truncate hidden lg:inline"><?= e($navName) ?></span>
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div data-nav-dropdown-panel class="<?= nav_dropdown_panel_classes('right') ?>">
                        <div class="px-4 py-3 border-b border-border mb-1">
                            <span class="inline-flex rounded-lg bg-admin-soft px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-admin"><?= e($roleLabel ?? 'Staff') ?></span>
                            <p class="text-sm font-semibold text-content-primary truncate mt-1.5"><?= e($navName) ?></p>
                        </div>
                        <?php if ($isOwner): ?>
                            <a href="/admin/staff" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/staff', true), true) ?>">Manajemen Staff</a>
                            <a href="/admin/pengaturan" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/pengaturan', true), true) ?>">Pengaturan</a>
                        <?php endif; ?>
                        <a href="/admin/change-password" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/change-password', true), true) ?>">Ubah Password</a>
                        <div class="my-1.5 border-t border-border"></div>
                        <form method="POST" action="/admin/logout">
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
                    aria-controls="admin-mobile-nav"
                    aria-label="Buka menu navigasi"
                    class="lg:hidden cursor-pointer inline-flex items-center justify-center min-h-10 min-w-10 rounded-xl text-content-secondary transition duration-soft hover:bg-admin-soft hover:text-admin focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-admin"
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

        <div id="admin-mobile-nav" data-nav-mobile-panel class="hidden lg:hidden border-t border-border/80 pb-4">
            <div class="pt-3 space-y-3">
                <div class="rounded-2xl bg-page/70 p-2 shadow-soft-inset">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-content-secondary">Menu Utama</p>
                    <div class="space-y-0.5">
                        <a href="/admin/dashboard" class="<?= nav_mobile_link_classes(nav_is_active('/admin/dashboard'), true) ?>">Dashboard</a>
                        <a href="/admin/notifikasi" class="<?= nav_mobile_link_classes(nav_is_active('/admin/notifikasi', true), true) ?> justify-between">
                            <span>Notifikasi</span>
                            <?php if ($navUnreadCount > 0): ?>
                                <span class="<?= e($badge) ?>"><?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/admin/pelanggan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/pelanggan', true), true) ?>">Pelanggan</a>
                        <a href="/admin/laporan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/laporan', true), true) ?>">Laporan</a>
                        <a href="/admin/transaksi" class="<?= nav_mobile_link_classes(nav_is_active('/admin/transaksi', true), true) ?>">Riwayat</a>
                    </div>
                </div>

                <div class="rounded-2xl bg-page/70 p-2 shadow-soft-inset">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-content-secondary">Layanan</p>
                    <div class="space-y-0.5">
                        <a href="/admin/pet-care/layanan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/pet-care', true), true) ?>">Pet Care</a>
                        <a href="/admin/grooming/layanan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/grooming', true), true) ?>">Grooming</a>
                        <a href="/admin/penitipan/booking" class="<?= nav_mobile_link_classes(nav_is_active('/admin/penitipan', true), true) ?>">Penitipan</a>
                        <a href="/admin/grooming/pembayaran" class="<?= nav_mobile_link_classes(nav_is_active('/admin/grooming/pembayaran', true), true) ?>">Verifikasi Grooming</a>
                        <a href="/admin/penitipan/pembayaran" class="<?= nav_mobile_link_classes(nav_is_active('/admin/penitipan/pembayaran', true), true) ?>">Verifikasi Penitipan</a>
                    </div>
                </div>

                <div class="rounded-2xl bg-page/70 p-2 shadow-soft-inset md:hidden">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-content-secondary">Akun</p>
                    <div class="px-3 py-2 mb-1 flex items-center gap-3">
                        <span class="<?= nav_avatar_classes(true) ?>"><?= e($navInitials) ?></span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-content-primary truncate"><?= e($navName) ?></p>
                            <span class="text-xs text-content-secondary"><?= e($roleLabel ?? 'Staff') ?></span>
                        </div>
                    </div>
                    <div class="space-y-0.5">
                        <?php if ($isOwner): ?>
                            <a href="/admin/staff" class="<?= nav_mobile_link_classes(nav_is_active('/admin/staff', true), true) ?>">Manajemen Staff</a>
                            <a href="/admin/pengaturan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/pengaturan', true), true) ?>">Pengaturan</a>
                        <?php endif; ?>
                        <a href="/admin/change-password" class="<?= nav_mobile_link_classes(nav_is_active('/admin/change-password', true), true) ?>">Ubah Password</a>
                        <form method="POST" action="/admin/logout">
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
