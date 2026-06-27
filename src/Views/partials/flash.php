<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

$success = Session::getFlash('success');
$error = Session::getFlash('error');
?>
<?php if ($success): ?>
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
        <?= e((string) $success) ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>
