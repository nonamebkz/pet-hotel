<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<div class="mb-4 rounded-lg bg-slate-50 border border-slate-200 px-4 py-3 flex items-start gap-3">
    <svg class="h-5 w-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
    </svg>
    <div>
        <p class="text-sm font-medium text-gray-800">Login aman</p>
        <p class="text-xs text-gray-500 mt-0.5">Sesi terenkripsi. Hanya untuk staff dan owner petshop.</p>
    </div>
</div>

<h2 class="text-xl font-semibold text-gray-800 mb-4">Login Staff / Owner</h2>

<?php if (!empty($error)): ?>
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800" role="alert">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/login" class="space-y-4" data-loading-submit>
    <?= Csrf::field() ?>
    <div>
        <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">Email atau Username</label>
        <input type="text" id="identifier" name="identifier" value="<?= e((string) old('identifier')) ?>" required
               autocomplete="username"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-500">
    </div>
    <div>
        <div class="flex items-center justify-between mb-1">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <button type="button" data-password-toggle="password"
                    class="text-xs text-slate-600 hover:underline">Tampilkan</button>
        </div>
        <input type="password" id="password" name="password" required autocomplete="current-password"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-500">
    </div>
    <button type="submit"
            class="w-full bg-slate-800 text-white rounded-lg py-2 font-medium hover:bg-slate-900 disabled:opacity-60 disabled:cursor-not-allowed">
        Login
    </button>
</form>

<p class="mt-4 text-center text-sm">
    <a href="/admin/forgot-password" class="text-slate-700 hover:underline font-medium">Lupa password?</a>
</p>
