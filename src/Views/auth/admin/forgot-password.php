<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<header class="mb-6">
    <h2 class="font-heading text-xl font-semibold text-foreground">Lupa Password</h2>
    <p class="mt-1 text-sm text-muted-foreground">Kami akan mengirim link reset ke email staff Anda.</p>
</header>

<form method="POST" action="/admin/forgot-password" class="<?= e(design_cn(design_surface('metric'), 'space-y-4')) ?>">
    <?= Csrf::field() ?>
    <div>
        <label for="email" class="<?= e(ui_form_label_class()) ?>">Email</label>
        <input type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required
               class="<?= e(ui_form_input_class()) ?>">
    </div>
    <button type="submit" class="<?= e(ui_btn_primary()) ?> w-full">
        Kirim Link Reset
    </button>
</form>

<?php if (!empty($reset_url)): ?>
    <div class="<?= e(design_cn(design_alert_inline('warning'), 'mt-4 px-4 py-3 text-sm')) ?>">
        <p class="font-medium mb-1">Link reset (mode dev):</p>
        <a href="<?= e($reset_url) ?>" class="text-primary break-all hover:underline"><?= e($reset_url) ?></a>
    </div>
<?php endif; ?>

<p class="mt-6 text-center text-sm text-muted-foreground">
    <a href="/admin/login" class="<?= e(ui_back_link_class()) ?> font-semibold text-primary">Kembali ke login</a>
</p>
