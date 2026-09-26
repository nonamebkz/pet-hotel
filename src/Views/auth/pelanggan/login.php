<?php

declare(strict_types=1);

use App\Core\Csrf;

$inputClass = 'w-full rounded-lg border border-border bg-background py-3 pl-11 pr-3.5 text-base text-foreground placeholder:text-muted-foreground transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
?>
<header class="mb-6">
    <h2 class="font-heading text-2xl text-foreground">Halo, sahabat hewan</h2>
    <p class="mt-1 text-sm text-muted-foreground">Masuk untuk booking grooming, penitipan, dan layanan lainnya.</p>
</header>

<?php if (!empty($error)): ?>
    <div class="<?= e(design_cn(design_alert_inline('warning'), 'mb-5 px-4 py-3 text-sm')) ?>" role="alert">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/login" class="<?= e(design_cn(design_surface('metric'), 'space-y-5')) ?>" data-loading-submit>
    <?= Csrf::field() ?>

    <div>
        <label for="email" class="mb-1.5 block text-sm font-semibold text-foreground">Email</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-muted-foreground">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                </svg>
            </span>
            <input type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required
                   autocomplete="email"
                   placeholder="nama@email.com"
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
            <input type="password" id="password" name="password" required
                   autocomplete="current-password"
                   placeholder="••••••••"
                   class="<?= e($inputClass) ?>">
        </div>
    </div>

    <button type="submit"
            class="<?= e(design_cn(ui_btn_primary(), 'mt-1 w-full active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60')) ?>">
        Masuk
    </button>
</form>

<div class="mt-6 space-y-2.5 text-center text-sm text-muted-foreground">
    <p>
        <a href="/forgot-password" class="cursor-pointer font-semibold text-primary transition hover:opacity-90 focus:outline-none focus-visible:underline">
            Lupa password?
        </a>
    </p>
    <p>
        Belum punya akun?
        <a href="/register" class="cursor-pointer font-semibold text-primary transition hover:opacity-90 focus:outline-none focus-visible:underline">
            Daftar sekarang
        </a>
    </p>
</div>
