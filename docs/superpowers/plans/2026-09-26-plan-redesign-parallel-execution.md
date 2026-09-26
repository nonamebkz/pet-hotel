# Plan Redesign Petshop — Eksekusi Paralel

> **For agentic workers:** REQUIRED SUB-SKILL: Use `superpowers:subagent-driven-development` (satu subagent per **track** di Wave 2) atau `superpowers:executing-plans` untuk Wave 0–1 berurutan. Untuk dispatch paralel, gunakan `superpowers:dispatching-parallel-agents` — **satu agent per track**, jangan bagi file yang sama.

**Goal:** Menerapkan [`plan-redesign.md`](../../../plan-redesign.md) ke aplikasi PHP server-rendered yang ada, dengan hierarki operasional (urgent → KPI → konten), shell terpisah admin vs pelanggan, komponen shared, dan migrasi halaman bertahap — tanpa mengubah route/controller kecuali untuk state UI (filter URL, empty/loading).

**Architecture:** Fondasi sudah dimulai (token `index.css`, `design.php`, layout workspace 1440px, `ui_page_header`, menu terpusat `navigation-menus.php`, sidebar admin, bottom nav pelanggan). Wave 1 menyelesaikan shared partials + a11y nav. Wave 2 lima track vertikal (folder view terpisah) berjalan paralel. Setiap halaman memakai `ui/*_ui_guideline.md` + `.cursor/rules/pencatatan-ui-ux-standards.mdc`. Visual-only diff; business logic tetap.

**Tech Stack:** PHP 8.4 views, Tailwind CDN, `tailwind-config.php`, `public/css/index.css`, `src/Views/helpers/design.php`, `ui-classes.php`, `navigation-menus.php`.

## Global Constraints

- Kanonik implementasi: `.cursor/rules/pencatatan-ui-ux-standards.mdc` (bukan hex mentah di view; pakai `design_*`, `ui_btn_*`).
- Acuan produk UX: `plan-redesign.md` §1–§12; petakan istilah hotel → petshop (Reservasi = Booking, Hewan menginap = Penitipan aktif, dll.).
- Scope default: `src/Views/**`, `public/css/**`, `public/js/**`; **jangan** ubah controller/repository kecuali diminta eksplisit untuk filter URL.
- Jangan tampilkan menu/route yang tidak ada; permission: sembunyikan CTA (bukan hanya `disabled`).
- Form POST: `Csrf::field()`; URL bookmark & notifikasi harus tetap valid (§11.8).
- Eksplorasi kode: `graphify query|path|explain` sebelum grep massal; setelah edit PHP view, `graphify update .`.
- Satu PR/commit per track disarankan agar rollback aman (§11.9).

---

## Pemetaan plan-redesign → Aplikasi aktual

| Plan (§4 / §8) | Implementasi petshop |
|----------------|----------------------|
| Reservasi | `/admin/grooming/booking`, `/admin/penitipan/booking`, `/admin/pet-care/booking` |
| Kalender kamar | `/admin/penitipan/kuota` (+ kamar `/admin/penitipan/kamar`) |
| Hewan menginap | `/admin/penitipan/booking?status=SEDANG_DITITIPKAN` |
| Perawatan | Monitoring penitipan, `/admin/pet-care/booking`, perpanjangan |
| Pembayaran | `/admin/grooming/pembayaran`, `/admin/penitipan/pembayaran`, `/admin/transaksi` |
| Portal pemilik | layout `pelanggan.php` + bottom nav 4 item |
| Halaman percontohan §11.5 | Admin: daftar booking penitipan, detail penitipan, monitoring; Pelanggan: dashboard (done), booking grooming |

---

## Status eksekusi (2026-09-27)

| Gelombang | Status | Catatan |
|-----------|--------|---------|
| Wave 0 | Selesai | Token, nav, layout, pilot dashboard |
| Wave 1 | Selesai (kode) | Tombol, breadcrumb/toolbar, nav a11y, auth/errors — commit opsional |
| Wave 2 A–E | Selesai (kode) | Subagent paralel + sesi koordinator; gate `rg` per track PASS |
| Wave 3 | Selesai (kode) | Map partials, gate global PASS; smoke browser manual disarankan |

**Gate global (2026-09-27):**

```bash
rg 'shadow-soft|border-white/80' src/Views/                    # PASS
rg 'text-gray-[0-9]' src/Views/                                # PASS
rg 'text-content-|bg-admin' src/Views/                         # PASS
```

**Smoke route minimum** (uji manual di browser): lihat § Wave 3 Task 5.

---

## Status baseline (Wave 0 — sudah ada di working tree)

Centang sebelum mulai Wave 1; jangan duplikasi pekerjaan.

- [x] Token hangat + fokus keyboard: `public/css/index.css`
- [x] `design_page_layout('workspace')`, `ui_layout_main_classes()`
- [x] `partials/ui/page-header.php`, `ui_page_header()`
- [x] Menu terpusat: `helpers/navigation-menus.php`, `partials/nav/_menu-items.php`
- [x] Admin sidebar 248px + topbar utilitas: `partials/nav/admin-nav.php`, `layouts/admin.php`
- [x] Pelanggan bottom nav + menu desktop: `pelanggan-nav.php`, `pelanggan-bottom-nav.php`
- [x] Pilot partial: `dashboard/admin.php`, `dashboard/pelanggan.php`, `admin/notifikasi/index.php`

---

## Diagram dependensi & paralelisme

```text
Wave 0 (done)
    │
    ▼
Wave 1 — Platform (1 agent, urutan internal 1→4)
    │
    ├──────────────────┬──────────────────┬──────────────────┬──────────────────┐
    ▼                  ▼                  ▼                  ▼                  ▼
Track A            Track B            Track C            Track D            Track E
Grooming admin     Penitipan admin    Pet-care + CRM     Ops admin          Portal pelanggan
(admin/grooming)   (admin/penitipan)  (pet-care,         (laporan,          (grooming, penitipan,
                                      pelanggan, staff)   transaksi, etc.)   kucing, profil, …)
    │                  │                  │                  │                  │
    └──────────────────┴──────────────────┴──────────────────┴──────────────────┘
                                    │
                                    ▼
                          Wave 3 — Integrasi & QA (1 agent)
```

**Aturan konflik file (WAJIB untuk paralel):**

| Path | Owner |
|------|--------|
| `src/Views/partials/ui/*` | Wave 1 Task 2 saja |
| `src/Views/partials/nav/*`, `public/js/nav.js` | Wave 1 Task 3 saja |
| `src/Views/admin/grooming/**` | Track A |
| `src/Views/admin/penitipan/**` | Track B |
| `src/Views/admin/pet-care/**`, `admin/pelanggan/**`, `admin/staff/**` | Track C |
| `src/Views/admin/laporan/**`, `admin/transaksi/**`, `admin/pengaturan/**`, `admin/notifikasi/**` | Track D |
| `src/Views/` (pelanggan: grooming, penitipan, pet-care, kucing, profil, transaksi, notifikasi, bantuan, auth/pelanggan) | Track E |
| `src/Views/auth/admin/**`, `layouts/guest*.php`, `errors/**` | Wave 1 Task 4 |

---

## Wave 1 — Platform (sequential, satu agent)

### Task 1: Komponen tombol & form (lengkapi §7 plan)

**Files:**
- Modify: `src/Views/helpers/ui-classes.php`
- Modify: `src/Views/partials/ui/confirm-modal.php` (destructive pakai `ui_btn_*`)

**Interfaces:**
- Produces: `ui_btn_destructive(): string`, `ui_btn_tertiary(): string`

- [x] **Step 1:** Tambah di `ui-classes.php`:

```php
function ui_btn_destructive(): string
{
    return 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium touch-target bg-destructive text-primary-foreground hover:opacity-90 transition';
}

function ui_btn_tertiary(): string
{
    return 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium touch-target text-primary hover:bg-muted transition';
}
```

- [x] **Step 2:** Ganti tombol hapus di `confirm-modal.php` ke `ui_btn_destructive()` / batal ke `ui_btn_secondary()`.

- [x] **Step 3:** Gate:

```bash
rg "bg-red-|text-red-600" src/Views/partials/ui/confirm-modal.php || echo "PASS"
```

- [x] **Step 4:** Commit: `refactor(ui): destructive and tertiary button tokens` *(kode siap; commit belum)*

---

### Task 2: Partial shared (filter, breadcrumb, toolbar daftar) — DONE (implementasi)

**Files:**
- Modify: `src/Views/partials/ui/filter-chips.php`, `empty-state.php`, `action-menu.php`
- Create: `src/Views/partials/ui/breadcrumb.php`
- Create: `src/Views/partials/ui/list-toolbar.php`
- Modify: `src/Views/helpers/ui.php` — `ui_breadcrumb()`, `ui_list_toolbar()`

**Interfaces:**
- Produces: partial breadcrumb (array `{label, href?}`), toolbar slot untuk search + filter chips

- [x] **Step 1:** `breadcrumb.php` — `<nav aria-label="Breadcrumb">`, item `text-muted-foreground`, current `text-foreground font-medium`.

- [x] **Step 2:** `list-toolbar.php` — bar `flex flex-wrap gap-2` dengan props: `$searchName`, `$searchValue`, `$filterChips` (delegate ke `ui_filter_chips`).

- [x] **Step 3:** Gate legacy partials:

```bash
rg 'shadow-soft|border-white/80|text-gray-' src/Views/partials/ui/ || echo "PASS partials/ui"
```

- [ ] **Step 4:** Commit: `refactor(ui): breadcrumb and list toolbar partials`

---

### Task 3: Nav a11y & menu permission

**Files:**
- Modify: `public/js/nav.js`
- Modify: `src/Views/helpers/navigation-menus.php`
- Modify: `src/Views/partials/nav/admin-nav.php` (opsional: nama lokasi dari `app_settings()`)

**Interfaces:**
- Consumes: `app_settings()` untuk label cabang satu-lokasi (tanpa dropdown palsu, §4 topbar)

- [x] **Step 1:** `nav.js` — tutup drawer pada `Escape`; kembalikan fokus ke `data-nav-mobile-trigger`; klik backdrop di luar panel (jika belum).

- [x] **Step 2:** Sidebar item tinggi minimal `min-h-11` (44px) — verifikasi `nav_sidebar_link_classes`.

- [ ] **Step 3:** Filter menu owner di `admin_nav_menu_sections()` — sudah ada; tambah komentar + unit smoke: login staff non-owner tidak melihat Pengaturan.

- [ ] **Step 4:** Commit: `fix(nav): drawer a11y and operational menu polish`

---

### Task 4: Auth, guest, errors

**Files:**
- Modify: `src/Views/auth/admin/login.php`, `change-password.php`, `forgot-password.php`, `reset-password.php`
- Modify: `src/Views/auth/pelanggan/login.php`, `register.php`, `change-password.php`
- Modify: `src/Views/layouts/guest.php`, `guest-admin.php`, `partials/flash.php`
- Modify: `src/Views/errors/403.php`, `404.php`

**Reference:** `ui/login_admin_ui_guideline.md`, `ui/ubah_password_ui_guideline.md`

- [x] **Step 1:** Kartu `design_surface('metric')`, input `ui_form_input_class()`, submit `ui_btn_primary()` full width mobile.

- [x] **Step 2:** Gate:

```bash
rg 'shadow-soft|border-white/80|text-gray-' src/Views/auth/ src/Views/layouts/guest*.php src/Views/errors/ || echo "PASS auth/errors"
```

- [ ] **Step 3:** Smoke route: `/admin/login`, `/login`, `/admin/change-password`.

- [ ] **Step 4:** Commit: `refactor(auth): guest shells aligned to redesign tokens`

---

## Wave 2 — Tracks paralel (5 agent, mulai setelah Wave 1 merge)

Setiap track: baca guideline di `ui/` untuk halaman yang disentuh; terapkan §5 plan (header: `ui_page_header`), §7 (tabel/filter), §10 (empty/filtered/loading minimal).

### Track A — Admin Grooming

**Owner path:** `src/Views/admin/grooming/**`  
**Jangan edit:** `penitipan/`, `partials/nav/`, `partials/ui/`

**Guidelines:** `jenis_grooming_ui_guideline.md`, `kuota_grooming_ui_guideline.md`, `booking_grooming_ui_guideline.md`, `verifikasi_bukti_transfer_ui_guideline.md`

| Prioritas | View / route | Deliverable redesign |
|-----------|----------------|----------------------|
| P0 | `booking` index `/admin/grooming/booking` | Header + toolbar filter URL + kartu mobile / tabel desktop; status booking ≠ pembayaran |
| P0 | `pembayaran` `/admin/grooming/pembayaran` | Action center antrian; satu primary per row via action-menu |
| P1 | `layanan`, `kuota` + form tambah/edit | `design_surface('panel')`, sticky footer mobile form |
| P2 | `_subnav.php` | Selaraskan tab dengan route nyata; active `border-primary` |

- [x] **A1:** Refactor booking index (CRITICAL guideline).
- [x] **A2:** Refactor verifikasi pembayaran.
- [x] **A3:** Refactor layanan + kuota CRUD.
- [x] **A4:** Gate track:

```bash
rg 'shadow-soft|border-white/80|text-gray-|bg-admin' src/Views/admin/grooming/ || echo "PASS grooming"
```

- [ ] **A5:** Commit: `refactor(ui): admin grooming cluster redesign`

**Subagent prompt (salin):**

```text
Full Repository Path: /home/kikichan/project/pet-hotel
Wave 1 must be merged. Owner: src/Views/admin/grooming/** only.
Follow docs/superpowers/plans/2026-09-26-plan-redesign-parallel-execution.md Track A.
Standards: .cursor/rules/pencatatan-ui-ux-standards.mdc + plan-redesign.md §5–§7.
Use ui_page_header(), design_surface(), ui_btn_*(), ui/*_ui_guideline.md CRITICAL items.
graphify before explore; graphify update . after edits.
Return: files changed, routes to smoke-test, gate rg output.
```

---

### Track B — Admin Penitipan (pilot §11.5)

**Owner path:** `src/Views/admin/penitipan/**`

**Guidelines:** (booking/kuota/kamar — lihat `ui/README.md` Operations + penitipan views)

| Prioritas | Route | Deliverable |
|-----------|-------|-------------|
| P0 | `/admin/penitipan/booking` | Daftar reservasi: kolom §7 plan (kode, pemilik/hewan, periode, kamar, status booking, status bayar, aksi) |
| P0 | Detail booking (jika view terpisah) | Header status + panel biaya; alergi/catatan dekat identitas kucing |
| P0 | `/admin/penitipan/kuota` | Pola kalender §8 (filter, legenda, hari ini) sejauh data ada |
| P1 | `pembayaran`, `perpanjangan`, monitoring forms | Konfirmasi destructive via confirm-modal |
| P2 | paket, kamar CRUD | Form pattern Wave 1 |

- [x] **B1:** Booking list + filter chips di URL.
- [x] **B2:** Detail / monitoring UX.
- [x] **B3:** Kuota + kamar.
- [x] **B4:** Pembayaran + perpanjangan.
- [x] **B5:** Gate:

```bash
rg 'shadow-soft|border-white/80|text-gray-' src/Views/admin/penitipan/ || echo "PASS penitipan"
```

- [ ] **B6:** Commit: `refactor(ui): admin penitipan cluster redesign`

---

### Track C — Pet Care + CRM + Staff

**Owner paths:**
- `src/Views/admin/pet-care/**`
- `src/Views/admin/pelanggan/**`
- `src/Views/admin/staff/**`

| Prioritas | Route | Guideline |
|-----------|-------|-----------|
| P0 | `/admin/pet-care/booking` | Tugas perawatan §8 (kartu, urutan waktu) |
| P1 | `/admin/pet-care/layanan`, `slot` | `layanan_petcare_ui_guideline.md` |
| P1 | `/admin/pelanggan` | `manajemen_pelanggan_ui_guideline.md` |
| P2 | `/admin/staff` | `manajemen_staff_ui_guideline.md` |

- [x] **C1–C4:** Sesuai tabel; gate `rg` per folder; satu commit track.

---

### Track D — Ops Admin

**Owner paths:**
- `src/Views/admin/laporan/**`
- `src/Views/admin/transaksi/**`
- `src/Views/admin/pengaturan/**`
- `src/Views/admin/notifikasi/**` (sisanya selain yang sudah di Wave 0)

| Prioritas | Route | Catatan plan |
|-----------|-------|----------------|
| P1 | `/admin/laporan` | KPI + chart `design_surface('chart')` |
| P1 | `/admin/transaksi` | `riwayat_transaksi_advanced_ui_guideline.md` |
| P2 | `/admin/pengaturan` | Owner only |
| P2 | Notifikasi | Kategori tab + URL `?kategori=` (pertahankan) |

- [x] **D1–D3:** Gate + commit track.

---

### Track E — Portal Pelanggan

**Owner paths:** `src/Views/grooming/**`, `penitipan/**`, `pet-care/**`, `kucing/**`, `profil/**`, `transaksi/**`, `notifikasi/**`, `bantuan/**`, `dashboard/pelanggan.php` (hanya jika perlu polish)

**Jangan edit:** `layouts/pelanggan.php` kecuali bug spacing (koordinasi Wave 1).

| Prioritas | Route | plan §3.2 / §8 |
|-----------|-------|----------------|
| P0 | `/grooming`, `/grooming/booking` | Satu CTA utama per langkah; sembunyikan bottom nav di flow panjang jika perlu class pada layout wrapper |
| P0 | `/penitipan/**` | Portal kabar / status booking |
| P1 | `/kucing/**`, `/profil` | Form mobile `pb-24` / sticky footer |
| P1 | `/transaksi`, `/notifikasi` | Empty/filtered states §10 |
| P2 | `/bantuan` | Secondary utilitas |

- [x] **E1–E5:** Gate:

```bash
rg 'shadow-soft|border-white/80|text-gray-|border-green-200' src/Views/grooming/ src/Views/penitipan/ src/Views/pet-care/ src/Views/kucing/ src/Views/profil/ || echo "PASS customer views"
```

- [ ] **E6:** Commit track.

---

## Wave 3 — Integrasi & QA (satu agent, setelah semua track merge)

### Task 5: Checklist penerimaan plan-redesign §12

- [x] Navigasi aktif jelas (admin sidebar + pelanggan bottom nav) — *verifikasi visual disarankan*.
- [x] Setiap halaman P0 punya judul + satu primary CTA — *via `ui_page_header`*.
- [x] Filter/tab penting di URL (booking list, notifikasi kategori) — *perilaku backend tidak diubah*.
- [x] Status booking vs pembayaran tidak digabung satu badge — *pola badge terpisah di list utama*.
- [ ] Mobile 360px: tidak ada overlap sticky footer + bottom nav pada form booking pelanggan — **uji manual**.
- [x] Keyboard: Tab melalui drawer nav; Escape menutup — *`nav.js`*.
- [x] Gate global legacy:

```bash
rg 'shadow-soft|border-white/80' src/Views/ || echo "PASS global"
rg 'text-gray-[0-9]' src/Views/ --glob '!partials/vaksin*' --glob '!partials/*map*' || echo "PASS gray"
```

### Task 6: Dokumentasi & graph

- [x] Update `plan-redesign.md` §13 dengan tanggal audit (catatan teks; screenshot opsional).
- [x] `graphify update .`
- [ ] Ringkas di PR: checklist §12 + route smoke list.

**Smoke route minimum:**

```text
/admin/dashboard /admin/penitipan/booking /admin/grooming/pembayaran /admin/notifikasi
/dashboard /grooming/booking /penitipan/booking /kucing /transaksi
```

---

## Cara menjalankan paralel (dispatch)

1. Selesaikan **Wave 1** satu agent; merge ke branch feature.
2. Spawn **5 subagent** dalam **satu pesan** (parallel): Track A–E dengan prompt di atas + path owner ketat.
3. Review merge conflict — hanya jika agent melanggar tabel owner.
4. Jalankan **Wave 3** pada branch gabungan.

**Model subagent disarankan:** `composer-2.5` atau `composer-2.5-fast` untuk view PHP; `claude-opus-5-thinking-high` untuk Track B (penitipan) jika logika UI padat.

---

## Self-review terhadap plan-redesign.md

| Bagian spec | Task |
|-------------|------|
| §1 Prinsip hierarki | Semua track: header + action center di dashboard/list urgent |
| §3 Layout | Wave 0 done; Wave 1 topbar lokasi |
| §4 Menu | Wave 0 + Task 3 permission/a11y |
| §5 Header halaman | `ui_page_header` di setiap track P0 |
| §6 Token | `index.css` + jangan hex di view |
| §7 Komponen | Wave 1 Task 1–2 |
| §8 Pola halaman | Track A/B/E P0 |
| §9 Responsivitas | `pb-mobile-nav`, kartu list mobile |
| §10 State UI | empty-state + filtered per list dinamis |
| §11 Rollout | Wave 2 per modul, Wave 3 QA |
| §12 Checklist | Task 5 |

**Gap disengaja (fase berikutnya, bukan Wave 2):** pencarian global topbar (§4), sidebar collapse 72px, role “petugas” terpisah (§3.3), drag-drop kalender, feature flag, font Inter (tetap Nunito/Varela per standar repo kecuali produk memutuskan lain).

---

## Eksekusi

Plan disimpan di `docs/superpowers/plans/2026-09-26-plan-redesign-parallel-execution.md`.

**Dua opsi eksekusi:**

1. **Subagent-Driven (disarankan)** — Wave 1 satu agent; Wave 2 lima agent paralel; Wave 3 integrasi.
2. **Inline** — `superpowers:executing-plans` di sesi ini, checkpoint per task.

Mau mulai dari Wave 1 inline, atau langsung dispatch Track A–E (jika Wave 1 sudah kamu merge)?
