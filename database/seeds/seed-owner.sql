-- Seed akun owner default (development)
-- Email: owner@petshop.local
-- Password: password123

USE petshop;

INSERT INTO staff (id, nama, email, username, password_hash, role, status)
VALUES (
    '00000000-0000-4000-8000-000000000001',
    'Owner Petshop',
    'owner@petshop.local',
    'owner',
    '$2y$12$oCXAGd4E8MexPj.Qo..msOhaSLxQ55vpqE5mpcdDqs3wdIKJXu9RC',
    'OWNER',
    'AKTIF'
)
ON DUPLICATE KEY UPDATE nama = VALUES(nama);

-- Seed akun staff default (development)
-- Email: staff@petshop.local
-- Password: password123

INSERT INTO staff (id, nama, email, username, password_hash, role, status)
VALUES (
    '00000000-0000-4000-8000-000000000002',
    'Staff Petshop',
    'staff@petshop.local',
    'staff',
    '$2y$12$oCXAGd4E8MexPj.Qo..msOhaSLxQ55vpqE5mpcdDqs3wdIKJXu9RC',
    'STAFF',
    'AKTIF'
)
ON DUPLICATE KEY UPDATE nama = VALUES(nama);
