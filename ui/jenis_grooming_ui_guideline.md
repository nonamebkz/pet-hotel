# UI Design Guideline – Jenis Grooming

> **Modul:** Grooming Admin  
> **Audience:** Staff / Owner  
> **Route:** `/admin/grooming/layanan`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/grooming/layanan/index.php`](../src/Views/admin/grooming/layanan/index.php)  
> **Controller:** `Admin\GroomingController`  
> **Related:** [`kuota_grooming_ui_guideline.md`](./kuota_grooming_ui_guideline.md), [`booking_grooming_ui_guideline.md`](./booking_grooming_ui_guideline.md), [`verifikasi_bukti_transfer_ui_guideline.md`](./verifikasi_bukti_transfer_ui_guideline.md)  
> **Master:** [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md)

## 0. Cara Eksekusi

- Baca [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) untuk tokens & pola global
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/grooming/layanan/index.php`](../src/Views/admin/grooming/layanan/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini sudah cukup rapi secara struktur tabel, tetapi masih belum menjadi **service configuration system yang aman dan scalable**.

Masalah utama:
- aksi Edit/Hapus terlalu exposed (risk tinggi)
- tidak ada grouping atau kategori grooming
- tidak ada hierarchy layanan (medical vs basic vs premium)
- tab UI belum benar-benar mengubah konteks data

Ini membuat halaman berisiko saat jumlah layanan bertambah. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Tab sudah ada (Jenis, Kuota, Booking, Verifikasi Bukti) tetapi belum jelas perbedaan konteks data
- Table layout sudah clean dan readable
- Harga dan status sudah jelas
- Aksi Edit/Hapus terlalu langsung (tanpa safety layer)
- Tidak ada search/filter/sorting
- Tidak ada indikator popular/recommended service
- Deskripsi layanan cukup kecil dan kurang hierarki

---

## 3. Core UX Problems

### 3.1 Flat Service Architecture
Semua jenis grooming dianggap setara:
- Grooming Jamur
- Grooming Kutu
- Grooming Lengkap

Tidak ada:
- prioritization
- categorization
- business importance tagging

---

### 3.2 Dangerous Action Exposure
- Delete langsung terlihat di table
- tidak ada confirm modal / soft delete pattern
Ini meningkatkan risiko kesalahan operasional. [Kemungkinan Besar]

---

### 3.3 Weak Tab System
Tab saat ini:
- terlihat seperti navigation
- tapi belum mengubah state data secara signifikan

---

## 4. Recommended Improvements

### 4.1 Upgrade Table → Service Management System (CRITICAL)

Tambahkan:
- search bar (nama layanan)
- filter status (aktif/nonaktif)
- sorting harga
- pagination

---

### 4.2 Add Service Categorization Layer

Tambahkan badge:
- Medical Care
- Preventive Care
- Full Service
- Basic Care

Contoh:
- Grooming Kutu → Medical Care
- Grooming Lengkap → Full Service

---

### 4.3 Improve Action Safety (HIGH IMPACT)

Ubah aksi:
- Edit → primary action
- Hapus → pindahkan ke menu “⋮”
- Tambahkan confirm modal sebelum delete
- optional: soft delete (nonaktifkan)

---

### 4.4 Enhance Visual Hierarchy

Perbaikan tampilan:
- harga dibuat lebih bold
- status badge lebih compact
- deskripsi 2-line clamp
- nama layanan lebih dominant

---

### 4.5 Improve Tab Functionality

Tab harus:
- mengubah dataset nyata
- bukan hanya visual switch
- contoh:
  - Jenis → service list
  - Kuota → availability system
  - Booking → transactional view
  - Verifikasi → approval system

---

## 5. Component Recommendations

### Service Row (Improved)
- Service name (bold)
- category badge
- description (2 lines max)
- price (bold)
- status pill
- action menu (⋮)

---

### Action Menu
- Edit
- Toggle Active/Inactive
- Delete (danger zone)

---

### Header Controls
- search input
- filter dropdown
- + Tambah Jenis button

---

## 6. Layout Structure (Improved)

1. Page header (title + CTA)
2. Tab navigation (true context switch)
3. Control bar (search + filter)
4. Service table
5. pagination
6. modals (delete confirmation)

---

## 7. High Priority Fixes

1. Move delete to safe action menu (CRITICAL)
2. Add categorization system (HIGH)
3. Improve tab behavior to real data switch (HIGH)
4. Add search/filter/sort (MEDIUM)
5. Improve visual hierarchy (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Delete dipindah ke action menu ⋮ + confirm modal
- [ ] **[HIGH]** Tab grooming admin switch route/dataset nyata
- [ ] **[HIGH]** Kategorisasi layanan (badge/kategori)
- [ ] **[MEDIUM]** Search, filter, sort pada tabel
- [ ] **[MEDIUM]** Visual hierarchy harga vs nama layanan

---

## 9. Cross-References

### Guideline Terkait

- [`kuota_grooming_ui_guideline.md`](./kuota_grooming_ui_guideline.md)
- [`booking_grooming_ui_guideline.md`](./booking_grooming_ui_guideline.md)
- [`verifikasi_bukti_transfer_ui_guideline.md`](./verifikasi_bukti_transfer_ui_guideline.md)

### Partial / File Reuse

- Ekstrak tab grooming admin ke partial `_subnav.php` (shared dengan kuota, booking, verifikasi)
- Lihat pola global di [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) section C

### Pola Global yang Berlaku

- Admin Table — lihat MASTER section C
- Destructive Action — lihat MASTER section C
- Status Badge — lihat MASTER section C
- Tab Navigation — lihat MASTER section C

