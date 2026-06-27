-- =============================================================================
-- Schema Database Auth — Aplikasi Petshop (MariaDB)
-- Fase: Autentikasi & Role
-- =============================================================================

CREATE DATABASE IF NOT EXISTS petshop
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE petshop;

-- =============================================================================
-- AKUN & PENGGUNA
-- =============================================================================

CREATE TABLE IF NOT EXISTS pelanggan (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    nama                        VARCHAR(150) NOT NULL,
    email                       VARCHAR(255) NOT NULL,
    no_telepon                  VARCHAR(20) NULL,
    password_hash               VARCHAR(255) NOT NULL,
    alamat_lengkap              TEXT NULL,
    latitude                    DECIMAL(10, 8) NULL,
    longitude                   DECIMAL(11, 8) NULL,
    foto_profil_url             VARCHAR(500) NULL,
    pernah_pakai_promo_penitipan TINYINT(1) NOT NULL DEFAULT 0,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_pelanggan_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS staff (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    nama                        VARCHAR(150) NOT NULL,
    email                       VARCHAR(255) NOT NULL,
    username                    VARCHAR(100) NULL,
    password_hash               VARCHAR(255) NOT NULL,
    role                        ENUM('STAFF', 'OWNER') NOT NULL DEFAULT 'STAFF',
    status                      ENUM('AKTIF', 'NONAKTIF') NOT NULL DEFAULT 'AKTIF',
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_staff_email (email),
    UNIQUE KEY uq_staff_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    email                       VARCHAR(255) NOT NULL,
    token_hash                  VARCHAR(255) NOT NULL,
    user_type                   ENUM('PELANGGAN', 'STAFF') NOT NULL,
    expires_at                  DATETIME NOT NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_password_reset_email (email),
    INDEX idx_password_reset_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
