# UI Design Guideline – Manajemen Staff

> **Modul:** Staff  
> **Audience:** Owner  
> **Route:** `/admin/staff`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/staff/index.php`](../src/Views/admin/staff/index.php)  
> **Controller:** `Admin\StaffManagementController`  
> **Related:** [`tambah_staff_ui_guideline.md`](./tambah_staff_ui_guideline.md)  
> **Master:** [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md)

## 0. Cara Eksekusi

- Baca [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) untuk tokens & pola global
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/staff/index.php`](../src/Views/admin/staff/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini saat ini masih berfungsi sebagai **basic user table**, bukan sistem manajemen akses (access control system).

Masalah utama:
- tidak ada role/permission structure
- action terlalu banyak dan langsung (Edit / Reset Password / Nonaktifkan)
- tidak ada audit atau aktivitas terakhir
- tidak ada indikasi keamanan akun
- tidak ada search/filter untuk staff

Ini membuat sistem rawan secara operasional dan tidak scalable untuk organisasi yang berkembang. [Kemungkinan Besar]

---

## 2. Key UX Observations

- tabel sudah clean dan readable
- status aktif sudah ada
- informasi dasar (nama, email, username) sudah cukup
- aksi terlalu “danger exposed”
- tidak ada informasi aktivitas user (last login)
- tidak ada role (admin/staff/supervisor)
- tidak ada bulk management

---

## 3. Core UX Problems

### 3.1 Missing Access Control Model
Saat ini semua staff dianggap sama:
- tidak ada role hierarchy
- tidak ada permission level
- tidak ada pembatasan aksi berdasarkan role

---

### 3.2 Unsafe Action Exposure
Aksi kritikal terlalu langsung:
- Reset Password
- Nonaktifkan

Tidak ada:
- confirmation modal
- audit log
- reason for deactivation

---

### 3.3 Lack of Operational Context
Tidak ada:
- last login
- status aktivitas (online/offline/inactive long-term)
- performance or usage indicator

---

## 4. Recommended Improvements

### 4.1 Upgrade to Access Control System (CRITICAL)

Tambahkan:
- Role column:
  - Owner
  - Admin
  - Staff

---

### 4.2 Improve Action Safety (HIGH IMPACT)

Ubah aksi menjadi:
- View Detail
- Edit
- More (⋮ menu)

Dalam menu:
- Reset Password (with confirm modal)
- Deactivate (soft delete)
- Audit Log

---

### 4.3 Add Security Context (HIGH IMPACT)

Tambahkan:
- Last login
- Account status:
  - Active
  - Suspended
  - Locked

---

### 4.4 Add Search & Filtering

Tambahkan:
- search by name/email
- filter by role
- filter by status

---

### 4.5 Add Audit Trail (IMPORTANT FOR SCALE)

Setiap action harus tercatat:
- siapa reset password
- siapa menonaktifkan akun
- kapan perubahan terjadi

---

## 5. Component Recommendations

### Staff Table Row (Improved)
- Name + avatar
- Email
- Username
- Role badge
- Status badge
- Last login
- Action menu (⋮)

---

### Status System
- Active (green)
- Suspended (yellow)
- Locked (red)

---

### Action Menu
- View Detail
- Edit User
- Reset Password
- Deactivate
- View Audit Log

---

### Filter Bar
- search input
- role dropdown
- status dropdown

---

## 6. Layout Structure (Improved)

1. Page header (title + CTA add staff)
2. Filter + search bar
3. Staff table
4. pagination
5. bulk action tools (optional)
6. audit log drawer (optional)

---

## 7. High Priority Fixes

1. Add role & permission system (CRITICAL)
2. Move destructive actions into safe menu (CRITICAL)
3. Add last login tracking (HIGH)
4. Add filtering system (HIGH)
5. Add audit logging (HIGH)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Role & permission badges (Owner/Admin/Staff)
- [x] **[CRITICAL]** Destructive actions di safe menu ⋮
- [ ] **[HIGH]** Last login tracking column
- [ ] **[HIGH]** Filter by role/status
- [ ] **[HIGH]** Audit log indicator untuk aksi sensitif

---

## 9. Cross-References

### Guideline Terkait

- [`tambah_staff_ui_guideline.md`](./tambah_staff_ui_guideline.md)

### Partial / File Reuse

- Lihat pola global di [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) section C

### Pola Global yang Berlaku

- Admin Table — lihat MASTER section C
- Destructive Action — lihat MASTER section C
- Status Badge — lihat MASTER section C

