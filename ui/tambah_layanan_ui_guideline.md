# UI Design Guideline – Tambah Layanan Pet Care

> **Modul:** Pet Care Admin  
> **Audience:** Staff / Owner  
> **Route:** `/admin/pet-care/layanan/tambah`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/pet-care/layanan/form.php`](../src/Views/admin/pet-care/layanan/form.php)  
> **Controller:** `Admin\PetCareController`  
> **Related:** [`layanan_petcare_ui_guideline.md`](./layanan_petcare_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/pet-care/layanan/form.php`](../src/Views/admin/pet-care/layanan/form.php)

## 1. Critical UX Issue (Core Finding)
Form ini terlihat sederhana dan clean, tetapi sebenarnya belum memiliki **input validation layer dan information hierarchy yang jelas**.

Masalah utama:
- semua field dianggap sama penting
- tidak ada guidance untuk input harga & durasi
- status “Aktif” default tanpa konfirmasi risiko
- tidak ada preview atau review sebelum submit

Ini meningkatkan risiko input tidak konsisten saat data layanan bertambah. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Form sudah clean dan minimal
- Layout single-column sudah tepat untuk input admin
- Harga & durasi dipisah dengan baik
- Status dropdown sudah ada
- CTA “Simpan Layanan” sudah jelas
- Tidak ada helper text atau constraint input
- Tidak ada validation feedback (error/success)
- Tidak ada cancel/back clarity visual

---

## 3. Core UX Problems

### 3.1 Lack of Input Guidance
User tidak dibantu untuk:
- format harga (contoh: 150000 vs Rp 150.000)
- batasan durasi
- deskripsi ideal layanan

---

### 3.2 Missing Validation System
Tidak terlihat:
- required field indicator
- error state
- inline validation

---

### 3.3 Weak Status Risk Design
Default “Aktif”:
- berisiko user langsung publish tanpa review

---

## 4. Recommended Improvements

### 4.1 Add Form Structure Layer (CRITICAL)

Ubah menjadi 3 section:

1. Basic Information
   - Nama layanan
   - Deskripsi

2. Pricing & Duration
   - Harga estimasi
   - Estimasi durasi

3. System Settings
   - Status (Aktif / Draft)

---

### 4.2 Improve Input Guidance (HIGH IMPACT)

Tambahkan:
- placeholder contoh:
  "contoh: 150000"
- helper text:
  "tanpa titik atau koma"
- durasi format:
  "dalam menit (contoh: 30)"

---

### 4.3 Add Validation Layer

- required field indicator (*)
- inline error message
- disable submit until valid

---

### 4.4 Improve Status Safety

Ganti default:
- Draft (default)
- Aktif hanya setelah review

---

### 4.5 Add Review State (Optional but powerful)

Before submit:
- summary modal:
  - nama layanan
  - harga
  - durasi
  - status

---

## 5. Component Recommendations

### Form Section Card
- grouped input blocks
- section title
- spacing consistent 24–32px

---

### Input Field Enhancement
- helper text under input
- error state inline
- numeric formatting support (price)

---

### Status Selector
- toggle or segmented control:
  Draft | Aktif

---

### Submit Flow
- primary button: Simpan Layanan
- secondary: Batal / Kembali

---

## 6. Layout Structure (Improved)

1. Page Header (title + back navigation)
2. Form Section Cards
3. Pricing & Duration block
4. Status block
5. Action buttons
6. Optional review modal

---

## 7. High Priority Fixes

1. Add form sectioning (CRITICAL)
2. Add validation + helper text (HIGH)
3. Change default status to Draft (HIGH)
4. Improve input formatting guidance (MEDIUM)
5. Add review confirmation step (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Form sectioning (Basic Info, Pricing, Settings cards)
- [ ] **[HIGH]** Validation + helper text inline
- [ ] **[HIGH]** Default status = Draft
- [ ] **[MEDIUM]** Input formatting guidance (harga, durasi)
- [ ] **[MEDIUM]** Review confirmation step sebelum save

---

## 9. Cross-References

### Guideline Terkait

- [`layanan_petcare_ui_guideline.md`](./layanan_petcare_ui_guideline.md)

### Partial / File Reuse

- Lihat pola global di [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) §18 Pola UX global

### Pola Global yang Berlaku

- Admin Table — lihat MASTER §18 Pola UX global
- Destructive Action — lihat MASTER §18 Pola UX global
- Status Badge — lihat MASTER §18 Pola UX global
- Form Sections — lihat MASTER §18 Pola UX global

