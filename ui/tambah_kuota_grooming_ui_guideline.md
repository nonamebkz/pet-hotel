# UI Design Guideline – Tambah Kuota Grooming

> **Modul:** Grooming Admin  
> **Audience:** Staff / Owner  
> **Route:** `/admin/grooming/kuota/tambah`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/grooming/kuota/form.php`](../src/Views/admin/grooming/kuota/form.php)  
> **Controller:** `Admin\GroomingController`  
> **Related:** [`kuota_grooming_ui_guideline.md`](./kuota_grooming_ui_guideline.md)  
> **Master:** [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md)

## 0. Cara Eksekusi

- Baca [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) untuk tokens & pola global
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/grooming/kuota/form.php`](../src/Views/admin/grooming/kuota/form.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini terlihat sangat sederhana, tetapi secara konsep masih belum merepresentasikan **capacity scheduling system** yang sebenarnya.

Saat ini hanya:
- input tanggal
- input slot maksimal
- simpan

Ini membuat sistem tidak scalable untuk manajemen kuota jangka panjang. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Form sangat minimal (good for speed input)
- Tidak ada context apakah kuota sudah ada sebelumnya
- Tidak ada feedback konflik data (duplicate date)
- Default value (5) tidak dijelaskan konteksnya
- Tidak ada bulk creation (harian/mingguan)
- Tidak ada preview kapasitas sebelum save

---

## 3. Core UX Problems

### 3.1 Lack of Scheduling Intelligence
User harus input satu per satu:
- tidak ada repeat rule
- tidak ada pattern setting (weekday/weekend)

---

### 3.2 Missing Data Safety Layer
Tidak ada:
- warning jika tanggal sudah memiliki kuota
- edit vs overwrite decision flow

---

### 3.3 Weak Context for “Slot Maksimal”
Angka “5” tidak punya:
- baseline
- rekomendasi
- occupancy insight

---

## 4. Recommended Improvements

### 4.1 Upgrade from Single Input → Scheduling System (CRITICAL)

Tambahkan mode:
- Single Day (current)
- Range Input (bulk)
- Recurring Pattern (weekly rule)

---

### 4.2 Add Conflict Detection (HIGH IMPACT)

Jika tanggal sudah ada kuota:
- show warning
- offer overwrite or edit existing

---

### 4.3 Improve Slot Context

Tambahkan:
- helper text: "maksimal kapasitas grooming per hari"
- suggested range: 3–10 slot
- optional smart recommendation

---

### 4.4 Add Bulk Creation Tool

Contoh:
- 01–07 July → set 5 slot each day
- apply to weekdays only

---

### 4.5 Add Preview Before Save

Preview:
- list tanggal affected
- slot per day
- confirmation step

---

## 5. Component Recommendations

### Date Input System
- single date picker
- range picker
- calendar selector

---

### Slot Input
- numeric stepper (+ / -)
- validation min/max
- hint recommendation

---

### Conflict Modal
- "Kuota sudah ada untuk tanggal ini"
- options:
  - overwrite
  - edit existing
  - cancel

---

## 6. Layout Structure (Improved)

1. Page header
2. Mode selector (Single / Range / Recurring)
3. Date input system
4. Slot input
5. Preview section
6. Save confirmation

---

## 7. High Priority Fixes

1. Add scheduling modes (CRITICAL)
2. Add conflict detection (CRITICAL)
3. Add bulk input capability (HIGH)
4. Improve slot context explanation (MEDIUM)
5. Add preview before save (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Mode scheduling: single date, range, recurring
- [x] **[CRITICAL]** Conflict detection jika kuota sudah ada
- [ ] **[HIGH]** Bulk input capability (multi-date picker)
- [ ] **[MEDIUM]** Helper text konteks slot/kapasitas
- [ ] **[MEDIUM]** Preview summary sebelum save

---

## 9. Cross-References

### Guideline Terkait

- [`kuota_grooming_ui_guideline.md`](./kuota_grooming_ui_guideline.md)

### Partial / File Reuse

- Lihat pola global di [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) section C

### Pola Global yang Berlaku

- Admin Table — lihat MASTER section C
- Destructive Action — lihat MASTER section C
- Status Badge — lihat MASTER section C
- Tab Navigation — lihat MASTER section C
- Form Sections — lihat MASTER section C

