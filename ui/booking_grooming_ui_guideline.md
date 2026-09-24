# UI Design Guideline – Booking Grooming

> **Modul:** Grooming Admin  
> **Audience:** Staff / Owner  
> **Route:** `/admin/grooming/booking`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/grooming/booking/index.php`](../src/Views/admin/grooming/booking/index.php)  
> **Controller:** `Admin\GroomingController`  
> **Related:** [`jenis_grooming_ui_guideline.md`](./jenis_grooming_ui_guideline.md), [`kuota_grooming_ui_guideline.md`](./kuota_grooming_ui_guideline.md), [`verifikasi_bukti_transfer_ui_guideline.md`](./verifikasi_bukti_transfer_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/grooming/booking/index.php`](../src/Views/admin/grooming/booking/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini saat ini berfungsi sebagai **query + empty state view**, bukan booking management system yang operasional.

Masalah utama:
- tidak ada data list booking sama sekali (hanya empty state)
- filter ada tetapi tidak memberikan konteks hasil
- tidak ada workflow status booking (pending, confirmed, done, cancelled)
- tidak ada action management (approve, reschedule, cancel)

Ini membuat halaman tidak usable untuk operasional grooming harian. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Tab system sudah ada (Jenis, Kuota, Booking, Verifikasi Bukti)
- Filter tanggal dan status sudah tersedia
- UI clean dan minimal
- Empty state terlalu sederhana (“Tidak ada booking”)
- Tidak ada guidance apa yang harus dilakukan user
- Tidak ada indikator apakah data memang kosong atau hasil filter

---

## 3. Core UX Problems

### 3.1 Missing Booking Data Model
Tidak ada representasi:
- booking list
- booking status lifecycle
- booking priority

---

### 3.2 Weak Empty State Intelligence
Empty state tidak menjelaskan:
- apakah belum ada booking sama sekali
- atau filter terlalu ketat
- atau sistem belum aktif

---

### 3.3 No Operational Workflow
Tidak ada fitur:
- approve booking
- reschedule booking
- cancel booking
- assign groomer

---

## 4. Recommended Improvements

### 4.1 Upgrade Empty State → Contextual System (CRITICAL)

Ganti menjadi:

Jika benar kosong:
- “Belum ada booking grooming”

Jika karena filter:
- “Tidak ada booking untuk filter yang dipilih”

Tambahkan:
- CTA: “Reset Filter”
- CTA: “Buat Booking Baru” (jika admin flow memungkinkan)

---

### 4.2 Introduce Booking Status System (HIGH IMPACT)

Tambahkan status:
- Pending
- Confirmed
- In Progress
- Completed
- Cancelled

Dengan color coding:
- yellow = pending
- blue = confirmed
- green = completed
- red = cancelled

---

### 4.3 Add Booking Table (CRITICAL)

Table harus memiliki:
- Nama pelanggan
- Jenis grooming
- Tanggal & jam
- Status
- Assigned groomer
- Action menu

---

### 4.4 Add Action Workflow

Per booking:
- View detail
- Approve / Reject
- Reschedule
- Cancel

---

### 4.5 Improve Filter System

Tambahkan:
- quick filter chips:
  - Today
  - This Week
  - Pending only
- result counter (“0 bookings found”)

---

## 5. Component Recommendations

### Booking Table Row
- customer name
- service type
- schedule datetime
- status badge
- action menu (⋮)

---

### Status Badge System
- consistent color system
- icon + label

---

### Empty State Component
- icon calendar
- contextual message
- CTA buttons

---

### Filter Bar
- date range picker
- status dropdown
- quick filters

---

## 6. Layout Structure (Improved)

1. Page header (title + context)
2. Filter bar (date + status + quick filters)
3. Booking table
4. pagination
5. empty state (conditional)
6. action modals (approve/reschedule)

---

## 7. High Priority Fixes

1. Add booking lifecycle system (CRITICAL)
2. Add booking table structure (CRITICAL)
3. Improve empty state logic (HIGH)
4. Add operational actions (HIGH)
5. Improve filter feedback (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Booking lifecycle badges (menunggu, dikonfirmasi, selesai, dibatalkan)
- [x] **[CRITICAL]** Struktur tabel booking dengan kolom operasional lengkap
- [ ] **[HIGH]** Empty state contextual (kosong vs filtered)
- [ ] **[HIGH]** Operational actions: konfirmasi, tolak, update status
- [ ] **[MEDIUM]** Filter feedback: chips + counter hasil

---

## 9. Cross-References

### Guideline Terkait

- [`jenis_grooming_ui_guideline.md`](./jenis_grooming_ui_guideline.md)
- [`kuota_grooming_ui_guideline.md`](./kuota_grooming_ui_guideline.md)
- [`verifikasi_bukti_transfer_ui_guideline.md`](./verifikasi_bukti_transfer_ui_guideline.md)

### Partial / File Reuse

- Shared tab: grooming admin cluster — [`jenis_grooming_ui_guideline.md`](./jenis_grooming_ui_guideline.md), [`verifikasi_bukti_transfer_ui_guideline.md`](./verifikasi_bukti_transfer_ui_guideline.md)
- Lihat pola global di [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) §18 Pola UX global

### Pola Global yang Berlaku

- Admin Table — lihat MASTER §18 Pola UX global
- Destructive Action — lihat MASTER §18 Pola UX global
- Tab Navigation — lihat MASTER §18 Pola UX global
- Filter Bar — lihat MASTER §18 Pola UX global
- Empty State — lihat MASTER §18 Pola UX global
- Status Badge — lihat MASTER §18 Pola UX global
- Filter Bar — lihat MASTER §18 Pola UX global
- Empty State — lihat MASTER §18 Pola UX global
- Tab Navigation — lihat MASTER §18 Pola UX global

