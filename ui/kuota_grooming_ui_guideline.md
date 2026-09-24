# UI Design Guideline – Kuota Grooming

> **Modul:** Grooming Admin  
> **Audience:** Staff / Owner  
> **Route:** `/admin/grooming/kuota`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/grooming/kuota/index.php`](../src/Views/admin/grooming/kuota/index.php)  
> **Controller:** `Admin\GroomingController`  
> **Related:** [`jenis_grooming_ui_guideline.md`](./jenis_grooming_ui_guideline.md), [`tambah_kuota_grooming_ui_guideline.md`](./tambah_kuota_grooming_ui_guideline.md), [`booking_grooming_ui_guideline.md`](./booking_grooming_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/grooming/kuota/index.php`](../src/Views/admin/grooming/kuota/index.php)

## 1. Critical UX Issue (Core Finding)
Tampilan ini sebenarnya bukan “tabel data”, tetapi **sistem manajemen kapasitas (capacity planning)**. Namun saat ini masih diperlakukan seperti CRUD harian sederhana, sehingga tidak membantu operasional grooming sama sekali.

Akibatnya user hanya melihat angka statis tanpa konteks beban operasional. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Setiap baris tanggal memiliki pola yang sama (Slot Maksimal = 5, Terisi = 0, Sisa = 5)
- Tidak ada visualisasi kapasitas (hanya angka mentah)
- Edit/Hapus muncul di setiap baris (raw destructive action)
- Tidak ada bulk action untuk range tanggal
- Tidak ada calendar view atau weekly planning
- Tidak ada indikator utilisasi atau performa kuota

---

## 3. Core UX Problems

### 3.1 Lack of Capacity Visualization
Data tidak menunjukkan:
- seberapa penuh antrian grooming
- hari sibuk vs hari kosong

---

### 3.2 No Temporal Intelligence
System tidak bisa:
- membandingkan hari
- melihat tren mingguan
- memprediksi demand

---

### 3.3 Weak Operational Efficiency
Semua input dilakukan per baris:
- tidak scalable
- rawan human error

---

## 4. Recommended Improvements

### 4.1 Upgrade Table → Capacity Planning System (CRITICAL)

Ubah menjadi:
- Calendar / hybrid grid view
- setiap tanggal = card dengan progress bar

Contoh:
- 3/5 terisi → progress 60%

---

### 4.2 Add Occupancy Visualization (HIGH IMPACT)

Tambahkan:
- progress bar per hari
- color coding:
  - hijau (low occupancy)
  - kuning (medium)
  - merah (full)

---

### 4.3 Add Bulk Operations (CRITICAL)

Tambahkan:
- set slot untuk range tanggal
- copy configuration dari hari sebelumnya
- auto-fill weekday pattern

---

### 4.4 Improve Action Safety

Ubah:
- Edit → safe modal
- Hapus → move ke menu (⋮)
- tambah confirm dialog

---

### 4.5 Add Operational Insights

Tambahkan KPI header:
- Average occupancy
- Peak day
- Total capacity this week
- Utilization rate %

---

## 5. Component Recommendations

### Day Capacity Card
- date
- slot max
- filled count
- progress bar
- status badge

---

### Bulk Action Panel
- date range picker
- set capacity input
- apply button
- copy previous day

---

### Calendar/Grid View
- weekly layout
- hover detail
- quick edit drawer

---

## 6. Layout Structure (Improved)

1. KPI summary strip (top)
2. Calendar / grid capacity view
3. Bulk action panel (sticky)
4. Detail drawer (per day)
5. fallback table view (advanced users)

---

## 7. High Priority Fixes

1. Add occupancy visualization (CRITICAL)
2. Add bulk operations (CRITICAL)
3. Replace raw table with calendar/grid view (HIGH)
4. Move destructive actions into safe menu (HIGH)
5. Add operational insights KPI (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Visualisasi occupancy (terisi/total per tanggal)
- [x] **[CRITICAL]** Bulk operations (multi-select + aksi massal)
- [ ] **[HIGH]** Calendar/grid view atau hybrid selain raw table
- [ ] **[HIGH]** Destructive actions di safe menu ⋮
- [ ] **[MEDIUM]** KPI strip: hari penuh, rata-rata occupancy

---

## 9. Cross-References

### Guideline Terkait

- [`jenis_grooming_ui_guideline.md`](./jenis_grooming_ui_guideline.md)
- [`tambah_kuota_grooming_ui_guideline.md`](./tambah_kuota_grooming_ui_guideline.md)
- [`booking_grooming_ui_guideline.md`](./booking_grooming_ui_guideline.md)

### Partial / File Reuse

- Shared tab: [`jenis_grooming_ui_guideline.md`](./jenis_grooming_ui_guideline.md) (grooming admin `_subnav.php`)
- Lihat pola global di [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) §18 Pola UX global

### Pola Global yang Berlaku

- Admin Table — lihat MASTER §18 Pola UX global
- Destructive Action — lihat MASTER §18 Pola UX global
- Tab Navigation — lihat MASTER §18 Pola UX global
- Status Badge — lihat MASTER §18 Pola UX global
- Tab Navigation — lihat MASTER §18 Pola UX global

