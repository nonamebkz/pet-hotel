<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;
use App\Repositories\NotifikasiRepository;

$navUnreadCount = $unreadNotificationCount ?? null;

if ($navUnreadCount === null && Session::get('auth.staff_id')) {
    $navUnreadCount = (new NotifikasiRepository())->countUnreadByStaff(
        (string) Session::get('auth.staff_id'),
    );
}

$navUnreadCount = (int) ($navUnreadCount ?? 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> — Admin Petshop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100">
    <nav class="bg-slate-800 text-white print:hidden">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="/admin/dashboard" class="font-bold">Petshop Admin</a>
                <a href="/admin/dashboard" class="text-sm text-slate-300 hover:text-white">Dashboard</a>
                <a href="/admin/notifikasi" class="text-sm text-slate-300 hover:text-white inline-flex items-center gap-1">
                    Notifikasi
                    <?php if ($navUnreadCount > 0): ?>
                        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-orange-500 text-white text-xs font-medium">
                            <?= $navUnreadCount > 99 ? '99+' : $navUnreadCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="/admin/pet-care/layanan" class="text-sm text-slate-300 hover:text-white">Pet Care</a>
                <a href="/admin/grooming/layanan" class="text-sm text-slate-300 hover:text-white">Grooming</a>
                <a href="/admin/penitipan/paket" class="text-sm text-slate-300 hover:text-white">Penitipan</a>
                <a href="/admin/pelanggan" class="text-sm text-slate-300 hover:text-white">Pelanggan</a>
                <a href="/admin/laporan" class="text-sm text-slate-300 hover:text-white">Laporan</a>
                <a href="/admin/transaksi" class="text-sm text-slate-300 hover:text-white">Riwayat Transaksi</a>
                <a href="/admin/grooming/pembayaran" class="text-sm text-slate-300 hover:text-white">Verifikasi Grooming</a>
                <a href="/admin/penitipan/pembayaran" class="text-sm text-slate-300 hover:text-white">Verifikasi Penitipan</a>
                <?php if (($role ?? null)?->value === 'OWNER'): ?>
                    <a href="/admin/staff" class="text-sm text-slate-300 hover:text-white">Manajemen Staff</a>
                    <a href="/admin/pengaturan" class="text-sm text-slate-300 hover:text-white">Pengaturan</a>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs bg-slate-700 px-2 py-1 rounded"><?= e($roleLabel ?? 'Staff') ?></span>
                <span class="text-sm text-slate-300"><?= e((string) ($nama ?? '')) ?></span>
                <a href="/admin/change-password" class="text-sm text-slate-300 hover:text-white">Ubah Password</a>
                <form method="POST" action="/admin/logout" class="inline">
                    <?= Csrf::field() ?>
                    <button type="submit" class="text-sm text-red-400 hover:text-red-300">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="max-w-6xl mx-auto px-4 py-8 print:max-w-none print:px-0">
        <div class="print:hidden">
            <?php require __DIR__ . '/../partials/flash.php'; ?>
        </div>
        <?= $content ?? '' ?>
    </main>
</body>
</html>
