aplikasi petshop memiliki fitur

- penitipan kucing (pet hotel)
- grooming
- pet care
  - booking system

---

## Dokumentasi diagram UML

Lihat folder [diagrams/](./diagrams/README.md):

- [Use Case Diagram](./diagrams/usecase/usecase-diagram.md)
- [Activity Diagram](./diagrams/activity/activity-diagram.md)
- [Class Diagram](./diagrams/class/class-diagram.md)
- [ERD & Struktur Database](./diagrams/erd/erd-diagram.md) — skema SQL: [database/schema.sql](./database/schema.sql), LRS: [database/lrs.md](./database/lrs.md)

---

## peran pengguna (role)

| Role | Siapa | Akses |
|------|-------|-------|
| **Pelanggan** | Pemilik kucing / pengguna layanan | Booking, data kucing, pembayaran, riwayat |
| **Staff (pegawai)** | Karyawan petshop | Operasional harian: konfirmasi booking, verifikasi pembayaran, master data, monitoring |
| **Owner** | Pemilik bisnis petshop | Semua akses staff **+** manajemen akun staff |

- istilah **staff** = pegawai internal petshop (bukan pelanggan)
- istilah **owner** = pemilik bisnis, role tertinggi di sisi internal
- di dokumen ini, aksi operasional (konfirmasi booking, verifikasi transfer, dll.) dilakukan oleh **staff**; owner bisa melakukan hal yang sama **dan** mengelola akun staff

---

## dashboard pelanggan

### autentikasi

- daftar akun
- login akun
- logout
- lupa / reset password
- ubah password (setelah login)

### profil & akun

- lihat & edit profil pelanggan
  - alamat lengkap (wajib jika memilih opsi antar-jemput di booking grooming / penitipan)
  - alamat dipakai untuk hitung jarak ke petshop & biaya antar-jemput
- upload foto profil (opsional)

### data kucing (sumber: diinput pelanggan sendiri)

- kapan diisi: setelah daftar/login, lewat menu "Kucing Saya" (wajib minimal 1 kucing sebelum booking layanan)
- tambah kucing (form)
  - nama kucing
  - jenis kelamin
  - ras
  - umur / tanggal lahir
  - berat badan
  - foto kucing (opsional)
  - catatan kesehatan / alergi / kondisi khusus (opsional)
  - riwayat vaksin (opsional di master data — form array, bisa tambah lebih dari 1 entri)
    - jenis vaksin (contoh: FVRCP, rabies, dll.)
    - tanggal vaksin
    - upload sertifikat / bukti vaksin (opsional — **tidak wajib** di master data kucing)
  - catatan: riwayat vaksin boleh kosong saat tambah/edit kucing; syarat vaksin **hanya divalidasi** saat booking **pet hotel**
- edit data kucing (hanya kucing milik akun login)
- hapus data kucing (ditolak jika masih ada booking aktif pada kucing tersebut)
- lihat daftar kucing terdaftar
- relasi: setiap kucing terhubung ke akun pelanggan (user_id), bukan data global petshop
- catatan: data kucing dipakai ulang di fitur grooming, penitipan, dan pet care

### layanan antar-jemput (global, hardcode — berlaku grooming & penitipan saja)

- opsi di booking grooming & penitipan: **antar-jemput** atau **antar sendiri**
- pet care: **hanya antar sendiri** (pelanggan bawa kucing sendiri ke petshop, tanpa opsi antar-jemput)
- jika pilih antar-jemput → alamat profil pelanggan wajib lengkap
- perhitungan jarak: dari alamat petshop (koordinat hardcode) ke alamat pelanggan
- aturan biaya (hardcode, tidak bisa diubah dari dashboard):
  - jarak ≤ 3 km → biaya antar-jemput **gratis** (Rp 0)
  - jarak > 3 km → biaya tambahan = `(jarak - 3) × biaya per km`
  - konstanta hardcode:
    - `PICKUP_FREE_RADIUS_KM = 3`
    - `PICKUP_EXTRA_FEE_PER_KM = 5000` (contoh: Rp 5.000 per km di atas 3 km)
    - `PETSHOP_LAT`, `PETSHOP_LNG` (lokasi petshop)
- tampilkan estimasi jarak & biaya antar-jemput saat pelanggan pilih opsi antar-jemput (sebelum submit booking)
- biaya antar-jemput masuk rincian tagihan terpisah (bukan digabung ke harga layanan)
- jika pilih **antar sendiri** → biaya antar-jemput = Rp 0

### home (ringkasan dashboard)

- booking aktif & mendatang (grooming, penitipan, pet care)
- info promo penitipan aktif (jika pelanggan belum pernah pakai & memenuhi syarat)
- tagihan / pembayaran menunggu
- notifikasi terbaru
- shortcut: tambah kucing, ajukan grooming, ajukan penitipan

### grooming

- melihat kuota grooming tersedia per tanggal
- ajukan booking grooming (form)
  - pilih kucing (dropdown dari daftar "Kucing Saya" milik pelanggan yang login)
  - pilih jenis grooming (lengkap / jamur / kutu.)
  - pilih tanggal grooming (hanya tanggal dengan kuota tersisa)
  - pilih opsi antar-jemput atau antar sendiri
    - jika antar-jemput: tampilkan jarak & biaya tambahan (gratis ≤ 3 km, charge di atas 3 km)
  - catatan khusus (opsional)
  - ringkasan biaya (harga grooming + biaya antar-jemput jika ada)
- melihat status booking
  - menunggu konfirmasi jam
  - menunggu pembayaran
  - menunggu verifikasi bukti transfer (setelah upload bukti)
  - terkonfirmasi / sedang proses / selesai
  - dibatalkan
- melihat jam grooming setelah staff petshop mengisi
- riwayat grooming per kucing
- pembatalan grooming
  - sebelum terkonfirmasi: pelanggan bisa batalkan langsung dari app
  - setelah terkonfirmasi & sudah bayar: lihat aturan **pembatalan & refund (global)**

### penitipan kucing (pet hotel)

- syarat vaksin pet hotel (hardcode di sistem, tidak bisa diubah dari dashboard)
  - wajib saat booking pet hotel: minimal **1 riwayat vaksin** dengan **jenis vaksin** & **tanggal vaksin** terisi
  - upload sertifikat / bukti vaksin **tetap opsional** (tidak wajib upload meskipun untuk pet hotel)
  - konstanta hardcode: `MIN_VACCINATION_COUNT = 1`
  - validasi saat pilih kucing di form penitipan:
    - jika riwayat vaksin kosong / < 1 entri lengkap → kucing tidak bisa dipilih / form ditolak
    - tampilkan pesan: lengkapi riwayat vaksin (jenis & tanggal) di menu "Kucing Saya" terlebih dahulu
  - validasi saat konfirmasi staff:
    - jika data vaksin tidak lengkap → booking **ditolak**
  - dropdown kucing hanya menampilkan kucing yang memenuhi syarat vaksin (atau tampilkan semua dengan status eligible / belum eligible)
- promo penitipan (hardcode di sistem, tidak bisa diubah dari dashboard)
  - syarat: durasi penitipan > 7 hari (selisih tanggal check-in & check-out)
  - potongan: 10% dari total tagihan penitipan
  - berlaku 1 kali per akun pelanggan (berlaku setiap kali ada penitipan)
  - tidak kumulatif: jika durasi 14 hari atau lebih, tetap hanya potongan 10% (bukan 20%)
  - konstanta hardcode:
    - `PROMO_MIN_DAYS = 7`
    - `PROMO_DISCOUNT_PERCENT = 10`
  - saat ajukan penitipan: tampilkan estimasi harga normal & harga setelah promo (jika eligible)
- ajukan penitipan (form)
  - pilih kucing (dari "Kucing Saya" — hanya kucing dengan riwayat vaksin ≥ 1)
  - tampilkan ringkasan riwayat vaksin kucing terpilih (read-only)
  - tanggal check-in & check-out
  - input lama penitipan dalam hari
  - catatan makan & kebiasaan kucing
  - opsi antar-jemput atau antar sendiri
    - jika antar-jemput: tampilkan jarak & biaya tambahan (gratis ≤ 3 km, charge di atas 3 km)
  - ringkasan biaya (subtotal penitipan, potongan promo jika ada, biaya antar-jemput jika ada, total akhir)
- melihat status penitipan
  - menunggu konfirmasi
  - menunggu pembayaran
  - menunggu verifikasi bukti transfer (setelah upload bukti)
  - check-in / sedang dititipkan / check-out
  - dibatalkan
- monitoring harian (dari update staff petshop)
  - foto kucing
  - catatan makan & kondisi
  - aktivitas harian
- pembatalan penitipan
  - sebelum terkonfirmasi: pelanggan bisa batalkan langsung dari app
  - setelah terkonfirmasi & sudah bayar: lihat aturan **pembatalan & refund (global)**
- riwayat penitipan per kucing

### pet care (booking system)

- master data layanan (sumber: diinput staff/owner, dipakai berulang di setiap booking)
  - daftar layanan pet care diambil dari master data yang aktif
  - pelanggan hanya bisa pilih layanan yang statusnya **aktif**
- lihat daftar layanan pet care yang tersedia (nama, deskripsi, harga, estimasi durasi)
- ajukan booking pet care (form)
  - pilih kucing
  - pilih layanan (dropdown dari master data layanan pet care)
  - pilih tanggal & slot waktu (jika tersedia)
  - opsi pengantaran: **antar sendiri** saja (fixed, tidak ada antar-jemput)
  - catatan khusus (opsional)
  - ringkasan biaya (harga layanan saja, tanpa biaya antar-jemput)
- melihat status booking pet care
  - menunggu konfirmasi
  - menunggu pembayaran
  - menunggu verifikasi bukti transfer (setelah upload bukti)
  - terkonfirmasi / sedang proses / selesai
  - dibatalkan
- pembatalan booking pet care
  - sebelum terkonfirmasi: pelanggan bisa batalkan langsung dari app
  - setelah terkonfirmasi & sudah bayar: lihat aturan **pembatalan & refund (global)**
- riwayat booking pet care

### pembayaran & transaksi

- daftar tagihan menunggu (grooming, penitipan, pet care)
- rincian tagihan:
  - subtotal layanan
  - potongan promo penitipan 10% (jika applicable)
  - biaya antar-jemput (hanya grooming & penitipan; Rp 0 jika ≤ 3 km atau antar sendiri; charge jika > 3 km)
  - total bayar
- metode pembayaran: **transfer bank manual saja** (tidak ada payment gateway)
  - tampilkan rekening tujuan petshop (hardcode: bank, no. rekening, atas nama)
  - pelanggan transfer sesuai total tagihan
  - **wajib upload bukti transfer** (form tidak bisa disubmit tanpa bukti)
  - setelah upload → status **menunggu verifikasi staff**
  - booking dianggap belum bayar sampai staff menyetujui bukti transfer
- batas waktu pembayaran setelah booking disetujui staff
- jika lewat batas waktu → booking otomatis dibatalkan & kuota dikembalikan
- unduh invoice / struk setelah pembayaran diverifikasi staff
- riwayat transaksi (semua layanan)

### pembatalan & refund (global, berlaku grooming, penitipan, pet care)

- dua skenario pembatalan:
  - **belum terkonfirmasi / belum bayar** → pelanggan batalkan langsung dari app
  - **sudah terkonfirmasi & sudah bayar (perlu refund)** → pelanggan **tidak bisa** batalkan otomatis dari app
- fitur **Hubungi Kami** (hardcode)
  - tombol/link ke WhatsApp: `https://wa.me/{PETSHOP_WHATSAPP}` (konstanta hardcode)
  - tampilkan di halaman detail booking & menu bantuan
  - pelanggan hubungi via WhatsApp, sampaikan ID booking & alasan pembatalan
  - refund diproses manual oleh staff/owner (transfer balik) setelah verifikasi
- alur setelah pelanggan hubungi staff:
  1. pelanggan klik **Hubungi Kami** → redirect ke link `wa.me`
  2. staff verifikasi permintaan di WhatsApp
  3. staff batalkan booking dari dashboard internal
  4. staff update status refund pada transaksi (opsional: pending refund / refunded)
  5. notifikasi ke pelanggan: booking dibatalkan & refund sedang/sudah diproses

### notifikasi

- pusat notifikasi in-app (daftar semua pemberitahuan)
- trigger notifikasi:
  - jam grooming diupdate staff
  - booking disetujui / ditolak
  - reminder pembayaran & pembayaran jatuh tempo
  - update monitoring penitipan
  - layanan selesai
  - booking dibatalkan & status refund (setelah staff proses)
- notifikasi email (opsional, untuk trigger penting)

---

## dashboard owner (pemilik bisnis petshop)

### autentikasi

- login owner (terpisah dari akun pelanggan & staff)
- logout
- ubah password

### manajemen akun staff (khusus owner)

- lihat daftar akun staff (pegawai)
- tambah akun staff (form)
  - nama
  - email / username
  - password awal
  - status (aktif / nonaktif)
- edit data akun staff
- reset password staff
- nonaktifkan / aktifkan kembali akun staff
- owner **tidak bisa** dihapus / dinonaktifkan oleh staff

### akses operasional

- owner memiliki **semua fitur dashboard staff** (grooming, penitipan, pet care, laporan, dll.)
- section di bawah **dashboard staff** berlaku juga untuk owner

---

## dashboard staff (pegawai petshop)

### autentikasi

- login staff (terpisah dari akun pelanggan)
- logout
- ubah password
- staff **tidak bisa** mengelola akun staff lain (khusus owner)

### home (ringkasan)

- jumlah booking hari ini (grooming, penitipan, pet care)
- pembayaran menunggu verifikasi
- penitipan aktif
- pendapatan ringkas (harian / mingguan)

### grooming

- melihat detail kucing pada setiap booking (data dari profil pelanggan, read-only)
- management kuota grooming per hari (slot maksimal per tanggal)
- management jenis grooming & harga
- melihat daftar booking masuk (filter: tanggal, status)
- konfirmasi atau tolak booking
- lihat opsi antar-jemput & rincian biaya tambahan jarak (> 3 km) pada detail booking
- mengupdate jam grooming pada booking yang diajukan pelanggan
- setelah jam diupdate → notifikasi ke pelanggan untuk melakukan pembayaran (batas waktu X jam/hari)
- verifikasi bukti transfer (wajib — setujui / tolak)
  - lihat bukti transfer yang diupload pelanggan
  - jika disetujui → status booking terkonfirmasi & pembayaran lunas
  - jika ditolak → pelanggan diminta upload ulang bukti transfer
- update status layanan (sedang proses → selesai)
- melihat laporan booking & pendapatan grooming

### penitipan kucing (pet hotel)

- promo penitipan (read-only, hardcode)
  - lihat apakah booking memakai promo 10% (durasi ≥ 7 hari & pelanggan belum pernah pakai promo)
  - lihat rincian: subtotal, potongan promo, biaya antar-jemput, total akhir pada detail booking
- melihat daftar booking penitipan (filter: tanggal, status)
- konfirmasi atau tolak penitipan
  - cek riwayat vaksin kucing pada detail booking (read-only)
  - tolak booking jika riwayat vaksin kosong / tidak memenuhi minimal 1 entri (jenis & tanggal wajib terisi)
  - upload sertifikat vaksin **bukan** syarat wajib penolakan
- lihat opsi antar-jemput & jarak/biaya tambahan pada detail booking
- management ketersediaan kamar / slot penitipan
- management paket & harga penitipan
- update status (check-in → sedang dititipkan → check-out)
- input monitoring harian per kucing
  - upload foto
  - catatan makan & kondisi
  - aktivitas harian
- notifikasi ke pelanggan saat monitoring diupdate
- verifikasi bukti transfer (wajib — setujui / tolak)
- melihat laporan penitipan & pendapatan

### pet care

- master data layanan pet care (CRUD — input manual staff/owner, dipakai berulang)
  - tambah layanan (form)
    - nama layanan
    - deskripsi
    - harga
    - estimasi durasi (menit / jam)
    - status (aktif / nonaktif)
  - edit layanan
  - nonaktifkan / hapus layanan (soft delete — layanan lama di riwayat booking tetap tersimpan)
  - layanan aktif otomatis muncul di form booking pelanggan
- management kuota / slot waktu per layanan
- melihat daftar booking masuk
- konfirmasi atau tolak booking
- update status layanan
- verifikasi bukti transfer (wajib — setujui / tolak)
- melihat laporan booking & pendapatan pet care

### manajemen pelanggan & kucing

- lihat daftar pelanggan terdaftar
- lihat detail profil pelanggan
- lihat daftar kucing milik pelanggan (read-only)
  - lihat riwayat vaksin per kucing (untuk verifikasi pet hotel — cek jenis & tanggal, bukan sertifikat)

### laporan & transaksi

- daftar pembayaran menunggu verifikasi bukti transfer (semua layanan)
- verifikasi bukti transfer: setujui / tolak + catatan (jika ditolak)
- riwayat transaksi semua layanan
- laporan pendapatan (filter: periode, layanan)
- export data (opsional, untuk keperluan skripsi / operasional)

### pembatalan & refund

- batalkan booking grooming / penitipan / pet care dari dashboard internal
  - khusus booking **sudah terkonfirmasi & sudah bayar** (permintaan via WhatsApp pelanggan)
- update status booking → dibatalkan
- update status refund pada transaksi (pending refund / refunded)
- kuota / slot layanan dikembalikan setelah pembatalan
- notifikasi otomatis ke pelanggan setelah staff memproses pembatalan

