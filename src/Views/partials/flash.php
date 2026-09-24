<?php

declare(strict_types=1);

use App\Core\Session;

$success = Session::getFlash('success');
$error = Session::getFlash('error');
$successCtaLabel = Session::getFlash('success_cta_label');
$successCtaHref = Session::getFlash('success_cta_href');
?>
<?php if ($success): ?>
    <div class="mb-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-800 dark:text-emerald-200">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <span><?= e((string) $success) ?></span>
            <?php if ($successCtaHref && $successCtaLabel): ?>
                <a href="<?= e((string) $successCtaHref) ?>"
                   class="<?= e(ui_btn_primary()) ?> text-xs px-3 py-1.5">
                    <?= e((string) $successCtaLabel) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="<?= e(design_cn(design_advice_panel_surface('danger'), 'mb-4 rounded-2xl border px-4 py-3 text-sm text-destructive')) ?>">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>
