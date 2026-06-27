<?php

declare(strict_types=1);

$whatsapp = $whatsapp ?? (string) app_settings('petshop_whatsapp');
$bookingId = $bookingId ?? null;
$label = $label ?? 'Hubungi Kami (WhatsApp)';

$prefill = $bookingId
    ? 'Halo, saya ingin batalkan booking dengan ID: ' . $bookingId
    : 'Halo, saya butuh bantuan terkait booking petshop.';
$url = whatsapp_url($prefill);
?>
<a href="<?= e($url) ?>" target="_blank" rel="noopener"
   class="text-sm text-green-700 underline font-medium hover:text-green-800">
    <?= e($label) ?>
</a>
