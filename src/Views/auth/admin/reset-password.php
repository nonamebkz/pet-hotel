<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<header class="mb-6">
    <h2 class="font-heading text-xl font-semibold text-foreground">Reset Password</h2>
    <p class="mt-1 text-sm text-muted-foreground">Buat password baru minimal 8 karakter.</p>
</header>

<?php if (!empty($error)): ?>
    <div class="<?= e(design_cn(design_alert_inline('warning'), 'mb-4 px-4 py-3 text-sm')) ?>" role="alert">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/reset-password" class="<?= e(design_cn(design_surface('metric'), 'space-y-4')) ?>">
    <?= Csrf::field() ?>
    <input type="hidden" name="token" value="<?= e((string) ($token ?? '')) ?>">
    <div>
        <label for="password" class="<?= e(ui_form_label_class()) ?>">Password Baru</label>
        <input type="password" id="password" name="password" required minlength="8"
               class="<?= e(ui_form_input_class()) ?>">
    </div>
    <div>
        <label for="password_confirmation" class="<?= e(ui_form_label_class()) ?>">Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
               class="<?= e(ui_form_input_class()) ?>">
    </div>
    <button type="submit" class="<?= e(ui_btn_primary()) ?> w-full">
        Reset Password
    </button>
</form>
