<?php

declare(strict_types=1);

use App\Core\Csrf;

$staff = $staff ?? null;
$errors = $errors ?? [];
$action = $action ?? '';

$inputClass = static function (string $field, array $errors): string {
    $base = design_cn(ui_form_input_class(), 'py-3 pr-24');
    if (!empty($errors[$field])) {
        return $base . ' border-destructive focus:border-destructive focus:ring-destructive/25';
    }

    return $base;
};
?>
<div class="<?= e(design_cn(ui_page_content_shell_classes(), design_page_layout('formSm'))) ?>">
    <?php
    ui_breadcrumb([
        ['label' => 'Manajemen Staff', 'href' => '/admin/staff'],
        ['label' => 'Reset Password'],
    ]);
    ui_page_header(
        'Reset password staff',
        'Staff · Administrasi',
        'Atur password baru untuk ' . (string) ($staff['nama'] ?? '') . ' (' . (string) ($staff['email'] ?? '') . ').',
    );
    ?>

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
                <span class="<?= e(design_icon_badge('default')) ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </span>
                <div>
                    <h2 class="font-heading text-lg text-foreground">Password baru</h2>
                    <p class="text-xs text-muted-foreground">Minimal 8 karakter</p>
                </div>
            </div>
            <button type="button"
                    data-generate-password="reset-password"
                    data-generate-password-confirm="reset-password-confirm"
                    class="cursor-pointer inline-flex items-center gap-1.5 rounded-xl border border-border bg-background px-3 py-2 text-xs font-semibold text-primary transition transition hover:bg-muted/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                Generate
            </button>
        </div>

        <div data-password-field>
            <label for="reset-password" class="mb-1.5 block text-sm font-semibold text-foreground">Password baru</label>
            <div class="relative">
                <input type="password" id="reset-password" name="password" required minlength="8"
                       autocomplete="new-password"
                       data-password-strength="reset-password-meter"
                       class="<?= e($inputClass('password', $errors)) ?>">
                <button type="button" data-password-toggle="reset-password"
                        class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer rounded-lg px-2.5 py-1.5 text-xs font-medium text-muted-foreground transition transition hover:bg-muted/50 hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    Tampilkan
                </button>
            </div>
            <div id="reset-password-meter" class="mt-2.5">
                <div class="h-1.5 overflow-hidden rounded-full bg-muted/50">
                    <div data-strength-bar class="h-full rounded-full transition-all transition" style="width: 0%"></div>
                </div>
                <p data-strength-label class="mt-1 text-xs text-muted-foreground"></p>
            </div>
            <?php if (!empty($errors['password'])): ?>
                <p class="mt-1.5 text-xs text-red-600"><?= e((string) $errors['password']) ?></p>
            <?php endif; ?>
        </div>

        <div data-password-field>
            <label for="reset-password-confirm" class="mb-1.5 block text-sm font-semibold text-foreground">Konfirmasi password</label>
            <div class="relative">
                <input type="password" id="reset-password-confirm" name="password_confirmation" required minlength="8"
                       autocomplete="new-password"
                       data-password-match="reset-password"
                       class="<?= e($inputClass('password_confirmation', $errors)) ?>">
                <button type="button" data-password-toggle="reset-password-confirm"
                        class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer rounded-lg px-2.5 py-1.5 text-xs font-medium text-muted-foreground transition transition hover:bg-muted/50 hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    Tampilkan
                </button>
            </div>
            <p data-match-hint class="mt-1.5 text-xs text-muted-foreground"></p>
            <?php if (!empty($errors['password_confirmation'])): ?>
                <p class="mt-1 text-xs text-red-600"><?= e((string) $errors['password_confirmation']) ?></p>
            <?php endif; ?>
        </div>

        <aside class="rounded-xl border border-amber-200/80 bg-warning-bg/70 px-4 py-3 text-xs text-amber-950">
            Salin password sebelum menutup halaman. Staff harus login ulang dengan password baru.
        </aside>

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <button type="submit"
                    class="<?= e(design_cn(ui_btn_primary(), 'px-5 py-3 disabled:cursor-not-allowed disabled:opacity-60')) ?>">
                Reset Password
            </button>
            <a href="/admin/staff"
               class="cursor-pointer inline-flex items-center justify-center rounded-xl px-4 py-3 text-sm font-semibold text-muted-foreground transition transition hover:text-primary focus:outline-none focus-visible:underline">
                Batal
            </a>
        </div>
    </form>
</div>
