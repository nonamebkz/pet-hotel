-- =============================================================================
-- Schema Database — Aplikasi Petshop
-- Database: PostgreSQL 14+
-- Berdasarkan: idea.md, diagrams/erd/erd-diagram.md
-- =============================================================================

CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- =============================================================================
-- ENUM TYPES
-- =============================================================================

CREATE TYPE staff_role AS ENUM ('STAFF', 'OWNER');
CREATE TYPE status_akun AS ENUM ('AKTIF', 'NONAKTIF');
CREATE TYPE jenis_kelamin AS ENUM ('JANTAN', 'BETINA');
CREATE TYPE opsi_pengantaran AS ENUM ('ANTAR_JEMPUT', 'ANTAR_SENDIRI');
CREATE TYPE status_layanan AS ENUM ('AKTIF', 'NONAKTIF');

CREATE TYPE status_booking_grooming AS ENUM (
    'MENUNGGU_KONFIRMASI',
    'MENUNGGU_PEMBAYARAN',
    'MENUNGGU_VERIFIKASI_BUKTI',
    'TERKONFIRMASI',
    'SEDANG_PROSES',
    'SELESAI',
    'DIBATALKAN'
);

CREATE TYPE status_penitipan AS ENUM (
    'MENUNGGU_KONFIRMASI',
    'MENUNGGU_PEMBAYARAN',
    'MENUNGGU_VERIFIKASI_BUKTI',
    'CHECK_IN',
    'SEDANG_DITITIPKAN',
    'CHECK_OUT',
    'DIBATALKAN'
);

CREATE TYPE status_booking AS ENUM (
    'MENUNGGU_KONFIRMASI',
    'MENUNGGU_PEMBAYARAN',
    'MENUNGGU_VERIFIKASI_BUKTI',
    'TERKONFIRMASI',
    'SEDANG_PROSES',
    'SELESAI',
    'DIBATALKAN'
);

CREATE TYPE jenis_layanan AS ENUM ('GROOMING', 'PENITIPAN', 'PET_CARE');

CREATE TYPE status_pembayaran AS ENUM (
    'MENUNGGU_PEMBAYARAN',
    'MENUNGGU_VERIFIKASI',
    'LUNAS',
    'DIBATALKAN',
    'KEDALUWARSA'
);

CREATE TYPE status_verifikasi AS ENUM ('MENUNGGU', 'DISETUJUI', 'DITOLAK');
CREATE TYPE status_refund AS ENUM ('TIDAK_ADA', 'PENDING_REFUND', 'REFUNDED');
CREATE TYPE tipe_penerima AS ENUM ('PELANGGAN', 'STAFF');

CREATE TYPE jenis_notifikasi AS ENUM (
    'BOOKING_DISETUJUI',
    'BOOKING_DITOLAK',
    'JAM_GROOMING_DIUPDATE',
    'REMINDER_PEMBAYARAN',
    'PEMBAYARAN_JATUH_TEMPO',
    'MONITORING_PENITIPAN',
    'LAYANAN_SELESAI',
    'BOOKING_DIBATALKAN',
    'STATUS_REFUND'
);

-- =============================================================================
-- AKUN & PENGGUNA
-- =============================================================================

CREATE TABLE pelanggan (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nama                        VARCHAR(150) NOT NULL,
    email                       VARCHAR(255) NOT NULL,
    no_telepon                  VARCHAR(20),
    password_hash               VARCHAR(255) NOT NULL,
    alamat_lengkap              TEXT,
    latitude                    DECIMAL(10, 8),
    longitude                   DECIMAL(11, 8),
    foto_profil_url             VARCHAR(500),
    pernah_pakai_promo_penitipan BOOLEAN NOT NULL DEFAULT FALSE,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_pelanggan_email UNIQUE (email)
);

CREATE TABLE staff (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nama                        VARCHAR(150) NOT NULL,
    email                       VARCHAR(255) NOT NULL,
    username                    VARCHAR(100),
    password_hash               VARCHAR(255) NOT NULL,
    role                        staff_role NOT NULL DEFAULT 'STAFF',
    status                      status_akun NOT NULL DEFAULT 'AKTIF',
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_staff_email UNIQUE (email),
    CONSTRAINT uq_staff_username UNIQUE (username)
);

-- =============================================================================
-- DATA KUCING
-- =============================================================================

CREATE TABLE kucing (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pelanggan_id                UUID NOT NULL REFERENCES pelanggan(id) ON DELETE RESTRICT,
    nama                        VARCHAR(100) NOT NULL,
    jenis_kelamin               jenis_kelamin NOT NULL,
    ras                         VARCHAR(100),
    tanggal_lahir               DATE,
    berat_badan                 DECIMAL(5, 2),
    foto_url                    VARCHAR(500),
    catatan_kesehatan           TEXT,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_kucing_pelanggan_id ON kucing (pelanggan_id);

CREATE TABLE riwayat_vaksin (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    kucing_id                   UUID NOT NULL REFERENCES kucing(id) ON DELETE CASCADE,
    jenis_vaksin                VARCHAR(100) NOT NULL,
    tanggal_vaksin              DATE NOT NULL,
    sertifikat_url              VARCHAR(500),
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_riwayat_vaksin_kucing_id ON riwayat_vaksin (kucing_id);

-- =============================================================================
-- MASTER DATA LAYANAN
-- =============================================================================

CREATE TABLE jenis_grooming (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nama                        VARCHAR(100) NOT NULL,
    deskripsi                   TEXT,
    harga                       DECIMAL(12, 2) NOT NULL CHECK (harga >= 0),
    aktif                       BOOLEAN NOT NULL DEFAULT TRUE,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_jenis_grooming_nama UNIQUE (nama)
);

CREATE TABLE kuota_grooming (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tanggal                     DATE NOT NULL,
    slot_maksimal               INT NOT NULL CHECK (slot_maksimal >= 0),
    slot_terisi                 INT NOT NULL DEFAULT 0 CHECK (slot_terisi >= 0),
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_kuota_grooming_tanggal UNIQUE (tanggal),
    CONSTRAINT chk_kuota_grooming_terisi CHECK (slot_terisi <= slot_maksimal)
);

CREATE TABLE paket_penitipan (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nama                        VARCHAR(100) NOT NULL,
    harga_per_hari              DECIMAL(12, 2) NOT NULL CHECK (harga_per_hari >= 0),
    deskripsi                   TEXT,
    aktif                       BOOLEAN NOT NULL DEFAULT TRUE,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_paket_penitipan_nama UNIQUE (nama)
);

CREATE TABLE kamar_penitipan (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nama_kamar                  VARCHAR(100) NOT NULL,
    kapasitas                   INT NOT NULL CHECK (kapasitas > 0),
    aktif                       BOOLEAN NOT NULL DEFAULT TRUE,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_kamar_penitipan_nama UNIQUE (nama_kamar)
);

CREATE TABLE kuota_penitipan (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    kamar_penitipan_id          UUID NOT NULL REFERENCES kamar_penitipan(id) ON DELETE RESTRICT,
    tanggal                     DATE NOT NULL,
    slot_maksimal               INT NOT NULL CHECK (slot_maksimal >= 0),
    slot_terisi                 INT NOT NULL DEFAULT 0 CHECK (slot_terisi >= 0),
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_kuota_penitipan_kamar_tanggal UNIQUE (kamar_penitipan_id, tanggal),
    CONSTRAINT chk_kuota_penitipan_terisi CHECK (slot_terisi <= slot_maksimal)
);

CREATE TABLE layanan_pet_care (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nama                        VARCHAR(150) NOT NULL,
    deskripsi                   TEXT,
    harga                       DECIMAL(12, 2) NOT NULL CHECK (harga >= 0),
    estimasi_durasi_menit       INT NOT NULL CHECK (estimasi_durasi_menit > 0),
    status                      status_layanan NOT NULL DEFAULT 'AKTIF',
    deleted_at                  TIMESTAMPTZ,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE kuota_pet_care (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    layanan_pet_care_id         UUID NOT NULL REFERENCES layanan_pet_care(id) ON DELETE RESTRICT,
    tanggal                     DATE NOT NULL,
    slot_waktu                  TIME NOT NULL,
    slot_maksimal               INT NOT NULL CHECK (slot_maksimal >= 0),
    slot_terisi                 INT NOT NULL DEFAULT 0 CHECK (slot_terisi >= 0),
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_kuota_pet_care_layanan_tanggal_slot UNIQUE (layanan_pet_care_id, tanggal, slot_waktu),
    CONSTRAINT chk_kuota_pet_care_terisi CHECK (slot_terisi <= slot_maksimal)
);

-- =============================================================================
-- BOOKING
-- =============================================================================

CREATE TABLE booking_grooming (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pelanggan_id                UUID NOT NULL REFERENCES pelanggan(id) ON DELETE RESTRICT,
    kucing_id                   UUID NOT NULL REFERENCES kucing(id) ON DELETE RESTRICT,
    jenis_grooming_id           UUID NOT NULL REFERENCES jenis_grooming(id) ON DELETE RESTRICT,
    kuota_grooming_id           UUID NOT NULL REFERENCES kuota_grooming(id) ON DELETE RESTRICT,
    dikonfirmasi_oleh_staff_id  UUID REFERENCES staff(id) ON DELETE SET NULL,
    tanggal                     DATE NOT NULL,
    jam_grooming                TIME,
    opsi_pengantaran            opsi_pengantaran NOT NULL DEFAULT 'ANTAR_SENDIRI',
    jarak_km                    DECIMAL(8, 2),
    biaya_antar_jemput          DECIMAL(12, 2) NOT NULL DEFAULT 0 CHECK (biaya_antar_jemput >= 0),
    harga_layanan               DECIMAL(12, 2) NOT NULL CHECK (harga_layanan >= 0),
    status                      status_booking_grooming NOT NULL DEFAULT 'MENUNGGU_KONFIRMASI',
    catatan                     TEXT,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_booking_grooming_pelanggan ON booking_grooming (pelanggan_id);
CREATE INDEX idx_booking_grooming_status ON booking_grooming (status);
CREATE INDEX idx_booking_grooming_tanggal ON booking_grooming (tanggal);

CREATE TABLE booking_penitipan (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pelanggan_id                UUID NOT NULL REFERENCES pelanggan(id) ON DELETE RESTRICT,
    kucing_id                   UUID NOT NULL REFERENCES kucing(id) ON DELETE RESTRICT,
    paket_penitipan_id          UUID NOT NULL REFERENCES paket_penitipan(id) ON DELETE RESTRICT,
    kamar_penitipan_id          UUID NOT NULL REFERENCES kamar_penitipan(id) ON DELETE RESTRICT,
    dikonfirmasi_oleh_staff_id  UUID REFERENCES staff(id) ON DELETE SET NULL,
    check_in                    DATE NOT NULL,
    check_out                   DATE NOT NULL,
    lama_hari                   INT NOT NULL CHECK (lama_hari > 0),
    promo_dipakai               BOOLEAN NOT NULL DEFAULT FALSE,
    subtotal_penitipan          DECIMAL(12, 2) NOT NULL CHECK (subtotal_penitipan >= 0),
    potongan_promo              DECIMAL(12, 2) NOT NULL DEFAULT 0 CHECK (potongan_promo >= 0),
    opsi_pengantaran            opsi_pengantaran NOT NULL DEFAULT 'ANTAR_SENDIRI',
    jarak_km                    DECIMAL(8, 2),
    biaya_antar_jemput          DECIMAL(12, 2) NOT NULL DEFAULT 0 CHECK (biaya_antar_jemput >= 0),
    status                      status_penitipan NOT NULL DEFAULT 'MENUNGGU_KONFIRMASI',
    catatan_makan               TEXT,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT chk_booking_penitipan_tanggal CHECK (check_out > check_in)
);

CREATE INDEX idx_booking_penitipan_pelanggan ON booking_penitipan (pelanggan_id);
CREATE INDEX idx_booking_penitipan_status ON booking_penitipan (status);
CREATE INDEX idx_booking_penitipan_check_in ON booking_penitipan (check_in);

CREATE TABLE monitoring_penitipan (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    booking_penitipan_id        UUID NOT NULL REFERENCES booking_penitipan(id) ON DELETE CASCADE,
    staff_id                    UUID NOT NULL REFERENCES staff(id) ON DELETE RESTRICT,
    tanggal                     DATE NOT NULL,
    foto_url                    VARCHAR(500),
    catatan_makan               TEXT,
    kondisi                     TEXT,
    aktivitas_harian            TEXT,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_monitoring_penitipan_booking ON monitoring_penitipan (booking_penitipan_id);

CREATE TABLE booking_pet_care (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pelanggan_id                UUID NOT NULL REFERENCES pelanggan(id) ON DELETE RESTRICT,
    kucing_id                   UUID NOT NULL REFERENCES kucing(id) ON DELETE RESTRICT,
    layanan_pet_care_id         UUID NOT NULL REFERENCES layanan_pet_care(id) ON DELETE RESTRICT,
    kuota_pet_care_id           UUID NOT NULL REFERENCES kuota_pet_care(id) ON DELETE RESTRICT,
    dikonfirmasi_oleh_staff_id  UUID REFERENCES staff(id) ON DELETE SET NULL,
    tanggal                     DATE NOT NULL,
    slot_waktu                  TIME NOT NULL,
    harga_layanan               DECIMAL(12, 2) NOT NULL CHECK (harga_layanan >= 0),
    status                      status_booking NOT NULL DEFAULT 'MENUNGGU_KONFIRMASI',
    catatan                     TEXT,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_booking_pet_care_pelanggan ON booking_pet_care (pelanggan_id);
CREATE INDEX idx_booking_pet_care_status ON booking_pet_care (status);
CREATE INDEX idx_booking_pet_care_tanggal ON booking_pet_care (tanggal);

-- =============================================================================
-- PEMBAYARAN & TRANSAKSI
-- =============================================================================

CREATE TABLE transaksi (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pelanggan_id                UUID NOT NULL REFERENCES pelanggan(id) ON DELETE RESTRICT,
    jenis_layanan               jenis_layanan NOT NULL,
    booking_id                  UUID NOT NULL,
    subtotal_layanan            DECIMAL(12, 2) NOT NULL CHECK (subtotal_layanan >= 0),
    potongan_promo              DECIMAL(12, 2) NOT NULL DEFAULT 0 CHECK (potongan_promo >= 0),
    biaya_antar_jemput          DECIMAL(12, 2) NOT NULL DEFAULT 0 CHECK (biaya_antar_jemput >= 0),
    total_bayar                 DECIMAL(12, 2) NOT NULL CHECK (total_bayar >= 0),
    status_pembayaran           status_pembayaran NOT NULL DEFAULT 'MENUNGGU_PEMBAYARAN',
    status_refund               status_refund NOT NULL DEFAULT 'TIDAK_ADA',
    batas_waktu_bayar           TIMESTAMPTZ,
    dibayar_at                  TIMESTAMPTZ,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_transaksi_booking UNIQUE (jenis_layanan, booking_id)
);

CREATE INDEX idx_transaksi_pelanggan ON transaksi (pelanggan_id);
CREATE INDEX idx_transaksi_status_pembayaran ON transaksi (status_pembayaran);

CREATE TABLE bukti_transfer (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    transaksi_id                UUID NOT NULL REFERENCES transaksi(id) ON DELETE CASCADE,
    file_url                    VARCHAR(500) NOT NULL,
    status_verifikasi           status_verifikasi NOT NULL DEFAULT 'MENUNGGU',
    diverifikasi_oleh_staff_id  UUID REFERENCES staff(id) ON DELETE SET NULL,
    catatan_penolakan           TEXT,
    uploaded_at                 TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    diverifikasi_at             TIMESTAMPTZ,

    CONSTRAINT uq_bukti_transfer_transaksi UNIQUE (transaksi_id)
);

CREATE TABLE invoice (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    transaksi_id                UUID NOT NULL REFERENCES transaksi(id) ON DELETE CASCADE,
    nomor_invoice               VARCHAR(50) NOT NULL,
    file_url                    VARCHAR(500),
    issued_at                   TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_invoice_transaksi UNIQUE (transaksi_id),
    CONSTRAINT uq_invoice_nomor UNIQUE (nomor_invoice)
);

-- =============================================================================
-- NOTIFIKASI
-- =============================================================================

CREATE TABLE notifikasi (
    id                          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    penerima_id                 UUID NOT NULL,
    tipe_penerima               tipe_penerima NOT NULL DEFAULT 'PELANGGAN',
    jenis                       jenis_notifikasi NOT NULL,
    judul                       VARCHAR(200) NOT NULL,
    pesan                       TEXT NOT NULL,
    referensi_id                UUID,
    referensi_tipe              VARCHAR(50),
    sudah_dibaca                BOOLEAN NOT NULL DEFAULT FALSE,
    created_at                  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_notifikasi_penerima ON notifikasi (penerima_id, sudah_dibaca);
CREATE INDEX idx_notifikasi_created_at ON notifikasi (created_at DESC);

-- =============================================================================
-- TRIGGER: updated_at otomatis
-- =============================================================================

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_pelanggan_updated_at
    BEFORE UPDATE ON pelanggan
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_staff_updated_at
    BEFORE UPDATE ON staff
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_kucing_updated_at
    BEFORE UPDATE ON kucing
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_jenis_grooming_updated_at
    BEFORE UPDATE ON jenis_grooming
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_kuota_grooming_updated_at
    BEFORE UPDATE ON kuota_grooming
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_paket_penitipan_updated_at
    BEFORE UPDATE ON paket_penitipan
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_kamar_penitipan_updated_at
    BEFORE UPDATE ON kamar_penitipan
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_kuota_penitipan_updated_at
    BEFORE UPDATE ON kuota_penitipan
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_layanan_pet_care_updated_at
    BEFORE UPDATE ON layanan_pet_care
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_kuota_pet_care_updated_at
    BEFORE UPDATE ON kuota_pet_care
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_booking_grooming_updated_at
    BEFORE UPDATE ON booking_grooming
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_booking_penitipan_updated_at
    BEFORE UPDATE ON booking_penitipan
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_booking_pet_care_updated_at
    BEFORE UPDATE ON booking_pet_care
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_transaksi_updated_at
    BEFORE UPDATE ON transaksi
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
