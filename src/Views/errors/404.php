<?php

declare(strict_types=1);
?>
<div class="min-h-screen flex items-center justify-center bg-background p-4">
    <div class="<?= e(design_cn(design_surface('metric'), 'max-w-md w-full p-8 text-center')) ?>">
        <h1 class="font-heading text-4xl font-bold text-foreground mb-2">404</h1>
        <p class="text-muted-foreground">Halaman tidak ditemukan.</p>
        <a href="/" class="<?= e(ui_btn_primary()) ?> inline-flex mt-6">Kembali ke beranda</a>
    </div>
</div>
