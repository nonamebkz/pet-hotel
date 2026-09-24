<?php

declare(strict_types=1);

use App\Core\Csrf;

$inputClass = 'w-full rounded-lg border border-border bg-background py-3 pl-11 pr-3.5 text-base text-foreground placeholder:text-muted-foreground transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
?>
<div class="<?= e(design_cn(design_surface('metric'), 'mb-6 flex items-start gap-3')) ?>">
    <span class="<?= e(design_cn(design_icon_badge('default'), 'mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center')) ?>">
        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
        </svg>
    </span>
    <div>
        <p class="text-sm font-semibold text-foreground">Akses terbatas</p>
        <p class="mt-0.5 text-xs leading-relaxed text-muted-foreground">Sesi terenkripsi. Hanya untuk staff dan owner petshop.</p>
    </div>
</div>

<header class="mb-6">
    <h2 class="font-heading text-2xl text-foreground">Selamat datang kembali</h2>
    <p class="mt-1 text-sm text-muted-foreground">Masuk untuk mengelola operasional petshop.</p>
</header>

<?php if (!empty($error)): ?>
    <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
        <span><?= e((string) $error) ?></span>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/login" class="<?= e(design_cn(design_surface('metric'), 'space-y-5')) ?>" data-loading-submit>
    <?= Csrf::field() ?>

    <div>
        <label for="identifier" class="mb-1.5 block text-sm font-semibold text-foreground">Email atau Username</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-muted-foreground">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
            </span>
            <input type="text" id="identifier" name="identifier" value="<?= e((string) old('identifier')) ?>" required
                   autocomplete="username"
                   placeholder="nama@petshop.com"
                   class="<?= e($inputClass) ?>">
        </div>
    </div>

    <div>
        <div class="mb-1.5 flex items-center justify-between gap-3">
            <label for="password" class="block text-sm font-semibold text-foreground">Password</label>
            <button type="button" data-password-toggle="password"
                    class="cursor-pointer text-xs font-medium text-primary transition hover:opacity-90 focus:outline-none focus-visible:underline">
                Tampilkan
            </button>
        </div>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-muted-foreground">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </span>
            <input type="password" id="password" name="password" required autocomplete="current-password"
                   placeholder="••••••••"
                   class="<?= e($inputClass) ?>">
        </div>
    </div>

    <button type="submit"
            class="<?= e(design_cn(ui_btn_primary(), 'mt-1 w-full active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60')) ?>">
        Masuk ke Dashboard
    </button>
</form>

<p class="mt-6 text-center text-sm text-muted-foreground">
    <a href="/admin/forgot-password" class="cursor-pointer font-semibold text-primary transition hover:opacity-90 focus:outline-none focus-visible:underline">
        Lupa password?
    </a>
</p>
