<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Petshop') ?> — Admin</title>
    <?php require __DIR__ . '/../partials/head/tailwind-config.php'; ?>
</head>
<body class="min-h-screen font-body text-foreground antialiased relative overflow-x-hidden bg-background">
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-muted via-background to-accent/30" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -top-28 -left-24 h-72 w-72 rounded-full bg-primary/10 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-28 -right-20 h-80 w-80 rounded-full bg-muted/80 blur-3xl" aria-hidden="true"></div>

    <main class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-md animate-[fadeIn_0.35s_ease-out]">
            <div class="text-center mb-8">
                <div class="inline-flex flex-col items-center gap-2">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-sm">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                    </span>
                    <h1 class="font-heading text-3xl tracking-tight text-admin">Petshop Admin</h1>
                </div>
                <p class="mt-2 text-sm text-content-secondary">Portal Staff &amp; Owner</p>
            </div>

            <div class="<?= e(design_cn(design_surface('panel'), 'bg-card/95 p-6 sm:p-8')) ?>">
                <?php require __DIR__ . '/../partials/flash.php'; ?>
                <?= $content ?? '' ?>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/ui/confirm-modal.php'; ?>
    <script src="/js/ui.js" defer></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            .animate-\[fadeIn_0\.35s_ease-out\] {
                animation: none !important;
            }
        }
    </style>
</body>
</html>
