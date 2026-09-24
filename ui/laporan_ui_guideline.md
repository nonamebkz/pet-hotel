# UI Design Guideline – Laporan

> **Modul:** Operations  
> **Audience:** Staff / Owner  
> **Route:** `/admin/laporan`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/laporan/index.php`](../src/Views/admin/laporan/index.php)  
> **Controller:** `Admin\LaporanController`  
> **Related:** [`dashboard_owner_ui_guideline.md`](./dashboard_owner_ui_guideline.md), [`riwayat_transaksi_advanced_ui_guideline.md`](./riwayat_transaksi_advanced_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/laporan/index.php`](../src/Views/admin/laporan/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman laporan ini saat ini hanya berfungsi sebagai **summary counter statis**, bukan sistem analitik operasional.

Masalah utama:
- semua nilai = 0 tanpa konteks
- tidak ada insight atau trend
- filter periode tidak memberikan value analitik

Ini membuat laporan terasa seperti placeholder, bukan decision support system. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Tab kategori sudah ada (Ringkasan, Grooming, Pet Hotel, Pet Care) tetapi belum mengubah struktur insight
- Filter tanggal ada, namun tidak ada feedback perubahan data
- KPI cards terlalu sederhana (hanya angka)
- Tidak ada visualisasi data (chart/trend)
- CTA “Cetak / Simpan PDF” sudah ada tapi tidak didukung insight
- Tidak ada comparison (vs periode sebelumnya)

---

## 3. Core UX Problems

### 3.1 Static Reporting Problem
Semua data:
- tidak berubah secara visual signifikan
- tidak memberikan sense of performance

---

### 3.2 Missing Analytical Layer
Tidak ada:
- trend
- growth comparison
- peak analysis
- segmentation per layanan

---

### 3.3 Weak Filter Feedback
User tidak tahu:
- apakah filter berhasil diterapkan
- apakah data berubah

---

## 4. Recommended Improvements

### 4.1 Upgrade dari “Summary” → “Analytics Dashboard” (CRITICAL)

Tambahkan:
- revenue trend chart
- booking trend chart
- service distribution chart

---

### 4.2 Improve KPI Cards (HIGH IMPACT)

Setiap card harus punya:
- current value
- comparison vs previous period
  contoh: +12% vs bulan lalu
- mini sparkline

---

### 4.3 Improve Tab Behavior
Tabs harus:
- mengubah dataset, bukan hanya label
- memfilter chart + KPI sekaligus

---

### 4.4 Improve Date Filter UX
Tambahkan:
- quick presets (7 hari, 30 hari, bulan ini)
- loading state saat apply filter
- success feedback “Data berhasil diperbarui”

---

### 4.5 Strengthen Export Functionality
“Cetak / Simpan PDF” harus:
- export sesuai filter aktif
- include chart + insight, bukan hanya angka

---

## 5. Component Recommendations

### KPI Card (Enhanced)
- title
- value
- delta (% change)
- sparkline mini chart

---

### Report Chart Section
- line chart (trend)
- bar chart (kategori layanan)
- pie chart (distribution)

---

### Filter Bar
- date range
- quick presets
- apply button + loading state

---

### Tab System
- each tab = dataset context switch
- persistent filters across tabs

---

## 6. Layout Structure (Improved)

1. Page Header (title + export button)
2. Filter Section (date + presets)
3. KPI Overview (with comparison)
4. Charts Section (insight layer)
5. Category Tabs (context switch)
6. Detailed breakdown (optional)
7. Export CTA

---

## 7. High Priority Fixes

1. Add analytics charts (CRITICAL)
2. Add comparison vs previous period (HIGH)
3. Improve KPI meaning (HIGH)
4. Improve filter feedback loop (MEDIUM)
5. Upgrade export to full report (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Analytics charts (revenue, booking trend)
- [ ] **[HIGH]** Comparison vs periode sebelumnya
- [ ] **[HIGH]** KPI cards dengan konteks (bukan angka 0 tanpa penjelasan)
- [ ] **[MEDIUM]** Filter feedback loop (chips + counter)
- [ ] **[MEDIUM]** Export full report

---

## 9. Cross-References

### Guideline Terkait

- [`dashboard_owner_ui_guideline.md`](./dashboard_owner_ui_guideline.md)
- [`riwayat_transaksi_advanced_ui_guideline.md`](./riwayat_transaksi_advanced_ui_guideline.md)

### Partial / File Reuse

- [`src/Views/admin/laporan/_subnav.php`](../src/Views/admin/laporan/_subnav.php)

### Pola Global yang Berlaku

- Filter Bar — lihat MASTER §18 Pola UX global
- Empty State — lihat MASTER §18 Pola UX global

