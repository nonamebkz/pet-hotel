# Sequence Diagram — Aplikasi Petshop

Diagram urutan interaksi berdasarkan [idea.md](../../idea.md) dan [activity diagram](../activity/activity-diagram.md).

**Aktor:**
- **Pelanggan** — pengguna layanan
- **Staff / Owner** — operasional internal petshop

**Komponen sistem:**
- **Aplikasi Web** — antarmuka pengguna (frontend)
- **Backend API** — logika bisnis & autentikasi
- **Database** — PostgreSQL
- **Scheduler (Sistem)** — job otomatis (batas waktu pembayaran)

> **Preview:** Gunakan ekstensi PlantUML di VS Code/Cursor, atau render di [plantuml.com](https://www.plantuml.com/plantuml/uml).
>
> File `.puml` terpisah: `sequence-autentikasi-pelanggan.puml`, `sequence-autentikasi-staff.puml`, `sequence-data-kucing.puml`, `sequence-booking-grooming.puml`, `sequence-booking-penitipan.puml`, `sequence-booking-petcare.puml`, `sequence-pembayaran.puml`, `sequence-pembatalan-refund.puml`, `sequence-monitoring-penitipan.puml`, `sequence-perpanjangan-penitipan.puml`, `sequence-manajemen-staff-owner.puml`, `sequence-pengaturan-petshop.puml`, `sequence-laporan.puml`

---

## Daftar diagram

| No | Diagram | Aktor utama | File |
|----|---------|-------------|------|
| 1 | Autentikasi Pelanggan | Pelanggan | [sequence-autentikasi-pelanggan.puml](./sequence-autentikasi-pelanggan.puml) |
| 2 | Autentikasi Staff / Owner | Staff, Owner | [sequence-autentikasi-staff.puml](./sequence-autentikasi-staff.puml) |
| 3 | Kelola Data Kucing | Pelanggan | [sequence-data-kucing.puml](./sequence-data-kucing.puml) |
| 4 | Booking Grooming | Pelanggan, Staff | [sequence-booking-grooming.puml](./sequence-booking-grooming.puml) |
| 5 | Booking Penitipan | Pelanggan, Staff | [sequence-booking-penitipan.puml](./sequence-booking-penitipan.puml) |
| 6 | Booking Pet Care | Pelanggan, Staff | [sequence-booking-petcare.puml](./sequence-booking-petcare.puml) |
| 7 | Pembayaran & Verifikasi | Pelanggan, Staff, Sistem | [sequence-pembayaran.puml](./sequence-pembayaran.puml) |
| 8 | Pembatalan & Refund | Pelanggan, Staff | [sequence-pembatalan-refund.puml](./sequence-pembatalan-refund.puml) |
| 9 | Monitoring Penitipan | Staff, Pelanggan | [sequence-monitoring-penitipan.puml](./sequence-monitoring-penitipan.puml) |
| 10 | Perpanjangan Penitipan | Pelanggan, Staff | [sequence-perpanjangan-penitipan.puml](./sequence-perpanjangan-penitipan.puml) |
| 11 | Manajemen Staff (Owner) | Owner | [sequence-manajemen-staff-owner.puml](./sequence-manajemen-staff-owner.puml) |
| 12 | Pengaturan Bisnis Petshop (Owner) | Owner | [sequence-pengaturan-petshop.puml](./sequence-pengaturan-petshop.puml) |
| 13 | Laporan (Dashboard Admin) | Staff, Owner | [sequence-laporan.puml](./sequence-laporan.puml) |

---

## 1. Autentikasi Pelanggan

Alur daftar akun, login, lupa/reset password, ubah password, dan logout.

```plantuml
@startuml sequence-autentikasi-pelanggan
skinparam sequenceMessageAlign center
skinparam responseMessageBelowArrow true

title Sequence Diagram — Autentikasi Pelanggan

actor Pelanggan
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB

== Daftar Akun ==

Pelanggan -> App : Buka halaman daftar
Pelanggan -> App : Isi form (nama, email, password)
App -> API : POST /auth/register
API -> API : Validasi input
alt Data tidak valid
  API --> App : 422 Error validasi
  App --> Pelanggan : Tampilkan pesan error
else Data valid
  API -> DB : INSERT pelanggan
  DB --> API : OK
  API --> App : 201 Created
  App --> Pelanggan : Redirect ke login / dashboard
end

== Login ==

Pelanggan -> App : Isi email & password
App -> API : POST /auth/login
API -> DB : SELECT pelanggan BY email
DB --> API : Data pelanggan
alt Kredensial salah
  API --> App : 401 Unauthorized
  App --> Pelanggan : Tampilkan error login
else Kredensial benar
  API -> API : Buat sesi / JWT token
  API --> App : 200 OK + token
  App --> Pelanggan : Masuk dashboard
end

== Logout ==

Pelanggan -> App : Klik logout
App -> API : POST /auth/logout
API -> API : Hapus sesi / invalidate token
API --> App : 200 OK
App --> Pelanggan : Redirect ke halaman login

@enduml
```

---

## 2. Autentikasi Staff / Owner

Login terpisah dari akun pelanggan; owner dan staff memakai portal internal yang sama.

```plantuml
@startuml sequence-autentikasi-staff
skinparam sequenceMessageAlign center

title Sequence Diagram — Autentikasi Staff / Owner

actor "Staff / Owner" as User
participant "Aplikasi Web\n(Dashboard Internal)" as App
participant "Backend API" as API
database "Database" as DB

User -> App : Isi email/username & password
App -> API : POST /internal/auth/login
API -> DB : SELECT staff BY email/username
DB --> API : Data staff
alt Akun nonaktif
  API --> App : 403 Forbidden
  App --> User : Akun dinonaktifkan
else Kredensial benar & akun aktif
  API -> API : Buat sesi / JWT (role: STAFF/OWNER)
  API --> App : 200 OK + token + role
  App --> User : Masuk dashboard internal
end

@enduml
```

---

## 3. Kelola Data Kucing

Tambah, edit, hapus kucing milik pelanggan; validasi booking aktif saat hapus.

```plantuml
@startuml sequence-data-kucing
skinparam sequenceMessageAlign center

title Sequence Diagram — Kelola Data Kucing

actor Pelanggan
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB

== Tambah Kucing ==

Pelanggan -> App : Isi form kucing + vaksin (opsional)
App -> API : POST /kucing
API -> DB : INSERT kucing + riwayat_vaksin
DB --> API : OK
API --> App : 201 Created
App --> Pelanggan : Kucing berhasil ditambahkan

== Hapus Kucing ==

Pelanggan -> App : Hapus kucing
App -> API : DELETE /kucing/{id}
API -> DB : Cek booking aktif
alt Masih ada booking aktif
  API --> App : 409 Conflict
  App --> Pelanggan : Penghapusan ditolak
else Tidak ada booking aktif
  API -> DB : DELETE kucing
  App --> Pelanggan : Kucing dihapus
end

@enduml
```

---

## 4. Booking Grooming (End-to-End)

Alur lengkap: ajukan booking → konfirmasi staff & set jam → pembayaran → verifikasi → proses layanan.

```plantuml
@startuml sequence-booking-grooming
skinparam sequenceMessageAlign center

title Sequence Diagram — Booking Grooming (End-to-End)

actor Pelanggan
actor "Staff / Owner" as Staff
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB

Pelanggan -> App : Submit booking grooming
App -> API : POST /booking/grooming
API -> DB : INSERT booking + transaksi\nUPDATE kuota
App --> Pelanggan : Menunggu konfirmasi

Staff -> App : Konfirmasi & isi jam grooming
App -> API : PATCH .../konfirmasi
API -> DB : status = MENUNGGU_PEMBAYARAN
App --> Pelanggan : Notifikasi jam & tagihan

Pelanggan -> App : Upload bukti transfer
App -> API : POST /transaksi/{id}/bukti-transfer
Staff -> App : Setujui bukti
App -> API : PATCH .../setujui
API -> DB : status = TERKONFIRMASI, LUNAS
App --> Pelanggan : Invoice + notifikasi lunas

Staff -> App : Update status → Selesai
App --> Pelanggan : Notifikasi layanan selesai

@enduml
```

---

## 5. Booking Penitipan / Pet Hotel (End-to-End)

Validasi vaksin, promo 10%, antar-jemput, monitoring harian, check-in/out.

```plantuml
@startuml sequence-booking-penitipan
skinparam sequenceMessageAlign center

title Sequence Diagram — Booking Penitipan (Ringkas)

actor Pelanggan
actor "Staff / Owner" as Staff
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB

Pelanggan -> App : Pilih kucing (cek vaksin ≥ 1)
alt Vaksin tidak lengkap
  App --> Pelanggan : Form ditolak
else Vaksin OK
  Pelanggan -> App : Submit penitipan
  App -> API : POST /booking/penitipan
  API -> DB : INSERT booking + transaksi
end

Staff -> App : Cek vaksin & konfirmasi
App -> API : PATCH .../konfirmasi
App --> Pelanggan : Tagihan menunggu bayar

Pelanggan -> App : Upload bukti transfer
Staff -> App : Setujui bukti
API -> DB : LUNAS + promo flag jika dipakai

loop Setiap hari penitipan
  Staff -> App : Input monitoring harian
  App -> API : POST /monitoring-penitipan
  App --> Pelanggan : Notifikasi monitoring
end

Staff -> App : CHECK_OUT
App --> Pelanggan : Penitipan selesai

@enduml
```

---

## 6. Booking Pet Care (Booking Only)

Booking-only, auto-confirm, pembayaran di loket; jadwal slot dokter global.

```plantuml
@startuml sequence-booking-petcare
skinparam sequenceMessageAlign center

title Sequence Diagram — Booking Pet Care (Booking Only)

actor Pelanggan
actor "Staff / Owner" as Staff
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB

== Ajukan Booking ==

Pelanggan -> App : Buka menu Pet Care
App -> API : GET /pet-care/layanan?status=AKTIF
API -> DB : SELECT layanan_pet_care\nWHERE status=AKTIF AND deleted_at IS NULL
DB --> API : Daftar layanan
API --> App : Layanan aktif
App --> Pelanggan : Tampilkan layanan (nama, harga estimasi, durasi)

Pelanggan -> App : Pilih tanggal
App -> API : GET /pet-care/slot?tanggal
API -> DB : SELECT kuota_pet_care\nWHERE tanggal = ?\nAND status_slot = TERSEDIA\nAND slot_terisi = 0
DB --> API : Slot tersedia
API --> App : Daftar slot kosong
App --> Pelanggan : Tampilkan slot tersedia

Pelanggan -> App : Pilih slot, layanan, kucing\n+ catatan opsional
note over App, Pelanggan
  Pengantaran: antar sendiri saja
  Pembayaran di loket saat kunjungan
end note
App -> API : POST /booking/pet-care
API -> DB : BEGIN TRANSACTION
API -> DB : Cek slot_terisi = 0\nAND status_slot = TERSEDIA
API -> DB : INSERT booking_pet_care\nstatus = TERKONFIRMASI
API -> DB : UPDATE kuota_pet_care.slot_terisi = 1
API -> DB : INSERT notifikasi BOOKING_DISETUJUI
API -> DB : COMMIT
DB --> API : OK
API --> App : 201 Created
App --> Pelanggan : Booking terkonfirmasi

== Operasional Staff ==

Staff -> App : Buka daftar booking pet care
Staff -> App : Update status → Sedang Proses → Selesai
App -> API : PATCH /internal/booking/pet-care/{id}/status
API -> DB : UPDATE status booking
API -> DB : INSERT notifikasi LAYANAN_SELESAI
App --> Pelanggan : Notifikasi layanan selesai

== Pembatalan ==

alt Pelanggan batalkan
  Pelanggan -> App : Batalkan booking
  App -> API : PATCH /booking/pet-care/{id}/batalkan
  API -> DB : UPDATE status = DIBATALKAN\ndibatalkan_oleh = PELANGGAN
  API -> DB : UPDATE kuota_pet_care.slot_terisi = 0
  API -> DB : INSERT notifikasi BOOKING_DIBATALKAN
  App --> Pelanggan : Notifikasi dibatalkan
else Staff batalkan
  Staff -> App : Batalkan booking\n(alasan opsional)
  App -> API : PATCH /internal/booking/pet-care/{id}/batalkan
  API -> DB : UPDATE status = DIBATALKAN\ndibatalkan_oleh = STAFF
  API -> DB : UPDATE kuota_pet_care.slot_terisi = 0
  API -> DB : INSERT notifikasi BOOKING_DIBATALKAN
  App --> Pelanggan : Notifikasi dibatalkan
end

@enduml
```

---

## 7. Pembayaran & Verifikasi Bukti Transfer

Berlaku untuk grooming & penitipan (**pet care dikecualikan**); termasuk pembatalan otomatis jika lewat batas waktu.

```plantuml
@startuml sequence-pembayaran
skinparam sequenceMessageAlign center

title Sequence Diagram — Pembayaran & Verifikasi (Global)

actor Pelanggan
actor "Staff / Owner" as Staff
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB
participant "Scheduler\n(Sistem)" as Cron

Pelanggan -> App : Lihat rincian tagihan & rekening petshop
Pelanggan -> App : Transfer + upload bukti (wajib)
App -> API : POST /transaksi/{id}/bukti-transfer
API -> DB : INSERT bukti_transfer

Staff -> App : Review bukti transfer
alt Ditolak
  App --> Pelanggan : Upload ulang bukti
else Disetujui
  API -> DB : LUNAS + TERKONFIRMASI + invoice
  App --> Pelanggan : Notifikasi lunas
end

Cron -> API : Cek batas_waktu_bayar lewat
API -> DB : DIBATALKAN + kembalikan kuota
note over Cron : Notifikasi ke pelanggan

@enduml
```

---

## 8. Pembatalan & Refund

Dua skenario: batalkan langsung (belum bayar) vs hubungi staff via WhatsApp (sudah bayar).

```plantuml
@startuml sequence-pembatalan-refund
skinparam sequenceMessageAlign center

title Sequence Diagram — Pembatalan & Refund

actor Pelanggan
actor "Staff / Owner" as Staff
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB

alt Belum terkonfirmasi / belum bayar
  Pelanggan -> App : Batalkan booking
  App -> API : PATCH /booking/{id}/batalkan
  API -> DB : DIBATALKAN + kembalikan kuota
  App --> Pelanggan : Notifikasi pembatalan
else Sudah terkonfirmasi & sudah bayar
  Pelanggan -> App : Klik Hubungi Kami
  App --> Pelanggan : Redirect wa.me/{PETSHOP_WHATSAPP}
  Staff -> App : Batalkan dari dashboard
  API -> DB : DIBATALKAN + PENDING_REFUND
  Staff -> App : Update refund → REFUNDED
  App --> Pelanggan : Notifikasi status refund
end

@enduml
```

---

## 9. Monitoring Harian Penitipan

Staff input monitoring; pelanggan menerima notifikasi dan melihat riwayat.

```plantuml
@startuml sequence-monitoring-penitipan
skinparam sequenceMessageAlign center

title Sequence Diagram — Monitoring Harian Penitipan

actor "Staff / Owner" as Staff
actor Pelanggan
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB

Staff -> App : Input monitoring (foto, makan, kondisi, aktivitas)
App -> API : POST /internal/monitoring-penitipan
API -> DB : INSERT monitoring_penitipan
API -> DB : INSERT notifikasi MONITORING_PENITIPAN
App --> Pelanggan : Notifikasi update monitoring

Pelanggan -> App : Lihat riwayat monitoring penitipan
App -> API : GET /booking/penitipan/{id}/monitoring
API -> DB : SELECT monitoring_penitipan
App --> Pelanggan : Tampilkan foto & catatan harian

@enduml
```

---

## 10. Perpanjangan Penitipan

Alur perpanjangan durasi penitipan setelah booking terkonfirmasi (check-in / sedang dititipkan).

```plantuml
@startuml sequence-perpanjangan-penitipan
skinparam sequenceMessageAlign center

title Sequence Diagram — Perpanjangan Penitipan (End-to-End)

actor Pelanggan
actor "Staff / Owner" as Staff
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB

== Ajukan Perpanjangan ==

Pelanggan -> App : Buka detail booking penitipan
App -> API : GET /booking/penitipan/{id}
API -> DB : SELECT booking (CHECK_IN / SEDANG_DITITIPKAN)
DB --> API : Detail booking
App --> Pelanggan : Tampilkan tombol Perpanjang

Pelanggan -> App : Pilih check-out baru
App -> API : POST /booking/penitipan/{id}/perpanjangan/estimasi
API -> API : Hitung tambah_hari & subtotal\n(tanpa promo & antar-jemput)
API -> DB : Cek kuota hari tambahan
API --> App : Ringkasan biaya

Pelanggan -> App : Submit permintaan perpanjangan
App -> API : POST /booking/penitipan/{id}/perpanjangan
API -> DB : INSERT perpanjangan_penitipan\nstatus = MENUNGGU_KONFIRMASI
API -> DB : INSERT notifikasi ke staff

== Konfirmasi Staff ==

Staff -> App : Konfirmasi / tolak perpanjangan
App -> API : PATCH /internal/perpanjangan-penitipan/{id}/konfirmasi|tolak
API -> DB : UPDATE status + INSERT transaksi (jika konfirmasi)
App --> Pelanggan : Notifikasi tagihan / ditolak

== Pembayaran ==

Pelanggan -> App : Upload bukti transfer
App -> API : POST /transaksi/{id}/bukti-transfer
Staff -> App : Setujui bukti transfer
App -> API : PATCH /internal/bukti-transfer/{id}/setujui
API -> DB : UPDATE transaksi LUNAS\nUPDATE perpanjangan DISETUJUI\nUPDATE booking check_out & lama_hari
App --> Pelanggan : Notifikasi perpanjangan aktif + invoice

@enduml
```

---

## 11. Manajemen Akun Staff (Owner)

Khusus owner: CRUD akun staff, reset password, aktif/nonaktif.

```plantuml
@startuml sequence-manajemen-staff-owner
skinparam sequenceMessageAlign center

title Sequence Diagram — Manajemen Staff (Owner)

actor Owner
participant "Aplikasi Web" as App
participant "Backend API" as API
database "Database" as DB

Owner -> App : Tambah akun staff
App -> API : POST /internal/staff
API -> API : Verifikasi role = OWNER
API -> DB : INSERT staff (role=STAFF)
App --> Owner : Staff ditambahkan

Owner -> App : Nonaktifkan staff
App -> API : PATCH /internal/staff/{id}/status
API -> DB : UPDATE status = NONAKTIF
App --> Owner : Staff dinonaktifkan

note over Owner, DB
  Owner tidak bisa dinonaktifkan oleh staff
end note

@enduml
```

---

## 12. Laporan (Dashboard Admin)

Staff/Owner membuka menu Laporan terpisah, memfilter data per layanan, dan melihat agregat booking (serta pendapatan untuk grooming & pet hotel).

```plantuml
@startuml sequence-laporan
skinparam sequenceMessageAlign center

title Sequence Diagram — Laporan (Dashboard Admin)

actor "Staff / Owner" as User
participant "Aplikasi Web\n(Dashboard Admin)" as App
participant "Backend API" as API
database "Database" as DB

== Lihat Ringkasan Laporan ==

User -> App : Buka menu Laporan
App -> API : GET /internal/laporan/ringkasan?periode=...
API -> API : Verifikasi role = STAFF / OWNER
API -> DB : Aggregate booking_grooming,\nbooking_penitipan, booking_pet_care
DB --> API : Total booking per layanan
API --> App : 200 OK + ringkasan kartu
App --> User : Tampilkan halaman indeks Laporan

== Laporan Data Grooming ==

User -> App : Buka Laporan Data Grooming\n+ set filter periode & status
App -> API : GET /internal/laporan/grooming?mulai=...&akhir=...&status=...
API -> API : Verifikasi role = STAFF / OWNER
API -> DB : SELECT booking_grooming\nJOIN transaksi, jenis_grooming\n(aggregate + detail)
DB --> API : Data laporan grooming
API --> App : 200 OK + metrik & tabel
App --> User : Tampilkan laporan grooming

== Laporan Data Pet Hotel ==

User -> App : Buka Laporan Data Pet Hotel\n+ set filter periode & status
App -> API : GET /internal/laporan/penitipan?mulai=...&akhir=...&status=...
API -> API : Verifikasi role = STAFF / OWNER
API -> DB : SELECT booking_penitipan\nJOIN transaksi, perpanjangan_penitipan\n(aggregate + detail)
DB --> API : Data laporan pet hotel
API --> App : 200 OK + metrik & tabel
App --> User : Tampilkan laporan pet hotel

== Laporan Data Booking Pet Care ==

User -> App : Buka Laporan Data Booking Pet Care\n+ set filter periode, status, layanan
App -> API : GET /internal/laporan/pet-care?mulai=...&akhir=...&layanan=...
API -> API : Verifikasi role = STAFF / OWNER
API -> DB : SELECT booking_pet_care\nJOIN layanan_pet_care, slot_dokter\n(aggregate jumlah — tanpa pendapatan)
DB --> API : Data laporan pet care
API --> App : 200 OK + metrik & tabel
App --> User : Tampilkan laporan pet care

== Export (Opsional) ==

User -> App : Klik Export CSV / PDF
App -> API : GET /internal/laporan/{jenis}/export?format=...
API -> API : Verifikasi role = STAFF / OWNER
API -> DB : Query data laporan (sama seperti di atas)
DB --> API : Data
API --> App : 200 OK + file export
App --> User : Unduh file

note over User, DB
  Menu Laporan hanya tersedia
  di dashboard admin (Staff/Owner).
  Pelanggan tidak memiliki akses.
end note

@enduml
```

---

## Overview — Interaksi Utama Sistem

Diagram ringkas interaksi antar komponen untuk alur booking umum.

```mermaid
sequenceDiagram
    autonumber
    actor P as Pelanggan
    participant A as Aplikasi Web
    participant B as Backend API
    participant D as Database
    actor S as Staff

    P->>A: Ajukan booking
    A->>B: POST /booking
    B->>D: Simpan booking + transaksi
    D-->>B: OK
    B-->>A: 201 Created
    A-->>P: Menunggu konfirmasi

    S->>A: Konfirmasi booking
    A->>B: PATCH /konfirmasi
    B->>D: Update status + batas waktu bayar
    B-->>A: OK
    A-->>P: Notifikasi tagihan

    P->>A: Upload bukti transfer
    A->>B: POST /bukti-transfer
    B->>D: Simpan bukti

    S->>A: Verifikasi bukti
    A->>B: PATCH /setujui
    B->>D: LUNAS + invoice
    B-->>A: OK
    A-->>P: Notifikasi lunas
```

---

## Notasi & simbol

| Simbol | Arti |
|--------|------|
| `->` | Panggilan / request (sinkron) |
| `-->` | Response / return |
| `alt / else / end` | Percabangan kondisi |
| `opt / end` | Langkah opsional |
| `loop / end` | Pengulangan |
| `== ... ==` | Pemisah fase/alur |
| `note over` | Catatan pada diagram |

---

## File terkait

- Activity diagram: [activity-diagram.md](../activity/activity-diagram.md)
- Use case diagram: [usecase-diagram.md](../usecase/usecase-diagram.md)
- Class diagram: [class-diagram.md](../class/class-diagram.md)
- ERD: [erd-diagram.md](../erd/erd-diagram.md)
