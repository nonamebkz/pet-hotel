<?php

declare(strict_types=1);

use App\Core\Csrf;

require __DIR__ . '/../../helpers/navigation.php';

$navName = (string) ($nama ?? 'Staff');
$navInitials = nav_initials($navName);
$isOwner = ($role ?? null)?->value === 'OWNER';
$menuSections = admin_nav_menu_sections($isOwner);

$akunActive = nav_is_active([
    '/admin/staff',
    '/admin/pengaturan',
    '/admin/change-password',
], true);

$badge = nav_badge_classes(true);
// Satu lokasi — tampilkan label statis (tanpa dropdown cabang palsu, plan-redesign §4).
$locationLabel = 'Panel operasional petshop';
?>
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-xl focus:bg-primary focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
    Lewati ke konten
</a>

<aside
    class="hidden lg:flex lg:flex-col w-[248px] shrink-0 border-r border-border bg-card print:hidden sticky top-0 h-screen overflow-y-auto"
    aria-label="Navigasi operasional"
>
    <div class="flex h-16 items-center gap-2.5 border-b border-border px-4">
        <a href="/admin/dashboard" class="group flex min-w-0 items-center gap-2.5 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm">
                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                </svg>
            </span>
            <span class="min-w-0">
                <span class="block truncate font-heading text-base leading-tight text-primary">Petshop</span>
                <span class="block text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">Operasional</span>
            </span>
        </a>
    </div>
    <nav class="flex-1 overflow-y-auto pb-6">
        <?php
        $mobile = false;
        require __DIR__ . '/_menu-items.php';
        ?>
    </nav>
</aside>

<div class="flex min-w-0 flex-1 flex-col">
    <header class="sticky top-0 z-40 print:hidden border-b border-border bg-card/90 backdrop-blur-md shadow-sm" data-nav>
        <div class="mx-auto flex h-14 md:h-16 w-full max-w-[90rem] items-center justify-between gap-3 px-4 sm:px-6 lg:px-8 safe-top">
            <div class="flex min-w-0 items-center gap-3 lg:hidden">
                <a href="/admin/dashboard" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm" aria-label="Beranda admin">
                    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </a>
                <span class="font-heading text-base font-semibold text-foreground truncate">Admin</span>
            </div>

            <p class="hidden lg:block text-sm text-muted-foreground truncate">
                <?= e($locationLabel) ?>
            </p>

            <div class="flex items-center gap-1.5 sm:gap-2">
                <a href="/admin/notifikasi"
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
                            <span class="inline-flex rounded-lg bg-primary/10 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary"><?= e($roleLabel ?? 'Staff') ?></span>
                            <p class="text-sm font-semibold text-foreground truncate mt-1.5"><?= e($navName) ?></p>
                        </div>
                        <a href="/admin/change-password" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/change-password', true), true) ?>">Ubah Password</a>
                        <?php if ($isOwner): ?>
                            <a href="/admin/staff" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/staff', true), true) ?>">Manajemen Staff</a>
                            <a href="/admin/pengaturan" class="<?= nav_dropdown_link_classes(nav_is_active('/admin/pengaturan', true), true) ?>">Pengaturan</a>
                        <?php endif; ?>
                        <div class="my-1.5 border-t border-border"></div>
                        <form method="POST" action="/admin/logout">
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
                    aria-controls="admin-mobile-nav"
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

        <div id="admin-mobile-nav" data-nav-mobile-panel class="hidden lg:hidden border-t border-border/80 pb-4 max-h-[70vh] overflow-y-auto">
            <div class="mx-auto max-w-[90rem] px-4 pt-3 space-y-3">
                <?php
                $mobile = true;
                require __DIR__ . '/_menu-items.php';
                ?>
                <div class="rounded-2xl bg-muted/40 p-2 ring-1 ring-border md:hidden">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Akun</p>
                    <div class="px-3 py-2 mb-1 flex items-center gap-3">
                        <span class="<?= nav_avatar_classes(true) ?>"><?= e($navInitials) ?></span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-foreground truncate"><?= e($navName) ?></p>
                            <span class="text-xs text-muted-foreground"><?= e($roleLabel ?? 'Staff') ?></span>
                        </div>
                    </div>
                    <div class="space-y-0.5">
                        <a href="/admin/change-password" class="<?= nav_mobile_link_classes(nav_is_active('/admin/change-password', true), true) ?>">Ubah Password</a>
                        <?php if ($isOwner): ?>
                            <a href="/admin/staff" class="<?= nav_mobile_link_classes(nav_is_active('/admin/staff', true), true) ?>">Manajemen Staff</a>
                            <a href="/admin/pengaturan" class="<?= nav_mobile_link_classes(nav_is_active('/admin/pengaturan', true), true) ?>">Pengaturan</a>
                        <?php endif; ?>
                        <form method="POST" action="/admin/logout">
                            <?= Csrf::field() ?>
                            <button type="submit" class="cursor-pointer flex items-center min-h-11 px-3.5 py-2.5 text-sm font-medium text-destructive hover:bg-destructive/10 rounded-xl w-full transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-destructive">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
