-- =============================================================================
-- Schema Database Penitipan (Pet Hotel) — Aplikasi Petshop (MariaDB)
-- Fase: Booking penitipan, monitoring, perpanjangan
-- =============================================================================

USE petshop;

CREATE TABLE IF NOT EXISTS paket_penitipan (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    nama                        VARCHAR(100) NOT NULL,
    harga_per_hari              DECIMAL(12, 2) NOT NULL,
    deskripsi                   TEXT NULL,
    aktif                       TINYINT(1) NOT NULL DEFAULT 1,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_paket_penitipan_nama UNIQUE (nama),
    CONSTRAINT chk_paket_penitipan_harga CHECK (harga_per_hari >= 0),
    INDEX idx_paket_penitipan_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kamar_penitipan (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    nama_kamar                  VARCHAR(100) NOT NULL,
    kapasitas                   INT NOT NULL,
    aktif                       TINYINT(1) NOT NULL DEFAULT 1,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_kamar_penitipan_nama UNIQUE (nama_kamar),
    CONSTRAINT chk_kamar_penitipan_kapasitas CHECK (kapasitas > 0),
    INDEX idx_kamar_penitipan_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kuota_penitipan (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    kamar_penitipan_id          CHAR(36) NOT NULL,
    tanggal                     DATE NOT NULL,
    slot_maksimal               INT NOT NULL,
    slot_terisi                 INT NOT NULL DEFAULT 0,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_kuota_penitipan_kamar
        FOREIGN KEY (kamar_penitipan_id) REFERENCES kamar_penitipan(id) ON DELETE RESTRICT,
    CONSTRAINT uq_kuota_penitipan_kamar_tanggal UNIQUE (kamar_penitipan_id, tanggal),
    CONSTRAINT chk_kuota_penitipan_maksimal CHECK (slot_maksimal >= 0),
    CONSTRAINT chk_kuota_penitipan_terisi CHECK (slot_terisi >= 0 AND slot_terisi <= slot_maksimal),
    INDEX idx_kuota_penitipan_tanggal (tanggal),
    INDEX idx_kuota_penitipan_kamar (kamar_penitipan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS booking_penitipan (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    pelanggan_id                CHAR(36) NOT NULL,
    kucing_id                   CHAR(36) NOT NULL,
    paket_penitipan_id          CHAR(36) NOT NULL,
    kamar_penitipan_id          CHAR(36) NOT NULL,
    dikonfirmasi_oleh_staff_id  CHAR(36) NULL,
    check_in                    DATE NOT NULL,
    check_out                   DATE NOT NULL,
    lama_hari                   INT NOT NULL,
    promo_dipakai               TINYINT(1) NOT NULL DEFAULT 0,
    subtotal_penitipan          DECIMAL(12, 2) NOT NULL,
    potongan_promo              DECIMAL(12, 2) NOT NULL DEFAULT 0,
    opsi_pengantaran            ENUM('ANTAR_JEMPUT', 'ANTAR_SENDIRI') NOT NULL DEFAULT 'ANTAR_SENDIRI',
    jarak_km                    DECIMAL(8, 2) NULL,
    biaya_antar_jemput          DECIMAL(12, 2) NOT NULL DEFAULT 0,
    status                      ENUM(
        'MENUNGGU_KONFIRMASI',
        'MENUNGGU_PEMBAYARAN',
        'MENUNGGU_VERIFIKASI_BUKTI',
        'CHECK_IN',
        'SEDANG_DITITIPKAN',
        'CHECK_OUT',
        'DIBATALKAN'
    ) NOT NULL DEFAULT 'MENUNGGU_KONFIRMASI',
    catatan_makan               TEXT NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_booking_penitipan_pelanggan
        FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_penitipan_kucing
        FOREIGN KEY (kucing_id) REFERENCES kucing(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_penitipan_paket
        FOREIGN KEY (paket_penitipan_id) REFERENCES paket_penitipan(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_penitipan_kamar
        FOREIGN KEY (kamar_penitipan_id) REFERENCES kamar_penitipan(id) ON DELETE RESTRICT,
    CONSTRAINT fk_booking_penitipan_staff
        FOREIGN KEY (dikonfirmasi_oleh_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    CONSTRAINT chk_booking_penitipan_lama_hari CHECK (lama_hari > 0),
    CONSTRAINT chk_booking_penitipan_subtotal CHECK (subtotal_penitipan >= 0),
    CONSTRAINT chk_booking_penitipan_potongan CHECK (potongan_promo >= 0),
    CONSTRAINT chk_booking_penitipan_biaya_antar CHECK (biaya_antar_jemput >= 0),
    CONSTRAINT chk_booking_penitipan_tanggal CHECK (check_out > check_in),
    INDEX idx_booking_penitipan_pelanggan (pelanggan_id),
    INDEX idx_booking_penitipan_status (status),
    INDEX idx_booking_penitipan_check_in (check_in),
    INDEX idx_booking_penitipan_kamar (kamar_penitipan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS monitoring_penitipan (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    booking_penitipan_id        CHAR(36) NOT NULL,
    staff_id                    CHAR(36) NOT NULL,
    tanggal                     DATE NOT NULL,
    foto_url                    VARCHAR(500) NULL,
    catatan_makan               TEXT NULL,
    kondisi                     TEXT NULL,
    aktivitas_harian            TEXT NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_monitoring_penitipan_booking
        FOREIGN KEY (booking_penitipan_id) REFERENCES booking_penitipan(id) ON DELETE CASCADE,
    CONSTRAINT fk_monitoring_penitipan_staff
        FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE RESTRICT,
    INDEX idx_monitoring_penitipan_booking (booking_penitipan_id),
    INDEX idx_monitoring_penitipan_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS perpanjangan_penitipan (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    booking_penitipan_id        CHAR(36) NOT NULL,
    check_out_sebelum           DATE NOT NULL,
    check_out_baru              DATE NOT NULL,
    tambah_hari                 INT NOT NULL,
    subtotal_tambahan           DECIMAL(12, 2) NOT NULL,
    status                      ENUM(
        'MENUNGGU_KONFIRMASI',
        'MENUNGGU_PEMBAYARAN',
        'MENUNGGU_VERIFIKASI_BUKTI',
        'DISETUJUI',
        'DITOLAK',
        'DIBATALKAN'
    ) NOT NULL DEFAULT 'MENUNGGU_KONFIRMASI',
    dikonfirmasi_oleh_staff_id  CHAR(36) NULL,
    catatan_penolakan           TEXT NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_perpanjangan_penitipan_booking
        FOREIGN KEY (booking_penitipan_id) REFERENCES booking_penitipan(id) ON DELETE RESTRICT,
    CONSTRAINT fk_perpanjangan_penitipan_staff
        FOREIGN KEY (dikonfirmasi_oleh_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    CONSTRAINT chk_perpanjangan_tambah_hari CHECK (tambah_hari > 0),
    CONSTRAINT chk_perpanjangan_subtotal CHECK (subtotal_tambahan >= 0),
    CONSTRAINT chk_perpanjangan_tanggal CHECK (check_out_baru > check_out_sebelum),
    INDEX idx_perpanjangan_penitipan_booking (booking_penitipan_id),
    INDEX idx_perpanjangan_penitipan_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
