# UI Design Guideline – Layanan Pet Care

> **Modul:** Pet Care Admin  
> **Audience:** Staff / Owner  
> **Route:** `/admin/pet-care/layanan`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/pet-care/layanan/index.php`](../src/Views/admin/pet-care/layanan/index.php)  
> **Controller:** `Admin\PetCareController`  
> **Related:** [`tambah_layanan_ui_guideline.md`](./tambah_layanan_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/pet-care/layanan/index.php`](../src/Views/admin/pet-care/layanan/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini sudah memiliki struktur tabel yang rapi, tetapi masih berfungsi sebagai **static CRUD list**, bukan service management system yang optimal.

Masalah utama:
- tidak ada hierarchy antar layanan
- aksi Edit/Hapus terlalu langsung (risk tinggi)
- tidak ada status management yang benar-benar bermakna
- tidak ada sorting/filtering untuk operasional

Ini membuat halaman sulit digunakan saat jumlah layanan bertambah. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Tabel sudah clean dan readable
- “Tambah Layanan” sudah jelas sebagai primary action
- Status “Aktif” sudah ada tapi belum punya sistem state yang kuat
- Harga dan durasi sudah ditampilkan dengan baik
- Deskripsi layanan masih terlalu kecil (kurang scanning-friendly)
- Aksi Edit/Hapus terlalu exposed (raw destructive action)

---

## 3. Core UX Problems

### 3.1 Flat Service Structure
Semua layanan terlihat sama penting:
- Konsultasi
- Luka
- Vaksinasi

Tidak ada:
- featured service
- recommended service
- medical priority tagging

---

### 3.2 Weak Action Safety Layer
- Hapus langsung tanpa confirmation pattern
- Edit langsung tanpa preview/detail mode
Ini meningkatkan risiko human error. [Kemungkinan Besar]

---

### 3.3 Missing Operational Tools
Tidak ada:
- search service
- filter status (aktif/nonaktif)
- sorting harga/durasi
- bulk actions

---

## 4. Recommended Improvements

### 4.1 Upgrade Table → Service Management System (CRITICAL)

Tambahkan:
- search bar (nama layanan)
- filter status
- sorting harga & durasi
- pagination

---

### 4.2 Improve Service Hierarchy

Tambahkan badge:
- Recommended
- Medical
- Basic Care

Contoh:
- Vaksinasi → Medical
- Konsultasi → Recommended

---

### 4.3 Improve Action Safety (HIGH IMPACT)

Ubah aksi:
- Edit → tombol utama
- Hapus → masuk dropdown “More (⋮)”
- Tambahkan confirm modal untuk delete

---

### 4.4 Enhance Visual Priority

Perbaiki tampilan:
- harga dibuat lebih bold & prominent
- durasi sebagai secondary text
- deskripsi dibuat lebih readable (max 2 lines)

---

### 4.5 Add Service Status System

Status tidak hanya “Aktif”, tapi:
- Active
- Inactive
- Draft (optional future)

---

## 5. Component Recommendations

### Service Table Row (Improved)
- Service name (bold)
- Badge category (Medical / Basic / Recommended)
- Description (2-line clamp)
- Price (bold)
- Duration (muted)
- Status pill
- Action menu (⋮)

---

### Action Menu
- Edit
- Toggle Active/Inactive
- Delete (danger zone)

---

### Service Header Controls
- Search input
- Filter dropdown
- + Tambah Layanan button

---

## 6. Layout Structure (Improved)

1. Page Header (title + CTA)
2. Control Bar (search + filter)
3. Service Table
4. Pagination
5. Confirmation modals (delete/edit state)

---

## 7. High Priority Fixes

1. Move Delete into safe action menu (CRITICAL)
2. Add search + filter system (HIGH)
3. Add service categorization (HIGH)
4. Improve visual hierarchy pricing (MEDIUM)
5. Add sorting/pagination (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Delete di action menu ⋮ + confirm modal
- [ ] **[HIGH]** Search + filter system
- [ ] **[HIGH]** Service categorization badges
- [ ] **[MEDIUM]** Visual hierarchy pricing
- [ ] **[MEDIUM]** Sorting dan pagination

---

## 9. Cross-References

### Guideline Terkait

- [`tambah_layanan_ui_guideline.md`](./tambah_layanan_ui_guideline.md)

### Partial / File Reuse

- Lihat pola global di [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) §18 Pola UX global

### Pola Global yang Berlaku

- Admin Table — lihat MASTER §18 Pola UX global
- Destructive Action — lihat MASTER §18 Pola UX global
- Status Badge — lihat MASTER §18 Pola UX global

