-- =============================================================================
-- Schema Database Transaksi — Aplikasi Petshop (MariaDB)
-- Fase: Pembayaran grooming & penitipan (bukti transfer, invoice)
-- =============================================================================

USE petshop;

CREATE TABLE IF NOT EXISTS transaksi (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    pelanggan_id                CHAR(36) NOT NULL,
    jenis_layanan               ENUM('GROOMING', 'PENITIPAN') NOT NULL,
    booking_id                  CHAR(36) NOT NULL,
    perpanjangan_penitipan_id   CHAR(36) NULL,
    subtotal_layanan            DECIMAL(12, 2) NOT NULL,
    potongan_promo              DECIMAL(12, 2) NOT NULL DEFAULT 0,
    biaya_antar_jemput          DECIMAL(12, 2) NOT NULL DEFAULT 0,
    total_bayar                 DECIMAL(12, 2) NOT NULL,
    status_pembayaran           ENUM(
        'MENUNGGU_PEMBAYARAN',
        'MENUNGGU_VERIFIKASI',
        'LUNAS',
        'DIBATALKAN',
        'KEDALUWARSA'
    ) NOT NULL DEFAULT 'MENUNGGU_PEMBAYARAN',
    status_refund               ENUM('TIDAK_ADA', 'PENDING_REFUND', 'REFUNDED') NOT NULL DEFAULT 'TIDAK_ADA',
    batas_waktu_bayar           DATETIME NULL,
    dibayar_at                  DATETIME NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_transaksi_pelanggan
        FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT,
    CONSTRAINT chk_transaksi_subtotal CHECK (subtotal_layanan >= 0),
    CONSTRAINT chk_transaksi_potongan CHECK (potongan_promo >= 0),
    CONSTRAINT chk_transaksi_biaya_antar CHECK (biaya_antar_jemput >= 0),
    CONSTRAINT chk_transaksi_total CHECK (total_bayar >= 0),
    INDEX idx_transaksi_pelanggan (pelanggan_id),
    INDEX idx_transaksi_status (status_pembayaran),
    INDEX idx_transaksi_jenis_booking (jenis_layanan, booking_id),
    INDEX idx_transaksi_batas_waktu (batas_waktu_bayar)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bukti_transfer (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    transaksi_id                CHAR(36) NOT NULL,
    file_url                    VARCHAR(500) NOT NULL,
    status_verifikasi           ENUM('MENUNGGU', 'DISETUJUI', 'DITOLAK') NOT NULL DEFAULT 'MENUNGGU',
    diverifikasi_oleh_staff_id  CHAR(36) NULL,
    catatan_penolakan           TEXT NULL,
    uploaded_at                 DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    diverifikasi_at             DATETIME NULL,

    CONSTRAINT fk_bukti_transfer_transaksi
        FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    CONSTRAINT fk_bukti_transfer_staff
        FOREIGN KEY (diverifikasi_oleh_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    CONSTRAINT uq_bukti_transfer_transaksi UNIQUE (transaksi_id),
    INDEX idx_bukti_transfer_status (status_verifikasi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS invoice (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    transaksi_id                CHAR(36) NOT NULL,
    nomor_invoice               VARCHAR(50) NOT NULL,
    file_url                    VARCHAR(500) NULL,
    issued_at                   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_invoice_transaksi
        FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    CONSTRAINT uq_invoice_transaksi UNIQUE (transaksi_id),
    CONSTRAINT uq_invoice_nomor UNIQUE (nomor_invoice)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
