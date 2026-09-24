# UI Design Guideline – Riwayat Transaksi Admin

> **Modul:** Transaksi  
> **Audience:** Staff / Owner  
> **Route:** `/admin/transaksi`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/transaksi/index.php`](../src/Views/admin/transaksi/index.php)  
> **Controller:** `Admin\TransaksiController`  
> **Related:** [`riwayat_transaksi_ui_guideline.md`](./riwayat_transaksi_ui_guideline.md), [`laporan_ui_guideline.md`](./laporan_ui_guideline.md), [`verifikasi_bukti_transfer_ui_guideline.md`](./verifikasi_bukti_transfer_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/transaksi/index.php`](../src/Views/admin/transaksi/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini memiliki struktur filter yang sangat lengkap, tetapi justru menciptakan **cognitive overload tanpa feedback yang kuat**.

Masalah utama:
- terlalu banyak filter ditampilkan sekaligus
- tidak ada “filter state summary”
- empty state tidak menjelaskan sebab data kosong
- tidak ada insight apakah hasil sudah difilter dengan benar

Ini membuat user tidak yakin apakah data benar-benar kosong atau hanya hasil filter. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Tab navigasi sudah ada (Verifikasi Grooming, Verifikasi Penitipan, Riwayat Transaksi)
- Filter lengkap: date range, status, jenis layanan, pelanggan
- Semua filter tampil default (tidak collapsible)
- Tombol “Terapkan Filter” masih manual (tidak real-time feedback)
- Empty state hanya 1 kalimat tanpa konteks
- Tidak ada “active filter chips”

---

## 3. Core UX Problems

### 3.1 Filter Overload Problem
Terlalu banyak input filter terlihat sekaligus:
- meningkatkan friction
- user tidak tahu mana yang penting

---

### 3.2 Missing Filter Feedback Loop
Setelah klik “Terapkan Filter”:
- tidak ada ringkasan filter aktif
- tidak ada indikator jumlah hasil

---

### 3.3 Weak Empty State Intelligence
Pesan:
“Tidak ada transaksi untuk filter yang dipilih.”

Masalah:
- tidak menjelaskan kenapa
- tidak memberi solusi (reset / ubah filter)

---

## 4. Recommended Improvements

### 4.1 Add Filter Summary Chips (CRITICAL)
Setelah apply filter tampilkan:
- [07/01–07/31]
- [Status: Semua]
- [Layanan: Grooming]

Tambahkan tombol:
- Reset Filter

---

### 4.2 Convert Filter to Progressive Disclosure
Kelompokkan filter:
- Basic Filter (always visible)
- Advanced Filter (collapsible)

---

### 4.3 Improve Empty State (HIGH IMPACT)

Ubah menjadi:

- Title: “Tidak ada transaksi ditemukan”
- Description: “Coba ubah atau reset filter untuk melihat data”
- CTA: “Reset Filter”

---

### 4.4 Improve Tab + Filter Interaction
Tabs harus:
- mempengaruhi dataset + filter default
- tidak hanya visual switching

---

### 4.5 Add Result Feedback
Setelah filter:
- “Menampilkan 12 transaksi”
- atau “0 hasil ditemukan”

---

## 5. Component Recommendations

### Filter Bar System
- date range picker
- dropdown grouped filter
- advanced collapsible section

---

### Active Filter Chips
- removable chips
- reset all button

---

### Transaction Table (Future Improvement)
- sortable columns
- status badge
- row click → detail drawer

---

### Empty State Component
- icon
- message contextual
- CTA reset filter

---

## 6. Layout Structure (Improved)

1. Page Header
2. Tab Navigation (context switch)
3. Filter Summary Bar (chips)
4. Filter Panel (basic + advanced)
5. Result Counter
6. Transaction Table / Empty State
7. Pagination

---

## 7. High Priority Fixes

1. Add filter summary chips (CRITICAL)
2. Improve empty state with CTA (HIGH)
3. Reduce filter cognitive load (HIGH)
4. Add result counter feedback (MEDIUM)
5. Introduce collapsible advanced filters (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Filter summary chips setelah apply
- [ ] **[HIGH]** Empty state dengan CTA Reset Filter
- [ ] **[HIGH]** Progressive disclosure: basic + advanced filters
- [ ] **[MEDIUM]** Result counter: Menampilkan N transaksi
- [ ] **[MEDIUM]** Collapsible advanced filter panel

---

## 9. Cross-References

### Guideline Terkait

- [`riwayat_transaksi_ui_guideline.md`](./riwayat_transaksi_ui_guideline.md) — versi pelanggan (pola empty/filter serupa)
- [`laporan_ui_guideline.md`](./laporan_ui_guideline.md)
- [`verifikasi_bukti_transfer_ui_guideline.md`](./verifikasi_bukti_transfer_ui_guideline.md)

### Partial / File Reuse

- Lihat pola global di [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) §18 Pola UX global

### Pola Global yang Berlaku

- Filter Bar — lihat MASTER §18 Pola UX global
- Empty State — lihat MASTER §18 Pola UX global

