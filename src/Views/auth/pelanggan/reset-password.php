<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<header class="mb-6">
    <h2 class="font-heading text-xl text-foreground sm:text-2xl">Reset Password</h2>
    <p class="mt-1 text-sm text-muted-foreground">Masukkan password baru untuk akun Anda.</p>
</header>

<?php if (!empty($error)): ?>
    <div class="mb-5 flex items-start gap-3 rounded-xl border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm text-destructive" role="alert">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/reset-password" class="space-y-4" data-loading-submit>
    <?= Csrf::field() ?>
    <input type="hidden" name="token" value="<?= e((string) ($token ?? '')) ?>">
    <div>
        <label for="password" class="<?= e(ui_form_label_class()) ?>">Password Baru</label>
        <input type="password" id="password" name="password" required minlength="8"
               autocomplete="new-password"
               class="<?= e(ui_form_input_class()) ?>">
    </div>
    <div>
        <label for="password_confirmation" class="<?= e(ui_form_label_class()) ?>">Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
               autocomplete="new-password"
               class="<?= e(ui_form_input_class()) ?>">
    </div>
    <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'w-full active:scale-[0.99]')) ?>">
        Reset Password
    </button>
</form>
