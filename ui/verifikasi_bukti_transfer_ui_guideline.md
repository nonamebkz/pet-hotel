# UI Design Guideline – Verifikasi Bukti Transfer

> **Modul:** Grooming Admin  
> **Audience:** Staff / Owner  
> **Route:** `/admin/grooming/pembayaran`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/grooming/pembayaran/index.php`](../src/Views/admin/grooming/pembayaran/index.php)  
> **Controller:** `Admin\GroomingController`  
> **Related:** [`booking_grooming_ui_guideline.md`](./booking_grooming_ui_guideline.md), [`dashboard_owner_ui_guideline.md`](./dashboard_owner_ui_guideline.md)  
> **Master:** [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md)

## 0. Cara Eksekusi

- Baca [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) untuk tokens & pola global
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/grooming/pembayaran/index.php`](../src/Views/admin/grooming/pembayaran/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini saat ini hanya berfungsi sebagai **empty verification queue**, tetapi tidak merepresentasikan sistem verifikasi pembayaran yang sebenarnya.

Masalah utama:
- tidak ada list bukti transfer (queue system tidak terlihat)
- empty state tidak menjelaskan status proses
- tidak ada workflow approval/reject
- tidak ada preview bukti (image/document handling)
- tidak ada priority system (urgent vs normal payment)

Ini membuat sistem verifikasi terlihat tidak aktif, padahal ini core revenue control system. [Pasti]

---

## 2. Key UX Observations

- Tab system sudah konsisten (Jenis, Kuota, Booking, Verifikasi Bukti)
- UI bersih dan minimal
- Empty state terlalu generik
- Tidak ada indikasi real-time incoming payments
- Tidak ada action system sama sekali
- Tidak ada audit trail atau history verifikasi

---

## 3. Core UX Problems

### 3.1 Missing Financial Control Workflow
Seharusnya halaman ini memiliki:
- queue bukti transfer masuk
- status verifikasi (pending, approved, rejected)

Saat ini:
- tidak ada transaksi yang terlihat sama sekali

---

### 3.2 Weak Empty State Design
Empty state hanya:
“Tidak ada bukti transfer menunggu verifikasi.”

Tidak menjawab:
- apakah sistem berjalan normal
- apakah belum ada transaksi
- apakah filter aktif

---

### 3.3 No Decision System
Tidak ada:
- approve button
- reject button
- review modal
- catatan verifikasi

---

## 4. Recommended Improvements

### 4.1 Upgrade Empty State → Operational State (CRITICAL)

Ganti menjadi contextual:

Jika kosong:
- “Semua pembayaran sudah diverifikasi”

Jika filter aktif:
- “Tidak ada bukti untuk filter ini”

Tambahkan:
- status indikator sistem aktif

---

### 4.2 Introduce Verification Queue System (CRITICAL)

Tambahkan list:
- nama pelanggan
- jumlah pembayaran
- metode transfer
- waktu upload bukti
- status

---

### 4.3 Add Image Preview Workflow (HIGH IMPACT)

Setiap bukti harus:
- bisa preview image
- zoom modal
- download option

---

### 4.4 Add Decision Actions (CRITICAL)

Per item:
- Approve
- Reject
- Request re-upload
- Add note

---

### 4.5 Add Priority System (MEDIUM)

Tambahkan:
- urgent payment badge
- overdue verification indicator
- time since upload

---

## 5. Component Recommendations

### Verification Card / Row
- customer name
- amount
- timestamp
- payment method
- status badge
- action buttons

---

### Preview Modal
- image viewer
- zoom controls
- verification notes panel

---

### Status System
- Pending (yellow)
- Approved (green)
- Rejected (red)

---

### Filter Bar
- status filter
- date range
- urgency filter

---

## 6. Layout Structure (Improved)

1. Page header
2. Filter + status summary
3. Verification queue table/cards
4. preview modal system
5. empty state (contextual)
6. audit log section (optional)

---

## 7. High Priority Fixes

1. Add verification queue (CRITICAL)
2. Add approve/reject workflow (CRITICAL)
3. Add image preview system (CRITICAL)
4. Improve empty state logic (HIGH)
5. Add status + priority system (HIGH)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Verification queue dengan prioritas visual
- [x] **[CRITICAL]** Approve/reject workflow dengan confirm
- [x] **[CRITICAL]** Image preview bukti transfer (lightbox/modal)
- [ ] **[HIGH]** Empty state: kosong vs semua sudah diverifikasi
- [ ] **[HIGH]** Status badge + priority indicator

---

## 9. Cross-References

### Guideline Terkait

- [`booking_grooming_ui_guideline.md`](./booking_grooming_ui_guideline.md)
- [`dashboard_owner_ui_guideline.md`](./dashboard_owner_ui_guideline.md)

### Partial / File Reuse

- [`src/Views/partials/uploaded-file-preview.php`](../src/Views/partials/uploaded-file-preview.php)
- Shared tab: grooming admin cluster — [`jenis_grooming_ui_guideline.md`](./jenis_grooming_ui_guideline.md), [`booking_grooming_ui_guideline.md`](./booking_grooming_ui_guideline.md)

### Pola Global yang Berlaku

- Admin Table — lihat MASTER section C
- Destructive Action — lihat MASTER section C
- Tab Navigation — lihat MASTER section C
- Status Badge — lihat MASTER section C
- Status Badge — lihat MASTER section C
- Tab Navigation — lihat MASTER section C

