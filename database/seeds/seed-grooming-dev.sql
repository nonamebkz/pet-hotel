-- Seed data Grooming (development)
-- 3 jenis grooming + kuota 14 hari ke depan (5 slot/hari)

USE petshop;

INSERT INTO jenis_grooming (id, nama, deskripsi, harga, aktif)
VALUES
    (
        '20000000-0000-4000-8000-000000000001',
        'Grooming Lengkap',
        'Mandi, potong kuku, bersihkan telinga, dan sisir bulu.',
        150000.00,
        1
    ),
    (
        '20000000-0000-4000-8000-000000000002',
        'Grooming Jamur',
        'Perawatan khusus untuk kucing dengan masalah jamur kulit.',
        200000.00,
        1
    ),
    (
        '20000000-0000-4000-8000-000000000003',
        'Grooming Kutu',
        'Perawatan anti kutu dan mandi obat.',
        175000.00,
        1
    )
ON DUPLICATE KEY UPDATE nama = VALUES(nama);

INSERT INTO kuota_grooming (id, tanggal, slot_maksimal, slot_terisi)
SELECT
    UUID(),
    DATE_ADD(CURDATE(), INTERVAL d.day_offset DAY),
    5,
    0
FROM (
    SELECT 1 AS day_offset UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
    SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL
    SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL
    SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14
) AS d
WHERE NOT EXISTS (
    SELECT 1 FROM kuota_grooming k
    WHERE k.tanggal = DATE_ADD(CURDATE(), INTERVAL d.day_offset DAY)
);
