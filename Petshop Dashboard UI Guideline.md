# Petshop Dashboard UI Guideline

## Tujuan Dokumen

Dokumen ini menjadi acuan desain untuk dashboard aplikasi **Petshop** agar tampilan, interaksi, dan penyajian informasi menjadi lebih konsisten, modern, mudah dipahami, dan mudah dikembangkan oleh tim desain maupun developer.

Fokus utama guideline ini adalah meningkatkan:

- kejelasan hierarki visual,
- konsistensi komponen,
- kenyamanan penggunaan,
- kualitas navigasi,
- dan persepsi profesional terhadap aplikasi.

---

## Evaluasi Singkat Tampilan Saat Ini

Berdasarkan dashboard yang ada saat ini, aplikasi sudah memiliki struktur fitur yang cukup lengkap, tetapi masih terdapat beberapa masalah visual dan usability yang menonjol.

Beberapa temuan utama:

- Navbar terlalu padat sehingga fokus pengguna mudah terpecah.
- Banyak aksi menggunakan warna yang sama sehingga prioritas tombol kurang jelas.
- Hirarki antar section belum cukup kuat; beberapa kartu terlihat memiliki bobot yang sama padahal kepentingannya berbeda.
- Komponen seperti card, link aksi, dan tombol masih terasa belum berada dalam satu sistem desain yang utuh.
- Empty state seperti booking kosong, tagihan kosong, dan notifikasi kosong masih terlalu pasif dan kurang membantu pengguna mengambil langkah berikutnya.

---

## Arah Redesign

Arah desain yang disarankan adalah dashboard yang:

- terasa ringan dan rapi,
- mudah dipindai dalam beberapa detik,
- menonjolkan informasi paling penting terlebih dahulu,
- dan memiliki sistem komponen yang seragam di seluruh aplikasi.

Prinsip utama redesign:

1. **Clarity** — pengguna langsung memahami apa yang penting.
2. **Consistency** — seluruh elemen visual mengikuti pola yang sama.
3. **Efficiency** — pengguna dapat melakukan aksi utama dengan cepat.
4. **Trustworthiness** — tampilan memberi kesan aman, profesional, dan meyakinkan.

---

## Struktur Informasi Dashboard yang Disarankan

Urutan konten yang direkomendasikan untuk halaman dashboard adalah sebagai berikut:

1. **Header sambutan**
   - sapaan nama pengguna,
   - email,
   - status akun singkat bila diperlukan.

2. **Ringkasan utama**
   - Booking aktif & mendatang,
   - Tagihan menunggu,
   - Notifikasi terbaru.

3. **Alert atau promo aktif**
   - promo penting,
   - informasi singkat,
   - satu tombol aksi yang jelas.

4. **Prasyarat booking**
   - progress kelengkapan data,
   - checklist item yang sudah/belum lengkap.

5. **Aksi cepat**
   - tambah kucing,
   - edit profil,
   - ajukan grooming,
   - ajukan penitipan,
   - ajukan pet care.

6. **Riwayat atau insight tambahan**
   - transaksi terakhir,
   - booking terakhir,
   - atau rekomendasi layanan.

---

## Navigasi

### Masalah Saat Ini

Menu horizontal saat ini terlalu banyak dan membuat beban baca tinggi. Pengguna harus memindai banyak item sekaligus sebelum masuk ke konten utama.

### Rekomendasi

Navigasi dibagi menjadi tiga kelompok:

- **Menu utama**: Dashboard, Notifikasi, Bantuan, Riwayat.
- **Layanan**: Grooming, Penitipan, Pet Care, Kucing Saya.
- **Akun**: Profil, Ubah Password, Logout.

### Pola yang Disarankan

- Di desktop, gunakan navbar yang lebih ringkas dengan dropdown untuk item layanan.
- Di mobile, gunakan menu hamburger.
- Avatar atau nama pengguna di kanan atas sebaiknya menjadi entry point untuk menu akun.
- Item aktif harus terlihat jelas dengan indikator visual yang konsisten.

---

## Design Tokens

### Warna

| Token | Fungsi | Nilai |
|---|---|---|
| Primary | Aksi utama / brand | `#F97316` |
| Primary Hover | Hover aksi utama | `#EA580C` |
| Success | Status sukses | `#16A34A` |
| Success Background | Background sukses | `#DCFCE7` |
| Warning | Perhatian | `#D97706` |
| Warning Background | Background warning | `#FEF3C7` |
| Danger | Error / hapus | `#DC2626` |
| Text Primary | Teks utama | `#111827` |
| Text Secondary | Teks sekunder | `#6B7280` |
| Border | Garis komponen | `#E5E7EB` |
| Page Background | Latar halaman | `#F8FAFC` |
| Card Background | Latar card | `#FFFFFF` |

### Aturan Penggunaan Warna

- Gunakan **primary** hanya untuk aksi utama.
- Gunakan **success** hanya untuk status positif atau konfirmasi berhasil.
- Gunakan **warning** untuk kondisi yang perlu perhatian tetapi bukan error.
- Gunakan **danger** untuk error, pembatalan, dan aksi destruktif.
- Hindari memakai terlalu banyak warna terang dalam satu area agar prioritas tetap jelas.

### Tipografi

| Elemen | Ukuran | Weight |
|---|---|---|
| Page Title | 28px | 700 |
| Section Title | 20px | 600 |
| Card Title | 16px | 600 |
| Body Text | 14px | 400 |
| Secondary Text | 13px–14px | 400 |
| Button Text | 14px | 600 |
| Helper Text | 12px | 400 |

### Spacing

Gunakan sistem spacing berbasis 8:

| Token | Nilai |
|---|---|
| XS | 4px |
| SM | 8px |
| MD | 12px |
| LG | 16px |
| XL | 24px |
| 2XL | 32px |

### Radius dan Border

- Radius default komponen: `12px`
- Radius tombol kecil: `10px`
- Border default: `1px solid #E5E7EB`
- Shadow gunakan sangat halus, atau tanpa shadow bila ingin tampilan lebih bersih.

---

## Komponen

### Card

Semua card harus mengikuti pola yang sama:

- background putih,
- border tipis abu muda,
- radius 12px,
- padding 20px–24px,
- judul di kiri atas,
- aksi sekunder di kanan atas bila ada.

Struktur card:

1. Judul
2. Informasi utama
3. Informasi sekunder atau status
4. Aksi

### Button

Gunakan hanya empat tipe tombol:

| Jenis | Kegunaan | Tampilan |
|---|---|---|
| Primary | Aksi paling penting | Fill primary, teks putih |
| Secondary | Aksi pendukung | Outline primary / neutral |
| Ghost | Aksi ringan | Tanpa fill, hover lembut |
| Danger | Aksi berisiko | Fill atau outline merah |

Aturan:

- Dalam satu section, usahakan hanya ada **satu** tombol primary.
- Aksi ringan seperti “Lihat semua” atau “Detail” lebih baik berbentuk text link atau ghost button.
- Ukuran tombol harus seragam dalam satu area.

### Link Aksi

Text link harus digunakan untuk:

- lihat detail,
- lihat semua,
- kelola data,
- buka riwayat.

Link aksi tidak boleh terlihat lebih dominan daripada tombol primary.

### Badge / Status

Status harus tampil menggunakan kombinasi **warna + teks**.

Contoh status:

- Aktif
- Menunggu pembayaran
- Lengkap
- Belum lengkap
- Promo aktif
- Selesai
- Dibatalkan

### Form Input

Aturan form:

- tinggi input konsisten,
- label selalu di atas field,
- helper text dan error text selalu berada di bawah field,
- state minimal: default, focus, error, disabled,
- placeholder tidak menggantikan label.

---

## Guideline Konten dan Microcopy

Bahasa antarmuka harus konsisten dan sederhana.

### Aturan Penulisan

- Gunakan **Bahasa Indonesia** yang ringkas dan jelas.
- Gunakan **Title Case** untuk judul section.
- Gunakan **Sentence case** untuk deskripsi dan helper text.
- Gunakan kata kerja yang konsisten untuk aksi.

### Rekomendasi Istilah

| Konteks | Gunakan |
|---|---|
| Memulai layanan | Ajukan Grooming / Ajukan Penitipan / Ajukan Pet Care |
| Mengubah data | Kelola Data / Edit Profil |
| Melihat informasi | Lihat Detail / Lihat Semua |
| Menambah entitas | Tambah Kucing |

Hindari mencampur istilah seperti “Booking Grooming”, “Ajukan grooming”, dan “Grooming sekarang” jika fungsinya sama.

---

## Empty State

Setiap kondisi kosong harus tetap informatif dan mengarahkan pengguna.

Format empty state yang disarankan:

- judul singkat,
- penjelasan satu kalimat,
- satu CTA utama.

Contoh:

### Booking kosong
**Belum ada booking aktif**  
Anda belum memiliki booking grooming, penitipan, atau pet care saat ini.  
**CTA:** Ajukan layanan

### Tagihan kosong
**Tidak ada tagihan menunggu**  
Semua transaksi Anda sudah dibayar atau belum ada tagihan baru.  
**CTA:** Lihat riwayat transaksi

### Notifikasi kosong
**Belum ada notifikasi**  
Informasi terbaru dari sistem akan muncul di sini.  
**CTA:** Muat ulang atau lihat riwayat aktivitas

---

## Checklist Prasyarat Booking

Section **Prasyarat Booking** sebaiknya diubah menjadi checklist progress, bukan hanya daftar biasa.

### Struktur yang Disarankan

- tampilkan progress, misalnya: **2 dari 3 persyaratan selesai**,
- beri ikon centang untuk item yang lengkap,
- beri indikator perhatian untuk item yang belum lengkap,
- sediakan aksi langsung di setiap item.

### Contoh Item

- Profil & Alamat — lengkap
- Data Kucing — lengkap
- Metode Kontak Darurat — belum lengkap

Manfaatnya adalah pengguna langsung paham apa yang harus diselesaikan sebelum booking.

---

## Shortcut / Aksi Cepat

Section shortcut perlu tampil lebih terstruktur.

### Aturan

- Gunakan judul **Aksi Cepat**.
- Semua tombol memiliki tinggi dan style yang konsisten.
- Bila memungkinkan, tambahkan ikon kecil.
- Pada mobile, tombol bisa berubah menjadi grid 2 kolom atau stack vertikal.

### Aksi yang Direkomendasikan

- Tambah Kucing
- Edit Profil
- Ajukan Grooming
- Ajukan Penitipan
- Ajukan Pet Care

---

## Responsiveness

### Desktop

- Gunakan container maksimum sekitar 1200px–1280px.
- Ringkasan utama dapat menggunakan 2–3 kolom.
- Navbar tampil horizontal tetapi tidak terlalu padat.

### Tablet

- Grid mulai diperkecil menjadi 2 kolom.
- Sebagian menu dapat dipindah ke dropdown.

### Mobile

- Semua card menjadi satu kolom.
- Navbar berubah menjadi hamburger menu.
- Tombol primary dibuat full width bila perlu.
- Jarak antar elemen diperbesar agar nyaman disentuh.

---

## Accessibility

Standar minimum yang wajib dijaga:

- ukuran teks isi minimal 14px,
- kontras teks cukup tinggi,
- area klik minimal 40x40px,
- status tidak boleh bergantung pada warna saja,
- focus state keyboard harus terlihat jelas,
- error message harus spesifik dan mudah dimengerti.

---

## Prioritas Implementasi

Urutan implementasi yang disarankan:

1. Rapikan struktur navigasi.
2. Tetapkan design tokens: warna, tipografi, spacing, radius.
3. Standarkan komponen dasar: button, card, badge, form.
4. Perbaiki hirarki dashboard.
5. Ubah empty state agar lebih komunikatif.
6. Ubah prasyarat booking menjadi checklist progress.
7. Uji konsistensi di desktop, tablet, dan mobile.

---

## Ringkasan Standar Konsistensi

Agar seluruh aplikasi konsisten, gunakan aturan inti berikut:

- Halaman menggunakan background `#F8FAFC`.
- Card menggunakan background putih, border tipis, radius 12px.
- Padding card konsisten di 20px–24px.
- Primary action selalu berwarna oranye.
- Status sukses selalu hijau.
- Warning selalu kuning.
- Body text minimal 14px.
- Jarak antar section utama minimal 24px.
- Setiap empty state harus memiliki penjelasan dan CTA.
- Satu section idealnya hanya punya satu primary action.

---

## Penutup

Dengan menerapkan guideline ini, dashboard Petshop akan terasa lebih konsisten, lebih mudah digunakan, dan lebih profesional. Fokus utama bukan hanya membuat tampilan lebih cantik, tetapi juga memastikan pengguna dapat memahami informasi dan menyelesaikan aksi penting dengan cepat.

Dokumen ini dapat dijadikan dasar untuk membuat:

- design system sederhana,
- library komponen frontend,
- SOP UI review,
- dan acuan redesign halaman lain di aplikasi.