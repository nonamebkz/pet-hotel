# Activity Diagram — Aplikasi Petshop

Diagram aktivitas berdasarkan [idea.md](../../idea.md).

**Swimlane / partisi utama:**
- **Pelanggan** — booking, pembayaran, pembatalan
- **Staff / Owner** — konfirmasi, verifikasi, operasional
- **Sistem** — aturan otomatis (batas waktu, kuota)

> **Preview:** Gunakan ekstensi PlantUML di VS Code/Cursor, atau render di [plantuml.com](https://www.plantuml.com/plantuml/uml).
>
> File `.puml` terpisah: `activity-autentikasi-pelanggan.puml`, `activity-data-kucing.puml`, `activity-booking-grooming.puml`, `activity-booking-penitipan.puml`, `activity-booking-petcare.puml`, `activity-pembayaran.puml`, `activity-pembatalan-refund.puml`, `activity-operasional-staff.puml`, `activity-penitipan-staff.puml`, `activity-manajemen-staff-owner.puml`

---

## 1. Autentikasi Pelanggan

Alur daftar akun, login, lupa/reset password, ubah password, dan logout.

```plantuml
@startuml activity-autentikasi-pelanggan
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}
skinparam ArrowColor #333333

title Autentikasi Pelanggan

|Pelanggan|
start
:Membuka halaman autentikasi;

if (Sudah punya akun?) then (belum)
  :Isi form daftar akun\n(nama, email, password);
  if (Data valid?) then (tidak)
    :Tampilkan error validasi;
    stop
  else (ya)
    :Simpan akun pelanggan;
    :Redirect ke login / dashboard;
  endif
else (sudah)
  :Isi email & password;
  if (Kredensial benar?) then (tidak)
    :Tampilkan error login;
    stop
  else (ya)
    :Buat sesi login;
  endif
endif

if (Lupa password?) then (ya)
  :Masukkan email;
  :Kirim link reset password;
  :Buka link & set password baru;
  :Password diperbarui;
endif

:Masuk dashboard pelanggan;

if (Ubah password?) then (ya)
  :Isi password lama & password baru;
  if (Password lama benar?) then (tidak)
  else (ya)
    :Update password;
  endif
endif

if (Logout?) then (ya)
  :Hapus sesi;
  stop
endif

stop
@enduml
```

---

## 2. Kelola Data Kucing

Alur tambah, edit, hapus kucing milik pelanggan. Hapus ditolak jika ada booking aktif.

```plantuml
@startuml activity-data-kucing
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}

title Kelola Data Kucing (Pelanggan)

|Pelanggan|
start
:Buka menu "Kucing Saya";

if (Aksi?) then (tambah)
  :Isi form kucing\n(nama, jenis kelamin, ras,\numur, berat, foto opsional,\ncatatan kesehatan opsional);
  :Opsional: tambah riwayat vaksin\n(jenis, tanggal, sertifikat opsional);
  :Simpan kucing ke akun pelanggan;
elseif (edit) then
  :Pilih kucing milik akun;
  :Edit data kucing;
  :Simpan perubahan;
else (hapus)
  :Pilih kucing milik akun;
  if (Ada booking aktif pada kucing?) then (ya)
    :Tolak penghapusan;
    stop
  else (tidak)
    :Hapus data kucing;
  endif
endif

:Lihat daftar kucing terdaftar;

note right
  Data kucing dipakai ulang
  di grooming, penitipan, pet care.
  Riwayat vaksin opsional di master data;
  validasi vaksin hanya saat booking pet hotel.
end note

stop
@enduml
```

---

## 3. Booking Grooming (End-to-End)

Alur lengkap dari ajukan booking hingga layanan selesai, termasuk antar-jemput, konfirmasi jam, dan pembayaran.

```plantuml
@startuml activity-booking-grooming
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}

title Alur Booking Grooming (End-to-End)

|Pelanggan|
start
:Login & buka menu Grooming;

if (Minimal 1 kucing terdaftar?) then (tidak)
  :Arahkan ke "Kucing Saya";
  stop
endif

:Lihat kuota grooming per tanggal;
:Isi form booking grooming\n(pilih kucing, jenis grooming, tanggal);

if (Opsi pengantaran?) then (antar-jemput)
  if (Alamat profil lengkap?) then (tidak)
    :Wajib lengkapi alamat profil;
    stop
  else (ya)
    :Hitung jarak dari petshop ke alamat pelanggan;
    if (Jarak ≤ 3 km?) then (ya)
      :Biaya antar-jemput = Rp 0;
    else (tidak)
      :Biaya = (jarak - 3) × Rp 5.000/km;
    endif
    :Tampilkan estimasi jarak & biaya;
  endif
else (antar sendiri)
  :Biaya antar-jemput = Rp 0;
endif

:Tampilkan ringkasan biaya\n(harga grooming + antar-jemput);
:Submit booking;
:Status → **Menunggu Konfirmasi Jam**;

|Staff / Owner|
:Melihat daftar booking masuk;
if (Konfirmasi booking?) then (tolak)
  :Status → **Dibatalkan**;
  |Pelanggan|
  :Terima notifikasi booking ditolak;
  stop
else (konfirmasi)
  :Isi / update jam grooming;
  :Status → **Menunggu Pembayaran**;
  :Set batas waktu pembayaran;
  |Pelanggan|
  :Terima notifikasi jam grooming & tagihan;
endif

|Pelanggan|
:Transfer ke rekening petshop;
:Upload bukti transfer (wajib);
:Status → **Menunggu Verifikasi Bukti Transfer**;

|Staff / Owner|
:Review bukti transfer;
if (Bukti disetujui?) then (tolak)
  |Pelanggan|
  :Diminta upload ulang bukti;
  detach
else (setujui)
  :Status → **Terkonfirmasi**;
  :Pembayaran lunas;
  |Pelanggan|
  :Terima notifikasi pembayaran diverifikasi;
  :Unduh invoice / struk;
endif

|Staff / Owner|
:Update status layanan\n→ Sedang Proses;
:Update status layanan\n→ Selesai;
|Pelanggan|
:Terima notifikasi layanan selesai;
:Lihat riwayat grooming per kucing;

stop
@enduml
```

---

## 4. Booking Penitipan / Pet Hotel (End-to-End)

Alur penitipan dengan validasi vaksin, promo 10%, antar-jemput, monitoring harian, dan check-out.

```plantuml
@startuml activity-booking-penitipan
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}

title Alur Booking Penitipan / Pet Hotel (End-to-End)

|Pelanggan|
start
:Login & buka menu Penitipan;

if (Minimal 1 kucing terdaftar?) then (tidak)
  :Arahkan ke "Kucing Saya";
  stop
endif

:Pilih kucing dari "Kucing Saya";

if (Riwayat vaksin ≥ 1 entri\n(jenis & tanggal terisi)?) then (tidak)
  :Kucing tidak eligible / form ditolak;
  :Tampilkan pesan: lengkapi riwayat vaksin\ndi menu "Kucing Saya";
  stop
else (ya)
  :Tampilkan ringkasan riwayat vaksin (read-only);
endif

:Isi tanggal check-in & check-out;
:Input lama penitipan (hari);
:Catatan makan & kebiasaan kucing;

if (Durasi > 7 hari?) then (ya)
  if (Pelanggan belum pernah pakai promo?) then (ya)
    :Estimasi potongan promo 10%;
  else (sudah)
    :Tidak ada potongan promo;
  endif
else (≤ 7 hari)
  :Tidak ada potongan promo;
endif

if (Opsi pengantaran?) then (antar-jemput)
  if (Alamat profil lengkap?) then (tidak)
    :Wajib lengkapi alamat profil;
    stop
  else (ya)
    :Hitung jarak & biaya antar-jemput\n(gratis ≤ 3 km, charge > 3 km);
  endif
else (antar sendiri)
  :Biaya antar-jemput = Rp 0;
endif

:Tampilkan ringkasan biaya\n(subtotal, potongan promo, antar-jemput, total);
:Submit penitipan;
:Status → **Menunggu Konfirmasi**;

|Staff / Owner|
:Melihat daftar booking penitipan;
:Cek riwayat vaksin kucing (read-only);

if (Vaksin memenuhi syarat?) then (tidak)
  :Tolak booking;
  |Pelanggan|
  :Terima notifikasi penitipan ditolak;
  stop
else (ya)
  if (Konfirmasi penitipan?) then (tolak)
    :Status → **Dibatalkan**;
    stop
  else (konfirmasi)
    :Status → **Menunggu Pembayaran**;
    :Set batas waktu pembayaran;
  endif
endif

|Pelanggan|
:Transfer & upload bukti transfer;
:Status → **Menunggu Verifikasi Bukti Transfer**;

|Staff / Owner|
if (Bukti transfer disetujui?) then (tolak)
  |Pelanggan|
  :Upload ulang bukti transfer;
  detach
else (setujui)
  :Status → **Terkonfirmasi**;
endif

|Staff / Owner|
:Update status → **Check-in**;
:Update status → **Sedang Dititipkan**;

repeat
  :Input monitoring harian\n(foto, catatan makan, aktivitas);
  |Pelanggan|
  :Terima notifikasi update monitoring;
  |Staff / Owner|
repeat while (Masih dalam masa penitipan?) is (ya)
->tidak;

:Update status → **Check-out**;
|Pelanggan|
:Lihat riwayat penitipan per kucing;

stop
@enduml
```

---

## 5. Booking Pet Care (End-to-End)

Alur booking pet care — hanya antar sendiri, tanpa biaya antar-jemput.

```plantuml
@startuml activity-booking-petcare
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}

title Alur Booking Pet Care (End-to-End)

|Pelanggan|
start
:Login & buka menu Pet Care;

if (Minimal 1 kucing terdaftar?) then (tidak)
  :Arahkan ke "Kucing Saya";
  stop
endif

:Lihat daftar layanan pet care aktif\n(nama, deskripsi, harga, durasi);
:Isi form booking pet care\n(pilih kucing, layanan, tanggal & slot waktu);
note right
  Pengantaran: **antar sendiri** saja
  (tidak ada opsi antar-jemput)
end note
:Opsional: catatan khusus;
:Tampilkan ringkasan biaya\n(harga layanan saja);
:Submit booking;
:Status → **Menunggu Konfirmasi**;

|Staff / Owner|
:Melihat daftar booking masuk;
if (Konfirmasi booking?) then (tolak)
  :Status → **Dibatalkan**;
  |Pelanggan|
  :Terima notifikasi booking ditolak;
  stop
else (konfirmasi)
  :Status → **Menunggu Pembayaran**;
  :Set batas waktu pembayaran;
  |Pelanggan|
  :Terima notifikasi tagihan;
endif

|Pelanggan|
:Transfer ke rekening petshop;
:Upload bukti transfer (wajib);
:Status → **Menunggu Verifikasi Bukti Transfer**;

|Staff / Owner|
if (Bukti transfer disetujui?) then (tolak)
  |Pelanggan|
  :Upload ulang bukti transfer;
  detach
else (setujui)
  :Status → **Terkonfirmasi**;
  |Pelanggan|
  :Terima notifikasi pembayaran diverifikasi;
  :Unduh invoice / struk;
endif

|Staff / Owner|
:Update status → Sedang Proses;
:Update status → Selesai;
|Pelanggan|
:Terima notifikasi layanan selesai;
:Lihat riwayat booking pet care;

stop
@enduml
```

---

## 6. Pembayaran & Verifikasi Bukti Transfer (Global)

Alur pembayaran transfer manual yang berlaku untuk grooming, penitipan, dan pet care.

```plantuml
@startuml activity-pembayaran
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}

title Alur Pembayaran & Verifikasi Bukti Transfer (Global)

|Staff / Owner|
start
:Konfirmasi booking pelanggan;
:Set status → **Menunggu Pembayaran**;
:Set batas waktu pembayaran;

|Pelanggan|
:Buka daftar tagihan menunggu;
:Lihat rincian tagihan;
note right
  Subtotal layanan
  + potongan promo penitipan 10% (jika ada)
  + biaya antar-jemput (grooming/penitipan)
  = Total bayar
end note
:Lihat info rekening tujuan petshop\n(bank, no. rekening, atas nama);
:Transfer sesuai total tagihan;

if (Bukti transfer diupload?) then (tidak)
  :Form tidak bisa disubmit;
  stop
else (ya)
  :Submit bukti transfer;
  :Status → **Menunggu Verifikasi Bukti Transfer**;
endif

|Staff / Owner|
:Buka daftar pembayaran menunggu verifikasi;
:Review bukti transfer pelanggan;

if (Bukti disetujui?) then (tolak)
  :Tambah catatan penolakan;
  |Pelanggan|
  :Diminta upload ulang bukti;
  detach
else (setujui)
  :Status booking → **Terkonfirmasi**;
  :Pembayaran lunas;
  |Pelanggan|
  :Terima notifikasi verifikasi berhasil;
  :Unduh invoice / struk;
  stop
endif

|Pelanggan|
if (Lewat batas waktu pembayaran?) then (ya)
  |Sistem|
  :Booking otomatis dibatalkan;
  :Kuota / slot dikembalikan;
  |Pelanggan|
  :Terima notifikasi pembatalan otomatis;
  stop
endif

stop
@enduml
```

---

## 7. Pembatalan & Refund (Global)

Dua skenario: batalkan langsung (belum terkonfirmasi) dan refund manual via WhatsApp (sudah bayar).

```plantuml
@startuml activity-pembatalan-refund
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}

title Alur Pembatalan & Refund (Global)

|Pelanggan|
start
:Buka detail booking\n(grooming / penitipan / pet care);

if (Status booking?) then (belum terkonfirmasi\n/ belum bayar)
  :Klik batalkan booking;
  :Status → **Dibatalkan**;
  |Sistem|
  :Kuota / slot dikembalikan;
  |Pelanggan|
  :Terima notifikasi pembatalan;
  stop
else (sudah terkonfirmasi\n& sudah bayar)
  :Tidak bisa batalkan otomatis dari app;
  :Klik **Hubungi Kami**;
  :Redirect ke WhatsApp\n(wa.me/{PETSHOP_WHATSAPP});
  :Sampaikan ID booking & alasan pembatalan;
endif

|Staff / Owner|
:Terima permintaan via WhatsApp;
:Verifikasi permintaan pembatalan;
:Batalkan booking dari dashboard internal;
:Update status booking → **Dibatalkan**;
:Update status refund pada transaksi\n(pending refund / refunded);
:Proses refund manual (transfer balik);
|Sistem|
:Kuota / slot dikembalikan;

|Pelanggan|
:Terima notifikasi\nbooking dibatalkan & status refund;

stop
@enduml
```

---

## 8. Operasional Staff — Grooming & Pet Care

Ringkasan aktivitas operasional harian staff pada grooming dan pet care.

```plantuml
@startuml activity-operasional-staff
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}

title Operasional Staff — Grooming & Pet Care

|Staff / Owner|
start
:Login dashboard staff / owner;

:Buka ringkasan home\n(booking hari ini, pembayaran menunggu,\npenitipan aktif, pendapatan ringkas);

partition "Grooming" {
  :Kelola kuota grooming per hari;
  :Kelola jenis grooming & harga;
  :Lihat daftar booking grooming\n(filter tanggal, status);
  :Lihat detail kucing & opsi antar-jemput;
  if (Konfirmasi / tolak?) then (tolak)
    :Update status → Dibatalkan;
  else (konfirmasi)
  endif
  :Update jam grooming;
  :Verifikasi bukti transfer;
  :Update status → Sedang Proses → Selesai;
  :Lihat laporan booking & pendapatan grooming;
}

partition "Pet Care" {
  :Kelola master data layanan pet care (CRUD);
  :Kelola kuota / slot waktu per layanan;
  :Lihat daftar booking pet care;
  if (Konfirmasi / tolak?) then (tolak)
    :Update status → Dibatalkan;
  else (konfirmasi)
  endif
  :Verifikasi bukti transfer;
  :Update status layanan;
  :Lihat laporan booking & pendapatan pet care;
}

partition "Pelanggan & Kucing" {
  :Lihat daftar pelanggan terdaftar;
  :Lihat detail profil pelanggan;
  :Lihat daftar kucing milik pelanggan (read-only);
  :Lihat riwayat vaksin per kucing;
}

partition "Laporan & Transaksi" {
  :Verifikasi bukti transfer semua layanan;
  :Lihat riwayat transaksi;
  :Lihat laporan pendapatan (filter periode, layanan);
  :Opsional: export data;
}

stop
@enduml
```

---

## 9. Operasional Staff — Penitipan (Pet Hotel)

Aktivitas staff pada penitipan: konfirmasi, monitoring harian, check-in/check-out.

```plantuml
@startuml activity-penitipan-staff
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}

title Operasional Staff — Penitipan (Pet Hotel)

|Staff / Owner|
start
:Login dashboard staff / owner;
:Buka menu Penitipan;

:Kelola ketersediaan kamar / slot penitipan;
:Kelola paket & harga penitipan;
:Lihat daftar booking penitipan\n(filter tanggal, status);

repeat
  :Buka detail booking penitipan;
  :Lihat rincian biaya\n(subtotal, promo 10%, antar-jemput, total);
  :Cek riwayat vaksin kucing (read-only);

  if (Vaksin memenuhi syarat?) then (tidak)
    :Tolak booking;
  else (ya)
    if (Konfirmasi?) then (tolak)
      :Status → Dibatalkan;
    else (konfirmasi)
      :Status → Menunggu Pembayaran;
      :Verifikasi bukti transfer;
      if (Bukti disetujui?) then (ya)
        :Status → Terkonfirmasi;
        :Update status → Check-in;
        :Update status → Sedang Dititipkan;

        repeat
          :Input monitoring harian per kucing;
          note right
            Upload foto
            Catatan makan & kondisi
            Aktivitas harian
          end note
          :Kirim notifikasi ke pelanggan;
        repeat while (Masih dititipkan?) is (ya)
        ->tidak;

        :Update status → Check-out;
      endif
    endif
  endif
repeat while (Ada booking lain?) is (ya)
->tidak;

:Lihat laporan penitipan & pendapatan;

stop
@enduml
```

---

## 10. Manajemen Akun Staff (Owner)

Alur khusus owner untuk mengelola akun pegawai internal.

```plantuml
@startuml activity-manajemen-staff-owner
skinparam activity {
  BackgroundColor #FEFEFE
  BorderColor #333333
}

title Manajemen Akun Staff (Owner)

|Owner|
start
:Login dashboard owner;

:Buka menu Manajemen Akun Staff;
:Lihat daftar akun staff (pegawai);

if (Aksi?) then (tambah)
  :Isi form staff\n(nama, email/username,\npassword awal, status aktif/nonaktif);
  :Simpan akun staff;
elseif (edit) then
  :Pilih akun staff;
  :Edit data staff;
  :Simpan perubahan;
elseif (reset password) then
  :Pilih akun staff;
  :Set password baru;
elseif (aktifkan / nonaktifkan) then
  :Pilih akun staff;
  if (Nonaktifkan owner?) then (ya)
    :Ditolak — owner tidak bisa dinonaktifkan;
    stop
  else (tidak)
    :Update status akun staff;
  endif
endif

note right
  Staff **tidak bisa** mengelola
  akun staff lain.
  Owner memiliki semua akses
  operasional staff.
end note

stop
@enduml
```

---

## Ringkasan Diagram

| No | Diagram | File `.puml` | Aktor / Swimlane |
|----|---------|--------------|------------------|
| 1 | Autentikasi Pelanggan | `activity-autentikasi-pelanggan.puml` | Pelanggan |
| 2 | Kelola Data Kucing | `activity-data-kucing.puml` | Pelanggan |
| 3 | Booking Grooming | `activity-booking-grooming.puml` | Pelanggan, Staff/Owner |
| 4 | Booking Penitipan | `activity-booking-penitipan.puml` | Pelanggan, Staff/Owner |
| 5 | Booking Pet Care | `activity-booking-petcare.puml` | Pelanggan, Staff/Owner |
| 6 | Pembayaran & Verifikasi | `activity-pembayaran.puml` | Pelanggan, Staff/Owner, Sistem |
| 7 | Pembatalan & Refund | `activity-pembatalan-refund.puml` | Pelanggan, Staff/Owner, Sistem |
| 8 | Operasional Grooming & Pet Care | `activity-operasional-staff.puml` | Staff/Owner |
| 9 | Operasional Penitipan | `activity-penitipan-staff.puml` | Staff/Owner |
| 10 | Manajemen Akun Staff | `activity-manajemen-staff-owner.puml` | Owner |
