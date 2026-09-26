<?php

declare(strict_types=1);

/**
 * Definisi menu navigasi — dipetakan dari plan-redesign §4 ke route aktual aplikasi.
 *
 * @return list<array{label: string, items: list<array<string, mixed>>}>
 */
/**
 * Menu sidebar admin. Bagian Pengaturan hanya untuk owner — backend tetap wajib cek role/permission.
 */
function admin_nav_menu_sections(bool $isOwner): array
{
    $operasional = [
        [
            'type' => 'link',
            'label' => 'Dashboard',
            'href' => '/admin/dashboard',
            'paths' => ['/admin/dashboard'],
        ],
        [
            'type' => 'group',
            'label' => 'Booking',
            'paths' => [
                '/admin/grooming/booking',
                '/admin/penitipan/booking',
                '/admin/pet-care/booking',
            ],
            'prefix' => true,
            'children' => [
                ['label' => 'Grooming', 'href' => '/admin/grooming/booking', 'paths' => ['/admin/grooming/booking']],
                ['label' => 'Penitipan', 'href' => '/admin/penitipan/booking', 'paths' => ['/admin/penitipan/booking'], 'prefix' => true],
                ['label' => 'Pet Care', 'href' => '/admin/pet-care/booking', 'paths' => ['/admin/pet-care/booking']],
            ],
        ],
        [
            'type' => 'link',
            'label' => 'Kuota Penitipan',
            'href' => '/admin/penitipan/kuota',
            'paths' => ['/admin/penitipan/kuota'],
            'prefix' => true,
        ],
        [
            'type' => 'link',
            'label' => 'Hewan Menginap',
            'href' => '/admin/penitipan/booking?status=SEDANG_DITITIPKAN',
            'paths' => ['/admin/penitipan/booking'],
            'matchQuery' => 'status=SEDANG_DITITIPKAN',
            'excludeQuery' => 'monitoring=belum_input',
        ],
        [
            'type' => 'group',
            'label' => 'Perawatan',
            'paths' => ['/admin/pet-care/booking', '/admin/penitipan/perpanjangan', '/admin/penitipan/monitoring'],
            'prefix' => true,
            'children' => [
                ['label' => 'Booking Pet Care', 'href' => '/admin/pet-care/booking', 'paths' => ['/admin/pet-care/booking']],
                [
                    'label' => 'Monitoring Belum Input',
                    'href' => '/admin/penitipan/booking?status=SEDANG_DITITIPKAN&monitoring=belum_input',
                    'paths' => ['/admin/penitipan/booking'],
                    'matchQuery' => 'monitoring=belum_input',
                ],
                ['label' => 'Perpanjangan Penitipan', 'href' => '/admin/penitipan/perpanjangan', 'paths' => ['/admin/penitipan/perpanjangan'], 'prefix' => true],
            ],
        ],
    ];

    $administrasi = [
        [
            'type' => 'link',
            'label' => 'Pelanggan',
            'href' => '/admin/pelanggan',
            'paths' => ['/admin/pelanggan'],
            'prefix' => true,
        ],
        [
            'type' => 'group',
            'label' => 'Pembayaran',
            'paths' => ['/admin/grooming/pembayaran', '/admin/penitipan/pembayaran', '/admin/transaksi'],
            'prefix' => true,
            'children' => [
                ['label' => 'Verifikasi Grooming', 'href' => '/admin/grooming/pembayaran', 'paths' => ['/admin/grooming/pembayaran']],
                ['label' => 'Verifikasi Penitipan', 'href' => '/admin/penitipan/pembayaran', 'paths' => ['/admin/penitipan/pembayaran']],
                ['label' => 'Riwayat Transaksi', 'href' => '/admin/transaksi', 'paths' => ['/admin/transaksi'], 'prefix' => true],
            ],
        ],
        [
            'type' => 'link',
            'label' => 'Laporan',
            'href' => '/admin/laporan',
            'paths' => ['/admin/laporan'],
            'prefix' => true,
        ],
    ];

    $konfigurasi = [
        [
            'type' => 'group',
            'label' => 'Kelola Layanan',
            'paths' => ['/admin/pet-care/layanan', '/admin/grooming/layanan', '/admin/penitipan/paket', '/admin/penitipan/kamar', '/admin/grooming/kuota', '/admin/pet-care/slot'],
            'prefix' => true,
            'children' => [
                ['label' => 'Jenis Pet Care', 'href' => '/admin/pet-care/layanan', 'paths' => ['/admin/pet-care/layanan'], 'prefix' => true],
                ['label' => 'Slot Pet Care', 'href' => '/admin/pet-care/slot', 'paths' => ['/admin/pet-care/slot'], 'prefix' => true],
                ['label' => 'Jenis Grooming', 'href' => '/admin/grooming/layanan', 'paths' => ['/admin/grooming/layanan'], 'prefix' => true],
                ['label' => 'Kuota Grooming', 'href' => '/admin/grooming/kuota', 'paths' => ['/admin/grooming/kuota'], 'prefix' => true],
                ['label' => 'Paket Penitipan', 'href' => '/admin/penitipan/paket', 'paths' => ['/admin/penitipan/paket'], 'prefix' => true],
                ['label' => 'Kamar Penitipan', 'href' => '/admin/penitipan/kamar', 'paths' => ['/admin/penitipan/kamar'], 'prefix' => true],
            ],
        ],
    ];

    $sections = [
        ['label' => 'Operasional', 'items' => $operasional],
        ['label' => 'Administrasi', 'items' => $administrasi],
        ['label' => 'Konfigurasi', 'items' => $konfigurasi],
    ];

    if ($isOwner) {
        $sections[] = [
            'label' => 'Pengaturan',
            'items' => [
                [
                    'type' => 'link',
                    'label' => 'Manajemen Staff',
                    'href' => '/admin/staff',
                    'paths' => ['/admin/staff'],
                    'prefix' => true,
                ],
                [
                    'type' => 'link',
                    'label' => 'Pengaturan Bisnis',
                    'href' => '/admin/pengaturan',
                    'paths' => ['/admin/pengaturan'],
                    'prefix' => true,
                ],
            ],
        ];
    }

    return $sections;
}

/**
 * @return list<array{label: string, items: list<array<string, mixed>>}>
 */
function pelanggan_nav_menu_sections(): array
{
    return [
        [
            'label' => 'Utama',
            'items' => [
                [
                    'type' => 'link',
                    'label' => 'Beranda',
                    'href' => '/dashboard',
                    'paths' => ['/dashboard'],
                ],
                [
                    'type' => 'group',
                    'label' => 'Booking',
                    'paths' => ['/grooming', '/penitipan', '/pet-care'],
                    'prefix' => true,
                    'children' => [
                        ['label' => 'Grooming', 'href' => '/grooming', 'paths' => ['/grooming'], 'prefix' => true],
                        ['label' => 'Penitipan', 'href' => '/penitipan', 'paths' => ['/penitipan'], 'prefix' => true],
                        ['label' => 'Pet Care', 'href' => '/pet-care', 'paths' => ['/pet-care'], 'prefix' => true],
                    ],
                ],
                [
                    'type' => 'group',
                    'label' => 'Peliharaan',
                    'paths' => ['/kucing'],
                    'prefix' => true,
                    'children' => [
                        ['label' => 'Kucing Saya', 'href' => '/kucing', 'paths' => ['/kucing'], 'prefix' => true],
                        ['label' => 'Riwayat Pet Care', 'href' => '/pet-care/riwayat', 'paths' => ['/pet-care/riwayat']],
                    ],
                ],
            ],
        ],
        [
            'label' => 'Akun & riwayat',
            'items' => [
                [
                    'type' => 'link',
                    'label' => 'Riwayat Transaksi',
                    'href' => '/transaksi',
                    'paths' => ['/transaksi'],
                    'prefix' => true,
                ],
                [
                    'type' => 'link',
                    'label' => 'Notifikasi',
                    'href' => '/notifikasi',
                    'paths' => ['/notifikasi'],
                    'prefix' => true,
                ],
                [
                    'type' => 'link',
                    'label' => 'Bantuan',
                    'href' => '/bantuan',
                    'paths' => ['/bantuan'],
                    'prefix' => true,
                ],
            ],
        ],
    ];
}

/**
 * @param array<string, mixed> $item
 */
function nav_menu_item_active(array $item): bool
{
    $paths = $item['paths'] ?? [];
    if ($paths === []) {
        return false;
    }

    $prefix = (bool) ($item['prefix'] ?? false);
    $pathActive = nav_is_active($paths, $prefix);

    if (!$pathActive) {
        return false;
    }

    $matchQuery = $item['matchQuery'] ?? null;
    if ($matchQuery === null || $matchQuery === '') {
        return true;
    }

    $query = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_QUERY) ?: '');

    if (!str_contains($query, (string) $matchQuery)) {
        return false;
    }

    $excludeQuery = $item['excludeQuery'] ?? null;
    if ($excludeQuery !== null && $excludeQuery !== '' && str_contains($query, (string) $excludeQuery)) {
        return false;
    }

    return true;
}

/**
 * @param list<array<string, mixed>> $children
 */
function nav_menu_group_active(array $children, array $groupPaths = [], bool $groupPrefix = true): bool
{
    foreach ($children as $child) {
        if (nav_menu_item_active($child)) {
            return true;
        }
    }

    if ($groupPaths !== []) {
        return nav_is_active($groupPaths, $groupPrefix);
    }

    return false;
}
