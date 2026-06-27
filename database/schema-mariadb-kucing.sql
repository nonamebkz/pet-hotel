-- =============================================================================
-- Schema Database Kucing & Riwayat Vaksin — Aplikasi Petshop (MariaDB)
-- Fase: Master Data & Profil (Prasyarat Booking)
-- =============================================================================

USE petshop;

CREATE TABLE IF NOT EXISTS kucing (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    pelanggan_id                CHAR(36) NOT NULL,
    nama                        VARCHAR(100) NOT NULL,
    jenis_kelamin               ENUM('JANTAN', 'BETINA') NOT NULL,
    ras                         VARCHAR(100) NULL,
    tanggal_lahir               DATE NULL,
    berat_badan                 DECIMAL(5, 2) NULL,
    foto_url                    VARCHAR(500) NULL,
    catatan_kesehatan           TEXT NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_kucing_pelanggan
        FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT,
    INDEX idx_kucing_pelanggan_id (pelanggan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS riwayat_vaksin (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    kucing_id                   CHAR(36) NOT NULL,
    jenis_vaksin                VARCHAR(100) NOT NULL,
    tanggal_vaksin              DATE NOT NULL,
    sertifikat_url              VARCHAR(500) NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_riwayat_vaksin_kucing
        FOREIGN KEY (kucing_id) REFERENCES kucing(id) ON DELETE CASCADE,
    INDEX idx_riwayat_vaksin_kucing_id (kucing_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
