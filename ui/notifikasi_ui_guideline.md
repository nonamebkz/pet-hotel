# UI Design Guideline – Notifikasi Admin

> **Modul:** Operations  
> **Audience:** Staff / Owner  
> **Route:** `/admin/notifikasi`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/notifikasi/index.php`](../src/Views/admin/notifikasi/index.php)  
> **Controller:** `Admin\NotifikasiController`  
> **Related:** [`dashboard_owner_ui_guideline.md`](./dashboard_owner_ui_guideline.md)  
> **Master:** [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md)

## 0. Cara Eksekusi

- Baca [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) untuk tokens & pola global
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/notifikasi/index.php`](../src/Views/admin/notifikasi/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman notifikasi ini saat ini hanya menampilkan empty state tanpa konteks sistem aktivitas, sehingga pengguna tidak bisa membedakan apakah:
- memang tidak ada event
- atau sistem notifikasi belum berjalan
- atau filter/status tidak aktif

Ini menciptakan ambiguity yang menurunkan trust sistem. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Empty state terlalu generik (“Belum ada notifikasi”)
- Tidak ada struktur jenis notifikasi (booking, pembayaran, sistem)
- Tidak ada konsep read / unread
- Tidak ada timeline atau grouping waktu
- Tidak ada indikator prioritas (urgent vs info)
- Tidak ada CTA atau action follow-up

---

## 3. Core UX Problems

### 3.1 Lack of Notification Model
Saat ini sistem belum jelas apakah notifikasi berbasis:
- event-driven (booking/payment)
- system alert
- user activity

---

### 3.2 Weak Empty State Logic
Empty state tidak menjelaskan:
- kondisi sistem
- contoh notifikasi
- atau tindakan yang bisa dilakukan user

---

### 3.3 No Information Hierarchy
Semua notifikasi (jika ada) akan tampil datar tanpa:
- urgency level
- category
- timestamp grouping

---

## 4. Recommended Improvements

### 4.1 Upgrade Empty State (CRITICAL)
Ganti menjadi contextual empty state:

Contoh:
- “Belum ada notifikasi hari ini”
- “Aktivitas booking dan pembayaran akan muncul di sini”

Tambahkan:
- icon bell / activity
- subtle explanation
- optional CTA: “Lihat Riwayat”

---

### 4.2 Introduce Notification Categories (HIGH IMPACT)

Buat kategori:
- Booking
- Pembayaran
- Sistem
- Reminder

Gunakan color coding ringan.

---

### 4.3 Add Read / Unread System
- unread → bold + dot indicator
- read → muted
- mark all as read button

---

### 4.4 Add Timeline Structure
Grouping berdasarkan waktu:
- Hari ini
- Kemarin
- Minggu ini

Ini meningkatkan scanability secara signifikan.

---

### 4.5 Add Priority System
- Urgent (red accent)
- Normal (blue/gray)
- Info (light gray)

---

## 5. Component Recommendations

### Notification Item
- icon category
- title
- short description
- timestamp
- status (read/unread)

---

### Notification Header Bar
- filter dropdown (all, unread, booking, payment)
- mark all as read
- search (optional)

---

### Empty State Component
- icon bell
- title
- description contextual
- optional CTA

---

## 6. Layout Structure (Improved)

1. Page header (title + filter actions)
2. Notification control bar
3. Timeline grouped list
4. Empty state (conditional)
5. Pagination (if needed)

---

## 7. High Priority Fixes

1. Improve empty state context (CRITICAL)
2. Add notification categories (HIGH)
3. Add read/unread system (HIGH)
4. Add timeline grouping (MEDIUM)
5. Add priority tagging (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Empty state contextual dengan CTA
- [ ] **[HIGH]** Notification categories/tabs
- [ ] **[HIGH]** Read/unread visual distinction
- [ ] **[MEDIUM]** Timeline grouping (hari ini, kemarin, lebih lama)
- [ ] **[MEDIUM]** Priority tagging untuk urgent

---

## 9. Cross-References

### Guideline Terkait

- [`dashboard_owner_ui_guideline.md`](./dashboard_owner_ui_guideline.md)

### Partial / File Reuse

- Lihat pola global di [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) section C

### Pola Global yang Berlaku

- Empty State — lihat MASTER section C
- Priority Layers — lihat MASTER section C

