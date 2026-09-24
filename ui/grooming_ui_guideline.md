# UI Design Guideline – Grooming Pelanggan

> **Modul:** Customer  
> **Audience:** Pelanggan  
> **Route:** `/grooming`  
> **Layout:** `pelanggan`  
> **View:** [`src/Views/grooming/index.php`](../src/Views/grooming/index.php)  
> **Controller:** `Pelanggan\GroomingController`  
> **Related:** [`tambah_kucing_ui_guideline.md`](./tambah_kucing_ui_guideline.md), [`booking_grooming_ui_guideline.md`](./booking_grooming_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/grooming/index.php`](../src/Views/grooming/index.php)

## 1. Critical UX Issue (Core Finding)
Halaman ini sudah cukup bersih secara visual, tetapi masih belum menjawab 2 hal penting:
1. Pengguna belum dipandu untuk memilih layanan dengan cepat
2. Tidak ada “decision support” sebelum klik booking

Akibatnya, halaman terlihat seperti katalog statis, bukan sistem pemesanan.

---

## 2. Key UX Observations

- Service cards sudah jelas, tetapi tidak memiliki hierarchy “recommended vs optional”
- CTA "Ajukan Booking" terlalu umum, tidak terhubung ke pilihan layanan
- Informasi antar-jemput dipisahkan dari konteks layanan (kurang kontekstual)
- Tidak ada comparison atau guidance untuk memilih paket
- Harga sudah jelas tapi belum ada framing value (kenapa beda harga?)

---

## 3. Core UX Problems

### 3.1 Lack of Decision Flow
User harus:
- membaca semua card
- membandingkan sendiri
- lalu baru klik booking

Ini menambah cognitive load yang tidak perlu. [Kemungkinan Besar]

---

### 3.2 CTA Misalignment
CTA tunggal tidak mencerminkan:
- layanan yang dipilih
- intent user

---

### 3.3 Weak Service Differentiation
"Grooming Jamur / Kutu / Lengkap" belum punya:
- badge rekomendasi
- level intensitas
- use case yang jelas

---

## 4. Recommended Improvements

### 4.1 Add Recommendation Layer
Tambahkan:
- “Recommended for most pets”
- “Best for medical condition”
- “Basic maintenance”

Contoh:
- Grooming Lengkap → Recommended
- Grooming Kutu → Medical Treatment

---

### 4.2 Contextual Booking Flow
Ubah flow:
- pilih layanan
- baru muncul CTA:
  "Booking Grooming Lengkap"

---

### 4.3 Improve Information Architecture
Susun ulang:
1. Hero + CTA
2. Service category cards
3. Comparison hint section
4. Delivery/antar-jemput info (contextual, bukan standalone banner)

---

### 4.4 Enhance Pricing Perception
Tambahkan:
- “What you get” per package
- durasi estimasi
- benefit breakdown

---

### 4.5 Improve Trust Layer
Tambahkan:
- groomer expertise badge
- hygiene / safety assurance
- review snippet

---

## 5. Component Recommendations

### Service Card (Upgrade)
- Title
- Short benefit description
- Badge (Recommended / Medical / Basic)
- Price
- CTA (contextual per card)

---

### Booking CTA (Dynamic)
- Disabled until selection
- Changes text based on selected service

---

### Info Banner (Antar-jemput)
- Move under selected service OR
- Make it collapsible info tooltip

---

## 6. Layout Structure (Improved)

1. Page Header (Title + subtitle)
2. Service selection grid
3. Comparison/helper hint section
4. Contextual info (pickup/delivery)
5. Dynamic CTA bar (sticky)

---

## 7. High Priority Fixes

1. Contextual CTA per service (CRITICAL)
2. Service recommendation labeling (HIGH)
3. Reposition antar-jemput info (HIGH)
4. Add service comparison clarity (MEDIUM)
5. Add trust signals (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Contextual CTA per service card (Ajukan / Lihat Detail)
- [ ] **[HIGH]** Service recommendation labeling
- [ ] **[HIGH]** Antar-jemput info repositioned clearly
- [ ] **[MEDIUM]** Service comparison clarity
- [ ] **[MEDIUM]** Trust signals (rating, durasi, harga jelas)

---

## 9. Cross-References

### Guideline Terkait

- [`tambah_kucing_ui_guideline.md`](./tambah_kucing_ui_guideline.md)
- [`booking_grooming_ui_guideline.md`](./booking_grooming_ui_guideline.md)

### Partial / File Reuse

- Lihat pola global di [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) §18 Pola UX global

### Pola Global yang Berlaku

- Empty State — lihat MASTER §18 Pola UX global
- Form Sections — lihat MASTER §18 Pola UX global

