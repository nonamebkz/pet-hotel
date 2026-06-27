-- Seed pengaturan bisnis default (development)
-- Nilai sama dengan default .env / config/app.php

USE petshop;

INSERT INTO pengaturan_petshop (
    id,
    petshop_lat,
    petshop_lng,
    pickup_free_radius_km,
    pickup_extra_fee_per_km,
    payment_deadline_hours,
    bank_name,
    bank_account_number,
    bank_account_name,
    promo_min_days,
    promo_discount_percent,
    min_vaccination_count,
    petshop_whatsapp,
    updated_by_staff_id
) VALUES (
    '00000000-0000-4000-8000-000000000001',
    -6.20880000,
    106.84560000,
    3.00,
    5000,
    24,
    'BCA',
    '1234567890',
    'Petshop Sejahtera',
    7,
    10,
    1,
    '6281234567890',
    NULL
) ON DUPLICATE KEY UPDATE
    petshop_lat = VALUES(petshop_lat),
    petshop_lng = VALUES(petshop_lng),
    pickup_free_radius_km = VALUES(pickup_free_radius_km),
    pickup_extra_fee_per_km = VALUES(pickup_extra_fee_per_km),
    payment_deadline_hours = VALUES(payment_deadline_hours),
    bank_name = VALUES(bank_name),
    bank_account_number = VALUES(bank_account_number),
    bank_account_name = VALUES(bank_account_name),
    promo_min_days = VALUES(promo_min_days),
    promo_discount_percent = VALUES(promo_discount_percent),
    min_vaccination_count = VALUES(min_vaccination_count),
    petshop_whatsapp = VALUES(petshop_whatsapp);
