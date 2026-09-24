# UI Design Guideline – Tambah Staff

> **Modul:** Staff  
> **Audience:** Owner  
> **Route:** `/admin/staff/tambah`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/staff/form.php`](../src/Views/admin/staff/form.php)  
> **Controller:** `Admin\StaffManagementController`  
> **Related:** [`manajemen_staff_ui_guideline.md`](./manajemen_staff_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/staff/form.php`](../src/Views/admin/staff/form.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini sudah cukup clean secara visual, tetapi secara sistem belum merepresentasikan **secure account provisioning flow**.

Masalah utama:
- tidak ada role selection (Owner/Admin/Staff)
- password dibuat manual tanpa strength indicator
- tidak ada auto-generate password option
- status “Aktif” terlalu early-binding (berisiko akun langsung aktif tanpa approval)
- tidak ada review sebelum akun dibuat

Ini membuat proses pembuatan akun staff berisiko dari sisi keamanan dan scaling. [Kemungkinan Besar]

---

## 2. Key UX Observations

- form sederhana dan mudah dipahami
- struktur field sudah logis (nama → email → username → password)
- konfirmasi password sudah ada (good)
- status aktif sudah tersedia
- tidak ada security enhancement (strength meter, rules, visibility toggle)
- tidak ada role-based access setup

---

## 3. Core UX Problems

### 3.1 Missing Role & Permission Setup
Saat ini semua staff dibuat tanpa:
- role assignment
- permission boundary

---

### 3.2 Weak Password Flow
Masalah:
- password dibuat manual
- tidak ada indikator keamanan password
- tidak ada opsi auto-generate secure password

---

### 3.3 Unsafe Activation Flow
Default status “Aktif”:
- akun langsung aktif tanpa review
- tidak ada approval step

---

### 3.4 No Provisioning Review
Tidak ada:
- summary akun sebelum disimpan
- konfirmasi final

---

## 4. Recommended Improvements

### 4.1 Add Role Selection (CRITICAL)
Tambahkan field:
- Owner
- Admin
- Staff

Role ini menentukan:
- akses fitur
- visibility menu

---

### 4.2 Improve Password System (HIGH IMPACT)

Tambahkan:
- password strength meter
- show/hide toggle
- auto-generate password button

---

### 4.3 Improve Status Flow (HIGH IMPACT)

Ubah default:
- Draft / Pending (bukan Aktif)

Aktif hanya setelah:
- review / approval

---

### 4.4 Add Account Review Step (CRITICAL)

Before submit:
- preview data akun
- role
- status
- username/email

---

### 4.5 Add Security Helpers

Tambahkan:
- password rule hint:
  "min 8 karakter, kombinasi huruf & angka"
- email validation inline

---

## 5. Component Recommendations

### Form Sections (Re-structure)

1. Personal Info
- Nama
- Email

2. Account Info
- Username
- Password
- Confirm Password

3. Access Control
- Role
- Status

---

### Password Field Enhancement
- strength bar
- show/hide icon
- generate button

---

### Status Selector
- Draft
- Active
- Suspended

---

### Review Modal
- summary card
- confirm button
- cancel button

---

## 6. Layout Structure (Improved)

1. Page header
2. Form section cards
3. Role & access section
4. Password security section
5. Status section
6. Review modal before submit

---

## 7. High Priority Fixes

1. Add role-based access control (CRITICAL)
2. Improve password security flow (CRITICAL)
3. Change default status to Draft (HIGH)
4. Add review/confirmation step (HIGH)
5. Add validation + helper text (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Role-based access control selector
- [x] **[CRITICAL]** Password security: strength meter, generate, show/hide
- [ ] **[HIGH]** Default status = Draft
- [ ] **[HIGH]** Review/confirmation modal sebelum create
- [ ] **[MEDIUM]** Validation + helper text

---

## 9. Cross-References

### Guideline Terkait

- [`manajemen_staff_ui_guideline.md`](./manajemen_staff_ui_guideline.md)

### Partial / File Reuse

- Lihat pola global di [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) §18 Pola UX global

### Pola Global yang Berlaku

- Form Sections — lihat MASTER §18 Pola UX global
- Admin Table — lihat MASTER §18 Pola UX global
- Destructive Action — lihat MASTER §18 Pola UX global
- Status Badge — lihat MASTER §18 Pola UX global

