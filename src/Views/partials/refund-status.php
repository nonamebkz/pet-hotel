<?php

declare(strict_types=1);

use App\Enums\StatusRefund;

$statusRefund = $statusRefund ?? null;
$refundEnum = $statusRefund instanceof StatusRefund
    ? $statusRefund
    : StatusRefund::tryFrom((string) ($statusRefund ?? ''));

if (!$refundEnum || $refundEnum === StatusRefund::TIDAK_ADA) {
    return;
}
?>
<div class="text-sm">
    <span class="text-xs px-2 py-1 rounded-full <?= e($refundEnum->badgeClass()) ?>">
        <?= e(StatusRefund::labels()[$refundEnum->value]) ?>
    </span>
</div>
