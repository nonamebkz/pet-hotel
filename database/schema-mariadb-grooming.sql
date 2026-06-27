-- =============================================================================
-- Schema Database Grooming — Aplikasi Petshop (MariaDB)
-- Fase: Booking Grooming (jenis, kuota, booking)
-- =============================================================================

USE petshop;

CREATE TABLE IF NOT EXISTS jenis_grooming (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    nama                        VARCHAR(100) NOT NULL,
    deskripsi                   TEXT NULL,
    harga                       DECIMAL(12, 2) NOT NULL,
    aktif                       TINYINT(1) NOT NULL DEFAULT 1,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_jenis_grooming_nama UNIQUE (nama),
    CONSTRAINT chk_jenis_grooming_harga CHECK (harga >= 0),
    INDEX idx_jenis_grooming_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kuota_grooming (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    tanggal                     DATE NOT NULL,
    slot_maksimal               INT NOT NULL,
    slot_terisi                 INT NOT NULL DEFAULT 0,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_kuota_grooming_tanggal UNIQUE (tanggal),
    CONSTRAINT chk_kuota_grooming_maksimal CHECK (slot_maksimal >= 0),
    CONSTRAINT chk_kuota_grooming_terisi CHECK (slot_terisi >= 0 AND slot_terisi <= slot_maksimal),
    INDEX idx_kuota_grooming_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS booking_grooming (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    pelanggan_id                CHAR(36) NOT NULL,
    kucing_id                   CHAR(36) NOT NULL,
    jenis_grooming_id           CHAR(36) NOT NULL,
    kuota_grooming_id           CHAR(36) NOT NULL,
    dikonfirmasi_oleh_staff_id  CHAR(36) NULL,
    tanggal                     DATE NOT NULL,
    jam_grooming                TIME NULL,
    opsi_pengantaran            ENUM('ANTAR_JEMPUT', 'ANTAR_SENDIRI') NOT NULL DEFAULT 'ANTAR_SENDIRI',
    jarak_km                    DECIMAL(8, 2) NULL,
    biaya_antar_jemput          DECIMAL(12, 2) NOT NULL DEFAULT 0,
    harga_layanan               DECIMAL(12, 2) NOT NULL,
    status                      ENUM(
        'MENUNGGU_KONFIRMASI',
        'MENUNGGU_PEMBAYARAN',
        'MENUNGGU_VERIFIKASI_BUKTI',
        'TERKONFIRMASI',
        'SEDANG_PROSES',
        'SELESAI',
        'DIBATALKAN'
    ) NOT NULL DEFAULT 'MENUNGGU_KONFIRMASI',
    catatan                     TEXT NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_booking_grooming_pelanggan
        FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_grooming_kucing
        FOREIGN KEY (kucing_id) REFERENCES kucing(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_grooming_jenis
        FOREIGN KEY (jenis_grooming_id) REFERENCES jenis_grooming(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_grooming_kuota
        FOREIGN KEY (kuota_grooming_id) REFERENCES kuota_grooming(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_grooming_staff
        FOREIGN KEY (dikonfirmasi_oleh_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    CONSTRAINT chk_booking_grooming_harga CHECK (harga_layanan >= 0),
    CONSTRAINT chk_booking_grooming_biaya_antar CHECK (biaya_antar_jemput >= 0),
    INDEX idx_booking_grooming_pelanggan (pelanggan_id),
    INDEX idx_booking_grooming_status (status),
    INDEX idx_booking_grooming_tanggal (tanggal),
    INDEX idx_booking_grooming_kuota (kuota_grooming_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
