-- =============================================================================
-- Schema Database Notifikasi — Aplikasi Petshop (MariaDB)
-- Fase: Notifikasi in-app pelanggan & staff
-- =============================================================================

USE petshop;

CREATE TABLE IF NOT EXISTS notifikasi (
    id                          CHAR(36) NOT NULL PRIMARY KEY,
    penerima_id                 CHAR(36) NOT NULL,
    tipe_penerima               ENUM('PELANGGAN', 'STAFF') NOT NULL DEFAULT 'PELANGGAN',
    jenis                       ENUM(
        'BOOKING_DISETUJUI',
        'BOOKING_DITOLAK',
        'JAM_GROOMING_DIUPDATE',
        'REMINDER_PEMBAYARAN',
        'PEMBAYARAN_JATUH_TEMPO',
        'MONITORING_PENITIPAN',
        'LAYANAN_SELESAI',
        'BOOKING_DIBATALKAN',
        'STATUS_REFUND',
        'PERPANJANGAN_PENITIPAN_MENUNGGU_KONFIRMASI',
        'PERPANJANGAN_PENITIPAN_DISETUJUI',
        'PERPANJANGAN_PENITIPAN_DITOLAK',
        'PERPANJANGAN_PENITIPAN_MENUNGGU_PEMBAYARAN'
    ) NOT NULL,
    judul                       VARCHAR(200) NOT NULL,
    pesan                       TEXT NOT NULL,
    referensi_id                CHAR(36) NULL,
    referensi_tipe              VARCHAR(50) NULL,
    sudah_dibaca                TINYINT(1) NOT NULL DEFAULT 0,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_notifikasi_penerima (penerima_id, sudah_dibaca),
    INDEX idx_notifikasi_created_at (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
