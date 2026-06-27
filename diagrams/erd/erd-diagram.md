# ERD — Aplikasi Petshop

Entity Relationship Diagram berdasarkan [idea.md](../../idea.md) dan [class diagram](../class/class-diagram.md).

**Database:** PostgreSQL  
**Konvensi penamaan:** `snake_case`, tabel jamak, PK `UUID`, timestamp `created_at` / `updated_at`

---

## 1. ERD Overview

```mermaid
erDiagram
    pelanggan ||--o{ kucing : memiliki
    pelanggan ||--o{ booking_grooming : mengajukan
    pelanggan ||--o{ booking_penitipan : mengajukan
    pelanggan ||--o{ booking_pet_care : mengajukan
    pelanggan ||--o{ transaksi : memiliki
    pelanggan ||--o{ notifikasi : menerima

    kucing ||--o{ riwayat_vaksin : memiliki
    kucing ||--o{ booking_grooming : dilayani
    kucing ||--o{ booking_penitipan : dititipkan
    kucing ||--o{ booking_pet_care : dilayani

    staff ||--o{ bukti_transfer : verifikasi
    staff ||--o{ monitoring_penitipan : input
    staff ||--o{ booking_grooming : konfirmasi
    staff ||--o{ booking_penitipan : konfirmasi
    staff ||--o{ booking_pet_care : konfirmasi

    jenis_grooming ||--o{ booking_grooming : dipilih
    kuota_grooming ||--o{ booking_grooming : menggunakan

    paket_penitipan ||--o{ booking_penitipan : dipilih
    kamar_penitipan ||--o{ booking_penitipan : menggunakan
    kamar_penitipan ||--o{ kuota_penitipan : memiliki
    booking_penitipan ||--o{ monitoring_penitipan : memiliki

    layanan_pet_care ||--o{ booking_pet_care : dipilih
    layanan_pet_care ||--o{ kuota_pet_care : memiliki
    kuota_pet_care ||--o{ booking_pet_care : menggunakan

    booking_grooming ||--|| transaksi : "1:1"
    booking_penitipan ||--|| transaksi : "1:1"
    booking_pet_care ||--|| transaksi : "1:1"

    transaksi ||--o| bukti_transfer : bukti
    transaksi ||--o| invoice : invoice

    pelanggan {
        uuid id PK
        varchar nama
        varchar email UK
        varchar password_hash
        varchar alamat_lengkap
        decimal latitude
        decimal longitude
        boolean pernah_pakai_promo_penitipan
    }

    staff {
        uuid id PK
        varchar nama
        varchar email UK
        enum role
        enum status_akun
    }

    kucing {
        uuid id PK
        uuid pelanggan_id FK
        varchar nama
        enum jenis_kelamin
        varchar ras
    }

    booking_grooming {
        uuid id PK
        uuid pelanggan_id FK
        uuid kucing_id FK
        enum status
        enum opsi_pengantaran
    }

    booking_penitipan {
        uuid id PK
        uuid pelanggan_id FK
        uuid kucing_id FK
        date check_in
        date check_out
        enum status
    }

    booking_pet_care {
        uuid id PK
        uuid pelanggan_id FK
        uuid kucing_id FK
        enum status
    }

    transaksi {
        uuid id PK
        uuid pelanggan_id FK
        enum jenis_layanan
        uuid booking_id
        decimal total_bayar
        enum status_pembayaran
    }
```

---

## 2. Modul Akun & Pengguna

```mermaid
erDiagram
    pelanggan {
        uuid id PK
        varchar nama "NOT NULL"
        varchar email UK "NOT NULL"
        varchar no_telepon
        varchar password_hash "NOT NULL"
        text alamat_lengkap
        decimal_10_8 latitude
        decimal_11_8 longitude
        varchar foto_profil_url
        boolean pernah_pakai_promo_penitipan "DEFAULT false"
        timestamptz created_at
        timestamptz updated_at
    }

    staff {
        uuid id PK
        varchar nama "NOT NULL"
        varchar email UK "NOT NULL"
        varchar username UK
        varchar password_hash "NOT NULL"
        staff_role role "STAFF | OWNER"
        status_akun status "AKTIF | NONAKTIF"
        timestamptz created_at
        timestamptz updated_at
    }
```

| Entitas | Keterangan |
|---------|------------|
| **pelanggan** | Akun pelanggan; autentikasi terpisah dari staff/owner |
| **staff** | Akun internal; kolom `role = OWNER` untuk pemilik bisnis (generalisasi Owner → Staff) |

---

## 3. Modul Data Kucing

```mermaid
erDiagram
    pelanggan ||--o{ kucing : "1:N"
    kucing ||--o{ riwayat_vaksin : "1:N"

    kucing {
        uuid id PK
        uuid pelanggan_id FK "NOT NULL"
        varchar nama "NOT NULL"
        jenis_kelamin jenis_kelamin "JANTAN | BETINA"
        varchar ras
        date tanggal_lahir
        decimal berat_badan
        varchar foto_url
        text catatan_kesehatan
        timestamptz created_at
        timestamptz updated_at
    }

    riwayat_vaksin {
        uuid id PK
        uuid kucing_id FK "NOT NULL"
        varchar jenis_vaksin "NOT NULL"
        date tanggal_vaksin "NOT NULL"
        varchar sertifikat_url "opsional"
        timestamptz created_at
    }
```

**Aturan:**
- `kucing.pelanggan_id` → data kucing milik pelanggan, bukan global petshop
- Hapus kucing ditolak jika masih ada booking aktif (dicek di aplikasi)
- Pet hotel: minimal 1 `riwayat_vaksin` dengan `jenis_vaksin` & `tanggal_vaksin` terisi

---

## 4. Modul Master Data Layanan

```mermaid
erDiagram
    kamar_penitipan ||--o{ kuota_penitipan : "1:N"
    layanan_pet_care ||--o{ kuota_pet_care : "1:N"

    jenis_grooming {
        uuid id PK
        varchar nama UK "NOT NULL"
        text deskripsi
        decimal harga "NOT NULL"
        boolean aktif "DEFAULT true"
        timestamptz created_at
        timestamptz updated_at
    }

    kuota_grooming {
        uuid id PK
        date tanggal UK "NOT NULL"
        int slot_maksimal "NOT NULL"
        int slot_terisi "DEFAULT 0"
        timestamptz created_at
        timestamptz updated_at
    }

    paket_penitipan {
        uuid id PK
        varchar nama UK "NOT NULL"
        decimal harga_per_hari "NOT NULL"
        text deskripsi
        boolean aktif "DEFAULT true"
        timestamptz created_at
        timestamptz updated_at
    }

    kamar_penitipan {
        uuid id PK
        varchar nama_kamar UK "NOT NULL"
        int kapasitas "NOT NULL"
        boolean aktif "DEFAULT true"
        timestamptz created_at
        timestamptz updated_at
    }

    kuota_penitipan {
        uuid id PK
        uuid kamar_penitipan_id FK "NOT NULL"
        date tanggal "NOT NULL"
        int slot_maksimal "NOT NULL"
        int slot_terisi "DEFAULT 0"
        timestamptz created_at
        timestamptz updated_at
    }

    layanan_pet_care {
        uuid id PK
        varchar nama "NOT NULL"
        text deskripsi
        decimal harga "NOT NULL"
        int estimasi_durasi_menit "NOT NULL"
        status_layanan status "AKTIF | NONAKTIF"
        timestamptz deleted_at "soft delete"
        timestamptz created_at
        timestamptz updated_at
    }

    kuota_pet_care {
        uuid id PK
        uuid layanan_pet_care_id FK "NOT NULL"
        date tanggal "NOT NULL"
        time slot_waktu "NOT NULL"
        int slot_maksimal "NOT NULL"
        int slot_terisi "DEFAULT 0"
        timestamptz created_at
        timestamptz updated_at
    }
```

| Entitas | Keterangan |
|---------|------------|
| **jenis_grooming** | Master jenis grooming & harga (lengkap, jamur, kutu, dll.) |
| **kuota_grooming** | Slot maksimal per tanggal; `slot_terisi` naik/turun saat booking |
| **paket_penitipan** | Harga per hari penitipan |
| **kamar_penitipan** + **kuota_penitipan** | Kamar & ketersediaan slot per tanggal |
| **layanan_pet_care** | CRUD staff/owner; soft delete via `deleted_at` |

---

## 5. Modul Booking

### 5a. Booking Grooming

```mermaid
erDiagram
    pelanggan ||--o{ booking_grooming : mengajukan
    kucing ||--o{ booking_grooming : dilayani
    jenis_grooming ||--o{ booking_grooming : dipilih
    kuota_grooming ||--o{ booking_grooming : menggunakan
    staff ||--o{ booking_grooming : "konfirmasi / update jam"

    booking_grooming {
        uuid id PK
        uuid pelanggan_id FK
        uuid kucing_id FK
        uuid jenis_grooming_id FK
        uuid kuota_grooming_id FK
        uuid dikonfirmasi_oleh_staff_id FK "nullable"
        date tanggal "NOT NULL"
        time jam_grooming "nullable, diisi staff"
        opsi_pengantaran opsi_pengantaran
        decimal jarak_km
        decimal biaya_antar_jemput
        decimal harga_layanan "snapshot harga"
        status_booking_grooming status
        text catatan
        timestamptz created_at
        timestamptz updated_at
    }
```

### 5b. Booking Penitipan (Pet Hotel)

```mermaid
erDiagram
    pelanggan ||--o{ booking_penitipan : mengajukan
    kucing ||--o{ booking_penitipan : dititipkan
    paket_penitipan ||--o{ booking_penitipan : dipilih
    kamar_penitipan ||--o{ booking_penitipan : menggunakan
    booking_penitipan ||--o{ monitoring_penitipan : memiliki
    staff ||--o{ monitoring_penitipan : input

    booking_penitipan {
        uuid id PK
        uuid pelanggan_id FK
        uuid kucing_id FK
        uuid paket_penitipan_id FK
        uuid kamar_penitipan_id FK
        uuid dikonfirmasi_oleh_staff_id FK
        date check_in "NOT NULL"
        date check_out "NOT NULL"
        int lama_hari "NOT NULL"
        boolean promo_dipakai "DEFAULT false"
        decimal subtotal_penitipan
        decimal potongan_promo
        opsi_pengantaran opsi_pengantaran
        decimal jarak_km
        decimal biaya_antar_jemput
        status_penitipan status
        text catatan_makan
        timestamptz created_at
        timestamptz updated_at
    }

    monitoring_penitipan {
        uuid id PK
        uuid booking_penitipan_id FK
        uuid staff_id FK
        date tanggal "NOT NULL"
        varchar foto_url
        text catatan_makan
        text kondisi
        text aktivitas_harian
        timestamptz created_at
    }
```

### 5c. Booking Pet Care

```mermaid
erDiagram
    pelanggan ||--o{ booking_pet_care : mengajukan
    kucing ||--o{ booking_pet_care : dilayani
    layanan_pet_care ||--o{ booking_pet_care : dipilih
    kuota_pet_care ||--o{ booking_pet_care : menggunakan

    booking_pet_care {
        uuid id PK
        uuid pelanggan_id FK
        uuid kucing_id FK
        uuid layanan_pet_care_id FK
        uuid kuota_pet_care_id FK
        uuid dikonfirmasi_oleh_staff_id FK
        date tanggal "NOT NULL"
        time slot_waktu "NOT NULL"
        decimal harga_layanan "snapshot harga"
        status_booking status
        text catatan
        timestamptz created_at
        timestamptz updated_at
    }
```

**Catatan booking:**
- Pet care **tidak** punya `opsi_pengantaran` (selalu antar sendiri)
- Grooming & penitipan menyimpan snapshot `jarak_km` & `biaya_antar_jemput`
- Harga layanan di-snapshot saat booking agar perubahan master data tidak mengubah riwayat

---

## 6. Modul Pembayaran & Transaksi

```mermaid
erDiagram
    pelanggan ||--o{ transaksi : memiliki
    transaksi ||--o| bukti_transfer : "0..1"
    transaksi ||--o| invoice : "0..1"
    staff ||--o{ bukti_transfer : verifikasi

    transaksi {
        uuid id PK
        uuid pelanggan_id FK
        jenis_layanan jenis_layanan "GROOMING | PENITIPAN | PET_CARE"
        uuid booking_id "polymorphic ref"
        decimal subtotal_layanan
        decimal potongan_promo
        decimal biaya_antar_jemput
        decimal total_bayar
        status_pembayaran status_pembayaran
        status_refund status_refund
        timestamptz batas_waktu_bayar
        timestamptz dibayar_at
        timestamptz created_at
        timestamptz updated_at
    }

    bukti_transfer {
        uuid id PK
        uuid transaksi_id FK UK
        varchar file_url "NOT NULL"
        status_verifikasi status_verifikasi
        uuid diverifikasi_oleh_staff_id FK
        text catatan_penolakan
        timestamptz uploaded_at
        timestamptz diverifikasi_at
    }

    invoice {
        uuid id PK
        uuid transaksi_id FK UK
        varchar nomor_invoice UK
        varchar file_url
        timestamptz issued_at
    }
```

**Relasi polymorphic:** `transaksi.jenis_layanan` + `transaksi.booking_id` merujuk ke tabel booking yang sesuai. Unique index `(jenis_layanan, booking_id)` menjamin 1 transaksi per booking.

---

## 7. Modul Notifikasi

```mermaid
erDiagram
    pelanggan ||--o{ notifikasi : menerima

    notifikasi {
        uuid id PK
        uuid penerima_id "pelanggan_id"
        tipe_penerima tipe_penerima "PELANGGAN | STAFF"
        jenis_notifikasi jenis
        varchar judul "NOT NULL"
        text pesan "NOT NULL"
        uuid referensi_id
        varchar referensi_tipe
        boolean sudah_dibaca "DEFAULT false"
        timestamptz created_at
    }
```

---

## 8. Diagram Relasi Lengkap (Cardinality)

```mermaid
erDiagram
    pelanggan ||--o{ kucing : "1:N"
    kucing ||--o{ riwayat_vaksin : "1:N"

    pelanggan ||--o{ booking_grooming : "1:N"
    pelanggan ||--o{ booking_penitipan : "1:N"
    pelanggan ||--o{ booking_pet_care : "1:N"

    kucing ||--o{ booking_grooming : "1:N"
    kucing ||--o{ booking_penitipan : "1:N"
    kucing ||--o{ booking_pet_care : "1:N"

    jenis_grooming ||--o{ booking_grooming : "1:N"
    kuota_grooming ||--o{ booking_grooming : "1:N"

    paket_penitipan ||--o{ booking_penitipan : "1:N"
    kamar_penitipan ||--o{ booking_penitipan : "1:N"
    kamar_penitipan ||--o{ kuota_penitipan : "1:N"

    layanan_pet_care ||--o{ booking_pet_care : "1:N"
    layanan_pet_care ||--o{ kuota_pet_care : "1:N"
    kuota_pet_care ||--o{ booking_pet_care : "1:N"

    booking_penitipan ||--o{ monitoring_penitipan : "1:N"
    staff ||--o{ monitoring_penitipan : "1:N"
    staff ||--o{ bukti_transfer : "1:N"

    booking_grooming ||--|| transaksi : "1:1"
    booking_penitipan ||--|| transaksi : "1:1"
    booking_pet_care ||--|| transaksi : "1:1"

    transaksi ||--o| bukti_transfer : "1:0..1"
    transaksi ||--o| invoice : "1:0..1"

    pelanggan ||--o{ transaksi : "1:N"
    pelanggan ||--o{ notifikasi : "1:N"
```

---

## 9. Daftar Tabel & Relasi

| No | Tabel | PK | FK utama | Relasi |
|----|-------|----|---------|----|
| 1 | `pelanggan` | id | — | 1→N kucing, booking, transaksi, notifikasi |
| 2 | `staff` | id | — | 1→N bukti_transfer, monitoring, konfirmasi booking |
| 3 | `kucing` | id | pelanggan_id | 1→N riwayat_vaksin, booking |
| 4 | `riwayat_vaksin` | id | kucing_id | N→1 kucing |
| 5 | `jenis_grooming` | id | — | 1→N booking_grooming |
| 6 | `kuota_grooming` | id | — | 1→N booking_grooming |
| 7 | `paket_penitipan` | id | — | 1→N booking_penitipan |
| 8 | `kamar_penitipan` | id | — | 1→N kuota_penitipan, booking_penitipan |
| 9 | `kuota_penitipan` | id | kamar_penitipan_id | N→1 kamar |
| 10 | `layanan_pet_care` | id | — | 1→N kuota_pet_care, booking_pet_care |
| 11 | `kuota_pet_care` | id | layanan_pet_care_id | N→1 layanan |
| 12 | `booking_grooming` | id | pelanggan, kucing, jenis, kuota | 1→1 transaksi |
| 13 | `booking_penitipan` | id | pelanggan, kucing, paket, kamar | 1→1 transaksi, 1→N monitoring |
| 14 | `monitoring_penitipan` | id | booking_penitipan_id, staff_id | N→1 booking |
| 15 | `booking_pet_care` | id | pelanggan, kucing, layanan, kuota | 1→1 transaksi |
| 16 | `transaksi` | id | pelanggan_id | 1→0..1 bukti_transfer, invoice |
| 17 | `bukti_transfer` | id | transaksi_id, staff_id | N→1 transaksi |
| 18 | `invoice` | id | transaksi_id | N→1 transaksi |
| 19 | `notifikasi` | id | penerima_id | N→1 pelanggan/staff |

**Total: 19 tabel**

---

## 10. Enum & Konstanta

### Enum di database

| Enum | Nilai |
|------|-------|
| `staff_role` | `STAFF`, `OWNER` |
| `status_akun` | `AKTIF`, `NONAKTIF` |
| `jenis_kelamin` | `JANTAN`, `BETINA` |
| `opsi_pengantaran` | `ANTAR_JEMPUT`, `ANTAR_SENDIRI` |
| `status_booking_grooming` | `MENUNGGU_KONFIRMASI`, `MENUNGGU_PEMBAYARAN`, `MENUNGGU_VERIFIKASI_BUKTI`, `TERKONFIRMASI`, `SEDANG_PROSES`, `SELESAI`, `DIBATALKAN` |
| `status_penitipan` | `MENUNGGU_KONFIRMASI`, `MENUNGGU_PEMBAYARAN`, `MENUNGGU_VERIFIKASI_BUKTI`, `CHECK_IN`, `SEDANG_DITITIPKAN`, `CHECK_OUT`, `DIBATALKAN` |
| `status_booking` | `MENUNGGU_KONFIRMASI`, `MENUNGGU_PEMBAYARAN`, `MENUNGGU_VERIFIKASI_BUKTI`, `TERKONFIRMASI`, `SEDANG_PROSES`, `SELESAI`, `DIBATALKAN` |
| `status_layanan` | `AKTIF`, `NONAKTIF` |
| `jenis_layanan` | `GROOMING`, `PENITIPAN`, `PET_CARE` |
| `status_pembayaran` | `MENUNGGU_PEMBAYARAN`, `MENUNGGU_VERIFIKASI`, `LUNAS`, `DIBATALKAN`, `KEDALUWARSA` |
| `status_verifikasi` | `MENUNGGU`, `DISETUJUI`, `DITOLAK` |
| `status_refund` | `TIDAK_ADA`, `PENDING_REFUND`, `REFUNDED` |
| `tipe_penerima` | `PELANGGAN`, `STAFF` |
| `jenis_notifikasi` | `BOOKING_DISETUJUI`, `BOOKING_DITOLAK`, `JAM_GROOMING_DIUPDATE`, `REMINDER_PEMBAYARAN`, `PEMBAYARAN_JATUH_TEMPO`, `MONITORING_PENITIPAN`, `LAYANAN_SELESAI`, `BOOKING_DIBATALKAN`, `STATUS_REFUND` |

### Konstanta hardcode (bukan tabel)

| Konstanta | Nilai | Dipakai di |
|-----------|-------|------------|
| `PICKUP_FREE_RADIUS_KM` | 3 | Grooming, Penitipan |
| `PICKUP_EXTRA_FEE_PER_KM` | 5000 | Grooming, Penitipan |
| `PETSHOP_LAT`, `PETSHOP_LNG` | koordinat | Hitung jarak |
| `MIN_VACCINATION_COUNT` | 1 | Validasi pet hotel |
| `PROMO_MIN_DAYS` | 7 | Promo penitipan |
| `PROMO_DISCOUNT_PERCENT` | 10 | Promo penitipan |
| `PETSHOP_WHATSAPP` | nomor WA | Hubungi Kami |
| Rekening bank | hardcode app | Transfer manual |

---

## 11. Index Penting

| Tabel | Index | Alasan |
|-------|-------|--------|
| `pelanggan` | `email` UNIQUE | Login |
| `staff` | `email`, `username` UNIQUE | Login |
| `kucing` | `pelanggan_id` | Daftar kucing per pelanggan |
| `riwayat_vaksin` | `kucing_id` | Cek syarat vaksin |
| `kuota_grooming` | `tanggal` UNIQUE | Kuota harian |
| `kuota_penitipan` | `(kamar_penitipan_id, tanggal)` UNIQUE | Kuota per kamar per hari |
| `kuota_pet_care` | `(layanan_pet_care_id, tanggal, slot_waktu)` UNIQUE | Slot unik |
| `booking_*` | `pelanggan_id`, `status`, `tanggal/check_in` | Filter dashboard |
| `transaksi` | `(jenis_layanan, booking_id)` UNIQUE | 1 transaksi per booking |
| `transaksi` | `pelanggan_id`, `status_pembayaran` | Tagihan menunggu |
| `notifikasi` | `(penerima_id, sudah_dibaca)` | Pusat notifikasi |

---

## File terkait

- Skema SQL implementasi: [database/schema.sql](../../database/schema.sql)
- LRS (Logical Record Structure): [database/lrs.md](../../database/lrs.md)
- Class diagram: [class-diagram.md](../class/class-diagram.md)
