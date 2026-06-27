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
- [Sequence Diagram](./diagrams/sequence/sequence-diagram.md)

---

## peran pengguna (role)

| Role | Siapa | Akses |
|------|-------|-------|
| **Pelanggan** | Pemilik kucing / pengguna layanan | Booking, data kucing, pembayaran, riwayat |
| **Staff (pegawai)** | Karyawan petshop | Operasional harian: konfirmasi booking, verifikasi pembayaran, master data, monitoring |
| **Owner** | Pemilik bisnis petshop | Semua akses staff **+** manajemen akun staff |

- istilah **staff** = pegawai internal petshop (bukan pelanggan)
- istilah **owner** = pemilik bisnis, role tertinggi di sisi internal
- istilah **admin / dashboard admin** = sisi internal Staff & Owner (bukan akun pelanggan)
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
- tagihan / pembayaran menunggu (grooming, penitipan, perpanjangan penitipan — **bukan pet care**)
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
- perpanjangan penitipan (setelah booking terkonfirmasi & sudah bayar)
  - hanya saat status **check-in** atau **sedang dititipkan** (belum check-out)
  - tombol "Perpanjang Penitipan" di halaman detail booking
  - form: pilih tanggal check-out baru (wajib > check-out saat ini)
  - tampilkan estimasi hari tambahan & biaya (`harga paket per hari × tambah hari`)
  - **tidak ada promo** pada hari tambahan; **tidak ada biaya antar-jemput tambahan**
  - pelanggan **boleh mengajukan perpanjangan berkali-kali** selama masih dalam masa penitipan (setiap ajuan = record terpisah)
  - `check_out` acuan ajuan baru = check-out booking **saat ini** (termasuk jika sudah pernah diperpanjang)
  - beberapa ajuan dapat berjalan paralel (masing-masing alur konfirmasi & pembayaran sendiri)
  - status permintaan perpanjangan:
    - menunggu konfirmasi staff
    - menunggu pembayaran
    - menunggu verifikasi bukti transfer
    - disetujui / ditolak / dibatalkan (lewat batas waktu bayar)
  - alur: staff konfirmasi ketersediaan kamar → pelanggan transfer & upload bukti → staff verifikasi bukti → check-out & lama penitipan diperbarui
  - riwayat perpanjangan per booking (semua ajuan, termasuk ditolak/dibatalkan)
- riwayat penitipan per kucing

### pet care (booking system)

- **booking-only**: tidak ada alur pembayaran di app (tidak ada tagihan, upload bukti transfer, atau invoice)
- **pembayaran di loket** saat kunjungan (tunai/transfer langsung di petshop); harga di app hanya **estimasi**
- **1 petshop = 1 dokter** → maksimal **1 booking aktif per slot waktu** (jadwal slot global, bukan per layanan)
- master data layanan (sumber: diinput staff/owner, dipakai berulang di setiap booking)
  - daftar layanan pet care diambil dari master data yang aktif
  - pelanggan hanya bisa pilih layanan yang statusnya **aktif**
- lihat daftar layanan pet care yang tersedia (nama, deskripsi, harga estimasi, estimasi durasi)
- lihat slot waktu tersedia per tanggal (dari jadwal slot dokter yang diset admin)
- ajukan booking pet care (form)
  - pilih tanggal
  - pilih slot waktu tersedia (jika kosong)
  - pilih layanan (dropdown dari master data layanan pet care)
  - pilih kucing (dropdown dari "Kucing Saya")
  - opsi pengantaran: **antar sendiri** saja (fixed, tidak ada antar-jemput)
  - catatan khusus (opsional)
  - ringkasan estimasi biaya (harga layanan saja, tanpa biaya antar-jemput)
  - catatan: "Pembayaran dilakukan langsung di petshop saat kunjungan"
  - submit → **otomatis terkonfirmasi** jika slot masih kosong (first-come-first-served)
- melihat status booking pet care
  - terkonfirmasi / sedang proses / selesai
  - dibatalkan
- pembatalan booking pet care
  - pelanggan bisa **batalkan kapan saja** langsung dari app (termasuk hari-H)
  - slot waktu langsung dikembalikan & bisa dibooking ulang pelanggan lain
  - **tidak ada refund** (karena tidak ada prepayment di app)
- riwayat booking pet care

#### edge cases pet care

| Situasi | Aturan |
|---------|--------|
| Dua pelanggan book slot sama bersamaan | Transaksi DB + cek slot belum penuh; tolak jika slot sudah terisi |
| Pelanggan batalkan hari-H | Slot langsung kosong; pelanggan lain bisa book slot tersebut |
| Staff batalkan booking aktif | Slot dikembalikan; notifikasi ke pelanggan |
| Slot di masa lalu (hari ini, jam sudah lewat) | Tidak ditampilkan / ditolak saat submit |
| Kucing yang sama book 2 slot berbeda di hari sama | Diizinkan |
| Kucing book slot yang sudah terisi | Ditolak (slot sudah penuh) |
| Admin hapus slot yang masih terbooking | Ditolak — admin harus batalkan booking dulu |
| Layanan dinonaktifkan setelah booking | Booking tetap jalan; snapshot `harga_layanan` tidak berubah |
| Durasi layanan > interval slot | Tanggung jawab admin saat set jadwal (jarak antar slot ≥ durasi layanan terpanjang) |
| Tidak ada slot tersedia di tanggal dipilih | Tampilkan pesan "tidak ada jadwal"; arahkan pilih tanggal lain |
| Booking `sedang proses` dibatalkan | Diizinkan; slot dikembalikan |

### pembayaran & transaksi

- daftar tagihan menunggu (grooming, penitipan, **perpanjangan penitipan** — **pet care dikecualikan**)
- rincian tagihan:
  - subtotal layanan
  - potongan promo penitipan 10% (jika applicable — **hanya booking awal**, bukan perpanjangan)
  - biaya antar-jemput (hanya grooming & penitipan booking awal; Rp 0 jika ≤ 3 km atau antar sendiri; charge jika > 3 km)
  - total bayar
- metode pembayaran: **transfer bank manual saja** (tidak ada payment gateway)
  - tampilkan rekening tujuan petshop (hardcode: bank, no. rekening, atas nama)
  - pelanggan transfer sesuai total tagihan
  - **wajib upload bukti transfer** (form tidak bisa disubmit tanpa bukti)
  - setelah upload → status **menunggu verifikasi staff**
  - booking dianggap belum bayar sampai staff menyetujui bukti transfer
- batas waktu pembayaran setelah booking disetujui staff (berlaku grooming & penitipan; juga tagihan perpanjangan setelah staff konfirmasi perpanjangan — **tidak berlaku pet care**)
- jika lewat batas waktu → booking / permintaan perpanjangan otomatis dibatalkan & kuota dikembalikan
- unduh invoice / struk setelah pembayaran diverifikasi staff
- riwayat transaksi (grooming, penitipan, perpanjangan penitipan)

### pembatalan & refund (global, berlaku grooming & penitipan saja)

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
- **pet care dikecualikan**: pembatalan langsung di app tanpa refund (tidak ada prepayment)

### notifikasi

- pusat notifikasi in-app (daftar semua pemberitahuan)
- trigger notifikasi:
  - jam grooming diupdate staff
  - booking disetujui / ditolak (grooming & penitipan)
  - booking pet care terkonfirmasi (otomatis saat submit)
  - reminder pembayaran & pembayaran jatuh tempo (grooming, penitipan, perpanjangan — **bukan pet care**)
  - update monitoring penitipan
  - permintaan perpanjangan penitipan masuk (ke staff)
  - perpanjangan penitipan disetujui / ditolak / menunggu pembayaran / pembayaran diverifikasi
  - layanan selesai
  - booking dibatalkan (pet care: langsung dari app; grooming/penitipan: & status refund setelah staff proses)
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
- pembayaran menunggu verifikasi (grooming, penitipan, perpanjangan — **bukan pet care**)
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
- verifikasi bukti transfer booking awal (wajib — setujui / tolak)
- perpanjangan penitipan
  - lihat daftar permintaan perpanjangan masuk (filter status)
  - konfirmasi atau tolak permintaan perpanjangan (cek ketersediaan kamar / kuota hari tambahan)
  - setelah konfirmasi → tagihan perpanjangan terpisah menunggu pembayaran pelanggan
  - verifikasi bukti transfer perpanjangan (reuse alur verifikasi global)
  - setelah pembayaran diverifikasi → check-out & lama penitipan booking diperbarui otomatis
  - tidak perlu re-validasi vaksin (booking awal sudah lolos verifikasi)

### pet care

- master data layanan pet care (CRUD — input manual staff/owner, dipakai berulang)
  - tambah layanan (form)
    - nama layanan
    - deskripsi
    - harga (estimasi — ditampilkan ke pelanggan)
    - estimasi durasi (menit / jam)
    - status (aktif / nonaktif)
  - edit layanan
  - nonaktifkan / hapus layanan (soft delete — layanan lama di riwayat booking tetap tersimpan)
  - layanan aktif otomatis muncul di form booking pelanggan
- **kelola jadwal slot dokter** (CRUD blok waktu per tanggal — global, tidak terikat layanan spesifik)
  - tambah slot waktu per hari (contoh: 09:00, 10:00, 11:00)
  - sistem cegah jadwal bentrok (unique per tanggal + jam)
  - maksimal 1 booking per slot (1 dokter)
  - admin bisa tutup slot tanpa hapus (status `DITUTUP`)
  - hapus slot hanya jika belum ada booking aktif
  - admin bertanggung jawab jarak antar slot ≥ durasi layanan terpanjang
- melihat daftar booking masuk (filter: tanggal, status)
- melihat detail kucing & layanan pada setiap booking (read-only)
- **batalkan booking pet care** dari dashboard (alasan opsional); slot waktu dikembalikan
- update status layanan (terkonfirmasi → sedang proses → selesai)

### manajemen pelanggan & kucing

- lihat daftar pelanggan terdaftar
- lihat detail profil pelanggan
- lihat daftar kucing milik pelanggan (read-only)
  - lihat riwayat vaksin per kucing (untuk verifikasi pet hotel — cek jenis & tanggal, bukan sertifikat)

### laporan

> Menu **Laporan** hanya tersedia di dashboard admin (Staff/Owner). Pelanggan tidak memiliki akses.

Struktur menu:

```
Laporan
├── Laporan Data Grooming
├── Laporan Data Pet Hotel
└── Laporan Data Booking Pet Care
```

Halaman indeks menampilkan ringkasan kartu (total booking per layanan untuk periode terpilih).

#### laporan data grooming

- filter: periode (tanggal mulai–akhir), status booking (opsional)
- metrik: jumlah booking, total pendapatan, rincian per jenis grooming, opsi antar-jemput
- export CSV/PDF (opsional, untuk keperluan skripsi / operasional)

#### laporan data pet hotel

- filter: periode (tanggal mulai–akhir), status booking (opsional)
- metrik: jumlah booking penitipan, total hari dititipkan, pendapatan (booking awal + perpanjangan), breakdown promo & antar-jemput
- export CSV/PDF (opsional)

#### laporan data booking pet care

- filter: periode (tanggal mulai–akhir), status booking (opsional), layanan (opsional)
- metrik: jumlah booking per layanan & per slot; **tanpa pendapatan** (bayar di loket)
- export CSV/PDF (opsional)

### transaksi

- daftar pembayaran menunggu verifikasi bukti transfer (grooming, penitipan, perpanjangan — **bukan pet care**)
- verifikasi bukti transfer: setujui / tolak + catatan (jika ditolak)
- riwayat transaksi semua layanan

### pembatalan & refund

- batalkan booking grooming / penitipan dari dashboard internal
  - khusus booking **sudah terkonfirmasi & sudah bayar** (permintaan via WhatsApp pelanggan)
- update status booking → dibatalkan
- update status refund pada transaksi (pending refund / refunded)
- kuota / slot layanan dikembalikan setelah pembatalan
- notifikasi otomatis ke pelanggan setelah staff memproses pembatalan
- **pet care**: staff batalkan langsung dari dashboard (tanpa alur WhatsApp/refund); slot dokter dikembalikan

