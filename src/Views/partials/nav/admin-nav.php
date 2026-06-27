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
?>
<nav class="bg-card border-b border-border print:hidden" data-nav>
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-14">
            <div class="flex items-center gap-6">
                <a href="/admin/dashboard" class="font-bold text-primary text-base shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 rounded-sm">
                    Petshop Admin
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="/admin/dashboard" class="<?= nav_link_classes(nav_is_active('/admin/dashboard')) ?>">Dashboard</a>
                    <a href="/admin/notifikasi" class="<?= nav_link_classes(nav_is_active('/admin/notifikasi', true)) ?> inline-flex items-center gap-1">
                        Notifikasi
                        <?php if ($navUnreadCount > 0): ?>
                            <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-primary text-white text-xs font-medium">
                                <?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="/admin/pelanggan" class="<?= nav_link_classes(nav_is_active('/admin/pelanggan', true)) ?>">Pelanggan</a>
                    <a href="/admin/laporan" class="<?= nav_link_classes(nav_is_active('/admin/laporan', true)) ?>">Laporan</a>
                    <a href="/admin/transaksi" class="<?= nav_link_classes(nav_is_active('/admin/transaksi', true)) ?>">Riwayat</a>

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
                            class="hidden absolute left-0 top-full mt-1 w-56 bg-card border border-border rounded-xl shadow-sm py-1 z-50"
                        >
                            <a href="/admin/pet-care/layanan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/pet-care', true)) ?>">Pet Care</a>
                            <a href="/admin/grooming/layanan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/grooming', true)) ?>">Grooming</a>
                            <a href="/admin/penitipan/paket" class="<?= nav_mobile_link_classes(nav_is_active('/admin/penitipan', true)) ?>">Penitipan</a>
                            <div class="my-1 border-t border-border"></div>
                            <a href="/admin/grooming/pembayaran" class="<?= nav_mobile_link_classes(nav_is_active('/admin/grooming/pembayaran', true)) ?>">Verifikasi Grooming</a>
                            <a href="/admin/penitipan/pembayaran" class="<?= nav_mobile_link_classes(nav_is_active('/admin/penitipan/pembayaran', true)) ?>">Verifikasi Penitipan</a>
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
                        class="hidden absolute right-0 top-full mt-1 w-56 bg-card border border-border rounded-xl shadow-sm py-1 z-50"
                    >
                        <div class="px-3 py-2 border-b border-border">
                            <div class="flex items-center gap-2">
                                <span class="text-xs bg-gray-100 text-content-secondary px-2 py-0.5 rounded font-medium"><?= e($roleLabel ?? 'Staff') ?></span>
                            </div>
                            <p class="text-sm font-semibold text-content-primary truncate mt-1"><?= e($navName) ?></p>
                        </div>
                        <?php if ($isOwner): ?>
                            <a href="/admin/staff" class="<?= nav_mobile_link_classes(nav_is_active('/admin/staff', true)) ?>">Manajemen Staff</a>
                            <a href="/admin/pengaturan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/pengaturan', true)) ?>">Pengaturan</a>
                        <?php endif; ?>
                        <a href="/admin/change-password" class="<?= nav_mobile_link_classes(nav_is_active('/admin/change-password', true)) ?>">Ubah Password</a>
                        <form method="POST" action="/admin/logout" class="border-t border-border mt-1 pt-1">
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
                    aria-controls="admin-mobile-nav"
                    aria-label="Buka menu navigasi"
                    class="md:hidden inline-flex items-center justify-center min-h-10 min-w-10 rounded-lg text-content-secondary hover:text-primary hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="admin-mobile-nav" data-nav-mobile-panel class="hidden md:hidden border-t border-border pb-4">
            <div class="pt-3 space-y-4">
                <div>
                    <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wide text-content-secondary">Menu Utama</p>
                    <div class="space-y-0.5">
                        <a href="/admin/dashboard" class="<?= nav_mobile_link_classes(nav_is_active('/admin/dashboard')) ?>">Dashboard</a>
                        <a href="/admin/notifikasi" class="<?= nav_mobile_link_classes(nav_is_active('/admin/notifikasi', true)) ?> justify-between">
                            <span>Notifikasi</span>
                            <?php if ($navUnreadCount > 0): ?>
                                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-primary text-white text-xs font-medium">
                                    <?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <a href="/admin/pelanggan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/pelanggan', true)) ?>">Pelanggan</a>
                        <a href="/admin/laporan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/laporan', true)) ?>">Laporan</a>
                        <a href="/admin/transaksi" class="<?= nav_mobile_link_classes(nav_is_active('/admin/transaksi', true)) ?>">Riwayat</a>
                    </div>
                </div>

                <div>
                    <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wide text-content-secondary">Layanan</p>
                    <div class="space-y-0.5">
                        <a href="/admin/pet-care/layanan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/pet-care', true)) ?>">Pet Care</a>
                        <a href="/admin/grooming/layanan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/grooming', true)) ?>">Grooming</a>
                        <a href="/admin/penitipan/paket" class="<?= nav_mobile_link_classes(nav_is_active('/admin/penitipan', true)) ?>">Penitipan</a>
                        <a href="/admin/grooming/pembayaran" class="<?= nav_mobile_link_classes(nav_is_active('/admin/grooming/pembayaran', true)) ?>">Verifikasi Grooming</a>
                        <a href="/admin/penitipan/pembayaran" class="<?= nav_mobile_link_classes(nav_is_active('/admin/penitipan/pembayaran', true)) ?>">Verifikasi Penitipan</a>
                    </div>
                </div>

                <div>
                    <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wide text-content-secondary">Akun</p>
                    <div class="px-3 py-2 mb-1 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-primary text-xs font-semibold">
                            <?= e($navInitials) ?>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-content-primary truncate"><?= e($navName) ?></p>
                            <span class="text-xs text-content-secondary"><?= e($roleLabel ?? 'Staff') ?></span>
                        </div>
                    </div>
                    <div class="space-y-0.5">
                        <?php if ($isOwner): ?>
                            <a href="/admin/staff" class="<?= nav_mobile_link_classes(nav_is_active('/admin/staff', true)) ?>">Manajemen Staff</a>
                            <a href="/admin/pengaturan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/pengaturan', true)) ?>">Pengaturan</a>
                        <?php endif; ?>
                        <a href="/admin/change-password" class="<?= nav_mobile_link_classes(nav_is_active('/admin/change-password', true)) ?>">Ubah Password</a>
                        <form method="POST" action="/admin/logout">
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
