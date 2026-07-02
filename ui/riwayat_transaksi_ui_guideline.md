# UI Design Guideline – Riwayat Transaksi Pelanggan

> **Modul:** Transaksi  
> **Audience:** Pelanggan  
> **Route:** `/transaksi`  
> **Layout:** `pelanggan`  
> **View:** [`src/Views/transaksi/index.php`](../src/Views/transaksi/index.php)  
> **Controller:** `Pelanggan\TransaksiController`  
> **Related:** [`riwayat_transaksi_advanced_ui_guideline.md`](./riwayat_transaksi_advanced_ui_guideline.md)  
> **Master:** [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md)

## 0. Cara Eksekusi

- Baca [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) untuk tokens & pola global
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/transaksi/index.php`](../src/Views/transaksi/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini saat ini gagal membedakan antara **state kosong (empty state)** dan **state hasil filter**, sehingga pengguna tidak tahu apakah memang belum ada data atau filter yang terlalu ketat.

[Kemungkinan Besar] Ini akan menurunkan trust pengguna karena sistem terlihat "tidak punya data", bukan "belum ada data sesuai kondisi".

---

## 2. Key UX Observations
- Empty state terlalu generic ("Belum ada transaksi") tanpa konteks status filter.
- Filter UI belum memberikan feedback aktif (no applied state indicator).
- Tidak ada skeleton/loading state untuk transisi data.
- Hierarki visual antara filter dan content terlalu dekat (kurang separation).
- CTA tidak ada (tidak ada arah tindakan setelah empty state).

---

## 3. Core UX Problems

### 3.1 Empty vs Filtered State Ambiguity
Pengguna tidak bisa membedakan apakah data memang kosong atau hasil filter terlalu ketat.

### 3.2 Missing Filter Feedback
Tidak ada indikator filter aktif, counter hasil, atau CTA reset.

### 3.3 Weak Visual Hierarchy
Filter section dan content area terlalu rapat tanpa separation visual.

### 3.4 Missing State System
Tidak ada loading skeleton atau error state dengan retry.

---

## 4. Recommended Improvements

### 4.1 Empty State Intelligence
Ganti empty state menjadi contextual:

- Default:
  "Belum ada transaksi yang tercatat"

- Jika filter aktif:
  "Tidak ditemukan transaksi dengan status 'X'"

Tambahkan:
- Icon ilustratif (calendar / box empty)
- CTA: "Reset Filter"

---

### 4.2 Filter UX Improvement
- Tambahkan active filter chip (misalnya: [Semua Status] ×)
- Button "Terapkan Filter" sebaiknya diganti real-time filtering atau auto-apply
- Tambahkan "Reset" action di sebelah filter

---

### 4.3 Layout Hierarchy Fix
- Pisahkan filter section dengan card content menggunakan:
  - background berbeda (#F8FAFC)
  - atau divider line subtle
- Tambahkan spacing vertical minimal 24–32px

---

### 4.4 State Design System

#### Loading State
- Skeleton table/cards (bukan blank space)

#### Empty State
- Icon + message + CTA

#### Error State
- Retry button + message teknis singkat

---

## 5. Component Recommendations

### Filter Bar
- Dropdown status
- Apply/Reset (or auto apply)
- Active filter indicator chip

### Transaction List
- Card/table hybrid
- Status badge (success, pending, failed)
- Date grouping (optional improvement)

### Empty State Component
- Icon (centered)
- Title (semibold)
- Description (secondary)
- CTA button

---

## 6. Layout Structure (Improved) Suggestion
1. Page Title
2. Filter Section (sticky optional)
3. Content Area:
   - Loading state OR
   - Transaction list OR
   - Empty state
4. Pagination (if data grows)

---

## 7. High Priority Fixes
1. Contextual empty state (CRITICAL)
2. Filter feedback (HIGH)
3. Loading skeleton (HIGH)
4. Layout spacing refinement (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Empty state membedakan kosong vs filtered
- [ ] **[HIGH]** Active filter chips + Reset Filter
- [ ] **[HIGH]** Loading skeleton saat filter/load
- [ ] **[MEDIUM]** Spacing filter section vs content (24-32px)

---

## 9. Cross-References

### Guideline Terkait

- [`riwayat_transaksi_advanced_ui_guideline.md`](./riwayat_transaksi_advanced_ui_guideline.md) — versi admin dengan filter advanced

### Partial / File Reuse

- Lihat pola global di [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) section C

### Pola Global yang Berlaku

- Filter Bar — lihat MASTER section C
- Empty State — lihat MASTER section C

