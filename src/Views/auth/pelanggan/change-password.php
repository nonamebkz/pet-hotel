<?php

declare(strict_types=1);

use App\Core\Csrf;

$error = $error ?? null;

$inputClass = 'w-full rounded-xl border border-border bg-page/60 px-3.5 py-3 text-sm text-content-primary placeholder:text-content-secondary/60 shadow-soft-inset transition duration-soft hover:border-primary/40 focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/25';
?>
<div class="font-body max-w-xl space-y-6">
    <section class="relative overflow-hidden rounded-2xl border border-white/80 bg-card p-6 sm:p-8 shadow-soft">
        <div class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary text-white shadow-soft" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-content-secondary">Keamanan akun</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-content-primary">Ubah Password</h1>
                <p class="mt-2 text-sm text-content-secondary">
                    Jaga akun Anda agar booking dan data hewan tetap aman.
                </p>
            </div>
        </div>
    </section>

    <aside class="flex items-start gap-3 rounded-2xl border border-primary/20 bg-primary-soft/80 px-4 py-3.5 shadow-soft">
        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-primary shadow-soft">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
        </span>
        <div class="text-sm text-content-primary">
            <p class="font-semibold">Tips keamanan</p>
            <p class="mt-0.5 text-xs leading-relaxed text-content-secondary">
                Gunakan password unik. Jangan bagikan ke orang lain, termasuk staff petshop.
            </p>
        </div>
    </aside>

    <?php if (!empty($error)): ?>
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <span><?= e((string) $error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="/change-password" class="rounded-2xl border border-white/80 bg-card p-5 sm:p-6 shadow-soft space-y-5" data-loading-submit>
        <?= Csrf::field() ?>

        <div data-password-field>
            <div class="mb-1.5 flex items-center justify-between gap-3">
                <label for="current_password" class="block text-sm font-semibold text-content-primary">Password lama</label>
                <button type="button" data-password-toggle="current_password"
                        class="cursor-pointer text-xs font-medium text-primary transition duration-soft hover:text-primary-hover focus:outline-none focus-visible:underline">
                    Tampilkan
                </button>
            </div>
            <input type="password" id="current_password" name="current_password" required
                   autocomplete="current-password"
                   placeholder="Password saat ini"
                   class="<?= e($inputClass) ?>">
        </div>

        <div data-password-field class="rounded-xl border border-border bg-page/40 p-4 space-y-2">
            <div class="mb-1 flex items-center justify-between gap-3">
                <label for="password" class="block text-sm font-semibold text-content-primary">Password baru</label>
                <button type="button" data-password-toggle="password"
                        class="cursor-pointer text-xs font-medium text-primary transition duration-soft hover:text-primary-hover focus:outline-none focus-visible:underline">
                    Tampilkan
                </button>
            </div>
            <input type="password" id="password" name="password" required minlength="8"
                   autocomplete="new-password"
                   data-password-strength="password-strength-meter"
                   placeholder="Minimal 8 karakter"
                   class="<?= e($inputClass) ?>">
            <div id="password-strength-meter" class="pt-1">
                <div class="h-1.5 overflow-hidden rounded-full bg-primary-muted">
                    <div data-strength-bar class="h-full rounded-full transition-all duration-soft" style="width: 0%"></div>
                </div>
                <p data-strength-label class="mt-1.5 text-xs text-content-secondary"></p>
            </div>
            <ul class="mt-2 space-y-1 text-xs text-content-secondary">
                <li class="flex items-center gap-1.5">
                    <span class="h-1 w-1 rounded-full bg-primary" aria-hidden="true"></span>
                    Minimal 8 karakter
                </li>
                <li class="flex items-center gap-1.5">
                    <span class="h-1 w-1 rounded-full bg-primary" aria-hidden="true"></span>
                    Lebih aman jika ada huruf besar, angka, dan simbol
                </li>
            </ul>
        </div>

        <div data-password-field>
            <div class="mb-1.5 flex items-center justify-between gap-3">
                <label for="password_confirmation" class="block text-sm font-semibold text-content-primary">Konfirmasi password baru</label>
                <button type="button" data-password-toggle="password_confirmation"
                        class="cursor-pointer text-xs font-medium text-primary transition duration-soft hover:text-primary-hover focus:outline-none focus-visible:underline">
                    Tampilkan
                </button>
            </div>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                   autocomplete="new-password"
                   data-password-match="password"
                   placeholder="Ulangi password baru"
                   class="<?= e($inputClass) ?>">
            <p data-match-hint class="mt-1.5 text-xs text-content-secondary"></p>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <button type="submit"
                    class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-soft transition duration-soft hover:bg-primary-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                Simpan Password
            </button>
            <a href="/dashboard"
               class="cursor-pointer inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm font-semibold text-content-secondary transition duration-soft hover:text-primary focus:outline-none focus-visible:underline">
                Batal
            </a>
        </div>
    </form>
</div>
