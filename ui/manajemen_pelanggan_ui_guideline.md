# UI Design Guideline – Manajemen Pelanggan

> **Modul:** CRM  
> **Audience:** Staff / Owner  
> **Route:** `/admin/pelanggan`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/pelanggan/index.php`](../src/Views/admin/pelanggan/index.php)  
> **Controller:** `Admin\PelangganController`  
> **Related:** [`manajemen_staff_ui_guideline.md`](./manajemen_staff_ui_guideline.md)  
> **Master:** [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md)

## 0. Cara Eksekusi

- Baca [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) untuk tokens & pola global
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/pelanggan/index.php`](../src/Views/admin/pelanggan/index.php)

## 1. Critical UX Issue (Core Finding)
Tampilan ini sudah benar secara struktur tabel, tetapi masih berfungsi sebagai **data listing pasif**, bukan sistem manajemen pelanggan.

Artinya:
- user hanya melihat data
- tidak ada insight atau aksi cepat
- tidak ada hierarchy pelanggan penting vs biasa

Ini membuat halaman kurang berguna untuk operasional harian. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Search bar sudah ada tetapi belum jelas apakah realtime atau submit-based
- Tabel terlalu “flat” tanpa sorting atau emphasis
- “Jumlah Kucing = 0” tidak diberi konteks (misalnya new user vs inactive)
- Tidak ada pagination / data scaling behavior
- Action hanya “Detail”, tidak ada quick actions
- Tidak ada customer segmentation (VIP / active / inactive)

---

## 3. Core UX Problems

### 3.1 Data Without Hierarchy
Semua pelanggan terlihat sama penting:
- tidak ada active customer highlight
- tidak ada engagement indicator
- tidak ada recent activity

---

### 3.2 Weak Action System
Saat ini hanya:
- Detail

Tidak ada:
- Edit
- View cats
- Contact user
- Tag customer

---

### 3.3 Missing Operational Context
Dashboard tidak menjawab:
- pelanggan aktif berapa?
- pelanggan baru minggu ini?
- pelanggan tanpa kucing?

---

## 4. Recommended Improvements

### 4.1 Upgrade Table → Smart Customer Table (CRITICAL)

Tambahkan:
- sorting (nama, tanggal daftar, jumlah kucing)
- filter (active, new, no pets, VIP)
- pagination or infinite scroll

---

### 4.2 Add Customer Segmentation Layer
Tambahkan badge:
- New Customer (≤7 hari)
- Active (booking pernah)
- No Pets (jumlah kucing = 0)

---

### 4.3 Improve Search UX
- debounce search (300–500ms)
- highlight matched text
- support multi-field search (nama/email/telepon)

---

### 4.4 Enhance Action Column
Ganti “Detail” menjadi:
- View Profile
- View Cats
- Quick Message (optional)
- More menu (⋮)

---

### 4.5 Add Row Interaction
- row click → open side drawer (customer profile)
- hover state subtle highlight

---

## 5. Component Recommendations

### Customer Table Row
- name (bold)
- email (secondary)
- phone (optional muted)
- badge (status)
- jumlah kucing (highlight number)
- date registered

---

### Customer Detail Drawer
- profile summary
- list of cats
- booking history
- quick actions

---

### Filter Bar
- status filter
- date range
- segmentation filter

---

## 6. Layout Structure (Improved)

1. Page Header (title + summary stats)
2. Search + Filter Bar
3. Customer KPI mini cards (optional)
4. Smart Table
5. Pagination / load more
6. Detail Drawer (on click row)

---

## 7. High Priority Fixes

1. Add segmentation system (CRITICAL)
2. Upgrade table to interactive system (HIGH)
3. Add row-level actions (HIGH)
4. Improve search UX (MEDIUM)
5. Add empty state + loading skeleton (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Segmentation badges (aktif, baru, dormant)
- [ ] **[HIGH]** Interactive table: row click → detail drawer
- [ ] **[HIGH]** Row-level actions menu ⋮
- [ ] **[MEDIUM]** Search UX dengan debounce/feedback
- [ ] **[MEDIUM]** Empty state + loading skeleton

---

## 9. Cross-References

### Guideline Terkait

- [`manajemen_staff_ui_guideline.md`](./manajemen_staff_ui_guideline.md)

### Partial / File Reuse

- Lihat pola global di [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) section C

### Pola Global yang Berlaku

- Admin Table — lihat MASTER section C
- Destructive Action — lihat MASTER section C
- Status Badge — lihat MASTER section C
- Filter Bar — lihat MASTER section C
- Empty State — lihat MASTER section C

