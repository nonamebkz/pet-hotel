<?php

declare(strict_types=1);

$today = $today ?? date('Y-m-d');
$bookingsToday = $bookingsToday ?? ['grooming' => 0, 'penitipan' => 0, 'pet_care' => 0, 'total' => 0];
$bookingsYesterday = $bookingsYesterday ?? ['total' => 0];
$pendingVerification = $pendingVerification ?? ['grooming' => 0, 'penitipan' => 0, 'total' => 0];
$penitipanAktif = $penitipanAktif ?? 0;
$pendapatan = $pendapatan ?? ['harian' => 0.0, 'kemarin' => 0.0, 'mingguan' => 0.0, 'mingguMulai' => $today, 'mingguAkhir' => $today];
$pendingVerificationPreview = $pendingVerificationPreview ?? [];

$bookingDelta = (int) $bookingsToday['total'] - (int) $bookingsYesterday['total'];
$revenueDelta = (float) $pendapatan['harian'] - (float) ($pendapatan['kemarin'] ?? 0);
?>
<div class="space-y-6">
    <div class="bg-white rounded-xl border p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Dashboard <?= e($roleLabel ?? 'Internal') ?></h1>
        <p class="text-gray-600">Selamat datang, <?= e((string) $nama) ?>!</p>
        <p class="text-sm text-gray-500 mt-1">Ringkasan operasional — <?= e(date('d/m/Y', strtotime($today))) ?></p>
    </div>

    <?php if ($pendingVerification['total'] > 0): ?>
        <div class="bg-amber-50 border-2 border-amber-300 rounded-xl p-6">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-semibold uppercase tracking-wide text-amber-800">Action Center</span>
                        <span class="text-xs bg-amber-200 text-amber-900 px-2 py-0.5 rounded-full">
                            <?= e((string) $pendingVerification['total']) ?> urgent
                        </span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-800">Bukti Transfer Menunggu Verifikasi</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Grooming <?= e((string) $pendingVerification['grooming']) ?>
                        · Penitipan <?= e((string) $pendingVerification['penitipan']) ?>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php if ($pendingVerification['grooming'] > 0): ?>
                        <a href="/admin/grooming/pembayaran"
                           class="bg-slate-800 text-white rounded-lg px-4 py-2 text-sm font-medium hover:bg-slate-700">
                            Verifikasi Grooming
                        </a>
                    <?php endif; ?>
                    <?php if ($pendingVerification['penitipan'] > 0): ?>
                        <a href="/admin/penitipan/pembayaran"
                           class="border border-slate-800 text-slate-800 rounded-lg px-4 py-2 text-sm font-medium hover:bg-white">
                            Verifikasi Penitipan
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($pendingVerificationPreview !== []): ?>
                <div class="space-y-2">
                    <?php foreach ($pendingVerificationPreview as $item): ?>
                        <a href="<?= e((string) $item['url']) ?>"
                           class="block p-3 rounded-lg bg-white border border-amber-200 hover:border-amber-400 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium text-gray-800"><?= e((string) $item['pelanggan_nama']) ?></div>
                                    <div class="text-sm text-gray-500"><?= e((string) $item['layanan_label']) ?></div>
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
    <?php else: ?>
        <?php
        $variant = 'success';
        $title = 'Semua pembayaran sudah diverifikasi';
        $description = 'Tidak ada bukti transfer yang menunggu tindakan.';
        $ctaLabel = 'Lihat riwayat transaksi';
        $ctaHref = '/admin/transaksi';
        require __DIR__ . '/../partials/ui/empty-state.php';
        ?>
    <?php endif; ?>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border p-6">
            <div class="text-sm text-gray-500 mb-1">Booking Hari Ini</div>
            <div class="text-3xl font-bold text-slate-800"><?= e((string) $bookingsToday['total']) ?></div>
            <div class="text-xs mt-2 <?= $bookingDelta >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= $bookingDelta >= 0 ? '+' : '' ?><?= e((string) $bookingDelta) ?> vs kemarin
            </div>
            <div class="text-xs text-gray-400 mt-2">
                Grooming <?= e((string) $bookingsToday['grooming']) ?>
                · Penitipan <?= e((string) $bookingsToday['penitipan']) ?>
                · Pet Care <?= e((string) $bookingsToday['pet_care']) ?>
            </div>
        </div>

        <div class="bg-white rounded-xl border p-6 ring-2 ring-amber-100">
            <div class="text-sm text-gray-500 mb-1">Menunggu Verifikasi</div>
            <div class="text-3xl font-bold text-amber-700"><?= e((string) $pendingVerification['total']) ?></div>
            <div class="text-xs text-gray-400 mt-2">Prioritas operasional hari ini</div>
            <a href="/admin/grooming/pembayaran" class="inline-block text-xs text-slate-600 hover:underline mt-3">Kelola verifikasi →</a>
        </div>

        <a href="/admin/penitipan/booking" class="bg-white rounded-xl border p-6 hover:border-slate-400 transition block">
            <div class="text-sm text-gray-500 mb-1">Penitipan Aktif</div>
            <div class="text-3xl font-bold text-slate-800"><?= e((string) $penitipanAktif) ?></div>
            <div class="text-xs text-gray-400 mt-2">
                <?= $penitipanAktif === 0 ? 'Belum ada penitipan aktif' : 'Check-in & sedang dititipkan' ?>
            </div>
        </a>

        <div class="bg-white rounded-xl border p-6">
            <div class="text-sm text-gray-500 mb-1">Pendapatan Terverifikasi</div>
            <div class="text-lg font-bold text-slate-800">
                Hari ini: Rp <?= e(number_format((float) $pendapatan['harian'], 0, ',', '.')) ?>
            </div>
            <div class="text-xs mt-1 <?= $revenueDelta >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= $revenueDelta >= 0 ? '+' : '' ?>Rp <?= e(number_format(abs($revenueDelta), 0, ',', '.')) ?> vs kemarin
            </div>
            <div class="text-sm font-semibold text-slate-700 mt-2">
                Minggu ini: Rp <?= e(number_format((float) $pendapatan['mingguan'], 0, ',', '.')) ?>
            </div>
            <a href="/admin/laporan" class="inline-block text-xs text-slate-600 hover:underline mt-2">Laporan detail →</a>
        </div>
    </div>

    <?php if (($role ?? null)?->value === 'OWNER'): ?>
        <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">
            Anda login sebagai <strong>Owner</strong> — akses penuh operasional staff + manajemen akun staff.
            <a href="/admin/staff" class="ml-2 text-slate-700 hover:underline font-medium">Manajemen Staff →</a>
        </div>
    <?php endif; ?>
</div>
