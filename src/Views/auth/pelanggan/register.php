<?php

declare(strict_types=1);

use App\Core\Csrf;

$errors = $errors ?? [];
?>
<header class="mb-6">
    <h2 class="font-heading text-xl text-foreground sm:text-2xl">Daftar Akun</h2>
    <p class="mt-1 text-sm text-muted-foreground">Buat akun untuk booking layanan petshop.</p>
</header>

<form method="POST" action="/register" class="space-y-4" data-loading-submit>
    <?= Csrf::field() ?>
    <div>
        <label for="nama" class="<?= e(ui_form_label_class()) ?>">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" value="<?= e((string) old('nama')) ?>" required
               class="<?= e(ui_form_input_class(!empty($errors['nama']))) ?>">
        <?php if (!empty($errors['nama'])): ?>
            <p class="<?= e(ui_field_error_class()) ?>"><?= e($errors['nama']) ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="email" class="<?= e(ui_form_label_class()) ?>">Email</label>
        <input type="email" id="email" name="email" value="<?= e((string) old('email')) ?>" required
               autocomplete="email"
               class="<?= e(ui_form_input_class(!empty($errors['email']))) ?>">
        <?php if (!empty($errors['email'])): ?>
            <p class="<?= e(ui_field_error_class()) ?>"><?= e($errors['email']) ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="password" class="<?= e(ui_form_label_class()) ?>">Password</label>
        <input type="password" id="password" name="password" required minlength="8"
               autocomplete="new-password"
               class="<?= e(ui_form_input_class(!empty($errors['password']))) ?>">
        <?php if (!empty($errors['password'])): ?>
            <p class="<?= e(ui_field_error_class()) ?>"><?= e($errors['password']) ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="password_confirmation" class="<?= e(ui_form_label_class()) ?>">Konfirmasi Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
               autocomplete="new-password"
               class="<?= e(ui_form_input_class(!empty($errors['password_confirmation']))) ?>">
        <?php if (!empty($errors['password_confirmation'])): ?>
            <p class="<?= e(ui_field_error_class()) ?>"><?= e($errors['password_confirmation']) ?></p>
        <?php endif; ?>
    </div>
    <button type="submit" class="<?= e(design_cn(ui_btn_primary(), 'w-full active:scale-[0.99]')) ?>">
        Daftar
    </button>
</form>

<p class="mt-6 text-center text-sm text-muted-foreground">
    Sudah punya akun?
    <a href="/login" class="font-semibold text-primary transition hover:opacity-90 focus:outline-none focus-visible:underline">Login</a>
</p>
