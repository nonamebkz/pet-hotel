-- =============================================================================
-- Schema Database Pet Care — Aplikasi Petshop (MariaDB)
-- Fase: Booking Pet Care (layanan, slot dokter, booking)
-- =============================================================================

USE petshop;

CREATE TABLE IF NOT EXISTS layanan_pet_care (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    nama                        VARCHAR(150) NOT NULL,
    deskripsi                   TEXT NULL,
    harga                       DECIMAL(12, 2) NOT NULL,
    estimasi_durasi_menit       INT NOT NULL,
    status                      ENUM('AKTIF', 'NONAKTIF') NOT NULL DEFAULT 'AKTIF',
    deleted_at                  DATETIME NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT chk_layanan_pet_care_harga CHECK (harga >= 0),
    CONSTRAINT chk_layanan_pet_care_durasi CHECK (estimasi_durasi_menit > 0),
    INDEX idx_layanan_pet_care_status (status),
    INDEX idx_layanan_pet_care_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kuota_pet_care (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    tanggal                     DATE NOT NULL,
    slot_waktu                  TIME NOT NULL,
    slot_maksimal               INT NOT NULL DEFAULT 1,
    slot_terisi                 INT NOT NULL DEFAULT 0,
    status_slot                 ENUM('TERSEDIA', 'DITUTUP') NOT NULL DEFAULT 'TERSEDIA',
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_kuota_pet_care_tanggal_slot UNIQUE (tanggal, slot_waktu),
    CONSTRAINT chk_kuota_pet_care_maksimal CHECK (slot_maksimal = 1),
    CONSTRAINT chk_kuota_pet_care_terisi CHECK (slot_terisi >= 0 AND slot_terisi <= slot_maksimal),
    INDEX idx_kuota_pet_care_tanggal (tanggal),
    INDEX idx_kuota_pet_care_status_slot (status_slot)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS booking_pet_care (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    pelanggan_id                CHAR(36) NOT NULL,
    kucing_id                   CHAR(36) NOT NULL,
    layanan_pet_care_id         CHAR(36) NOT NULL,
    kuota_pet_care_id           CHAR(36) NOT NULL,
    tanggal                     DATE NOT NULL,
    slot_waktu                  TIME NOT NULL,
    harga_layanan               DECIMAL(12, 2) NOT NULL,
    status                      ENUM('TERKONFIRMASI', 'SEDANG_PROSES', 'SELESAI', 'DIBATALKAN') NOT NULL DEFAULT 'TERKONFIRMASI',
    catatan                     TEXT NULL,
    dibatalkan_oleh             ENUM('PELANGGAN', 'STAFF') NULL,
    dibatalkan_oleh_staff_id    CHAR(36) NULL,
    alasan_pembatalan           TEXT NULL,
    waktu_dibatalkan            DATETIME NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_booking_pet_care_pelanggan
        FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_pet_care_kucing
        FOREIGN KEY (kucing_id) REFERENCES kucing(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_pet_care_layanan
        FOREIGN KEY (layanan_pet_care_id) REFERENCES layanan_pet_care(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_pet_care_kuota
        FOREIGN KEY (kuota_pet_care_id) REFERENCES kuota_pet_care(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_pet_care_staff_batal
        FOREIGN KEY (dibatalkan_oleh_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    CONSTRAINT chk_booking_pet_care_harga CHECK (harga_layanan >= 0),
    INDEX idx_booking_pet_care_pelanggan (pelanggan_id),
    INDEX idx_booking_pet_care_status (status),
    INDEX idx_booking_pet_care_tanggal (tanggal),
    INDEX idx_booking_pet_care_kuota (kuota_pet_care_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
