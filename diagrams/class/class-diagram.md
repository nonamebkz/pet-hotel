# Class Diagram — Aplikasi Petshop

Diagram kelas berdasarkan [idea.md](../../idea.md).

**Paket utama:**
- **Akun & Pengguna** — Pelanggan, Staff, Owner
- **Data Kucing** — Kucing, Riwayat Vaksin
- **Layanan** — Grooming, Penitipan (Pet Hotel), Pet Care
- **Pembayaran** — Transaksi, Bukti Transfer, Invoice
- **Sistem** — Notifikasi

---

## 1. Diagram Overview

```mermaid
classDiagram
    direction TB

    class Pelanggan {
        +UUID id
        +String nama
        +String email
        +String passwordHash
        +String alamatLengkap
        +Decimal latitude
        +Decimal longitude
        +String fotoProfilUrl
        +DateTime createdAt
        +login()
        +updateProfil()
    }

    class Staff {
        +UUID id
        +String nama
        +String email
        +String passwordHash
        +StatusAkun status
        +DateTime createdAt
        +login()
        +ubahPassword()
        +lihatLaporanGrooming()
        +lihatLaporanPetHotel()
        +lihatLaporanPetCare()
        +exportLaporan()
    }

    class Owner {
        +login()
        +kelolaAkunStaff()
    }

    class Kucing {
        +UUID id
        +String nama
        +JenisKelamin jenisKelamin
        +String ras
        +Date tanggalLahir
        +Decimal beratBadan
        +String fotoUrl
        +String catatanKesehatan
        +tambah()
        +edit()
        +hapus()
        +isEligiblePetHotel()
    }

    class RiwayatVaksin {
        +UUID id
        +String jenisVaksin
        +Date tanggalVaksin
        +String sertifikatUrl
    }

    class BookingGrooming {
        +UUID id
        +Date tanggal
        +Time jamGrooming
        +OpsiPengantaran opsiPengantaran
        +Decimal jarakKm
        +Decimal biayaAntarJemput
        +StatusBooking status
        +String catatan
        +ajukan()
        +batalkan()
    }

    class BookingPenitipan {
        +UUID id
        +Date checkIn
        +Date checkOut
        +Int lamaHari
        +Boolean promoDipakai
        +Decimal potonganPromo
        +OpsiPengantaran opsiPengantaran
        +Decimal jarakKm
        +Decimal biayaAntarJemput
        +StatusPenitipan status
        +String catatanMakan
        +ajukan()
        +batalkan()
        +perpanjangDurasi()
    }

    class PerpanjanganPenitipan {
        +UUID id
        +Date checkOutSebelum
        +Date checkOutBaru
        +Int tambahHari
        +StatusPerpanjanganPenitipan status
        +ajukan()
    }

    class BookingPetCare {
        +UUID id
        +Date tanggal
        +Time slotWaktu
        +StatusBookingPetCare status
        +String catatan
        +ajukan()
        +batalkan()
    }

    class Transaksi {
        +UUID id
        +JenisLayanan jenisLayanan
        +Decimal subtotalLayanan
        +Decimal potonganPromo
        +Decimal biayaAntarJemput
        +Decimal totalBayar
        +StatusPembayaran statusPembayaran
        +StatusRefund statusRefund
        +DateTime batasWaktuBayar
        +hitungTotal()
        +verifikasiBukti()
    }

    class Notifikasi {
        +UUID id
        +JenisNotifikasi jenis
        +String judul
        +String pesan
        +Boolean sudahDibaca
        +DateTime createdAt
        +kirim()
        +tandaiDibaca()
    }

    Owner --|> Staff : inherits
    Pelanggan "1" --> "*" Kucing : memiliki
    Kucing "1" --> "*" RiwayatVaksin : memiliki
    Pelanggan "1" --> "*" BookingGrooming : mengajukan
    Pelanggan "1" --> "*" BookingPenitipan : mengajukan
    Pelanggan "1" --> "*" BookingPetCare : mengajukan
    Kucing "1" --> "*" BookingGrooming : dilayani
    Kucing "1" --> "*" BookingPenitipan : dititipkan
    Kucing "1" --> "*" BookingPetCare : dilayani
    BookingPenitipan "1" --> "*" PerpanjanganPenitipan : memiliki
    BookingGrooming "1" --> "1" Transaksi : menghasilkan
    BookingPenitipan "1" --> "*" Transaksi : menghasilkan
    PerpanjanganPenitipan "1" --> "1" Transaksi : menghasilkan
    Pelanggan "1" --> "*" Notifikasi : menerima
    Staff "1" --> "*" Transaksi : verifikasi
```

---

## 2. Akun & Pengguna

Autentikasi terpisah: akun pelanggan vs akun internal (staff/owner).

```mermaid
classDiagram
    direction LR

    class Pelanggan {
        +UUID id
        +String nama
        +String email
        +String noTelepon
        +String passwordHash
        +String alamatLengkap
        +Decimal latitude
        +Decimal longitude
        +String fotoProfilUrl
        +DateTime createdAt
        +DateTime updatedAt
        +daftar()
        +login()
        +logout()
        +resetPassword()
        +ubahPassword()
        +updateProfil()
        +uploadFotoProfil()
        +isAlamatLengkap()
    }

    class Staff {
        +UUID id
        +String nama
        +String email
        +String username
        +String passwordHash
        +StatusAkun status
        +DateTime createdAt
        +DateTime updatedAt
        +login()
        +logout()
        +ubahPassword()
        +lihatLaporanGrooming(periode, filter)
        +lihatLaporanPetHotel(periode, filter)
        +lihatLaporanPetCare(periode, filter)
        +exportLaporan(jenis, format)
    }

    class Owner {
        +login()
        +logout()
        +ubahPassword()
        +lihatDaftarStaff()
        +tambahStaff()
        +editStaff()
        +resetPasswordStaff()
        +aktifkanStaff()
        +nonaktifkanStaff()
    }

    class StatusAkun {
        <<enumeration>>
        AKTIF
        NONAKTIF
    }

    Owner --|> Staff : generalisasi
    Staff --> StatusAkun : status
```

| Kelas | Keterangan |
|-------|------------|
| **Pelanggan** | Pemilik kucing; alamat wajib lengkap jika memilih antar-jemput |
| **Staff** | Pegawai operasional; tidak bisa kelola akun staff lain; akses menu Laporan (grooming, pet hotel, pet care) |
| **Owner** | Pemilik bisnis; mewarisi semua akses staff + manajemen akun staff |
| **StatusAkun** | Staff nonaktif tidak bisa login |

---

## 3. Data Kucing

Data kucing dimiliki pelanggan (`user_id`), bukan data global petshop.

```mermaid
classDiagram
    direction TB

    class Pelanggan {
        +UUID id
    }

    class Kucing {
        +UUID id
        +UUID pelangganId
        +String nama
        +JenisKelamin jenisKelamin
        +String ras
        +Date tanggalLahir
        +Int umur
        +Decimal beratBadan
        +String fotoUrl
        +String catatanKesehatan
        +DateTime createdAt
        +DateTime updatedAt
        +tambah()
        +edit()
        +hapus()
        +getUmur()
        +countRiwayatVaksinLengkap()
        +isEligiblePetHotel()
        +hasBookingAktif()
    }

    class RiwayatVaksin {
        +UUID id
        +UUID kucingId
        +String jenisVaksin
        +Date tanggalVaksin
        +String sertifikatUrl
        +DateTime createdAt
        +isLengkap()
    }

    class JenisKelamin {
        <<enumeration>>
        JANTAN
        BETINA
    }

    Pelanggan "1" --> "*" Kucing : memiliki
    Kucing "1" --> "*" RiwayatVaksin : memiliki
    Kucing --> JenisKelamin : jenisKelamin

    note for Kucing "Hapus ditolak jika masih ada booking aktif"
    note for RiwayatVaksin "Sertifikat opsional; jenis & tanggal wajib untuk pet hotel"
    note for Kucing "isEligiblePetHotel(): minimal 1 riwayat vaksin lengkap (MIN_VACCINATION_COUNT=1)"
```

---

## 4. Master Data Layanan

```mermaid
classDiagram
    direction TB

    class JenisGrooming {
        +UUID id
        +String nama
        +String deskripsi
        +Decimal harga
        +Boolean aktif
        +DateTime createdAt
    }

    class KuotaGrooming {
        +UUID id
        +Date tanggal
        +Int slotMaksimal
        +Int slotTerisi
        +getSisaKuota()
        +incrementTerisi()
        +decrementTerisi()
    }

    class PaketPenitipan {
        +UUID id
        +String nama
        +Decimal hargaPerHari
        +String deskripsi
        +Boolean aktif
    }

    class KamarPenitipan {
        +UUID id
        +String namaKamar
        +Int kapasitas
        +Boolean aktif
    }

    class KuotaPenitipan {
        +UUID id
        +UUID kamarPenitipanId
        +Date tanggal
        +Int slotMaksimal
        +Int slotTerisi
        +getSisaKuota()
    }

    class LayananPetCare {
        +UUID id
        +String nama
        +String deskripsi
        +Decimal harga
        +Int estimasiDurasiMenit
        +StatusLayanan status
        +DateTime deletedAt
        +DateTime createdAt
        +softDelete()
        +aktifkan()
        +nonaktifkan()
    }

    class KuotaPetCare {
        +UUID id
        +Date tanggal
        +Time slotWaktu
        +Int slotMaksimal
        +Int slotTerisi
        +StatusSlotPetCare statusSlot
        +getSisaKuota()
    }

    class StatusSlotPetCare {
        <<enumeration>>
        TERSEDIA
        DITUTUP
    }

    class StatusLayanan {
        <<enumeration>>
        AKTIF
        NONAKTIF
    }

    KamarPenitipan "1" --> "*" KuotaPenitipan : memiliki
    KuotaPetCare "1" --> "*" BookingPetCare : menggunakan
    LayananPetCare "1" --> "*" BookingPetCare : dipilih
    LayananPetCare --> StatusLayanan : status
    KuotaPetCare --> StatusSlotPetCare : statusSlot

    note for LayananPetCare "CRUD oleh staff/owner; soft delete — riwayat booking tetap tersimpan"
    note for KuotaPetCare "Jadwal slot dokter global; 1 dokter = maks 1 booking/slot"
    note for KuotaGrooming "Slot maksimal per tanggal; dikembalikan saat booking dibatalkan"
```

---

## 5. Booking — Grooming

```mermaid
classDiagram
    direction TB

    class Pelanggan {
        +UUID id
    }

    class Kucing {
        +UUID id
    }

    class JenisGrooming {
        +UUID id
        +Decimal harga
    }

    class KuotaGrooming {
        +UUID id
        +Date tanggal
    }

    class BookingGrooming {
        +UUID id
        +UUID pelangganId
        +UUID kucingId
        +UUID jenisGroomingId
        +UUID kuotaGroomingId
        +Date tanggal
        +Time jamGrooming
        +OpsiPengantaran opsiPengantaran
        +Decimal jarakKm
        +Decimal biayaAntarJemput
        +StatusBookingGrooming status
        +String catatan
        +DateTime createdAt
        +DateTime updatedAt
        +ajukan()
        +batalkan()
        +hitungBiayaAntarJemput()
        +getRingkasanBiaya()
    }

    class Staff {
        +UUID id
    }

    class OpsiPengantaran {
        <<enumeration>>
        ANTAR_JEMPUT
        ANTAR_SENDIRI
    }

    class StatusBookingGrooming {
        <<enumeration>>
        MENUNGGU_KONFIRMASI
        MENUNGGU_PEMBAYARAN
        MENUNGGU_VERIFIKASI_BUKTI
        TERKONFIRMASI
        SEDANG_PROSES
        SELESAI
        DIBATALKAN
    }

    Pelanggan "1" --> "*" BookingGrooming : mengajukan
    Kucing "1" --> "*" BookingGrooming : dilayani
    JenisGrooming "1" --> "*" BookingGrooming : dipilih
    KuotaGrooming "1" --> "*" BookingGrooming : menggunakan
    Staff "1" --> "*" BookingGrooming : konfirmasi / update jam
    BookingGrooming --> OpsiPengantaran : opsiPengantaran
    BookingGrooming --> StatusBookingGrooming : status

    note for BookingGrooming "Jam grooming diisi staff setelah konfirmasi awal"
    note for BookingGrooming "hitungBiayaAntarJemput(): gratis ≤3km, (jarak-3)×5000 jika >3km"
```

---

## 6. Booking — Penitipan (Pet Hotel)

```mermaid
classDiagram
    direction TB

    class BookingPenitipan {
        +UUID id
        +UUID pelangganId
        +UUID kucingId
        +UUID paketPenitipanId
        +UUID kamarPenitipanId
        +Date checkIn
        +Date checkOut
        +Int lamaHari
        +Boolean promoDipakai
        +Decimal subtotalPenitipan
        +Decimal potonganPromo
        +OpsiPengantaran opsiPengantaran
        +Decimal jarakKm
        +Decimal biayaAntarJemput
        +StatusPenitipan status
        +String catatanMakan
        +DateTime createdAt
        +ajukan()
        +batalkan()
        +hitungSubtotal()
        +hitungPromo()
        +isEligiblePromo()
        +perpanjangDurasi()
        +terapkanPerpanjangan()
    }

    class PerpanjanganPenitipan {
        +UUID id
        +UUID bookingPenitipanId
        +Date checkOutSebelum
        +Date checkOutBaru
        +Int tambahHari
        +Decimal subtotalTambahan
        +StatusPerpanjanganPenitipan status
        +UUID dikonfirmasiOlehStaffId
        +String catatanPenolakan
        +DateTime createdAt
        +ajukan()
        +konfirmasiStaff()
        +tolakStaff()
        +verifikasiPembayaran()
        +hitungSubtotalTambahan()
    }

    class MonitoringPenitipan {
        +UUID id
        +UUID bookingPenitipanId
        +UUID staffId
        +Date tanggal
        +String fotoUrl
        +String catatanMakan
        +String kondisi
        +String aktivitasHarian
        +DateTime createdAt
        +input()
    }

    class Pelanggan {
        +UUID id
        +Boolean pernahPakaiPromoPenitipan
    }

    class Kucing {
        +UUID id
    }

    class PaketPenitipan {
        +UUID id
    }

    class Staff {
        +UUID id
    }

    class StatusPenitipan {
        <<enumeration>>
        MENUNGGU_KONFIRMASI
        MENUNGGU_PEMBAYARAN
        MENUNGGU_VERIFIKASI_BUKTI
        CHECK_IN
        SEDANG_DITITIPKAN
        CHECK_OUT
        DIBATALKAN
    }

    class StatusPerpanjanganPenitipan {
        <<enumeration>>
        MENUNGGU_KONFIRMASI
        MENUNGGU_PEMBAYARAN
        MENUNGGU_VERIFIKASI_BUKTI
        DISETUJUI
        DITOLAK
        DIBATALKAN
    }

    Pelanggan "1" --> "*" BookingPenitipan : mengajukan
    Kucing "1" --> "*" BookingPenitipan : dititipkan
    PaketPenitipan "1" --> "*" BookingPenitipan : dipilih
    BookingPenitipan "1" --> "*" MonitoringPenitipan : memiliki
    BookingPenitipan "1" --> "*" PerpanjanganPenitipan : memiliki
    Staff "1" --> "*" MonitoringPenitipan : input
    Staff "1" --> "*" PerpanjanganPenitipan : konfirmasi
    BookingPenitipan --> StatusPenitipan : status
    PerpanjanganPenitipan --> StatusPerpanjanganPenitipan : status

    note for BookingPenitipan "Promo: durasi >7 hari → potongan 10%, 1× per akun (PROMO_MIN_DAYS=7)"
    note for BookingPenitipan "Validasi vaksin kucing wajib saat ajukan & konfirmasi staff"
    note for PerpanjanganPenitipan "Hanya saat CHECK_IN / SEDANG_DITITIPKAN; tanpa promo & tanpa biaya antar-jemput tambahan"
    note for PerpanjanganPenitipan "Boleh berkali-kali & paralel per booking; checkOutSebelum = checkOut booking saat ini"
    note for MonitoringPenitipan "Update memicu notifikasi ke pelanggan"
```

---

## 7. Booking — Pet Care

```mermaid
classDiagram
    direction TB

    class BookingPetCare {
        +UUID id
        +UUID pelangganId
        +UUID kucingId
        +UUID layananPetCareId
        +UUID kuotaPetCareId
        +Date tanggal
        +Time slotWaktu
        +StatusBookingPetCare status
        +String catatan
        +Decimal hargaLayanan
        +DibatalkanOleh dibatalkanOleh
        +UUID dibatalkanOlehStaffId
        +String alasanPembatalan
        +DateTime waktuDibatalkan
        +DateTime createdAt
        +ajukan()
        +batalkan()
    }

    class LayananPetCare {
        +UUID id
        +String nama
        +Decimal harga
        +Int estimasiDurasiMenit
        +StatusLayanan status
    }

    class KuotaPetCare {
        +UUID id
        +Date tanggal
        +Time slotWaktu
        +Int slotMaksimal
        +Int slotTerisi
        +StatusSlotPetCare statusSlot
    }

    class Pelanggan {
        +UUID id
    }

    class Kucing {
        +UUID id
    }

    class Staff {
        +UUID id
    }

    class StatusBookingPetCare {
        <<enumeration>>
        TERKONFIRMASI
        SEDANG_PROSES
        SELESAI
        DIBATALKAN
    }

    class DibatalkanOleh {
        <<enumeration>>
        PELANGGAN
        STAFF
    }

    class StatusSlotPetCare {
        <<enumeration>>
        TERSEDIA
        DITUTUP
    }

    Pelanggan "1" --> "*" BookingPetCare : mengajukan
    Kucing "1" --> "*" BookingPetCare : dilayani
    LayananPetCare "1" --> "*" BookingPetCare : dipilih
    KuotaPetCare "1" --> "*" BookingPetCare : menggunakan
    Staff "1" --> "*" BookingPetCare : batalkan
    BookingPetCare --> StatusBookingPetCare : status
    BookingPetCare --> DibatalkanOleh : dibatalkanOleh

    note for BookingPetCare "Booking-only; auto-confirm; bayar di loket; antar sendiri"
    note for KuotaPetCare "Slot global dokter; maks 1 booking per slot"
    note for LayananPetCare "Hanya layanan berstatus AKTIF yang bisa dipilih pelanggan"
```

---

## 8. Pembayaran & Transaksi

Metode pembayaran: transfer bank manual (grooming & penitipan); verifikasi bukti oleh staff wajib. **Pet care dikecualikan** — bayar di loket.

```mermaid
classDiagram
    direction TB

    class Transaksi {
        +UUID id
        +UUID pelangganId
        +UUID bookingId
        +UUID perpanjanganPenitipanId
        +JenisLayanan jenisLayanan
        +Decimal subtotalLayanan
        +Decimal potonganPromo
        +Decimal biayaAntarJemput
        +Decimal totalBayar
        +StatusPembayaran statusPembayaran
        +StatusRefund statusRefund
        +DateTime batasWaktuBayar
        +DateTime dibayarAt
        +DateTime createdAt
        +hitungTotal()
        +setBatasWaktuBayar()
        +batalkanOtomatis()
        +updateStatusRefund()
    }

    class BuktiTransfer {
        +UUID id
        +UUID transaksiId
        +String fileUrl
        +DateTime uploadedAt
        +StatusVerifikasi statusVerifikasi
        +UUID diverifikasiOlehStaffId
        +String catatanPenolakan
        +DateTime diverifikasiAt
        +upload()
        +setujui()
        +tolak()
    }

    class Invoice {
        +UUID id
        +UUID transaksiId
        +String nomorInvoice
        +DateTime issuedAt
        +String fileUrl
        +generate()
        +unduh()
    }

    class Pelanggan {
        +UUID id
    }

    class Staff {
        +UUID id
    }

    class BookingGrooming {
        +UUID id
    }

    class BookingPenitipan {
        +UUID id
    }

    class PerpanjanganPenitipan {
        +UUID id
    }

    class BookingPetCare {
        +UUID id
    }

    class JenisLayanan {
        <<enumeration>>
        GROOMING
        PENITIPAN
    }

    class StatusPembayaran {
        <<enumeration>>
        MENUNGGU_PEMBAYARAN
        MENUNGGU_VERIFIKASI
        LUNAS
        DIBATALKAN
        KEDALUWARSA
    }

    class StatusVerifikasi {
        <<enumeration>>
        MENUNGGU
        DISETUJUI
        DITOLAK
    }

    class StatusRefund {
        <<enumeration>>
        TIDAK_ADA
        PENDING_REFUND
        REFUNDED
    }

    Pelanggan "1" --> "*" Transaksi : memiliki
    Transaksi "1" --> "0..1" BuktiTransfer : bukti
    Transaksi "1" --> "0..1" Invoice : invoice
    Staff "1" --> "*" BuktiTransfer : verifikasi
    Transaksi --> JenisLayanan : jenisLayanan
    Transaksi --> StatusPembayaran : statusPembayaran
    Transaksi --> StatusRefund : statusRefund
    BuktiTransfer --> StatusVerifikasi : statusVerifikasi

    Transaksi ..> BookingGrooming : bookingId (polymorphic)
    Transaksi ..> BookingPenitipan : bookingId (polymorphic)
    Transaksi ..> PerpanjanganPenitipan : perpanjanganPenitipanId
    PerpanjanganPenitipan "1" --> "1" Transaksi : menghasilkan

    note for Transaksi "Lewat batasWaktuBayar → booking/perpanjangan dibatalkan & kuota dikembalikan"
    note for BuktiTransfer "Upload wajib; booking belum lunas sampai staff setujui"
    note for Transaksi "Refund diproses manual staff setelah permintaan via WhatsApp"
```

---

## 9. Layanan Antar-Jemput (Value Object / Service)

Logika antar-jemput hardcode; berlaku grooming & penitipan saja.

```mermaid
classDiagram
    direction LR

    class LayananAntarJemput {
        <<service>>
        +Decimal PETSHOP_LAT
        +Decimal PETSHOP_LNG
        +Int PICKUP_FREE_RADIUS_KM
        +Decimal PICKUP_EXTRA_FEE_PER_KM
        +hitungJarak(alamatPelanggan) Decimal
        +hitungBiaya(jarakKm) Decimal
        +validasiAlamatLengkap(pelanggan) Boolean
    }

    class Pelanggan {
        +String alamatLengkap
        +Decimal latitude
        +Decimal longitude
    }

    class BookingGrooming {
        +OpsiPengantaran opsiPengantaran
        +Decimal jarakKm
        +Decimal biayaAntarJemput
    }

    class BookingPenitipan {
        +OpsiPengantaran opsiPengantaran
        +Decimal jarakKm
        +Decimal biayaAntarJemput
    }

    LayananAntarJemput ..> Pelanggan : baca alamat
    LayananAntarJemput ..> BookingGrooming : hitung biaya
    LayananAntarJemput ..> BookingPenitipan : hitung biaya

    note for LayananAntarJemput "Gratis jika jarak ≤ 3 km; charge (jarak-3)×Rp5.000 jika > 3 km"
```

---

## 10. Notifikasi

```mermaid
classDiagram
    direction TB

    class Notifikasi {
        +UUID id
        +UUID penerimaId
        +TipePenerima tipePenerima
        +JenisNotifikasi jenis
        +String judul
        +String pesan
        +UUID referensiId
        +String referensiTipe
        +Boolean sudahDibaca
        +DateTime createdAt
        +kirim()
        +tandaiDibaca()
    }

    class Pelanggan {
        +UUID id
    }

    class Staff {
        +UUID id
    }

    class JenisNotifikasi {
        <<enumeration>>
        BOOKING_DISETUJUI
        BOOKING_DITOLAK
        JAM_GROOMING_DIUPDATE
        REMINDER_PEMBAYARAN
        PEMBAYARAN_JATUH_TEMPO
        MONITORING_PENITIPAN
        LAYANAN_SELESAI
        BOOKING_DIBATALKAN
        STATUS_REFUND
        PERPANJANGAN_PENITIPAN_MENUNGGU_KONFIRMASI
        PERPANJANGAN_PENITIPAN_DISETUJUI
        PERPANJANGAN_PENITIPAN_DITOLAK
        PERPANJANGAN_PENITIPAN_MENUNGGU_PEMBAYARAN
    }

    class TipePenerima {
        <<enumeration>>
        PELANGGAN
        STAFF
    }

    Pelanggan "1" --> "*" Notifikasi : menerima
    Notifikasi --> JenisNotifikasi : jenis
    Notifikasi --> TipePenerima : tipePenerima

    note for Notifikasi "Email opsional untuk trigger penting"
```

---

## 11. Relasi Antar Kelas (Ringkas)

```mermaid
classDiagram
    direction TB

    class Pelanggan
    class Staff
    class Owner
    class Kucing
    class RiwayatVaksin
    class JenisGrooming
    class KuotaGrooming
    class PaketPenitipan
    class KamarPenitipan
    class LayananPetCare
    class KuotaPetCare
    class BookingGrooming
    class BookingPenitipan
    class MonitoringPenitipan
    class PerpanjanganPenitipan
    class BookingPetCare
    class Transaksi
    class BuktiTransfer
    class Invoice
    class Notifikasi

    Owner --|> Staff

    Pelanggan "1" --> "*" Kucing
    Kucing "1" --> "*" RiwayatVaksin

    Pelanggan "1" --> "*" BookingGrooming
    Pelanggan "1" --> "*" BookingPenitipan
    Pelanggan "1" --> "*" BookingPetCare
    Pelanggan "1" --> "*" Transaksi
    Pelanggan "1" --> "*" Notifikasi

    Kucing "1" --> "*" BookingGrooming
    Kucing "1" --> "*" BookingPenitipan
    Kucing "1" --> "*" BookingPetCare

    JenisGrooming "1" --> "*" BookingGrooming
    KuotaGrooming "1" --> "*" BookingGrooming

    PaketPenitipan "1" --> "*" BookingPenitipan
    KamarPenitipan "1" --> "*" BookingPenitipan
    BookingPenitipan "1" --> "*" MonitoringPenitipan
    BookingPenitipan "1" --> "*" PerpanjanganPenitipan
    Staff "1" --> "*" MonitoringPenitipan
    Staff "1" --> "*" PerpanjanganPenitipan

    LayananPetCare "1" --> "*" BookingPetCare
    KuotaPetCare "1" --> "*" BookingPetCare

    BookingGrooming "1" --> "1" Transaksi
    BookingPenitipan "1" --> "*" Transaksi
    PerpanjanganPenitipan "1" --> "1" Transaksi

    Transaksi "1" --> "0..1" BuktiTransfer
    Transaksi "1" --> "0..1" Invoice
    Staff "1" --> "*" BuktiTransfer
```

---

## 12. Daftar Kelas & Atribut Utama

| Kelas | Atribut utama | Relasi |
|-------|---------------|--------|
| **Pelanggan** | nama, email, alamat, koordinat, foto profil | 1→* Kucing, Booking, Transaksi, Notifikasi |
| **Staff** | nama, email, status akun | verifikasi BuktiTransfer, input Monitoring, lihat Laporan |
| **Owner** | — | generalisasi Staff; kelola akun Staff |
| **Kucing** | nama, ras, berat, catatan kesehatan | milik Pelanggan; 1→* RiwayatVaksin |
| **RiwayatVaksin** | jenis vaksin, tanggal, sertifikat (opsional) | milik Kucing |
| **JenisGrooming** | nama, harga | master data grooming |
| **KuotaGrooming** | tanggal, slot maksimal/terisi | kuota harian |
| **PaketPenitipan** | nama, harga per hari | master data penitipan |
| **KamarPenitipan** | nama, kapasitas | 1→* KuotaPenitipan |
| **LayananPetCare** | nama, harga, durasi, status | soft delete; CRUD staff/owner |
| **BookingGrooming** | tanggal, jam, opsi pengantaran, status | → Transaksi |
| **BookingPenitipan** | check-in/out, promo, monitoring | 1→* Transaksi, 1→* Monitoring, 1→* PerpanjanganPenitipan |
| **PerpanjanganPenitipan** | checkOutSebelum/Baru, tambahHari, subtotal | hanya CHECK_IN/SEDANG_DITITIPKAN; → Transaksi |
| **BookingPetCare** | tanggal, slot waktu, status | booking-only; auto-confirm; bayar di loket; antar sendiri |
| **Transaksi** | subtotal, promo, antar-jemput, total, refund | grooming & penitipan saja; perpanjangan = transaksi tambahan |
| **BuktiTransfer** | file, status verifikasi, catatan penolakan | wajib upload pelanggan |
| **Invoice** | nomor, file | setelah pembayaran lunas |
| **Notifikasi** | jenis, judul, pesan, sudah dibaca | trigger in-app (+ email opsional) |
| **LayananAntarJemput** | konstanta jarak & biaya | service/helper, bukan entitas DB |

---

## Catatan

| Simbol | Arti |
|--------|------|
| `--\|>` | Generalisasi / inheritance (Owner → Staff) |
| `-->` | Asosiasi / relasi |
| `..>` | Dependensi (service, polymorphic reference) |
| `<<enumeration>>` | Tipe enum |
| `<<service>>` | Kelas layanan / helper (bukan entitas persisten) |

**Konstanta hardcode (bukan kelas DB):**

| Konstanta | Nilai | Dipakai di |
|-----------|-------|------------|
| `PICKUP_FREE_RADIUS_KM` | 3 | Grooming, Penitipan |
| `PICKUP_EXTRA_FEE_PER_KM` | 5000 | Grooming, Penitipan |
| `PETSHOP_LAT`, `PETSHOP_LNG` | koordinat petshop | Hitung jarak antar-jemput |
| `MIN_VACCINATION_COUNT` | 1 | Validasi booking pet hotel |
| `PROMO_MIN_DAYS` | 7 | Promo penitipan 10% |
| `PROMO_DISCOUNT_PERCENT` | 10 | Promo penitipan |
| `PETSHOP_WHATSAPP` | nomor WA | Link Hubungi Kami (refund manual) |
| Rekening bank petshop | bank, no rekening, atas nama | Halaman transfer manual |

**Aturan bisnis penting:**
- Setiap booking awal (grooming, penitipan) menghasilkan **1 Transaksi**; **pet care tidak ada transaksi** (bayar di loket).
- Setiap **perpanjangan penitipan** disetujui = transaksi tambahan terpisah.
- **Perpanjangan penitipan** hanya saat `CHECK_IN` / `SEDANG_DITITIPKAN`; tanpa promo & biaya antar-jemput tambahan; boleh berkali-kali & paralel per booking.
- **Antar-jemput** hanya pada grooming & penitipan; pet care **antar sendiri** fixed.
- **Pembatalan** sebelum konfirmasi: pelanggan batalkan langsung; setelah bayar: via WhatsApp + proses staff.
- **Kuota** grooming & penitipan dikembalikan otomatis saat booking dibatalkan atau kedaluwarsa; **pet care** slot dokter dikembalikan saat booking dibatalkan (pelanggan/staff).
