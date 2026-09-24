<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<header class="mb-6">
    <h2 class="font-heading text-xl text-foreground sm:text-2xl">Lupa Password</h2>
    <p class="mt-1 text-sm text-muted-foreground">Kami akan mengirim link reset ke email Anda.</p>
</header>

<form method="POST" action="/forgot-password" class="space-y-4" data-loading-submit>
    <?= Csrf::field() ?>
    <div>
        <label for="email" class="<?= e(ui_form_label_class()) ?>">Email</label>
        <input type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required
               autocomplete="email"
               class="<?= e(ui_form_input_class()) ?>">
    </div>
    <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'w-full active:scale-[0.99]')) ?>">
        Kirim Link Reset
    </button>
</form>

<?php if (!empty($reset_url)): ?>
    <div class="<?= e(design_cn(design_alert_inline('warning'), 'mt-4 flex-col items-start gap-1 px-4 py-3')) ?>">
        <p class="font-medium">Link reset (mode dev):</p>
        <a href="<?= e($reset_url) ?>" class="text-primary break-all hover:underline"><?= e($reset_url) ?></a>
    </div>
<?php endif; ?>

<p class="mt-6 text-center text-sm text-muted-foreground">
    <a href="/login" class="font-semibold text-primary transition hover:opacity-90 focus:outline-none focus-visible:underline">Kembali ke login</a>
</p>
