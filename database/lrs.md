# LRS (Logical Record Structure) — Aplikasi Petshop

Struktur record logis database berdasarkan [schema.sql](./schema.sql) dan [ERD](../diagrams/erd/erd-diagram.md).

**DBMS:** PostgreSQL 14+  
**Total tabel:** 20  
**Total tipe enum:** 17

---

## Keterangan simbol

| Simbol | Arti |
|--------|------|
| **PK** | Primary Key |
| **FK** | Foreign Key |
| **UQ** | Unique |
| **DF** | Default value |
| **CK** | Check constraint |

| Null | Arti |
|------|------|
| Tidak | Kolom wajib diisi (`NOT NULL`) |
| Ya | Kolom boleh kosong (`NULL`) |

---

## Daftar isi tabel

| No | Nama Tabel | Modul |
|----|------------|-------|
| 1 | pelanggan | Akun & Pengguna |
| 2 | staff | Akun & Pengguna |
| 3 | kucing | Data Kucing |
| 4 | riwayat_vaksin | Data Kucing |
| 5 | jenis_grooming | Master Data Layanan |
| 6 | kuota_grooming | Master Data Layanan |
| 7 | paket_penitipan | Master Data Layanan |
| 8 | kamar_penitipan | Master Data Layanan |
| 9 | kuota_penitipan | Master Data Layanan |
| 10 | layanan_pet_care | Master Data Layanan |
| 11 | kuota_pet_care | Master Data Layanan |
| 12 | booking_grooming | Booking |
| 13 | booking_penitipan | Booking |
| 14 | monitoring_penitipan | Booking |
| 15 | perpanjangan_penitipan | Booking |
| 16 | booking_pet_care | Booking |
| 17 | transaksi | Pembayaran |
| 18 | bukti_transfer | Pembayaran |
| 19 | invoice | Pembayaran |
| 20 | notifikasi | Notifikasi |

---

## Tipe Data Enum

### staff_role

| Nilai | Deskripsi |
|-------|-----------|
| STAFF | Pegawai operasional petshop |
| OWNER | Pemilik bisnis petshop (akses penuh + kelola staff) |

### status_akun

| Nilai | Deskripsi |
|-------|-----------|
| AKTIF | Akun staff dapat login |
| NONAKTIF | Akun staff dinonaktifkan, tidak dapat login |

### jenis_kelamin

| Nilai | Deskripsi |
|-------|-----------|
| JANTAN | Kucing jantan |
| BETINA | Kucing betina |

### opsi_pengantaran

| Nilai | Deskripsi |
|-------|-----------|
| ANTAR_JEMPUT | Petshop menjemput/mengantar kucing |
| ANTAR_SENDIRI | Pelanggan bawa kucing sendiri |

### status_layanan

| Nilai | Deskripsi |
|-------|-----------|
| AKTIF | Layanan pet care tersedia untuk booking |
| NONAKTIF | Layanan pet care tidak ditampilkan ke pelanggan |

### status_booking_grooming

| Nilai | Deskripsi |
|-------|-----------|
| MENUNGGU_KONFIRMASI | Booking baru, menunggu persetujuan staff |
| MENUNGGU_PEMBAYARAN | Disetujui, menunggu pembayaran pelanggan |
| MENUNGGU_VERIFIKASI_BUKTI | Bukti transfer diupload, menunggu verifikasi staff |
| TERKONFIRMASI | Pembayaran lunas, booking terkonfirmasi |
| SEDANG_PROSES | Grooming sedang berlangsung |
| SELESAI | Grooming selesai |
| DIBATALKAN | Booking dibatalkan |

### status_penitipan

| Nilai | Deskripsi |
|-------|-----------|
| MENUNGGU_KONFIRMASI | Penitipan baru, menunggu persetujuan staff |
| MENUNGGU_PEMBAYARAN | Disetujui, menunggu pembayaran |
| MENUNGGU_VERIFIKASI_BUKTI | Bukti transfer diupload, menunggu verifikasi |
| CHECK_IN | Kucing sudah check-in |
| SEDANG_DITITIPKAN | Kucing sedang dititipkan |
| CHECK_OUT | Kucing sudah check-out |
| DIBATALKAN | Penitipan dibatalkan |

### status_perpanjangan_penitipan

| Nilai | Deskripsi |
|-------|-----------|
| MENUNGGU_KONFIRMASI | Permintaan perpanjangan baru, menunggu konfirmasi staff |
| MENUNGGU_PEMBAYARAN | Staff konfirmasi, menunggu pembayaran pelanggan |
| MENUNGGU_VERIFIKASI_BUKTI | Bukti transfer diupload, menunggu verifikasi staff |
| DISETUJUI | Pembayaran diverifikasi, check-out booking diperbarui |
| DITOLAK | Permintaan perpanjangan ditolak staff |
| DIBATALKAN | Permintaan dibatalkan (lewat batas waktu bayar) |

### status_booking_pet_care

| Nilai | Deskripsi |
|-------|-----------|
| TERKONFIRMASI | Booking otomatis terkonfirmasi saat slot kosong |
| SEDANG_PROSES | Layanan pet care berlangsung |
| SELESAI | Layanan selesai |
| DIBATALKAN | Booking dibatalkan (pelanggan atau staff) |

### status_slot_pet_care

| Nilai | Deskripsi |
|-------|-----------|
| TERSEDIA | Slot dokter tersedia untuk booking |
| DITUTUP | Slot ditutup admin (tidak bisa dibooking) |

### dibatalkan_oleh

| Nilai | Deskripsi |
|-------|-----------|
| PELANGGAN | Booking dibatalkan oleh pelanggan |
| STAFF | Booking dibatalkan oleh staff/owner |

### jenis_layanan

| Nilai | Deskripsi |
|-------|-----------|
| GROOMING | Transaksi dari booking grooming |
| PENITIPAN | Transaksi dari booking penitipan |

> Pet care **tidak** memiliki transaksi di app (pembayaran di loket).

### status_pembayaran

| Nilai | Deskripsi |
|-------|-----------|
| MENUNGGU_PEMBAYARAN | Belum ada bukti transfer |
| MENUNGGU_VERIFIKASI | Bukti transfer menunggu verifikasi staff |
| LUNAS | Pembayaran diverifikasi & diterima |
| DIBATALKAN | Transaksi dibatalkan |
| KEDALUWARSA | Lewat batas waktu bayar |

### status_verifikasi

| Nilai | Deskripsi |
|-------|-----------|
| MENUNGGU | Bukti transfer belum diverifikasi |
| DISETUJUI | Bukti transfer disetujui staff |
| DITOLAK | Bukti transfer ditolak staff |

### status_refund

| Nilai | Deskripsi |
|-------|-----------|
| TIDAK_ADA | Tidak ada permintaan refund |
| PENDING_REFUND | Refund sedang diproses staff |
| REFUNDED | Refund sudah dikembalikan ke pelanggan |

### tipe_penerima

| Nilai | Deskripsi |
|-------|-----------|
| PELANGGAN | Notifikasi untuk pelanggan |
| STAFF | Notifikasi untuk staff |

### jenis_notifikasi

| Nilai | Deskripsi |
|-------|-----------|
| BOOKING_DISETUJUI | Booking disetujui staff |
| BOOKING_DITOLAK | Booking ditolak staff |
| JAM_GROOMING_DIUPDATE | Staff mengisi jam grooming |
| REMINDER_PEMBAYARAN | Pengingat pembayaran |
| PEMBAYARAN_JATUH_TEMPO | Batas waktu bayar hampir/sudah lewat |
| MONITORING_PENITIPAN | Update monitoring penitipan harian |
| LAYANAN_SELESAI | Layanan selesai |
| BOOKING_DIBATALKAN | Booking dibatalkan |
| STATUS_REFUND | Update status refund |
| PERPANJANGAN_PENITIPAN_MENUNGGU_KONFIRMASI | Permintaan perpanjangan masuk (ke staff) |
| PERPANJANGAN_PENITIPAN_DISETUJUI | Perpanjangan disetujui & check-out diperbarui |
| PERPANJANGAN_PENITIPAN_DITOLAK | Permintaan perpanjangan ditolak staff |
| PERPANJANGAN_PENITIPAN_MENUNGGU_PEMBAYARAN | Staff konfirmasi perpanjangan, menunggu pembayaran |

---

## 1. Tabel `pelanggan`

Menyimpan data akun pelanggan (pemilik kucing).

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik pelanggan |
| 2 | nama | VARCHAR | 150 | Tidak | — | Nama lengkap pelanggan |
| 3 | email | VARCHAR | 255 | Tidak | UQ | Email login pelanggan |
| 4 | no_telepon | VARCHAR | 20 | Ya | — | Nomor telepon/WhatsApp |
| 5 | password_hash | VARCHAR | 255 | Tidak | — | Hash password (bcrypt/argon2) |
| 6 | alamat_lengkap | TEXT | — | Ya | — | Alamat lengkap pelanggan |
| 7 | latitude | DECIMAL | 10,8 | Ya | — | Koordinat lintang alamat |
| 8 | longitude | DECIMAL | 11,8 | Ya | — | Koordinat bujur alamat |
| 9 | foto_profil_url | VARCHAR | 500 | Ya | — | URL foto profil (opsional) |
| 10 | pernah_pakai_promo_penitipan | BOOLEAN | — | Tidak | DF | Flag promo penitipan 10% sudah dipakai |
| 11 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu pendaftaran akun |
| 12 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir profil diubah |

---

## 2. Tabel `staff`

Menyimpan data akun internal petshop (staff & owner).

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik staff/owner |
| 2 | nama | VARCHAR | 150 | Tidak | — | Nama lengkap staff |
| 3 | email | VARCHAR | 255 | Tidak | UQ | Email login staff |
| 4 | username | VARCHAR | 100 | Ya | UQ | Username alternatif login |
| 5 | password_hash | VARCHAR | 255 | Tidak | — | Hash password |
| 6 | role | staff_role | — | Tidak | DF | Peran: STAFF atau OWNER |
| 7 | status | status_akun | — | Tidak | DF | Status akun: AKTIF / NONAKTIF |
| 8 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu akun dibuat |
| 9 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir data diubah |

---

## 3. Tabel `kucing`

Menyimpan data kucing milik pelanggan.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik kucing |
| 2 | pelanggan_id | UUID | — | Tidak | FK → pelanggan.id | Pemilik kucing |
| 3 | nama | VARCHAR | 100 | Tidak | — | Nama kucing |
| 4 | jenis_kelamin | jenis_kelamin | — | Tidak | — | JANTAN / BETINA |
| 5 | ras | VARCHAR | 100 | Ya | — | Ras kucing |
| 6 | tanggal_lahir | DATE | — | Ya | — | Tanggal lahir kucing |
| 7 | berat_badan | DECIMAL | 5,2 | Ya | — | Berat badan (kg) |
| 8 | foto_url | VARCHAR | 500 | Ya | — | URL foto kucing |
| 9 | catatan_kesehatan | TEXT | — | Ya | — | Alergi, kondisi khusus, dll. |
| 10 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu data kucing ditambahkan |
| 11 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir data diubah |

---

## 4. Tabel `riwayat_vaksin`

Menyimpan riwayat vaksin per kucing.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik riwayat vaksin |
| 2 | kucing_id | UUID | — | Tidak | FK → kucing.id | Kucing terkait |
| 3 | jenis_vaksin | VARCHAR | 100 | Tidak | — | Jenis vaksin (FVRCP, rabies, dll.) |
| 4 | tanggal_vaksin | DATE | — | Tidak | — | Tanggal vaksinasi |
| 5 | sertifikat_url | VARCHAR | 500 | Ya | — | URL bukti/sertifikat vaksin (opsional) |
| 6 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu entri ditambahkan |

---

## 5. Tabel `jenis_grooming`

Master data jenis layanan grooming dan harga.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik jenis grooming |
| 2 | nama | VARCHAR | 100 | Tidak | UQ | Nama jenis (lengkap, jamur, kutu, dll.) |
| 3 | deskripsi | TEXT | — | Ya | — | Deskripsi layanan |
| 4 | harga | DECIMAL | 12,2 | Tidak | CK ≥ 0 | Harga grooming (Rp) |
| 5 | aktif | BOOLEAN | — | Tidak | DF | Status tampil di form booking |
| 6 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu data dibuat |
| 7 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir diubah |

---

## 6. Tabel `kuota_grooming`

Kuota slot grooming per tanggal.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik kuota |
| 2 | tanggal | DATE | — | Tidak | UQ | Tanggal kuota grooming |
| 3 | slot_maksimal | INT | — | Tidak | CK ≥ 0 | Jumlah slot maksimal per hari |
| 4 | slot_terisi | INT | — | Tidak | DF, CK | Jumlah slot sudah terbooking |
| 5 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu kuota dibuat |
| 6 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir diubah |

---

## 7. Tabel `paket_penitipan`

Master data paket harga penitipan kucing (pet hotel).

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik paket |
| 2 | nama | VARCHAR | 100 | Tidak | UQ | Nama paket penitipan |
| 3 | harga_per_hari | DECIMAL | 12,2 | Tidak | CK ≥ 0 | Tarif per hari (Rp) |
| 4 | deskripsi | TEXT | — | Ya | — | Deskripsi paket |
| 5 | aktif | BOOLEAN | — | Tidak | DF | Status paket aktif |
| 6 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu data dibuat |
| 7 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir diubah |

---

## 8. Tabel `kamar_penitipan`

Master data kamar/slot fisik penitipan.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik kamar |
| 2 | nama_kamar | VARCHAR | 100 | Tidak | UQ | Nama/label kamar |
| 3 | kapasitas | INT | — | Tidak | CK > 0 | Kapasitas kucing per kamar |
| 4 | aktif | BOOLEAN | — | Tidak | DF | Status kamar aktif |
| 5 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu data dibuat |
| 6 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir diubah |

---

## 9. Tabel `kuota_penitipan`

Kuota ketersediaan kamar penitipan per tanggal.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik kuota |
| 2 | kamar_penitipan_id | UUID | — | Tidak | FK → kamar_penitipan.id | Kamar terkait |
| 3 | tanggal | DATE | — | Tidak | UQ* | Tanggal kuota (*unik per kamar) |
| 4 | slot_maksimal | INT | — | Tidak | CK ≥ 0 | Slot maksimal per hari |
| 5 | slot_terisi | INT | — | Tidak | DF, CK | Slot sudah terbooking |
| 6 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu kuota dibuat |
| 7 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir diubah |

---

## 10. Tabel `layanan_pet_care`

Master data layanan pet care (CRUD staff/owner).

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik layanan |
| 2 | nama | VARCHAR | 150 | Tidak | — | Nama layanan pet care |
| 3 | deskripsi | TEXT | — | Ya | — | Deskripsi layanan |
| 4 | harga | DECIMAL | 12,2 | Tidak | CK ≥ 0 | Harga layanan (Rp) |
| 5 | estimasi_durasi_menit | INT | — | Tidak | CK > 0 | Estimasi durasi (menit) |
| 6 | status | status_layanan | — | Tidak | DF | AKTIF / NONAKTIF |
| 7 | deleted_at | TIMESTAMPTZ | — | Ya | — | Soft delete timestamp |
| 8 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu layanan dibuat |
| 9 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir diubah |

---

## 11. Tabel `kuota_pet_care`

Jadwal slot dokter pet care per tanggal (global — 1 dokter, maks 1 booking per slot).

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik slot |
| 2 | tanggal | DATE | — | Tidak | UQ* | Tanggal slot (*unik per tanggal+slot) |
| 3 | slot_waktu | TIME | — | Tidak | UQ* | Jam slot waktu dokter |
| 4 | slot_maksimal | INT | — | Tidak | DF, CK = 1 | Selalu 1 (1 dokter) |
| 5 | slot_terisi | INT | — | Tidak | DF, CK 0–1 | 0 = kosong, 1 = terbooking |
| 6 | status_slot | status_slot_pet_care | — | Tidak | DF | TERSEDIA / DITUTUP |
| 7 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu slot dibuat |
| 8 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir diubah |

---

## 12. Tabel `booking_grooming`

Data booking layanan grooming.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik booking |
| 2 | pelanggan_id | UUID | — | Tidak | FK → pelanggan.id | Pelanggan pengaju |
| 3 | kucing_id | UUID | — | Tidak | FK → kucing.id | Kucing yang digrooming |
| 4 | jenis_grooming_id | UUID | — | Tidak | FK → jenis_grooming.id | Jenis grooming dipilih |
| 5 | kuota_grooming_id | UUID | — | Tidak | FK → kuota_grooming.id | Kuota tanggal dipakai |
| 6 | dikonfirmasi_oleh_staff_id | UUID | — | Ya | FK → staff.id | Staff yang konfirmasi |
| 7 | tanggal | DATE | — | Tidak | — | Tanggal grooming |
| 8 | jam_grooming | TIME | — | Ya | — | Jam grooming (diisi staff) |
| 9 | opsi_pengantaran | opsi_pengantaran | — | Tidak | DF | ANTAR_JEMPUT / ANTAR_SENDIRI |
| 10 | jarak_km | DECIMAL | 8,2 | Ya | — | Jarak alamat pelanggan ke petshop (km) |
| 11 | biaya_antar_jemput | DECIMAL | 12,2 | Tidak | DF, CK ≥ 0 | Biaya antar-jemput (Rp) |
| 12 | harga_layanan | DECIMAL | 12,2 | Tidak | CK ≥ 0 | Snapshot harga grooming saat booking |
| 13 | status | status_booking_grooming | — | Tidak | DF | Status alur booking |
| 14 | catatan | TEXT | — | Ya | — | Catatan khusus dari pelanggan |
| 15 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu booking diajukan |
| 16 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir status diubah |

---

## 13. Tabel `booking_penitipan`

Data booking penitipan kucing (pet hotel).

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik booking |
| 2 | pelanggan_id | UUID | — | Tidak | FK → pelanggan.id | Pelanggan pengaju |
| 3 | kucing_id | UUID | — | Tidak | FK → kucing.id | Kucing yang dititipkan |
| 4 | paket_penitipan_id | UUID | — | Tidak | FK → paket_penitipan.id | Paket harga dipilih |
| 5 | kamar_penitipan_id | UUID | — | Tidak | FK → kamar_penitipan.id | Kamar penitipan |
| 6 | dikonfirmasi_oleh_staff_id | UUID | — | Ya | FK → staff.id | Staff yang konfirmasi |
| 7 | check_in | DATE | — | Tidak | — | Tanggal check-in |
| 8 | check_out | DATE | — | Tidak | CK > check_in | Tanggal check-out |
| 9 | lama_hari | INT | — | Tidak | CK > 0 | Durasi penitipan (hari) |
| 10 | promo_dipakai | BOOLEAN | — | Tidak | DF | Apakah promo 10% dipakai |
| 11 | subtotal_penitipan | DECIMAL | 12,2 | Tidak | CK ≥ 0 | Subtotal sebelum promo |
| 12 | potongan_promo | DECIMAL | 12,2 | Tidak | DF, CK ≥ 0 | Potongan promo (Rp) |
| 13 | opsi_pengantaran | opsi_pengantaran | — | Tidak | DF | ANTAR_JEMPUT / ANTAR_SENDIRI |
| 14 | jarak_km | DECIMAL | 8,2 | Ya | — | Jarak antar-jemput (km) |
| 15 | biaya_antar_jemput | DECIMAL | 12,2 | Tidak | DF, CK ≥ 0 | Biaya antar-jemput (Rp) |
| 16 | status | status_penitipan | — | Tidak | DF | Status alur penitipan |
| 17 | catatan_makan | TEXT | — | Ya | — | Catatan makan & kebiasaan kucing |
| 18 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu booking diajukan |
| 19 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir status diubah |

---

## 14. Tabel `monitoring_penitipan`

Update monitoring harian kucing selama penitipan.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik monitoring |
| 2 | booking_penitipan_id | UUID | — | Tidak | FK → booking_penitipan.id | Booking penitipan terkait |
| 3 | staff_id | UUID | — | Tidak | FK → staff.id | Staff yang input monitoring |
| 4 | tanggal | DATE | — | Tidak | — | Tanggal monitoring |
| 5 | foto_url | VARCHAR | 500 | Ya | — | URL foto kucing hari itu |
| 6 | catatan_makan | TEXT | — | Ya | — | Catatan pola makan |
| 7 | kondisi | TEXT | — | Ya | — | Kondisi kesehatan kucing |
| 8 | aktivitas_harian | TEXT | — | Ya | — | Aktivitas harian kucing |
| 9 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu monitoring diinput |

---

## 15. Tabel `perpanjangan_penitipan`

Permintaan perpanjangan durasi penitipan (setelah booking terkonfirmasi & kucing sedang dititipkan).

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik permintaan perpanjangan |
| 2 | booking_penitipan_id | UUID | — | Tidak | FK → booking_penitipan.id | Booking penitipan terkait |
| 3 | check_out_sebelum | DATE | — | Tidak | — | Snapshot check-out booking saat ajuan |
| 4 | check_out_baru | DATE | — | Tidak | CK > check_out_sebelum | Tanggal check-out baru yang diajukan |
| 5 | tambah_hari | INT | — | Tidak | CK > 0 | Jumlah hari tambahan |
| 6 | subtotal_tambahan | DECIMAL | 12,2 | Tidak | CK ≥ 0 | Biaya hari tambahan (harga paket × tambah_hari) |
| 7 | status | status_perpanjangan_penitipan | — | Tidak | DF | Status alur perpanjangan |
| 8 | dikonfirmasi_oleh_staff_id | UUID | — | Ya | FK → staff.id | Staff yang konfirmasi/tolak |
| 9 | catatan_penolakan | TEXT | — | Ya | — | Alasan jika ditolak staff |
| 10 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu permintaan diajukan |
| 11 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir status diubah |

---

## 16. Tabel `booking_pet_care`

Data booking layanan pet care (booking-only, tanpa transaksi di app).

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik booking |
| 2 | pelanggan_id | UUID | — | Tidak | FK → pelanggan.id | Pelanggan pengaju |
| 3 | kucing_id | UUID | — | Tidak | FK → kucing.id | Kucing yang dilayani |
| 4 | layanan_pet_care_id | UUID | — | Tidak | FK → layanan_pet_care.id | Layanan dipilih |
| 5 | kuota_pet_care_id | UUID | — | Tidak | FK → kuota_pet_care.id | Slot dokter dipakai |
| 6 | tanggal | DATE | — | Tidak | — | Tanggal layanan |
| 7 | slot_waktu | TIME | — | Tidak | — | Jam slot layanan |
| 8 | harga_layanan | DECIMAL | 12,2 | Tidak | CK ≥ 0 | Snapshot estimasi harga saat booking |
| 9 | status | status_booking_pet_care | — | Tidak | DF | Status alur booking |
| 10 | catatan | TEXT | — | Ya | — | Catatan khusus pelanggan |
| 11 | dibatalkan_oleh | dibatalkan_oleh | — | Ya | CK* | PELANGGAN / STAFF (*wajib jika DIBATALKAN) |
| 12 | dibatalkan_oleh_staff_id | UUID | — | Ya | FK → staff.id | Staff pembatal (jika dibatalkan_oleh = STAFF) |
| 13 | alasan_pembatalan | TEXT | — | Ya | — | Alasan pembatalan (opsional) |
| 14 | waktu_dibatalkan | TIMESTAMPTZ | — | Ya | — | Waktu booking dibatalkan |
| 15 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu booking diajukan |
| 16 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir status diubah |

---

## 17. Tabel `transaksi`

Data pembayaran per booking awal atau per perpanjangan penitipan.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik transaksi |
| 2 | pelanggan_id | UUID | — | Tidak | FK → pelanggan.id | Pelanggan pemilik transaksi |
| 3 | jenis_layanan | jenis_layanan | — | Tidak | UQ* | GROOMING / PENITIPAN |
| 4 | booking_id | UUID | — | Tidak | UQ* | ID booking terkait (*unik per jenis jika transaksi awal) |
| 5 | perpanjangan_penitipan_id | UUID | — | Ya | FK → perpanjangan_penitipan.id, UQ† | Permintaan perpanjangan terkait (†unik jika not null) |
| 6 | subtotal_layanan | DECIMAL | 12,2 | Tidak | CK ≥ 0 | Subtotal harga layanan |
| 7 | potongan_promo | DECIMAL | 12,2 | Tidak | DF, CK ≥ 0 | Potongan promo penitipan (hanya booking awal) |
| 8 | biaya_antar_jemput | DECIMAL | 12,2 | Tidak | DF, CK ≥ 0 | Biaya antar-jemput (hanya booking awal) |
| 9 | total_bayar | DECIMAL | 12,2 | Tidak | CK ≥ 0 | Total yang harus dibayar |
| 10 | status_pembayaran | status_pembayaran | — | Tidak | DF | Status alur pembayaran |
| 11 | status_refund | status_refund | — | Tidak | DF | Status refund |
| 12 | batas_waktu_bayar | TIMESTAMPTZ | — | Ya | — | Deadline pembayaran |
| 13 | dibayar_at | TIMESTAMPTZ | — | Ya | — | Waktu pembayaran diverifikasi |
| 14 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu transaksi dibuat |
| 15 | updated_at | TIMESTAMPTZ | — | Tidak | DF | Waktu terakhir diubah |

**Index unik partial:**
- `(jenis_layanan, booking_id) WHERE perpanjangan_penitipan_id IS NULL` — satu transaksi booking awal.
- `(perpanjangan_penitipan_id) WHERE perpanjangan_penitipan_id IS NOT NULL` — satu transaksi per perpanjangan.

---

## 18. Tabel `bukti_transfer`

Bukti transfer bank yang diupload pelanggan.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik bukti |
| 2 | transaksi_id | UUID | — | Tidak | FK → transaksi.id, UQ | Transaksi terkait (1:1) |
| 3 | file_url | VARCHAR | 500 | Tidak | — | URL file bukti transfer |
| 4 | status_verifikasi | status_verifikasi | — | Tidak | DF | Status verifikasi staff |
| 5 | diverifikasi_oleh_staff_id | UUID | — | Ya | FK → staff.id | Staff verifikator |
| 6 | catatan_penolakan | TEXT | — | Ya | — | Alasan jika bukti ditolak |
| 7 | uploaded_at | TIMESTAMPTZ | — | Tidak | DF | Waktu bukti diupload |
| 8 | diverifikasi_at | TIMESTAMPTZ | — | Ya | — | Waktu verifikasi selesai |

---

## 19. Tabel `invoice`

Invoice/struk setelah pembayaran lunas.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik invoice |
| 2 | transaksi_id | UUID | — | Tidak | FK → transaksi.id, UQ | Transaksi terkait (1:1) |
| 3 | nomor_invoice | VARCHAR | 50 | Tidak | UQ | Nomor invoice unik |
| 4 | file_url | VARCHAR | 500 | Ya | — | URL file PDF invoice |
| 5 | issued_at | TIMESTAMPTZ | — | Tidak | DF | Waktu invoice diterbitkan |

---

## 20. Tabel `notifikasi`

Notifikasi in-app untuk pelanggan dan staff.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK, DF | Identitas unik notifikasi |
| 2 | penerima_id | UUID | — | Tidak | — | ID pelanggan atau staff penerima |
| 3 | tipe_penerima | tipe_penerima | — | Tidak | DF | PELANGGAN / STAFF |
| 4 | jenis | jenis_notifikasi | — | Tidak | — | Jenis/trigger notifikasi |
| 5 | judul | VARCHAR | 200 | Tidak | — | Judul notifikasi |
| 6 | pesan | TEXT | — | Tidak | — | Isi pesan notifikasi |
| 7 | referensi_id | UUID | — | Ya | — | ID entitas terkait (booking, transaksi, dll.) |
| 8 | referensi_tipe | VARCHAR | 50 | Ya | — | Tipe entitas referensi |
| 9 | sudah_dibaca | BOOLEAN | — | Tidak | DF | Status dibaca pelanggan |
| 10 | created_at | TIMESTAMPTZ | — | Tidak | DF | Waktu notifikasi dibuat |

---

## Ringkasan relasi antar tabel

| Tabel Induk | Tabel Anak | Relasi | Keterangan |
|-------------|------------|--------|------------|
| pelanggan | kucing | 1 : N | Satu pelanggan punya banyak kucing |
| pelanggan | booking_grooming | 1 : N | Satu pelanggan banyak booking grooming |
| pelanggan | booking_penitipan | 1 : N | Satu pelanggan banyak booking penitipan |
| pelanggan | booking_pet_care | 1 : N | Satu pelanggan banyak booking pet care |
| pelanggan | transaksi | 1 : N | Satu pelanggan banyak transaksi |
| kucing | riwayat_vaksin | 1 : N | Satu kucing banyak riwayat vaksin |
| kucing | booking_* | 1 : N | Satu kucing bisa banyak booking |
| kamar_penitipan | kuota_penitipan | 1 : N | Kuota per kamar per tanggal |
| kuota_pet_care | booking_pet_care | 1 : N | Slot dokter dipakai booking |
| booking_penitipan | monitoring_penitipan | 1 : N | Monitoring harian selama penitipan |
| booking_penitipan | perpanjangan_penitipan | 1 : N | Permintaan perpanjangan durasi (boleh berkali-kali & paralel) |
| perpanjangan_penitipan | transaksi | 1 : 1 | Satu transaksi per perpanjangan |
| booking_grooming / booking_penitipan | transaksi | 1 : 1 | Satu transaksi booking awal per booking |
| transaksi | bukti_transfer | 1 : 0..1 | Bukti transfer opsional sampai diupload |
| transaksi | invoice | 1 : 0..1 | Invoice setelah lunas |
| staff | bukti_transfer | 1 : N | Staff verifikasi bukti |
| staff | monitoring_penitipan | 1 : N | Staff input monitoring |
| staff | perpanjangan_penitipan | 1 : N | Staff konfirmasi/tolak perpanjangan |
| staff | pengaturan_petshop | 1 : 0..1 | Owner terakhir mengubah pengaturan |

---

## 20. Tabel `pengaturan_petshop`

Pengaturan bisnis petshop (single row). Default instalasi dari `.env`; owner mengubah lewat `/admin/pengaturan`.

| No | Nama Field | Tipe Data | Panjang | Null | Keterangan | Deskripsi |
|----|------------|-----------|---------|------|------------|-----------|
| 1 | id | UUID | — | Tidak | PK | Identitas unik baris pengaturan |
| 2 | petshop_lat | DECIMAL | 10,8 | Tidak | — | Koordinat lintang petshop |
| 3 | petshop_lng | DECIMAL | 11,8 | Tidak | — | Koordinat bujur petshop |
| 4 | pickup_free_radius_km | DECIMAL | 5,2 | Tidak | DF 3 | Radius gratis antar-jemput (km) |
| 5 | pickup_extra_fee_per_km | INT | — | Tidak | DF 5000 | Biaya per km di atas radius (Rp) |
| 6 | payment_deadline_hours | INT | — | Tidak | DF 24 | Batas waktu bayar setelah konfirmasi (jam) |
| 7 | bank_name | VARCHAR | 50 | Tidak | — | Nama bank tujuan transfer |
| 8 | bank_account_number | VARCHAR | 30 | Tidak | — | Nomor rekening |
| 9 | bank_account_name | VARCHAR | 100 | Tidak | — | Atas nama rekening |
| 10 | promo_min_days | INT | — | Tidak | DF 7 | Minimal hari promo penitipan |
| 11 | promo_discount_percent | INT | — | Tidak | DF 10 | Persen diskon promo penitipan |
| 12 | min_vaccination_count | INT | — | Tidak | DF 1 | Minimal riwayat vaksin pet hotel |
| 13 | petshop_whatsapp | VARCHAR | 20 | Tidak | — | Nomor WhatsApp (format 62…) |
| 14 | updated_by_staff_id | UUID | — | Ya | FK → staff.id | Staff/owner terakhir mengubah |
| 15 | updated_at | DATETIME | — | Tidak | DF | Waktu terakhir diubah |

---

## File terkait

- Skema SQL: [schema.sql](./schema.sql)
- ERD: [erd-diagram.md](../diagrams/erd/erd-diagram.md)
- Class diagram: [class-diagram.md](../diagrams/class/class-diagram.md)
