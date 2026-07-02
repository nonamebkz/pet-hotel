<?php

declare(strict_types=1);

use App\Core\Csrf;
?>
<h1 class="text-2xl font-bold text-gray-800 mb-2">Ubah Password</h1>

<div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">
    <strong>Perhatian keamanan:</strong> Setelah password diubah, sesi login aktif dapat berakhir dan Anda perlu login ulang.
</div>

<?php if (!empty($error)): ?>
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800" role="alert">
        <?= e((string) $error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/change-password" class="max-w-md space-y-4 bg-white rounded-xl border p-6" data-loading-submit>
    <?= Csrf::field() ?>
    <div>
        <div class="flex items-center justify-between mb-1">
            <label for="current_password" class="block text-sm font-medium text-gray-700">Password Lama</label>
            <button type="button" data-password-toggle="current_password" class="text-xs text-slate-600 hover:underline">Tampilkan</button>
        </div>
        <input type="password" id="current_password" name="current_password" required
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-500">
    </div>
    <div>
        <div class="flex items-center justify-between mb-1">
            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
            <button type="button" data-password-toggle="password" class="text-xs text-slate-600 hover:underline">Tampilkan</button>
        </div>
        <input type="password" id="password" name="password" required minlength="8"
               data-password-strength="password-strength-meter"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-500">
        <div id="password-strength-meter" class="mt-2">
            <div class="h-1.5 w-full bg-gray-200 rounded-full overflow-hidden">
                <div data-strength-bar class="h-1.5 rounded-full transition-all bg-gray-200" style="width:0%"></div>
            </div>
            <p data-strength-label class="text-xs text-gray-500 mt-1"></p>
        </div>
        <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter. Kombinasi huruf besar, angka, dan simbol lebih aman.</p>
    </div>
    <div>
        <div class="flex items-center justify-between mb-1">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
            <button type="button" data-password-toggle="password_confirmation" class="text-xs text-slate-600 hover:underline">Tampilkan</button>
        </div>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
               data-password-match="password"
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-500">
        <p data-match-hint class="text-xs mt-1"></p>
    </div>
    <button type="submit"
            class="bg-admin text-white rounded-lg px-4 py-2 font-medium hover:bg-admin-hover disabled:opacity-60">
        Simpan Password
    </button>
</form>
