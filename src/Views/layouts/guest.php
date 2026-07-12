<?php

declare(strict_types=1);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Petshop') ?> — Petshop</title>
    <?php require __DIR__ . '/../partials/head/tailwind-config.php'; ?>
</head>
<body class="min-h-screen font-body text-content-primary antialiased relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-primary-soft via-page to-primary-muted" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-primary/10 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-32 -left-20 h-80 w-80 rounded-full bg-success/10 blur-3xl" aria-hidden="true"></div>

    <main class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-md animate-[fadeIn_0.35s_ease-out]">
            <div class="text-center mb-8">
                <a href="/" class="inline-flex flex-col items-center gap-2 group focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 rounded-xl">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-soft text-primary transition-transform duration-soft group-hover:scale-[1.03]">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4-3.5-7-6.4-7-10a4 4 0 017-2.6A4 4 0 0119 11c0 3.6-3 6.5-7 10z"/>
                        </svg>
                    </span>
                    <span class="font-heading text-3xl tracking-tight text-content-primary">Petshop</span>
                </a>
                <p class="mt-2 text-sm text-content-secondary">Portal Pelanggan</p>
            </div>

            <div class="rounded-2xl bg-card/95 backdrop-blur-sm shadow-soft-lg border border-white/80 p-6 sm:p-8">
                <?php require __DIR__ . '/../partials/flash.php'; ?>
                <?= $content ?? '' ?>
            </div>
        </div>
    </main>

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
