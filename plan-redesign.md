# Panduan Redesign Web App Pet Hotel

Dokumen pendamping `sketch-aplikasi-pet-hotel.md`. Fokus: memperbaiki aplikasi yang sudah ada, bukan membangun ulang seluruh fitur. Nama menu disesuaikan dengan fitur aktual; jangan menampilkan menu yang belum berfungsi.

## 1. Prinsip redesign

### 1.1 Dahulukan pekerjaan pengguna
Tentukan satu tujuan utama per halaman. Dashboard operasional membantu petugas memutuskan tindakan berikutnya; detail reservasi membantu menyelesaikan proses tamu; portal pemilik membantu reservasi dan melihat kabar hewan. Jangan menggabungkan ketiganya dalam navigasi yang sama.

### 1.2 Pertahankan hal yang sudah familiar
Pertahankan istilah, URL penting, data, dan alur yang sudah dipahami pengguna kecuali ada masalah terukur. Jika menu dipindahkan, sediakan pengalihan dan petunjuk sementara. Redesign tidak boleh menghilangkan filter, shortcut, atau kemampuan operasional yang masih dibutuhkan.

### 1.3 Hierarki sebelum dekorasi
Urutan perhatian: keadaan darurat → tugas jatuh tempo → aksi utama → informasi pendukung. Gunakan ukuran, posisi, dan ruang untuk membangun hierarki sebelum menambah warna atau bayangan.

### 1.4 Satu aksi utama dalam satu konteks
Contoh: halaman daftar memiliki “Buat reservasi”; halaman kedatangan memiliki “Proses check-in”. Aksi sekunder memakai outline atau teks. Aksi berbahaya seperti pembatalan dipisahkan dari aksi rutin.

### 1.5 Desain berdasarkan risiko
Pemberian obat, pemindahan kamar, pembatalan, dan refund memerlukan informasi serta konfirmasi lebih kuat daripada mengubah catatan biasa. Jangan memperlambat semua interaksi dengan modal konfirmasi.

### 1.6 Tampilkan kondisi sebenarnya
“Terjadwal”, “sedang diproses”, dan “selesai” harus berbeda. Jangan menampilkan sukses sebelum server menerima tindakan penting. Status reservasi, pembayaran, kamar, dan kesehatan tidak digabung menjadi satu badge.

### 1.7 Aksesibilitas sejak awal
Target WCAG 2.2 AA: kontras teks normal minimal 4.5:1, teks besar 3:1, komponen penting 3:1. Semua fungsi utama dapat digunakan dengan keyboard. Status harus terbaca tanpa mengandalkan warna.

## 2. Audit sebelum mengubah UI

| Yang diperiksa | Bukti yang dikumpulkan | Hasil yang diharapkan |
|---|---|---|
| Navigasi | Menu aktual dan frekuensi penggunaan | Menu inti, menu sekunder, menu yang digabung |
| Alur penting | Rekaman langkah reservasi/check-in/tugas | Langkah yang membingungkan atau berulang |
| Kepadatan | Screenshot daftar dan detail | Kolom wajib versus informasi tambahan |
| Konsistensi | Tombol, input, modal, status | Daftar komponen yang perlu distandardisasi |
| Responsivitas | Desktop, tablet, ponsel | Overflow, tombol sulit disentuh, konten terpotong |
| Keandalan | Loading, error, akses ditolak, data kosong | State yang belum ditangani |

Baseline: waktu menyelesaikan tugas, jumlah salah input, jumlah klik, dan bantuan yang dibutuhkan. Gunakan hasil audit untuk memprioritaskan perubahan; jangan mengubah semua halaman hanya agar terlihat baru.

## 3. Struktur layout utama

### 3.1 Panel operasional desktop

```text
┌───────────────┬──────────────────────────────────────────────────┐
│ Logo          │ Cabang ▾   Cari reservasi/hewan    Bantuan  Akun │
│               ├──────────────────────────────────────────────────┤
│ Operasional   │ Breadcrumb jika diperlukan                      │
│ Dashboard     │ Judul halaman             [Aksi utama]          │
│ Reservasi     │ Deskripsi singkat / konteks tanggal             │
│ Kalender      ├──────────────────────────────────────────────────┤
│ Hewan menginap│ Ringkasan atau peringatan yang relevan           │
│ Perawatan     ├──────────────────────────────────────────────────┤
│               │ Pencarian lokal · Filter · Urutkan               │
│ Administrasi  ├──────────────────────────────────────────────────┤
│ Pelanggan     │ Konten utama: tabel / kalender / formulir        │
│ Pembayaran    │                                                  │
│ Laporan       │                                                  │
│               │                                                  │
│ Pengaturan    │ Pagination / informasi hasil                    │
└───────────────┴──────────────────────────────────────────────────┘
```

- Sidebar: 248 px; mode ringkas opsional 72 px.
- Topbar: 64 px; tetap terlihat saat halaman panjang bila membantu orientasi.
- Padding konten desktop: 32 px, turun menjadi 24 px pada layar menengah.
- Lebar konten umum: maksimal 1440 px dan rata tengah di area kerja.
- Tabel serta kalender boleh menggunakan seluruh lebar yang tersedia.
- Formulir: maksimal 760 px; halaman detail dua kolom boleh lebih lebar.
- Gunakan satu scroll vertikal utama. Scroll internal hanya untuk tabel panjang, kalender, atau panel yang memang memerlukannya.
- Elemen sticky tidak boleh menutup judul, fokus keyboard, pesan error, atau baris terakhir.

### 3.2 Portal pemilik
Gunakan shell terpisah dari admin. Desktop: logo kiri, menu inti di tengah/kiri, notifikasi dan akun kanan. Mobile: header sederhana dan bottom navigation. Jangan memperlihatkan sidebar operasional kepada pemilik.

### 3.3 Layar petugas
Utamakan “Tugas saya”, “Hewan”, dan “Aktivitas”. Kurangi grafik dan kontrol administrasi. Identitas hewan, nomor kamar, waktu tugas, serta peringatan harus terlihat sebelum tombol tindakan.

## 4. Guideline navbar dan sidebar

### Topbar operasional
- Kiri: pemilih cabang hanya jika aplikasi memiliki beberapa cabang. Untuk satu cabang, tampilkan nama lokasi tanpa dropdown palsu.
- Tengah: pencarian global opsional untuk reservasi, pemilik, dan hewan; kelompokkan hasil menurut tipe.
- Kanan: bantuan, notifikasi, avatar akun. Maksimal tiga kelompok utilitas.
- Avatar membuka profil, preferensi, dan keluar. Jangan meletakkan navigasi utama di dalamnya.
- Notifikasi memiliki kategori dan status dibaca; badge menunjukkan item yang memerlukan perhatian, bukan seluruh histori.
- Jangan menaruh “Buat reservasi” pada topbar jika tombol yang sama sudah menjadi aksi utama halaman.
- Pencarian global berbeda dari pencarian tabel; label keduanya harus jelas.

### Sidebar
- Kelompok pertama: Dashboard, Reservasi, Kalender kamar, Hewan menginap, Perawatan.
- Kelompok kedua: Pelanggan, Pembayaran, Laporan.
- Pengaturan ditempatkan di bawah; tampil hanya bagi peran yang berwenang.
- Tinggi item: 44–48 px; ikon 20 px; jarak ikon ke label 12 px.
- Status aktif: latar teal lembut + teks tebal + indikator bentuk. Jangan hanya mengganti warna ikon.
- Gunakan ikon dari satu keluarga dengan ketebalan garis konsisten.
- Maksimal dua tingkat hierarki. Hindari submenu di dalam submenu.
- Sidebar ringkas harus menyediakan label melalui tooltip saat hover dan fokus. Pada perangkat sentuh, gunakan drawer berlabel.
- Item menu adalah link untuk mendukung tab baru dan URL langsung; ekspansi grup adalah tombol.
- Menu yang disembunyikan berdasarkan peran tetap harus dilindungi oleh otorisasi backend.

### Mobile
- Admin: topbar 56 px dengan tombol menu; sidebar menjadi drawer.
- Pemilik: bottom navigation 4 item—Beranda, Booking, Peliharaan, Akun.
- Petugas: bottom navigation 3–4 item sesuai fitur aktual; jangan menambah item hanya untuk memenuhi jumlah.
- Bottom navigation memiliki ikon dan label; tinggi dasar 64 px ditambah safe area perangkat.
- Halaman alur bertahap dapat menyembunyikan bottom navigation dan memakai tombol kembali + progress.
- Tombol sticky bawah dan bottom navigation tidak boleh saling bertumpuk; pilih satu pola per konteks.
- Drawer bisa ditutup lewat tombol, area backdrop, dan Escape; fokus kembali ke pemicu saat ditutup.

## 5. Header halaman dan hierarki konten

Urutan standar: breadcrumb opsional → judul + aksi → deskripsi → ringkasan → toolbar → konten.

- Judul menyebut objek/tugas: “Reservasi”, “Detail Milo”, “Proses check-in”.
- Deskripsi maksimal dua baris; hindari paragraf pemasaran pada layar operasional.
- Breadcrumb hanya untuk hierarki lebih dalam, bukan pengganti tombol kembali pada alur mobile.
- Satu tombol primary; maksimal dua aksi sekunder terlihat. Aksi jarang digunakan masuk menu “Lainnya”.
- Pada mobile, aksi utama boleh menjadi tombol penuh di bawah judul atau sticky bawah jika form panjang.
- Tabs dipakai untuk beberapa tampilan objek yang sama, misalnya Ringkasan / Perawatan / Tagihan / Aktivitas.
- Tabs bukan pengganti navigasi aplikasi. Status tab yang penting harus tersimpan pada URL.

## 6. Token visual

| Token | Rekomendasi |
|---|---|
| Latar aplikasi | `#FAF7F2` |
| Permukaan utama | `#FFFFFF` |
| Permukaan sekunder | `#F2F5F3` |
| Teks utama | `#25332F` |
| Teks sekunder | `#52635C` |
| Aksi utama | `#236B63` dengan teks putih |
| Hover aksi utama | `#19564F` |
| Aksen dekoratif | `#F3C9AE`, bukan warna teks kecil |
| Border dekoratif | `#DDE5E0` |
| Border input | `#7C8D85`; verifikasi kontras pada latar aktual |
| Error | Teks `#B42318`, latar `#FEF3F2` |
| Peringatan | Teks `#854D0E`, latar `#FEF9C3` |
| Sukses | Teks `#166534`, latar `#F0FDF4` |
| Fokus keyboard | Outline 2 px yang kontras, offset 2 px |

Uji kombinasi warna aktual termasuk hover, disabled, dan tema bila ada. Jangan menganggap seluruh kombinasi aman hanya karena berasal dari palet yang sama.

### Tipografi
- Gunakan satu keluarga font, misalnya Inter dengan fallback system sans-serif.
- Judul halaman: 28–32 px desktop, 24 px mobile; weight 600–700.
- Judul section: 20 px; judul kartu: 16–18 px.
- Teks utama dan input: 16 px; tabel desktop boleh 14 px jika tetap terbaca.
- Metadata: 12–14 px; jangan gunakan untuk instruksi kritis.
- Line-height teks isi: sekitar 1.5. Hindari paragraf panjang dalam huruf kapital.
- Angka pada tabel menggunakan tabular numerals; nominal rata kanan.

### Spacing dan bentuk
- Skala spacing: 4, 8, 12, 16, 24, 32, 48 px.
- Antar-label dan input: 8 px; antar-field: 16–24 px.
- Padding kartu: 20–24 px desktop, 16 px mobile.
- Radius input/tombol: 8 px; kartu/modal: 12 px; pill hanya untuk badge.
- Gunakan border ringan sebagai pemisah utama. Shadow tipis hanya untuk elemen mengambang.
- Hindari kartu di dalam kartu secara berlapis tanpa kebutuhan hierarki.

## 7. Komponen inti

### Tombol
- Primary solid: aksi utama; secondary outline: alternatif; tertiary teks: aksi ringan; destructive: tindakan merusak.
- Tinggi default 44 px; versi padat desktop 36 px dengan area target memadai.
- Label berupa kata kerja + objek: “Simpan perubahan”, “Proses check-in”, “Kirim update”.
- Loading menampilkan indikator dan label proses, mempertahankan lebar tombol, serta mencegah submit ganda.
- Tombol ikon memiliki accessible name dan tooltip; jangan memakai ikon tanpa label untuk aksi kritis.
- Disabled harus disertai penjelasan yang dapat ditemukan, bukan hanya tooltip pada tombol yang tidak bisa fokus.

### Formulir
- Label selalu di atas input; placeholder hanya contoh, bukan pengganti label.
- Gunakan satu kolom untuk form sederhana. Dua kolom hanya bagi data yang berhubungan, seperti tanggal masuk/keluar.
- Tandai field wajib secara konsisten dan jelaskan penandanya.
- Validasi setelah field disentuh atau saat submit; jangan menampilkan error sebelum pengguna mulai mengisi.
- Pesan error menjelaskan masalah dan cara memperbaiki; letakkan dekat field dan hubungkan secara aksesibel.
- Saat submit gagal, tampilkan ringkasan error dan arahkan fokus ke error pertama.
- Pertahankan isian setelah error server. Peringatkan ketika meninggalkan perubahan yang belum tersimpan.
- Tanggal menggunakan format lokal yang tidak ambigu dan menampilkan zona waktu cabang bila relevan.
- Instruksi obat menggunakan struktur yang jelas, sumber instruksi, serta versi; jangan menjadikannya textarea bebas semata.

### Tabel dan filter
- Kolom default reservasi: kode, pemilik/hewan, periode, kamar, status reservasi, status pembayaran, aksi.
- Tampilkan hanya kolom yang diperlukan untuk keputusan rutin; detail tambahan masuk halaman detail.
- Tinggi baris 56–64 px; header jelas; zebra opsional, bukan wajib.
- Identitas hewan menggunakan thumbnail dan nama. Teks penting tidak hanya tersedia saat hover.
- Toolbar: pencarian lokal → filter utama → filter lanjutan → reset.
- Filter aktif terlihat sebagai chip dan tersimpan pada URL, termasuk saat kembali dari detail.
- Tampilkan jumlah hasil, pagination, dan pilihan ukuran halaman; hindari infinite scroll untuk pekerjaan administratif.
- Jangan membuat seluruh baris sebagai satu-satunya cara membuka detail; sediakan link nama/kode.
- Pada mobile ubah daftar operasional menjadi kartu ringkas. Kalender/tabel perbandingan dapat tetap scroll horizontal dengan petunjuk yang jelas.

### Badge dan peringatan
- Badge menampilkan label pendek: “Terkonfirmasi”, “Belum lunas”, “Dibersihkan”.
- Merah untuk kondisi membutuhkan perhatian serius; jangan gunakan untuk semua pembayaran yang belum jatuh tempo.
- Alergi dan instruksi kritis tampil sebagai alert yang terlihat pada detail/tugas, bukan tersembunyi di tooltip.
- Alert menjelaskan kondisi, dampak, dan tindakan berikutnya.

### Modal, drawer, dan toast
- Modal: konfirmasi singkat atau form kecil; maksimal satu modal aktif.
- Drawer: preview detail tanpa kehilangan konteks daftar; desktop sekitar 480–640 px, mobile menjadi layar penuh.
- Alur panjang seperti check-in dan reservasi menggunakan halaman, bukan modal bertingkat.
- Saat modal terbuka, fokus terkurung di dalamnya dan kembali ke pemicu saat ditutup.
- Toast hanya untuk informasi sementara. Error pembayaran atau gagal menyimpan obat tetap terlihat di konteks halaman.
- Jangan menutup modal setelah server error. Konfirmasi destruktif menjelaskan objek dan dampak tindakan.

## 8. Pola halaman utama

### Dashboard
Maksimal empat metrik operasional di atas: menginap, kedatangan, kepulangan, kamar siap. Di bawahnya: tugas perlu tindakan, kedatangan/kepulangan, kemudian kondisi kamar. Metrik dapat membuka daftar yang sudah terfilter.

### Detail reservasi
Header: kode reservasi, status, periode, aksi sesuai tahap. Konten utama: hewan, pemilik, kamar, instruksi. Panel pendukung: ringkasan biaya dan pembayaran. Alergi ditempatkan dekat identitas hewan. Histori perubahan tersedia tanpa memenuhi layar awal.

### Kalender kamar
Sediakan tanggal, filter spesies/tipe kamar, legenda, dan tombol hari ini. Bedakan booking, menginap, pembersihan, maintenance. Drag-and-drop bersifat tambahan; selalu sediakan alternatif “Pindahkan kamar”. Server memvalidasi konflik sebelum perubahan dianggap berhasil.

### Tugas perawatan
Urutkan berdasarkan keterlambatan dan waktu. Kartu menampilkan hewan, kamar, tugas, jadwal, penanggung jawab. Penyelesaian obat membutuhkan pemeriksaan identitas dan hasil aktual. Aksi ganda dari dua petugas harus ditolak atau direkonsiliasi dengan pesan yang jelas.

### Portal kabar pemilik
Utamakan update terakhir beserta waktu dan petugas. Tampilkan kegiatan selesai terpisah dari jadwal berikutnya. Bila belum ada update, jelaskan kapan kabar biasanya diberikan; jangan menyatakan hewan sehat tanpa catatan yang mendukung.

## 9. Responsivitas

| Lebar viewport | Perilaku |
|---|---|
| Di bawah 768 px | Satu kolom, padding 16 px, drawer navigasi, form penuh |
| 768–1199 px | Padding 24 px, sidebar drawer/ringkas sesuai ruang, detail dua kolom hanya jika nyaman |
| Mulai 1200 px | Sidebar penuh, padding 32 px, layout desktop |

Breakpoint adalah titik awal; pilih berdasarkan kepadatan konten aktual. Uji layar sempit 320–360 px, zoom 200%, dan pembesaran teks. Konten umum tidak boleh memerlukan scroll horizontal; kalender dan tabel kompleks boleh memiliki area scroll tersendiri.

## 10. State, interaksi, dan performa

- **Loading awal:** skeleton menyerupai struktur konten; hindari spinner penuh untuk setiap perubahan kecil.
- **Memuat ulang:** pertahankan data lama dengan indikator proses agar konteks tidak hilang.
- **Kosong pertama kali:** jelaskan manfaat dan satu aksi untuk memulai.
- **Tidak ada hasil filter:** tampilkan filter aktif dan tombol reset.
- **Error server:** penjelasan singkat, tombol coba lagi, dan data/isian yang tetap terjaga.
- **Offline:** banner koneksi; jangan menandai pembayaran atau obat berhasil hanya secara lokal.
- **Akses ditolak:** jelaskan keterbatasan dan arahkan ke halaman yang dapat diakses.
- **Perubahan oleh petugas lain:** tampilkan konflik dan data terbaru; jangan menimpa diam-diam.
- Animasi singkat sekitar 150–200 ms, tanpa gerakan dekoratif pada operasional; hormati reduced motion.
- Kompres foto, gunakan thumbnail pada daftar, dan lazy-load media di bawah layar.
- Sediakan skip link, landmark halaman, urutan heading, dan pengumuman aksesibel untuk hasil simpan.

## 11. Cara menerapkan ke web app yang sudah ada

1. Inventarisasi halaman, komponen, route, dan perilaku yang harus dipertahankan.
2. Buat token terpusat untuk warna, typography, spacing, radius, dan ukuran kontrol.
3. Redesign shell: sidebar, topbar, header halaman, dan navigasi mobile.
4. Terapkan komponen standar: tombol, input, badge, alert, modal, tabel.
5. Uji tiga halaman percontohan: daftar reservasi, detail reservasi, tugas perawatan.
6. Bandingkan alur lama dan baru dengan pengguna aktual; perbaiki sebelum migrasi massal.
7. Rollout bertahap per halaman/peran menggunakan feature flag bila tersedia.
8. Pertahankan URL atau sediakan redirect; pastikan bookmark dan tautan notifikasi tetap bekerja.
9. Pisahkan perubahan visual dari perubahan aturan bisnis agar masalah mudah dilacak dan rollback lebih aman.

## 12. Checklist penerimaan redesign

- [ ] Navigasi aktif dan judul membuat lokasi pengguna jelas.
- [ ] Peran pemilik, petugas, dan admin mendapat navigasi yang sesuai.
- [ ] Setiap halaman memiliki tujuan dan aksi utama yang jelas.
- [ ] Filter, tab, dan posisi pengguna tidak hilang tanpa alasan saat kembali.
- [ ] Reservasi, pembayaran, dan kondisi kamar memiliki status terpisah.
- [ ] Tugas obat serta alergi terlihat dan tercatat dengan aman.
- [ ] Seluruh state loading, kosong, error, konflik, dan sukses tersedia.
- [ ] Keyboard, fokus, kontras, label, dan target sentuh diuji.
- [ ] Mobile tidak mengalami tombol bertumpuk atau konten penting terpotong.
- [ ] URL, izin akses, dan perilaku bisnis lama yang dibutuhkan tetap bekerja.
- [ ] Waktu tugas dan tingkat kesalahan dibandingkan dengan baseline.

## 13. Bahan untuk menyesuaikan panduan dengan aplikasi aktual

Panduan ini adalah baseline, bukan audit tampilan yang belum dilihat. Untuk menentukan perubahan spesifik, gunakan screenshot navbar/sidebar, dashboard, daftar reservasi, detail reservasi, serta satu tampilan mobile. Sertakan peran pengguna yang paling diprioritaskan dan bagian yang paling sering dikeluhkan.

**Audit implementasi (2026-09-27):** Wave 0–3 redesign diterapkan ke `src/Views/` (shell admin/pelanggan, `ui_page_header`, token semantik `index.css` / `design.php`). Eksekusi paralel dan gate `rg` tercatat di `docs/superpowers/plans/2026-09-26-plan-redesign-parallel-execution.md`. Screenshot per halaman dan uji 360px form booking pelanggan masih disarankan sebelum rilis produksi.
