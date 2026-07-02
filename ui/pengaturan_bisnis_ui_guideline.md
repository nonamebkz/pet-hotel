# UI Design Guideline – Pengaturan Bisnis

> **Modul:** Operations  
> **Audience:** Owner  
> **Route:** `/admin/pengaturan`  
> **Layout:** `admin`  
> **View:** [`src/Views/admin/pengaturan/form.php`](../src/Views/admin/pengaturan/form.php)  
> **Controller:** `Admin\PengaturanController`  
> **Related:** —  
> **Master:** [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md)

## 0. Cara Eksekusi

- Baca [`MASTER_UI_GUIDELINE.md`](./MASTER_UI_GUIDELINE.md) untuk tokens & pola global
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/admin/pengaturan/form.php`](../src/Views/admin/pengaturan/form.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini sudah kaya fitur, tetapi saat ini masih berbentuk **form konfigurasi panjang tanpa struktur domain yang jelas**.

Masalah utama:
- semua setting dicampur dalam satu scroll panjang (map, pickup, payment, promo)
- tidak ada grouping berbasis domain bisnis
- tidak ada explanation impact dari setiap setting
- tidak ada validation context untuk konfigurasi sensitif (payment & bank)
- tidak ada preview dampak perubahan (pricing & radius)

Ini membuat halaman rawan error konfigurasi dan sulit scaling saat fitur bertambah. [Kemungkinan Besar]

---

## 2. Key UX Observations

- sudah ada fitur advanced (map, radius, payment, promo)
- layout sudah clean dan card-based
- input cukup lengkap untuk operasional bisnis
- Google Maps/Leaflet integration sudah bagus
- tapi semua fitur masih “flat form structure”
- tidak ada hierarchy prioritas (critical vs optional settings)

---

## 3. Core UX Problems

### 3.1 No Domain Separation
Saat ini semua bercampur:
- Location settings
- Operational rules
- Payment config
- Promo config

Tidak ada visual separation berbasis kategori bisnis.

---

### 3.2 Lack of Risk Awareness
Setting seperti:
- radius gratis
- biaya per km
- batas waktu pembayaran

tidak memiliki:
- warning impact
- simulasi perubahan
- guardrail nilai ekstrem

---

### 3.3 Weak Payment Configuration UX
Bagian payment:
- terlihat seperti form biasa
- tidak ada validasi format rekening
- tidak ada bank validation

---

## 4. Recommended Improvements

### 4.1 Convert to Sectioned Settings System (CRITICAL)

Ubah layout menjadi:

1. Location & Coverage
2. Pricing Rules (Antar-jemput)
3. Payment Configuration
4. Promotions & Business Rules

---

### 4.2 Add Visual Hierarchy (HIGH IMPACT)

Setiap section:
- card header
- description kecil (apa dampaknya)
- icon per section

Contoh:
- Payment = shield icon (security context)
- Promo = tag icon
- Location = map icon

---

### 4.3 Add Change Impact Hint (HIGH IMPACT)

Tambahkan helper text:

- “Perubahan ini mempengaruhi semua booking aktif”
- “Perubahan berlaku mulai booking baru”

---

### 4.4 Improve Payment Section Safety

Tambahkan:
- bank dropdown (bukan free text)
- account number validation
- name holder mismatch warning (optional)

---

### 4.5 Add Preview Simulation (ADVANCED)

Contoh:
- jika radius 3km → estimasi biaya rata-rata per booking
- jika biaya per km naik → simulasi dampak

---

## 5. Component Recommendations

### Settings Section Card
- title
- icon
- description
- grouped inputs

---

### Map Component
- marker fixed
- radius overlay visualization
- live radius preview circle

---

### Payment Block
- bank selector
- formatted input
- validation state

---

### Promo Block
- slider input untuk discount %
- min value constraint UI

---

## 6. Layout Structure (Improved)

1. Page header (Pengaturan Bisnis)
2. Section navigation (sticky sidebar optional)
3. Location settings card
4. Pricing rules card
5. Payment config card
6. Promo & rules card
7. Save sticky bar (bottom fixed CTA)

---

## 7. High Priority Fixes

1. Split settings into domain sections (CRITICAL)
2. Add impact explanation per section (HIGH)
3. Improve payment validation system (HIGH)
4. Add map radius visualization (HIGH)
5. Add sticky save bar (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Settings split into domain sections (cards)
- [ ] **[HIGH]** Impact explanation per section
- [ ] **[HIGH]** Payment validation feedback
- [ ] **[HIGH]** Map radius visualization
- [ ] **[MEDIUM]** Sticky save bar saat scroll

---

## 9. Cross-References

### Guideline Terkait

- —

### Partial / File Reuse

- [`src/Views/partials/petshop-location-map.php`](../src/Views/partials/petshop-location-map.php)

### Pola Global yang Berlaku

- Form Sections — lihat MASTER section C

