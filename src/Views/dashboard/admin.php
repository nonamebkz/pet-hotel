<?php

declare(strict_types=1);

$today = $today ?? date('Y-m-d');
$bookingsToday = $bookingsToday ?? ['grooming' => 0, 'penitipan' => 0, 'pet_care' => 0, 'total' => 0];
$pendingVerification = $pendingVerification ?? ['grooming' => 0, 'penitipan' => 0, 'total' => 0];
$penitipanAktif = $penitipanAktif ?? 0;
$pendapatan = $pendapatan ?? ['harian' => 0.0, 'mingguan' => 0.0, 'mingguMulai' => $today, 'mingguAkhir' => $today];
$pendingVerificationPreview = $pendingVerificationPreview ?? [];
?>
<div class="space-y-6">
    <div class="bg-white rounded-xl border p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Dashboard <?= e($roleLabel ?? 'Internal') ?></h1>
        <p class="text-gray-600 mb-4">Selamat datang, <?= e((string) $nama) ?>!</p>
        <p class="text-sm text-gray-500 mb-4">
            Ringkasan operasional — <?= e(date('d/m/Y', strtotime($today))) ?>
        </p>
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

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border p-6">
            <div class="text-sm text-gray-500 mb-1">Booking Hari Ini</div>
            <div class="text-3xl font-bold text-slate-800"><?= e((string) $bookingsToday['total']) ?></div>
            <div class="text-xs text-gray-400 mt-2">
                Grooming <?= e((string) $bookingsToday['grooming']) ?>
                · Penitipan <?= e((string) $bookingsToday['penitipan']) ?>
                · Pet Care <?= e((string) $bookingsToday['pet_care']) ?>
            </div>
            <div class="flex flex-wrap gap-x-2 gap-y-1 mt-3 text-xs">
                <a href="/admin/grooming/booking?tanggal=<?= e(urlencode($today)) ?>"
                   class="text-slate-600 hover:underline">Grooming →</a>
                <a href="/admin/penitipan/booking?check_in=<?= e(urlencode($today)) ?>"
                   class="text-slate-600 hover:underline">Penitipan →</a>
                <a href="/admin/pet-care/booking?tanggal=<?= e(urlencode($today)) ?>"
                   class="text-slate-600 hover:underline">Pet Care →</a>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6">
            <div class="text-sm text-gray-500 mb-1">Menunggu Verifikasi</div>
            <div class="text-3xl font-bold text-slate-800"><?= e((string) $pendingVerification['total']) ?></div>
            <div class="text-xs text-gray-400 mt-2">
                Grooming <?= e((string) $pendingVerification['grooming']) ?>
                · Penitipan <?= e((string) $pendingVerification['penitipan']) ?>
            </div>
            <div class="flex flex-wrap gap-x-2 gap-y-1 mt-3 text-xs">
                <a href="/admin/grooming/pembayaran" class="text-slate-600 hover:underline">Verifikasi Grooming →</a>
                <a href="/admin/penitipan/pembayaran" class="text-slate-600 hover:underline">Verifikasi Penitipan →</a>
                <a href="/admin/transaksi" class="text-slate-600 hover:underline">Riwayat Transaksi →</a>
            </div>
        </div>

        <a href="/admin/penitipan/booking"
           class="bg-white rounded-xl border p-6 hover:border-slate-400 transition block">
            <div class="text-sm text-gray-500 mb-1">Penitipan Aktif</div>
            <div class="text-3xl font-bold text-slate-800"><?= e((string) $penitipanAktif) ?></div>
            <div class="text-xs text-gray-400 mt-2">Check-in & sedang dititipkan</div>
            <div class="text-xs text-gray-400 mt-2">Lihat daftar →</div>
        </a>

        <div class="bg-white rounded-xl border p-6">
            <div class="text-sm text-gray-500 mb-1">Pendapatan Terverifikasi</div>
            <div class="text-lg font-bold text-slate-800">
                Hari ini: Rp <?= e(number_format((float) $pendapatan['harian'], 0, ',', '.')) ?>
            </div>
            <div class="text-lg font-bold text-slate-800 mt-1">
                Minggu ini: Rp <?= e(number_format((float) $pendapatan['mingguan'], 0, ',', '.')) ?>
            </div>
            <div class="text-xs text-gray-400 mt-2">
                <?= e(date('d/m', strtotime((string) $pendapatan['mingguMulai']))) ?>
                — <?= e(date('d/m/Y', strtotime((string) $pendapatan['mingguAkhir']))) ?>
                · grooming & penitipan
            </div>
            <a href="/admin/laporan" class="inline-block text-xs text-slate-600 hover:underline mt-2">Laporan detail →</a>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Bukti Transfer Menunggu Verifikasi</h2>
            <div class="flex items-center gap-3">
                <?php if ($pendingVerification['total'] > 0): ?>
                    <span class="text-xs bg-amber-100 text-amber-800 px-2 py-1 rounded-full">
                        <?= e((string) $pendingVerification['total']) ?> menunggu
                    </span>
                <?php endif; ?>
                <a href="/admin/transaksi" class="text-sm text-slate-600 hover:underline">Riwayat transaksi →</a>
            </div>
        </div>

        <?php if ($pendingVerificationPreview === []): ?>
            <p class="text-sm text-gray-500">Tidak ada bukti transfer menunggu verifikasi.</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($pendingVerificationPreview as $item): ?>
                    <a href="<?= e((string) $item['url']) ?>"
                       class="block p-3 rounded-lg border hover:border-slate-400 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-medium text-gray-800"><?= e((string) $item['pelanggan_nama']) ?></div>
                                <div class="text-sm text-gray-500"><?= e((string) $item['layanan_label']) ?></div>
                                <?php if (!empty($item['uploaded_at'])): ?>
                                    <div class="text-xs text-gray-400 mt-1">
                                        Upload: <?= e(date('d/m/Y H:i', strtotime((string) $item['uploaded_at']))) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-sm font-semibold text-slate-700 shrink-0">
                                Rp <?= e(number_format((float) $item['total_bayar'], 0, ',', '.')) ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
