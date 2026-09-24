# UI Design Guideline – Dashboard Owner

> **Modul:** Dashboard  
> **Audience:** Staff / Owner  
> **Route:** `/admin/dashboard`  
> **Layout:** `admin`  
> **View:** [`src/Views/dashboard/admin.php`](../src/Views/dashboard/admin.php)  
> **Controller:** `Admin\DashboardController`  
> **Related:** [`verifikasi_bukti_transfer_ui_guideline.md`](./verifikasi_bukti_transfer_ui_guideline.md), [`laporan_ui_guideline.md`](./laporan_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/dashboard/admin.php`](../src/Views/dashboard/admin.php)

## 1. Critical UX Issue (Core Finding)
Dashboard ini sudah menampilkan data operasional, tetapi belum memiliki **hierarki keputusan (decision hierarchy)** yang jelas.

Saat ini semua informasi tampil setara, sehingga user (Owner) tidak langsung diarahkan ke:
- apa yang penting hari ini
- apa yang perlu ditindak
- apa yang hanya informasi

Ini membuat dashboard terasa seperti laporan statis, bukan control center. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Success banner “Login berhasil” tidak relevan untuk dashboard utama
- KPI cards semua tampil sama kuat (tidak ada prioritization)
- Tidak ada visualisasi tren (revenue/booking)
- Section “Bukti Transfer Menunggu Verifikasi” tidak cukup menonjol
- Role indicator “Owner Petshop” ada tapi kurang meaningful secara konteks akses
- Banyak angka 0 tanpa konteks (tidak ada baseline atau insight)

---

## 3. Core UX Problems

### 3.1 Lack of Decision Layer
Dashboard tidak memisahkan:
- urgent actions
- operational metrics
- informational data

---

### 3.2 Weak Priority System
Semua card terlihat sama penting:
- Booking Hari Ini
- Menunggu Verifikasi
- Penitipan Aktif
- Pendapatan

Padahal secara bisnis:
- “Menunggu Verifikasi” harus lebih prioritas

---

### 3.3 Missing Business Insight Layer
Tidak ada:
- trend revenue
- trend booking
- comparison (hari ini vs kemarin / minggu lalu)

---

## 4. Recommended Improvements

### 4.1 Restructure Dashboard into 3 Layers (CRITICAL)

#### Layer 1: Action Center (TOP PRIORITY)
- Bukti Transfer Menunggu Verifikasi (highlight card)
- Pending approval list
- Urgent alerts

#### Layer 2: KPI Overview
- Booking hari ini
- Penitipan aktif
- Revenue (today/week)

#### Layer 3: Insights
- revenue chart
- booking trend chart

---

### 4.2 Replace Success Banner
- Hapus “Login berhasil”
- Ganti dengan toast notification (temporary only)

---

### 4.3 Improve KPI Cards
- Add comparison delta (↑ +12% vs yesterday)
- Add color meaning:
  - green = good
  - orange = warning
  - red = urgent

---

### 4.4 Improve Action Visibility
“Bukti Transfer Menunggu Verifikasi” harus:
- paling atas
- diberi badge count
- clickable → direct workflow page

---

### 4.5 Add Data Context
Jika value = 0:
- jangan hanya “0”
- tambahkan context:
  “Belum ada transaksi hari ini”

---

## 5. Component Recommendations

### Dashboard Card System
- Priority Card (large, highlighted)
- KPI Card (medium)
- Info Card (small)

---

### Alert System
- inline alert for pending verification
- color-coded severity

---

### Chart Component
- revenue line chart
- booking bar chart
- time filter (daily/weekly/monthly)

---

## 6. Layout Structure (Improved)

1. Action Center (urgent tasks)
2. KPI Grid (metrics overview)
3. Insights / Charts
4. Secondary info (history, logs)

---

## 7. High Priority Fixes

1. Add Action Center layer (CRITICAL)
2. Remove success login banner (HIGH)
3. Prioritize verification queue (HIGH)
4. Add analytics charts (MEDIUM)
5. Improve KPI meaning with comparison (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Action Center layer di bagian atas (verifikasi pending, urgent alerts)
- [ ] **[HIGH]** Hapus/ganti banner login berhasil dengan toast temporary
- [ ] **[HIGH]** Queue verifikasi bukti transfer prioritas visual (badge count, clickable)
- [ ] **[MEDIUM]** Chart revenue/booking trend dengan filter waktu
- [ ] **[MEDIUM]** KPI cards dengan comparison delta vs periode sebelumnya

---

## 9. Cross-References

### Guideline Terkait

- [`verifikasi_bukti_transfer_ui_guideline.md`](./verifikasi_bukti_transfer_ui_guideline.md)
- [`laporan_ui_guideline.md`](./laporan_ui_guideline.md)

### Partial / File Reuse

- [`src/Views/partials/flash.php`](../src/Views/partials/flash.php)

### Pola Global yang Berlaku

- Priority Layers — lihat MASTER §18 Pola UX global
- Empty State — lihat MASTER §18 Pola UX global

