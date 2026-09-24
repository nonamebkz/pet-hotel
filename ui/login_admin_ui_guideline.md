# UI Design Guideline – Login Admin

> **Modul:** Auth  
> **Audience:** Guest (Staff)  
> **Route:** `/admin/login`  
> **Layout:** `guest-admin`  
> **View:** [`src/Views/auth/admin/login.php`](../src/Views/auth/admin/login.php)  
> **Controller:** `Auth\StaffAuthController`  
> **Related:** [`ubah_password_ui_guideline.md`](./ubah_password_ui_guideline.md)  
> **Standar:** [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc)

## 0. Cara Eksekusi

- Baca [Standar UI/UX](../.cursor/rules/pencatatan-ui-ux-standards.mdc) untuk §3–§4 (token) dan §18 (pola global)
- Implementasi dimulai dari section 8 (Acceptance Criteria) item **CRITICAL**
- Jangan ubah route/controller kecuali state UI baru membutuhkannya
- Target file utama: [`src/Views/auth/admin/login.php`](../src/Views/auth/admin/login.php)

## 1. Critical UX Issue (Core Finding)
Halaman login ini secara visual sudah bersih, tetapi masih lemah dalam aspek **trust, security signaling, dan error-state readiness**.

Ini penting karena login adalah entry point sistem—jika di sini tidak terasa “secure”, maka seluruh platform ikut terdampak persepsi negatif. [Kemungkinan Besar]

---

## 2. Key UX Observations

- Layout centered card sudah baik dan minimalis
- Background gradient gelap cukup modern, tetapi kurang brand reinforcement
- Tidak ada visual trust indicator (security, verification, system identity)
- Form sangat minimal (email + password saja)
- CTA login sudah jelas namun kurang feedback state (loading/error/success)
- “Lupa password?” terlalu kecil secara visual hierarchy

---

## 3. Core UX Problems

### 3.1 Weak Trust Layer
Login page tidak memberikan:
- indikasi keamanan (secure login, encrypted session)
- branding reinforcement yang kuat (Petshop Admin hanya teks)
- visual assurance untuk staff/owner login

Ini membuat user “ragu secara subconscious” meskipun UI sederhana.

---

### 3.2 Lack of Authentication UX States
Tidak terlihat:
- error state (invalid password/email)
- loading state pada login button
- disabled state saat submit

---

### 3.3 Missing Utility Features
Tidak ada:
- show/hide password toggle
- remember me checkbox
- session warning / device recognition hint

---

## 4. Recommended Improvements

### 4.1 Strengthen Trust & Security Layer (HIGH IMPACT)
Tambahkan:
- “Secure Login (SSL Encrypted)” badge kecil
- icon lock di header
- subtle system identity block (Petshop Admin v1.0)

---

### 4.2 Improve Visual Hierarchy
- “Login Staff / Owner” → jadikan subheading
- “Petshop Admin” → primary brand title
- “Portal Staff & Owner” → secondary text

---

### 4.3 Enhance Form UX
Tambahkan:
- show/hide password icon
- inline validation error
- loading spinner inside button
- disabled button state when empty

---

### 4.4 Improve Accessibility
- increase contrast for secondary text
- ensure focus state visible (keyboard navigation)
- larger clickable area for “Lupa password?”

---

### 4.5 Add Optional Features (Medium Impact)
- Remember me checkbox
- Forgot password flow redesign (not just link)
- optional SSO (Google/Microsoft) if enterprise scale

---

## 5. Component Recommendations

### Login Card
- Title
- Subheading
- Form fields
- CTA button
- Secondary actions

---

### CTA Button
- full width
- loading state animation
- disabled state when invalid input

---

### Password Field
- toggle visibility icon
- validation feedback inline

---

## 6. Layout Structure (Improved)

1. Background (kept dark gradient, refined texture optional)
2. Centered login card
3. Brand header block (Petshop Admin)
4. Auth form
5. CTA button (primary)
6. Secondary actions (forgot password)

---

## 7. High Priority Fixes

1. Add trust/security indicators (CRITICAL)
2. Improve button states (HIGH)
3. Enhance password UX (HIGH)
4. Improve hierarchy typography (MEDIUM)
5. Add accessibility improvements (MEDIUM)

---

## 8. Acceptance Criteria

- [x] **[CRITICAL]** Indikator trust/security (secure login, encrypted session) tampil di login card
- [ ] **[HIGH]** Tombol login punya loading, disabled, dan error state
- [ ] **[HIGH]** Password field punya show/hide toggle
- [ ] **[MEDIUM]** Typography hierarchy: brand header > form > secondary actions
- [ ] **[MEDIUM]** Label form terhubung ke input; focus state jelas

---

## 9. Cross-References

### Guideline Terkait

- [`ubah_password_ui_guideline.md`](./ubah_password_ui_guideline.md)

### Partial / File Reuse

- [`src/Views/partials/head/tailwind-config.php`](../src/Views/partials/head/tailwind-config.php)

### Pola Global yang Berlaku

- Form Sections — lihat MASTER §18 Pola UX global

