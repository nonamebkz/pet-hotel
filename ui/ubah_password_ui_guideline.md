# UI Design Guideline – Ubah Password Admin

> **Modul:** Auth  
> **Audience:** Staff / Owner  
> **Route:** `/admin/change-password`  
> **Layout:** `admin`  
> **View:** [`src/Views/auth/admin/change-password.php`](../src/Views/auth/admin/change-password.php)  
> **Controller:** `Auth\StaffAuthController`  
> **Related:** [`login_admin_ui_guideline.md`](./login_admin_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/auth/admin/change-password.php`](../src/Views/auth/admin/change-password.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini terlihat sangat sederhana dan aman secara visual, tetapi secara sistem belum memenuhi standar **secure password update flow**.

Masalah utama:
- tidak ada show/hide password toggle
- tidak ada password strength indicator
- tidak ada rule password (min length, complexity)
- tidak ada indikator keamanan akun saat perubahan password
- tidak ada opsi recovery atau fallback context
- tidak ada warning jika password lemah atau reuse

Ini membuat fitur terlihat aman, tetapi sebenarnya rentan secara UX security. [Kemungkinan Besar]

---

## 2. Key UX Observations

- struktur form sudah benar (lama → baru → konfirmasi)
- layout clean dan minimal
- CTA jelas (“Simpan Password”)
- tidak ada helper text sama sekali
- tidak ada validation feedback
- tidak ada feedback success/error state
- tidak ada visibility toggle (major usability gap)

---

## 3. Core UX Problems

### 3.1 Weak Password Security UX
Saat ini user:
- tidak tahu kekuatan password
- tidak tahu standar minimal password

---

### 3.2 Missing Input Visibility Control
Password field:
- tidak bisa show/hide
- meningkatkan error input

---

### 3.3 No Validation Feedback System
Tidak ada:
- mismatch detection realtime
- error inline message
- success confirmation state

---

### 3.4 No Security Context
User tidak diberi tahu:
- apakah ini akan logout semua device
- apakah session lain akan terputus

---

## 4. Recommended Improvements

### 4.1 Add Password Strength System (CRITICAL)

Tambahkan:
- strength meter (weak / medium / strong)
- rule hint:
  - min 8 characters
  - kombinasi huruf & angka
  - optional symbol

---

### 4.2 Add Show/Hide Password Toggle (HIGH IMPACT)

Setiap field:
- icon eye toggle
- default hidden

---

### 4.3 Add Real-time Validation (CRITICAL)

Tambahkan:
- password mismatch detection
- inline error message
- disable submit until valid

---

### 4.4 Add Security Impact Notice (HIGH IMPACT)

Tambahkan info:
- “Mengubah password akan mengeluarkan semua sesi aktif”

---

### 4.5 Add Confirmation Step (MEDIUM)

Before submit:
- modal review:
  - confirm password change
  - security warning

---

## 5. Component Recommendations

### Password Field Component
- input + eye toggle
- strength bar under input
- helper text rules

---

### Validation System
- real-time check
- red error state
- success green state

---

### Security Notice Banner
- warning icon
- session logout info

---

### Confirmation Modal
- confirm / cancel
- security message

---

## 6. Layout Structure (Improved)

1. Page header
2. Security warning banner
3. Form section
   - old password
   - new password
   - confirm password
4. password strength indicator
5. submit button
6. confirmation modal

---

## 7. High Priority Fixes

1. Add password strength meter (CRITICAL)
2. Add validation system (CRITICAL)
3. Add show/hide toggle (HIGH)
4. Add security impact warning (HIGH)
5. Add confirmation modal (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Password strength meter untuk password baru
- [x] **[CRITICAL]** Validasi inline: min length, match confirm, strength rules
- [ ] **[HIGH]** Show/hide toggle pada semua field password
- [ ] **[HIGH]** Security impact warning (session logout setelah ubah password)
- [ ] **[MEDIUM]** Confirmation modal sebelum submit

---

## 9. Cross-References

### Guideline Terkait

- [`login_admin_ui_guideline.md`](./login_admin_ui_guideline.md)

### Partial / File Reuse

- [`src/Views/partials/flash.php`](../src/Views/partials/flash.php)
- Flow auth terkait: [`login_admin_ui_guideline.md`](./login_admin_ui_guideline.md)

### Pola Global yang Berlaku

- Form Sections — lihat MASTER §18 Pola UX global

