# UI Redesign (Pencatatan Standards) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign seluruh UI server-rendered PHP agar konsisten dengan `.cursor/rules/pencatatan-ui-ux-standards.mdc`, memakai `public/css/index.css`, `design.php`, dan partial shared — tanpa breaking route/controller.

**Architecture:** Fondasi (layout, nav, partial UI, helper tombol) disentuh sekali; lalu migrasi vertikal per modul mengikuti urutan §17 + cluster penitipan. Setiap fase diakhiri gate `rg` untuk pola legacy dan smoke-test route utama.

**Tech Stack:** PHP 8.4 views, Tailwind CDN, `tailwind-config.php`, `public/css/index.css`, `src/Views/helpers/design.php`.

## Global Constraints

- Standar kanonik: `.cursor/rules/pencatatan-ui-ux-standards.mdc` (§3–§4 token, §18 pola UX, §13 anti-pattern).
- Surface kartu: `rounded-2xl` via `design_surface()` / `design_interactive()` — bukan `rounded-xl` + `shadow-soft` + `border-white/80`.
- Warna view baru: semantic Tailwind (`primary`, `muted-foreground`, `border`) — bukan hex/`gray-*`/`bg-[#...]`.
- Form POST: tetap `Csrf::field()`; permission: sembunyikan CTA, jangan hanya `disabled`.
- Scope file: `src/Views/**`, `public/css/**`, `public/js/**` jika perlu; jangan `.tsx`.
- Mobile: `text-base` pada input field; konten di atas bottom nav pakai `pb-mobile-nav` atau `design_page_layout('formSm')`.
- Design doc: `docs/superpowers/specs/2026-09-24-ui-redesign-design.md`.

---

## File structure (target)

| Area | Tanggung jawab |
|------|----------------|
| `public/css/index.css` | Variables, base body, safe-area utilities |
| `src/Views/partials/head/tailwind-config.php` | CDN + color map; kurangi hex legacy bertahap |
| `src/Views/helpers/design.php` | Token kelas (existing) |
| `src/Views/helpers/ui.php` | Helper render partial (extend) |
| `src/Views/partials/ui/*.php` | Empty, filter, modal, **baru:** page-shell, page-header, form-footer, button classes |
| `src/Views/layouts/*.php` | Shell HTML + main spacing |
| `src/Views/partials/nav/*.php` | Admin/pelanggan nav + mobile title slot |
| `src/Views/helpers/navigation.php` | Nav link classes → semantic |
| Modul views | Tipis: susun partial + `design_*` |

---

### Task 1: UI helper primitives (tombol & shell)

**Files:**
- Create: `src/Views/helpers/ui-classes.php`
- Modify: `src/Core/Application.php` (require helper)
- Modify: `src/Views/helpers/ui.php` (optional wrappers)

**Interfaces:**
- Produces: `ui_btn_primary(): string`, `ui_btn_secondary(): string`, `ui_page_shell_classes(): string` — return Tailwind class strings per §9.

- [ ] **Step 1:** Tambah `ui-classes.php` dengan konstanta/fungsi:

```php
<?php
declare(strict_types=1);

function ui_btn_primary(): string
{
    return 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium touch-target bg-primary text-primary-foreground hover:opacity-90 transition';
}

function ui_btn_secondary(): string
{
    return 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium touch-target border border-border bg-background text-foreground hover:bg-muted transition';
}

function ui_page_shell_classes(): string
{
    return design_cn('space-y-6 md:space-y-8', design_page_layout('formSm'));
}
```

- [ ] **Step 2:** `require_once` di `Application.php` setelah `design.php`.

- [ ] **Step 3:** Verifikasi sintaks (jika PHP tersedia): `php -l src/Views/helpers/ui-classes.php`

- [ ] **Step 4:** Commit

```bash
git add src/Views/helpers/ui-classes.php src/Core/Application.php
git commit -m "feat(ui): add shared button and page shell class helpers"
```

---

### Task 2: Upgrade partial UI ke token

**Files:**
- Modify: `src/Views/partials/ui/empty-state.php`
- Modify: `src/Views/partials/ui/filter-chips.php`
- Modify: `src/Views/partials/ui/confirm-modal.php`
- Modify: `src/Views/partials/ui/action-menu.php`

**Interfaces:**
- Consumes: `design_surface('empty')`, `design_icon_badge()`, `ui_btn_primary()`, `ui_btn_secondary()`

- [ ] **Step 1:** `empty-state.php` — wrapper `class="<?= e(design_surface('empty')) ?> p-8 text-center"`; icon pakai `design_icon_badge()` tone per variant; teks `text-foreground` / `text-muted-foreground`; default CTA `ui_btn_primary()`.

- [ ] **Step 2:** `filter-chips.php` — chip `border-border bg-muted/50`; tombol reset `ui_btn_secondary()`.

- [ ] **Step 3:** `confirm-modal.php` & `action-menu.php` — `border-border`, `bg-card`, `shadow-sm`; hapus `shadow-soft` / `border-white/80`.

- [ ] **Step 4:** Gate file partial:

```bash
rg 'shadow-soft|border-white/80|text-gray-' src/Views/partials/ui/ || echo "PASS partials/ui"
```

Expected: no matches (atau hanya yang sudah diperbaiki).

- [ ] **Step 5:** Commit

```bash
git add src/Views/partials/ui/
git commit -m "refactor(ui): align shared partials with design tokens"
```

---

### Task 3: Layout shells (admin & pelanggan)

**Files:**
- Modify: `src/Views/layouts/admin.php`
- Modify: `src/Views/layouts/pelanggan.php`

**Interfaces:**
- Produces: `<main>` dengan `pb-mobile-nav md:pb-0` + spacing §7

- [ ] **Step 1:** `admin.php` body: `bg-background text-foreground font-body` (ganti `bg-page text-content-primary` jika alias sudah OK).

- [ ] **Step 2:** `main#main-content`:

```php
class="max-w-7xl mx-auto px-4 py-4 pb-mobile-nav md:py-8 md:pb-0 print:max-w-none print:px-0"
```

- [ ] **Step 3:** Ulangi pola setara di `pelanggan.php`.

- [ ] **Step 4:** Smoke: buka `/admin/dashboard` dan `/` (pelanggan) — tidak overflow horizontal 360px.

- [ ] **Step 5:** Commit

```bash
git add src/Views/layouts/admin.php src/Views/layouts/pelanggan.php
git commit -m "refactor(layout): mobile nav padding and semantic body colors"
```

---

### Task 4: Navigation (admin + pelanggan + navigation helper)

**Files:**
- Modify: `src/Views/partials/nav/admin-nav.php`
- Modify: `src/Views/partials/nav/pelanggan-nav.php`
- Modify: `src/Views/helpers/navigation.php`

- [ ] **Step 1:** Nav bar: `border-b border-border bg-card/90 backdrop-blur-md shadow-sm` (bukan `border-white/80 shadow-soft`).

- [ ] **Step 2:** Brand icon: migrasi `bg-admin` → `bg-primary text-primary-foreground` (admin); pelanggan tetap `primary`.

- [ ] **Step 3:** `navigation.php` — active link `border-primary text-primary`; inactive `text-muted-foreground hover:text-foreground`.

- [ ] **Step 4:** Gate:

```bash
rg 'shadow-soft|border-white/80' src/Views/partials/nav/ src/Views/helpers/navigation.php
```

Expected: 0 matches.

- [ ] **Step 5:** Commit

```bash
git add src/Views/partials/nav/ src/Views/helpers/navigation.php
git commit -m "refactor(nav): semantic colors and borders"
```

---

### Task 5: Guest layouts & flash

**Files:**
- Modify: `src/Views/layouts/guest.php`, `guest-admin.php`
- Modify: `src/Views/partials/flash.php`

- [ ] **Step 1:** Gradient dekoratif guest: `from-accent via-background to-muted` (hindari `primary-soft` legacy).

- [ ] **Step 2:** Flash messages: `design_alert_inline('warning')` atau variant success/danger via `design_status_badge` + banner semantic.

- [ ] **Step 3:** Gate auth routes: `/admin/login`, login pelanggan.

- [ ] **Step 4:** Commit

```bash
git add src/Views/layouts/guest.php src/Views/layouts/guest-admin.php src/Views/partials/flash.php
git commit -m "refactor(auth-shell): guest layouts and flash aligned to tokens"
```

---

### Task 6: Auth views (4 file inti)

**Files:**
- Modify: `src/Views/auth/admin/login.php`, `change-password.php`
- Modify: `src/Views/auth/pelanggan/login.php`, `change-password.php`
- Reference: `ui/login_admin_ui_guideline.md`, `ui/ubah_password_ui_guideline.md`

- [ ] **Step 1:** Kartu form → `design_surface('metric')`; input `text-base w-full border-input rounded-lg`.

- [ ] **Step 2:** Primary submit `class="<?= e(ui_btn_primary()) ?> w-full"`.

- [ ] **Step 3:** Centang CRITICAL di guideline masing-masing.

- [ ] **Step 4:** Gate:

```bash
rg 'shadow-soft|border-white/80' src/Views/auth/
```

- [ ] **Step 5:** Commit per cluster atau satu commit `refactor(auth): token-based auth views`

---

### Task 7: Dashboard admin (referensi modul)

**Files:**
- Modify: `src/Views/dashboard/admin.php`
- Reference: `ui/dashboard_owner_ui_guideline.md`

- [ ] **Step 1:** Urutan §18: Action center → KPI grid (`design_surface('metric')`) → insights.

- [ ] **Step 2:** KPI grid: `grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3`.

- [ ] **Step 3:** Gate dashboard file legacy count:

```bash
rg -c 'shadow-soft|border-white/80' src/Views/dashboard/admin.php
```

Target: 0.

- [ ] **Step 4:** Commit `refactor(dashboard): owner dashboard pencatatan layout`

**Catatan:** `dashboard/admin.php` saat ini ~25 legacy hits — treat as template untuk modul lain.

---

### Task 8: Grooming admin cluster

**Files:**
- Modify: `src/Views/admin/grooming/_subnav.php`
- Modify: `src/Views/admin/grooming/layanan/index.php`, `form.php`
- Modify: `src/Views/admin/grooming/kuota/index.php`, `form.php`
- Modify: `src/Views/admin/grooming/booking/index.php`
- Modify: `src/Views/admin/grooming/pembayaran/index.php`
- Reference: `ui/jenis_grooming_ui_guideline.md`, `kuota_*`, `booking_*`, `verifikasi_*`

- [ ] **Step 1:** `_subnav.php` tab: `border-b-2 border-primary` active; `text-muted-foreground` inactive.

- [ ] **Step 2:** List pages: hero section `design_surface('panel')`; list `design_interactive('listArticle')` + hover token.

- [ ] **Step 3:** Integrasi `ui_filter_chips()` / `ui_empty_state()` di index yang punya filter/data kosong.

- [ ] **Step 4:** Gate:

```bash
rg 'shadow-soft|border-white/80' src/Views/admin/grooming/
```

Target: 0.

- [ ] **Step 5:** Commit `refactor(grooming-admin): pencatatan tokens cluster-wide`

---

### Task 9: Penitipan admin cluster

**Files:**
- Modify: `src/Views/admin/penitipan/_nav.php`, `booking/index.php`, `booking/_card.php`
- Modify: `src/Views/admin/penitipan/kamar/`, `kuota/`, `paket/`, `pembayaran/`, `perpanjangan/`, `monitoring/form.php`
- (Tidak ada `ui/*` terpisah — ikuti §18 + pola grooming)

- [ ] **Step 1:** Samakan `_nav.php` dengan grooming subnav.

- [ ] **Step 2:** `_card.php` booking → `design_interactive('cardLink')` atau `listArticle`.

- [ ] **Step 3:** Gate:

```bash
rg 'shadow-soft|border-white/80' src/Views/admin/penitipan/
```

- [ ] **Step 4:** Commit `refactor(penitipan-admin): design tokens`

---

### Task 10: Pet Care admin + subnav

**Files:**
- Modify: `src/Views/admin/pet-care/_subnav.php`, `layanan/`, `slot/`, `booking/`
- Reference: `ui/layanan_petcare_ui_guideline.md`, `tambah_layanan_ui_guideline.md`

- [ ] **Step 1–4:** Same pattern as Task 8; gate `src/Views/admin/pet-care/`.

- [ ] **Step 5:** Commit `refactor(pet-care-admin): design tokens`

---

### Task 11: CRM, Staff, Operations

**Files:**
- Modify: `src/Views/admin/pelanggan/`, `staff/`, `notifikasi/index.php`, `laporan/*`, `pengaturan/form.php`, `transaksi/index.php`
- Reference: matching `ui/*_ui_guideline.md`

- [ ] **Step 1:** `laporan/_subnav.php` selaras tab §18.

- [ ] **Step 2:** Gate per folder:

```bash
rg 'shadow-soft|border-white/80' src/Views/admin/pelanggan/ src/Views/admin/staff/ src/Views/admin/notifikasi/ src/Views/admin/laporan/ src/Views/admin/pengaturan/ src/Views/admin/transaksi/
```

- [ ] **Step 3:** Commit `refactor(admin-ops): CRM staff laporan tokens`

---

### Task 12: Pelanggan-facing modules

**Files:**
- Modify: `src/Views/dashboard/pelanggan.php`
- Modify: `src/Views/grooming/*`, `penitipan/*`, `pet-care/*`, `kucing/*`, `transaksi/index.php`, `notifikasi/index.php`, `profil/`, `bantuan/`
- Modify: `src/Views/auth/pelanggan/register.php`, `forgot-password.php`, `reset-password.php`
- Reference: `ui/grooming_ui_guideline.md`, `tambah_kucing_ui_guideline.md`, `riwayat_transaksi_ui_guideline.md`

- [ ] **Step 1:** Booking forms panjang: tambah partial `src/Views/partials/ui/form-footer-mobile.php` (sticky `design_surface('stickyBar')` + `ui_btn_primary` w-full) — render di form yang perlu.

- [ ] **Step 2:** Gate seluruh pelanggan tree:

```bash
rg 'shadow-soft|border-white/80' src/Views/grooming/ src/Views/penitipan/ src/Views/pet-care/ src/Views/kucing/ src/Views/dashboard/pelanggan.php
```

- [ ] **Step 3:** Commit `refactor(pelanggan-ui): customer flows tokens`

---

### Task 13: Error pages & misc partials

**Files:**
- Modify: `src/Views/errors/404.php`, `403.php`
- Modify: `src/Views/partials/hubungi-kami.php`, domain partials yang masih `gray-*`

- [ ] **Step 1:** Error pages: centered `design_surface('empty')` + `ui_btn_primary` ke home.

- [ ] **Step 2:** Gate:

```bash
rg 'shadow-soft|border-white/80' src/Views/
```

Target: **0** repo-wide (final gate).

- [ ] **Step 3:** Commit `refactor(views): final legacy pattern removal`

---

### Task 14: Tailwind config cleanup (legacy hex)

**Files:**
- Modify: `src/Views/partials/head/tailwind-config.php`

- [ ] **Step 1:** Dokumentasi di comment: `admin` deprecated — gunakan `primary` untuk UI baru.

- [ ] **Step 2:** Hapus shadow `soft*` dari config hanya jika gate Task 13 PASS (tidak ada referensi `shadow-soft`).

- [ ] **Step 3:** Optional: tambah `<html class="">` hook dark mode di layout untuk QA `.dark`.

- [ ] **Step 4:** Commit `chore(tailwind): trim legacy shadows after view migration`

---

### Task 15: Dokumentasi & graphify

**Files:**
- Modify: `ui/README.md` (catatan fase selesai jika perlu)
- Run: `graphify update .`

- [ ] **Step 1:** Tambah di `ui/README.md` paragraf: migrasi UI mengacu rule §16; legacy selesai tanggal X.

- [ ] **Step 2:** `graphify update .`

- [ ] **Step 3:** Commit `docs: note UI redesign completion`

---

## Self-review (spec coverage)

| Requirement § | Task |
|---------------|------|
| §3 CSS semantic | 2–5, 14 |
| §4 design.php usage | 2, 6–13 |
| §5 safe area / pb-mobile-nav | 3, 12 |
| §7 layout hierarchy | 3, 7 |
| §9 partial catalog | 2, 12 (form-footer) |
| §11 states | 6–13 (empty/filter per halaman) |
| §13 anti-pattern removal | 13 gate |
| §16 execution order | 6→7→8→11→12 |
| §17 ui guidelines | 6–11 reference |
| §18 UX patterns | 7, 8, 11 |

**Placeholder scan:** tidak ada TBD — setiap task punya file dan command gate.

---

## Verifikasi akhir (manual)

- [ ] Viewport 360px: admin dashboard, grooming booking, penitipan booking, grooming pelanggan
- [ ] Form panjang: sticky footer tidak tertutup bottom nav
- [ ] Kontras primary hijau pada `bg-primary/10` (icon badge)
- [ ] CSRF tetap ada di form sample
- [ ] `rg 'shadow-soft|border-white/80' src/Views/` → 0

---

## Execution Handoff

**Plan complete and saved to `docs/superpowers/plans/2026-09-24-ui-redesign-pencatatan-standards.md`.**

**Design summary:** `docs/superpowers/specs/2026-09-24-ui-redesign-design.md`

**Two execution options:**

1. **Subagent-Driven (recommended)** — satu subagent per Task 1–15, review antar task, gate `rg` wajib PASS sebelum lanjut.

2. **Inline Execution** — jalankan fase 1–5 dulu (Tasks 1–7), checkpoint visual, lanjut cluster admin/pelanggan.

**Estimasi:** Tasks 1–5 (~1 sesi), 6–11 (~2–3 sesi), 12–15 (~2 sesi) — tergantung kedalaman guideline CRITICAL per halaman.
