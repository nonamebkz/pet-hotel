# UI Design Guideline – Tambah Kucing

> **Modul:** Customer  
> **Audience:** Pelanggan  
> **Route:** `/kucing/tambah`  
> **Layout:** `pelanggan`  
> **View:** [`src/Views/kucing/form.php`](../src/Views/kucing/form.php)  
> **Controller:** `Pelanggan\KucingController`  
> **Related:** [`grooming_ui_guideline.md`](./grooming_ui_guideline.md)  
> **Master:** [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md)

## 0. Cara Eksekusi

- Baca [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) untuk tokens & pola global
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/kucing/form.php`](../src/Views/kucing/form.php)

## 1. Critical UX Issue (Core Finding)
Form ini sudah lengkap secara data, tetapi terlalu “langsung tampil semua” tanpa struktur input yang jelas.

Akibatnya:
- User merasa form panjang dan melelahkan
- Field optional dan mandatory tidak dibedakan dengan kuat
- Bagian vaksinasi terlalu “berat” untuk tahap awal input

Ini meningkatkan risiko form abandonment. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Layout 2-column tidak konsisten untuk semua field
- Tidak ada grouping berdasarkan konteks data (profil, kesehatan, vaksin)
- Label optional tidak cukup jelas secara visual
- Date format masih mm/dd/yyyy (tidak sesuai lokal Indonesia)
- Upload file (foto & sertifikat) tidak punya preview state
- Section vaksin terlalu “advanced” untuk form utama

---

## 3. Core UX Problems

### 3.1 Lack of Structured Flow
Semua field ditampilkan sekaligus tanpa tahapan:
- Basic info
- Health info
- Medical/vaccination info

User harus berpikir sendiri urutan input.

---

### 3.2 Weak Visual Hierarchy
Tidak ada pembeda antara:
- mandatory vs optional
- primary vs secondary information

---

### 3.3 Overloaded Medical Section
Riwayat vaksin:
- terlalu teknis
- terlalu dini untuk input awal
- tidak ada progressive disclosure

---

## 4. Recommended Improvements

### 4.1 Convert to Stepper Form (HIGH IMPACT)
Ubah menjadi 3 langkah:

1. Basic Info
   - Nama
   - Ras
   - Jenis kelamin
   - Tanggal lahir

2. Physical Info
   - Berat badan
   - Foto kucing

3. Health Info (Optional Expand)
   - Alergi
   - Riwayat vaksin (accordion)

---

### 4.2 Progressive Disclosure for Vaksin
- Collapse default
- Expand only jika user klik “Tambah Riwayat Vaksin”
- Tambahkan tooltip: “Opsional untuk grooming, wajib untuk pet hotel”

---

### 4.3 Improve File Upload UX
Tambahkan:
- preview image kucing
- drag & drop
- file size indicator
- replace/remove action

---

### 4.4 Fix Date & Locale UX
- Ganti format ke DD/MM/YYYY
- Tambahkan date picker dengan locale Indonesia
- Tambahkan age auto-calculation (optional enhancement)

---

### 4.5 Improve Input Clarity
- Required field: bold label + red dot
- Optional field: muted label
- Add helper text per field penting

---

## 5. Component Recommendations

### Form Section Card
- Title per section
- Divider jelas
- collapsible optional section

---

### Input Field Upgrade
- error state inline
- helper text under input
- consistent height (no shifting layout)

---

### Vaccination Row Component
- repeatable row
- add/remove row button
- inline validation

---

## 6. Layout Structure (Improved)

1. Page Header (Back + Title)
2. Stepper navigation (Basic → Physical → Health)
3. Form container (card-based)
4. Conditional sections (vaccination)
5. Sticky Save Button

---

## 7. High Priority Fixes

1. Convert to stepper form (CRITICAL)
2. Improve optional vs required clarity (HIGH)
3. Collapse vaccination section (HIGH)
4. Fix date localization (MEDIUM)
5. Add file preview for uploads (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Stepper form (multi-step progressive disclosure)
- [ ] **[HIGH]** Required vs optional field clarity
- [ ] **[HIGH]** Vaccination section collapsible
- [ ] **[MEDIUM]** Date localization (format Indonesia)
- [ ] **[MEDIUM]** File preview untuk upload foto

---

## 9. Cross-References

### Guideline Terkait

- [`grooming_ui_guideline.md`](./grooming_ui_guideline.md)

### Partial / File Reuse

- [`src/Views/partials/vaksin-row.php`](../src/Views/partials/vaksin-row.php)

### Pola Global yang Berlaku

- Form Sections — lihat MASTER section C

