-- Seed data Penitipan / Pet Hotel (development)
-- 2 paket, 2 kamar, kuota 30 hari ke depan

USE petshop;

INSERT INTO paket_penitipan (id, nama, harga_per_hari, deskripsi, aktif)
VALUES
    (
        '30000000-0000-4000-8000-000000000001',
        'Standard',
        75000.00,
        'Penitipan harian dengan kamar standar, makan 2x sehari.',
        1
    ),
    (
        '30000000-0000-4000-8000-000000000002',
        'Premium',
        120000.00,
        'Kamar premium, makan premium 2x sehari + snack.',
        1
    )
ON DUPLICATE KEY UPDATE nama = VALUES(nama);

INSERT INTO kamar_penitipan (id, nama_kamar, kapasitas, aktif)
VALUES
    (
        '31000000-0000-4000-8000-000000000001',
        'Kamar A',
        3,
        1
    ),
    (
        '31000000-0000-4000-8000-000000000002',
        'Kamar B',
        2,
        1
    )
ON DUPLICATE KEY UPDATE nama_kamar = VALUES(nama_kamar);

INSERT INTO kuota_penitipan (id, kamar_penitipan_id, tanggal, slot_maksimal, slot_terisi)
SELECT
    UUID(),
    k.id,
    DATE_ADD(CURDATE(), INTERVAL d.day_offset DAY),
    k.kapasitas,
    0
FROM kamar_penitipan k
CROSS JOIN (
    SELECT 0 AS day_offset UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
    SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL
    SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL
    SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL
    SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL
    SELECT 24 UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL
    SELECT 29
) AS d
WHERE k.aktif = 1
  AND NOT EXISTS (
    SELECT 1 FROM kuota_penitipan kp
    WHERE kp.kamar_penitipan_id = k.id
      AND kp.tanggal = DATE_ADD(CURDATE(), INTERVAL d.day_offset DAY)
  );
