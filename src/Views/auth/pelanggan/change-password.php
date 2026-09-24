<?php

declare(strict_types=1);

use App\Core\Csrf;

$error = $error ?? null;

$inputClass = 'w-full rounded-lg border border-border bg-background px-3.5 py-3 text-base text-foreground placeholder:text-muted-foreground transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-ring/25';
?>
<div class="<?= e(design_cn('font-body max-w-xl', ui_page_shell_classes())) ?>">
    <section class="<?= e(design_cn(design_surface('metric'), 'relative overflow-hidden p-6 sm:p-8')) ?>">
        <div class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full bg-primary/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start gap-4">
            <span class="<?= e(design_cn(design_icon_badge('default'), 'flex h-12 w-12 shrink-0 items-center justify-center')) ?>" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Keamanan akun</p>
                <h1 class="mt-1 font-heading text-2xl sm:text-3xl text-foreground">Ubah Password</h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Jaga akun Anda agar booking dan data hewan tetap aman.
                </p>
            </div>
        </div>
    </section>

    <aside class="<?= e(design_cn(design_advice_panel_surface('warning'), 'flex items-start gap-3 rounded-2xl border px-4 py-3.5 text-sm')) ?>">
        <span class="<?= e(design_cn(design_icon_badge('default'), 'mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center')) ?>">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
        </span>
        <div class="text-foreground">
            <p class="font-semibold">Tips keamanan</p>
            <p class="mt-0.5 text-xs leading-relaxed text-muted-foreground">
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

    <form method="POST" action="/change-password" class="<?= e(design_cn(design_surface('metric'), 'space-y-5 p-5 sm:p-6')) ?>" data-loading-submit>
        <?= Csrf::field() ?>

        <div data-password-field>
            <div class="mb-1.5 flex items-center justify-between gap-3">
                <label for="current_password" class="block text-sm font-semibold text-foreground">Password lama</label>
                <button type="button" data-password-toggle="current_password"
                        class="cursor-pointer text-xs font-medium text-primary transition hover:opacity-90 focus:outline-none focus-visible:underline">
                    Tampilkan
                </button>
            </div>
            <input type="password" id="current_password" name="current_password" required
                   autocomplete="current-password"
                   placeholder="Password saat ini"
                   class="<?= e($inputClass) ?>">
        </div>

        <div data-password-field class="<?= e(design_cn(design_interactive('metricCellOutlined'), 'space-y-2 p-4')) ?>">
            <div class="mb-1 flex items-center justify-between gap-3">
                <label for="password" class="block text-sm font-semibold text-foreground">Password baru</label>
                <button type="button" data-password-toggle="password"
                        class="cursor-pointer text-xs font-medium text-primary transition hover:opacity-90 focus:outline-none focus-visible:underline">
                    Tampilkan
                </button>
            </div>
            <input type="password" id="password" name="password" required minlength="8"
                   autocomplete="new-password"
                   data-password-strength="password-strength-meter"
                   placeholder="Minimal 8 karakter"
                   class="<?= e($inputClass) ?>">
            <div id="password-strength-meter" class="pt-1">
                <div class="h-1.5 overflow-hidden rounded-full bg-muted">
                    <div data-strength-bar class="h-full rounded-full bg-primary transition-all duration-300" style="width: 0%"></div>
                </div>
                <p data-strength-label class="mt-1.5 text-xs text-muted-foreground"></p>
            </div>
            <ul class="mt-2 space-y-1 text-xs text-muted-foreground">
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
                <label for="password_confirmation" class="block text-sm font-semibold text-foreground">Konfirmasi password baru</label>
                <button type="button" data-password-toggle="password_confirmation"
                        class="cursor-pointer text-xs font-medium text-primary transition hover:opacity-90 focus:outline-none focus-visible:underline">
                    Tampilkan
                </button>
            </div>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                   autocomplete="new-password"
                   data-password-match="password"
                   placeholder="Ulangi password baru"
                   class="<?= e($inputClass) ?>">
            <p data-match-hint class="mt-1.5 text-xs text-muted-foreground"></p>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <button type="submit" class="<?= e(ui_btn_primary()) ?>">
                Simpan Password
            </button>
            <a href="/dashboard" class="<?= e(ui_btn_secondary()) ?>">
                Batal
            </a>
        </div>
    </form>
</div>
