<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$success = Session::getFlash('success');
$error = Session::getFlash('error');
$successCtaLabel = Session::getFlash('success_cta_label');
$successCtaHref = Session::getFlash('success_cta_href');
?>
<?php if ($success): ?>
    <div class="mb-4 rounded-lg bg-success-bg border border-success/20 px-4 py-3 text-sm text-success">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <span><?= e((string) $success) ?></span>
            <?php if ($successCtaHref && $successCtaLabel): ?>
                <a href="<?= e((string) $successCtaHref) ?>"
                   class="cursor-pointer inline-flex items-center justify-center rounded-lg bg-success px-3 py-1.5 text-xs font-semibold text-white transition duration-soft hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-success">
                    <?= e((string) $successCtaLabel) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>
