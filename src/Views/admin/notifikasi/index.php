<?php

declare(strict_types=1);

use App\Enums\JenisNotifikasi;

$notifikasiList = $notifikasiList ?? [];
$kategori = trim((string) ($_GET['kategori'] ?? ''));

$kategoriTabs = [
    '' => 'Semua',
    'booking' => 'Booking',
    'pembayaran' => 'Pembayaran',
    'reminder' => 'Reminder',
    'sistem' => 'Sistem',
];

$resolveKategori = static function (string $jenis): string {
    $bookingTypes = [
        JenisNotifikasi::BOOKING_DISETUJUI->value,
        JenisNotifikasi::BOOKING_DITOLAK->value,
        JenisNotifikasi::JAM_GROOMING_DIUPDATE->value,
        JenisNotifikasi::LAYANAN_SELESAI->value,
        JenisNotifikasi::BOOKING_DIBATALKAN->value,
        JenisNotifikasi::MONITORING_PENITIPAN->value,
        JenisNotifikasi::PERPANJANGAN_PENITIPAN_MENUNGGU_KONFIRMASI->value,
        JenisNotifikasi::PERPANJANGAN_PENITIPAN_DISETUJUI->value,
        JenisNotifikasi::PERPANJANGAN_PENITIPAN_DITOLAK->value,
        JenisNotifikasi::PERPANJANGAN_PENITIPAN_MENUNGGU_PEMBAYARAN->value,
    ];
    $pembayaranTypes = [
        JenisNotifikasi::PEMBAYARAN_JATUH_TEMPO->value,
        JenisNotifikasi::STATUS_REFUND->value,
    ];
    $reminderTypes = [
        JenisNotifikasi::REMINDER_PEMBAYARAN->value,
    ];

    if (in_array($jenis, $bookingTypes, true)) {
        return 'booking';
    }
    if (in_array($jenis, $pembayaranTypes, true)) {
        return 'pembayaran';
    }
    if (in_array($jenis, $reminderTypes, true)) {
        return 'reminder';
    }

    return 'sistem';
};

$filteredList = $notifikasiList;

if ($kategori !== '') {
    $filteredList = array_values(array_filter(
        $notifikasiList,
        static fn (array $notif): bool => $resolveKategori((string) ($notif['jenis'] ?? '')) === $kategori,
    ));
}
?>
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
        <p class="text-sm text-gray-500 mt-1">Semua pemberitahuan operasional untuk akun Anda.</p>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <?php foreach ($kategoriTabs as $key => $label): ?>
            <?php $isActive = $kategori === $key; ?>
            <a href="/admin/notifikasi<?= $key !== '' ? '?kategori=' . urlencode($key) : '' ?>"
               class="text-sm rounded-full px-4 py-1.5 border transition <?= $isActive
                   ? 'bg-admin text-white border-admin'
                   : 'bg-white text-gray-600 border-gray-200 hover:border-slate-400' ?>">
                <?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($filteredList === []): ?>
        <?php
        $variant = $kategori !== '' ? 'filtered' : 'empty';
        $title = $kategori !== ''
            ? 'Belum ada notifikasi untuk kategori ini'
            : 'Belum ada notifikasi';
        $description = 'Aktivitas booking, pembayaran, dan operasional akan muncul di sini secara otomatis.';
        $ctaLabel = 'Lihat Riwayat Transaksi';
        $ctaHref = '/admin/transaksi';
        $ctaClass = 'border border-gray-300 text-gray-700 rounded-lg px-4 py-2 text-sm font-medium hover:bg-gray-50 inline-block';
        require __DIR__ . '/../../partials/ui/empty-state.php';
        ?>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($filteredList as $notif): ?>
                <?php
                $jenis = (string) ($notif['jenis'] ?? '');
                $actionUrl = null;
                $kat = $resolveKategori($jenis);

                if ($jenis === JenisNotifikasi::PERPANJANGAN_PENITIPAN_MENUNGGU_KONFIRMASI->value) {
                    $actionUrl = '/admin/penitipan/perpanjangan';
                } elseif (in_array($kat, ['pembayaran', 'reminder'], true)) {
                    $actionUrl = '/admin/grooming/pembayaran';
                } elseif ($kat === 'booking') {
                    $actionUrl = '/admin/grooming/booking';
                }

                $katColors = [
                    'booking' => 'border-l-blue-400',
                    'pembayaran' => 'border-l-green-400',
                    'reminder' => 'border-l-amber-400',
                    'sistem' => 'border-l-gray-300',
                ];
                ?>
                <div class="bg-white rounded-xl border border-l-4 <?= e($katColors[$kat] ?? $katColors['sistem']) ?> p-4 <?= empty($notif['sudah_dibaca']) ? 'bg-slate-50/50' : '' ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <?php if (empty($notif['sudah_dibaca'])): ?>
                                    <span class="w-2 h-2 rounded-full bg-admin shrink-0" aria-hidden="true"></span>
                                <?php endif; ?>
                                <div class="font-medium text-gray-800"><?= e((string) $notif['judul']) ?></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-1"><?= e((string) $notif['pesan']) ?></p>
                            <?php if ($actionUrl !== null): ?>
                                <a href="<?= e($actionUrl) ?>" class="inline-block text-sm text-slate-700 hover:underline mt-2">
                                    Lihat detail →
                                </a>
                            <?php endif; ?>
                        </div>
                        <time class="text-xs text-gray-400 shrink-0">
                            <?= e(date('d/m/Y H:i', strtotime((string) $notif['created_at']))) ?>
                        </time>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
