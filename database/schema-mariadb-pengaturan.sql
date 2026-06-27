-- =============================================================================
-- Schema Pengaturan Bisnis — Aplikasi Petshop (MariaDB)
-- =============================================================================

USE petshop;

CREATE TABLE IF NOT EXISTS pengaturan_petshop (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    petshop_lat                 DECIMAL(10, 8) NOT NULL,
    petshop_lng                 DECIMAL(11, 8) NOT NULL,
    pickup_free_radius_km       DECIMAL(5, 2) NOT NULL DEFAULT 3.00,
    pickup_extra_fee_per_km     INT NOT NULL DEFAULT 5000,
    payment_deadline_hours      INT NOT NULL DEFAULT 24,
    bank_name                   VARCHAR(50) NOT NULL,
    bank_account_number         VARCHAR(30) NOT NULL,
    bank_account_name           VARCHAR(100) NOT NULL,
    promo_min_days              INT NOT NULL DEFAULT 7,
    promo_discount_percent      INT NOT NULL DEFAULT 10,
    min_vaccination_count       INT NOT NULL DEFAULT 1,
    petshop_whatsapp            VARCHAR(20) NOT NULL,
    updated_by_staff_id         CHAR(36) NULL,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_pengaturan_updated_by_staff
        FOREIGN KEY (updated_by_staff_id) REFERENCES staff (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
