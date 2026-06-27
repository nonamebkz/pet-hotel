-- Seed data Pet Care (development)
-- Layanan aktif + slot dokter 7 hari ke depan (09:00, 10:00, 11:00)

USE petshop;

INSERT INTO layanan_pet_care (id, nama, deskripsi, harga, estimasi_durasi_menit, status)
VALUES
    (
        '10000000-0000-4000-8000-000000000001',
        'Konsultasi Dokter Hewan',
        'Pemeriksaan kesehatan umum dan konsultasi keluhan kucing.',
        150000.00,
        30,
        'AKTIF'
    ),
    (
        '10000000-0000-4000-8000-000000000002',
        'Vaksinasi',
        'Pemberian vaksin sesuai rekomendasi dokter hewan.',
        200000.00,
        30,
        'AKTIF'
    ),
    (
        '10000000-0000-4000-8000-000000000003',
        'Pemeriksaan Luka',
        'Pemeriksaan dan perawatan luka ringan pada kucing.',
        175000.00,
        45,
        'AKTIF'
    )
ON DUPLICATE KEY UPDATE nama = VALUES(nama);

-- Slot untuk 7 hari ke depan (mulai besok)
INSERT INTO kuota_pet_care (id, tanggal, slot_waktu, slot_maksimal, slot_terisi, status_slot)
SELECT
    UUID(),
    DATE_ADD(CURDATE(), INTERVAL d.day_offset DAY),
    t.slot_waktu,
    1,
    0,
    'TERSEDIA'
FROM (
    SELECT 1 AS day_offset UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
    SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7
) AS d
CROSS JOIN (
    SELECT '09:00:00' AS slot_waktu UNION ALL
    SELECT '10:00:00' UNION ALL
    SELECT '11:00:00'
) AS t
WHERE NOT EXISTS (
    SELECT 1 FROM kuota_pet_care k
    WHERE k.tanggal = DATE_ADD(CURDATE(), INTERVAL d.day_offset DAY)
      AND k.slot_waktu = t.slot_waktu
);
