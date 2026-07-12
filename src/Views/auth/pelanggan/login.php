<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<header class="mb-6">
    <h2 class="font-heading text-2xl text-content-primary">Halo, sahabat hewan</h2>
    <p class="mt-1 text-sm text-content-secondary">Masuk untuk booking grooming, penitipan, dan layanan lainnya.</p>
</header>

<?php if (!empty($error)): ?>
    <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
        </svg>
        <span><?= e((string) $error) ?></span>
    </div>
<?php endif; ?>

<form method="POST" action="/login" class="space-y-5" data-loading-submit>
    <?= Csrf::field() ?>

    <div>
        <label for="email" class="mb-1.5 block text-sm font-semibold text-content-primary">Email</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-content-secondary">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                </svg>
            </span>
            <input type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required
                   autocomplete="email"
                   placeholder="nama@email.com"
                   class="w-full rounded-xl border border-border bg-page/60 py-3 pl-11 pr-3.5 text-sm text-content-primary placeholder:text-content-secondary/60 shadow-soft-inset transition duration-soft hover:border-primary/40 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/25">
        </div>
    </div>

    <div>
        <div class="mb-1.5 flex items-center justify-between gap-3">
            <label for="password" class="block text-sm font-semibold text-content-primary">Password</label>
            <button type="button" data-password-toggle="password"
                    class="cursor-pointer text-xs font-medium text-primary transition duration-soft hover:text-primary-hover focus:outline-none focus-visible:underline">
                Tampilkan
            </button>
        </div>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-content-secondary">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </span>
            <input type="password" id="password" name="password" required
                   autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full rounded-xl border border-border bg-page/60 py-3 pl-11 pr-3.5 text-sm text-content-primary placeholder:text-content-secondary/60 shadow-soft-inset transition duration-soft hover:border-primary/40 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/25">
        </div>
    </div>

    <button type="submit"
            class="mt-1 flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-primary py-3 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-primary-hover hover:shadow-soft-lg active:scale-[0.99] focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
        Masuk
    </button>
</form>

<div class="mt-6 space-y-2.5 text-center text-sm text-content-secondary">
    <p>
        <a href="/forgot-password" class="cursor-pointer font-semibold text-primary transition duration-soft hover:text-primary-hover focus:outline-none focus-visible:underline">
            Lupa password?
        </a>
    </p>
    <p>
        Belum punya akun?
        <a href="/register" class="cursor-pointer font-semibold text-primary transition duration-soft hover:text-primary-hover focus:outline-none focus-visible:underline">
            Daftar sekarang
        </a>
    </p>
</div>
