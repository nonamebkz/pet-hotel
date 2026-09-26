<?php

declare(strict_types=1);

require __DIR__ . '/../../helpers/navigation.php';

$bookingActive = nav_is_active(['/grooming', '/penitipan', '/pet-care'], true);
$perawatanActive = nav_is_active(['/kucing', '/pet-care/riwayat'], true);

$bottomItemClass = static function (bool $active): string {
    $base = 'flex flex-1 flex-col items-center justify-center gap-0.5 min-h-[4rem] px-1 text-[10px] font-medium transition touch-target';

    if ($active) {
        return $base . ' text-primary';
    }

    return $base . ' text-muted-foreground';
};

$iconWrapClass = static function (bool $active): string {
    if ($active) {
        return 'flex h-9 w-9 items-center justify-center rounded-xl bg-primary/10 text-primary';
    }

    return 'flex h-9 w-9 items-center justify-center rounded-xl text-muted-foreground';
};
?>
<nav
    class="fixed inset-x-0 bottom-0 z-30 border-t border-border bg-card/95 backdrop-blur-md safe-bottom md:hidden print:hidden"
    aria-label="Navigasi utama"
>
    <div class="flex items-stretch justify-around max-w-[90rem] mx-auto">
        <a href="/dashboard" class="<?= e($bottomItemClass(nav_is_active('/dashboard'))) ?>">
            <span class="<?= e($iconWrapClass(nav_is_active('/dashboard'))) ?>" aria-hidden="true">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
            </span>
            Beranda
        </a>
        <a href="/grooming" class="<?= e($bottomItemClass($bookingActive)) ?>">
            <span class="<?= e($iconWrapClass($bookingActive)) ?>" aria-hidden="true">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
            </span>
            Booking
        </a>
        <a href="/kucing" class="<?= e($bottomItemClass($perawatanActive)) ?>">
            <span class="<?= e($iconWrapClass($perawatanActive)) ?>" aria-hidden="true">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                </svg>
            </span>
            Peliharaan
        </a>
        <a href="/profil" class="<?= e($bottomItemClass(nav_is_active('/profil', true))) ?>">
            <span class="<?= e($iconWrapClass(nav_is_active('/profil', true))) ?>" aria-hidden="true">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
            </span>
            Akun
        </a>
    </div>
</nav>
