<?php

declare(strict_types=1);

use App\Core\Csrf;

$staff = $staff ?? null;
$errors = $errors ?? [];
$action = $action ?? '';

$inputClass = static function (string $field, array $errors): string {
    $base = 'w-full rounded-xl border bg-page/60 px-3.5 py-3 text-sm text-content-primary placeholder:text-content-secondary/60 transition duration-soft focus:bg-white focus:outline-none focus:ring-2 pr-24';
    if (!empty($errors[$field])) {
        return $base . ' border-red-400 focus:border-red-400 focus:ring-red-200';
    }

    return $base . ' border-border hover:border-admin/30 focus:border-admin focus:ring-admin/25';
};
?>
<div class="font-body space-y-6 max-w-xl">
    <nav class="flex flex-wrap items-center gap-2 text-sm text-content-secondary" aria-label="Breadcrumb">
        <a href="/admin/staff" class="cursor-pointer transition duration-soft hover:text-admin focus:outline-none focus-visible:underline">Manajemen Staff</a>
        <span aria-hidden="true">/</span>
        <span class="font-medium text-content-primary">Reset Password</span>
    </nav>

    <header>
        <h1 class="font-heading text-2xl sm:text-3xl text-content-primary">Reset password staff</h1>
        <p class="mt-2 text-sm text-content-secondary">
            Atur password baru untuk
            <strong class="font-semibold text-content-primary"><?= e((string) ($staff['nama'] ?? '')) ?></strong>
            (<?= e((string) ($staff['email'] ?? '')) ?>).
        </p>
    </header>

    <?php if (!empty($errors['general'])): ?>
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <svg class="h-5 w-5 shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <span><?= e((string) $errors['general']) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e($action) ?>" class="<?= e(design_cn(design_surface('panel'), 'p-5 sm:p-6 space-y-5')) ?>" data-loading-submit>
        <?= Csrf::field() ?>
        <input type="hidden" name="id" value="<?= e((string) ($staff['id'] ?? '')) ?>">

        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-admin-soft text-admin">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="font-heading text-lg text-content-primary">Password baru</h2>
                    <p class="text-xs text-content-secondary">Minimal 8 karakter</p>
                </div>
            </div>
            <button type="button"
                    data-generate-password="reset-password"
                    data-generate-password-confirm="reset-password-confirm"
                    class="cursor-pointer inline-flex items-center gap-1.5 rounded-xl border border-border bg-page/60 px-3 py-2 text-xs font-semibold text-admin transition duration-soft hover:bg-admin-soft focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                Generate
            </button>
        </div>

        <div data-password-field>
            <label for="reset-password" class="mb-1.5 block text-sm font-semibold text-content-primary">Password baru</label>
            <div class="relative">
                <input type="password" id="reset-password" name="password" required minlength="8"
                       autocomplete="new-password"
                       data-password-strength="reset-password-meter"
                       class="<?= e($inputClass('password', $errors)) ?>">
                <button type="button" data-password-toggle="reset-password"
                        class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer rounded-lg px-2.5 py-1.5 text-xs font-medium text-content-secondary transition duration-soft hover:bg-admin-soft hover:text-admin focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                    Tampilkan
                </button>
            </div>
            <div id="reset-password-meter" class="mt-2.5">
                <div class="h-1.5 overflow-hidden rounded-full bg-admin-soft">
                    <div data-strength-bar class="h-full rounded-full transition-all duration-soft" style="width: 0%"></div>
                </div>
                <p data-strength-label class="mt-1 text-xs text-content-secondary"></p>
            </div>
            <?php if (!empty($errors['password'])): ?>
                <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['password']) ?></p>
            <?php endif; ?>
        </div>

        <div data-password-field>
            <label for="reset-password-confirm" class="mb-1.5 block text-sm font-semibold text-content-primary">Konfirmasi password</label>
            <div class="relative">
                <input type="password" id="reset-password-confirm" name="password_confirmation" required minlength="8"
                       autocomplete="new-password"
                       data-password-match="reset-password"
                       class="<?= e($inputClass('password_confirmation', $errors)) ?>">
                <button type="button" data-password-toggle="reset-password-confirm"
                        class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer rounded-lg px-2.5 py-1.5 text-xs font-medium text-content-secondary transition duration-soft hover:bg-admin-soft hover:text-admin focus:outline-none focus-visible:ring-2 focus-visible:ring-admin">
                    Tampilkan
                </button>
            </div>
            <p data-match-hint class="mt-1.5 text-xs text-content-secondary"></p>
            <?php if (!empty($errors['password_confirmation'])): ?>
                <p class="mt-1 text-xs text-red-600"><?= e((string) $errors['password_confirmation']) ?></p>
            <?php endif; ?>
        </div>

        <aside class="rounded-xl border border-amber-200/80 bg-warning-bg/70 px-4 py-3 text-xs text-amber-950">
            Salin password sebelum menutup halaman. Staff harus login ulang dengan password baru.
        </aside>

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <button type="submit"
                    class="cursor-pointer inline-flex items-center justify-center rounded-xl bg-admin px-5 py-3 text-sm font-semibold text-white transition duration-soft hover:bg-admin-hover focus:outline-none focus-visible:ring-2 focus-visible:ring-admin focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                Reset Password
            </button>
            <a href="/admin/staff"
               class="cursor-pointer inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm font-semibold text-content-secondary transition duration-soft hover:text-admin focus:outline-none focus-visible:underline">
                Batal
            </a>
        </div>
    </form>
</div>
