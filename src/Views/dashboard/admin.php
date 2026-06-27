<?php

declare(strict_types=1);

?>
<div class="bg-white rounded-xl border p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard <?= e($roleLabel ?? 'Internal') ?></h1>
    <p class="text-gray-600 mb-4">Selamat datang, <?= e((string) $nama) ?>!</p>
    <?php if (($role ?? null)?->value === 'OWNER'): ?>
        <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900 mb-4">
            Anda login sebagai <strong>Owner</strong> — akses penuh operasional staff + manajemen akun staff.
        </div>
        <a href="/admin/staff" class="inline-block text-sm text-slate-700 hover:underline">→ Manajemen Staff</a>
    <?php else: ?>
        <div class="rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-900">
            Anda login sebagai <strong>Staff</strong> — akses operasional harian petshop.
        </div>
    <?php endif; ?>
</div>
