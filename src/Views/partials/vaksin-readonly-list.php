<?php

declare(strict_types=1);

/** @var list<array<string, mixed>> $vaksinList */
/** @var string|null $emptyMessage */

$vaksinList = $vaksinList ?? [];
$emptyMessage = $emptyMessage ?? 'Belum ada riwayat vaksin.';
?>
<div class="space-y-3">
    <?php if ($vaksinList === []): ?>
        <p class="text-xs text-gray-500"><?= e($emptyMessage) ?></p>
    <?php else: ?>
        <?php foreach ($vaksinList as $vaksin): ?>
            <div class="flex flex-wrap gap-3 items-start">
                <div class="min-w-[140px]">
                    <div class="text-gray-800"><?= e((string) $vaksin['jenis_vaksin']) ?></div>
                    <div class="text-xs text-gray-500">
                        <?= e(date('d/m/Y', strtotime((string) $vaksin['tanggal_vaksin']))) ?>
                    </div>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <?php if (!empty($vaksin['sertifikat_url'])): ?>
                        <?php
                        $fileUrl = (string) $vaksin['sertifikat_url'];
                        $label = 'Sertifikat';
                        $maxHeightClass = 'max-h-24';
                        require __DIR__ . '/uploaded-file-preview.php';
                        ?>
                    <?php else: ?>
                        <span class="text-xs text-gray-400">Tanpa sertifikat</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
