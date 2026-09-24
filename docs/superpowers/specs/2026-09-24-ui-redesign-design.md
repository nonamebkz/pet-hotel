# UI Redesign Pet Hotel — Design Summary

**Tanggal:** 2026-09-24  
**Spec kanonik:** `.cursor/rules/pencatatan-ui-ux-standards.mdc`  
**Status:** Disetujui implisit via permintaan plan redesign

## Tujuan

Menyelaraskan ~94 view PHP + partial nav/layout dengan token `public/css/index.css`, helper `design.php`, dan pola UX §18 — tanpa mengubah route, controller, atau business logic kecuali untuk state UI baru.

## Konteks saat ini

- Token CSS & `design.php` sudah ada; **0** pemakaian `design_*()` di view.
- ~50 file view masih memakai pola legacy `border-white/80` + `shadow-soft`.
- Partial `empty-state`, `filter-chips` masih coral/admin + `gray-*`.
- Layout `admin.php` / `pelanggan.php` belum `pb-mobile-nav` (§5–§7).
- Modul **penitipan** (admin + pelanggan) luas tetapi tidak semua ada di `ui/*_guideline.md`.

## Pendekatan (dipilih)

**Foundation-first, lalu modul vertikal** — urutan: fondasi shared → auth → dashboard → grooming → penitipan → pet-care/CRM/ops → pelanggan → pembersihan token legacy.

| Pendekatan | Pro | Kontra |
|------------|-----|--------|
| Big-bang semua view | Konsisten cepat | Risiko regressi tinggi, review besar |
| **Foundation + modul (dipilih)** | Setiap fase deployable, grep sebagai gate | Butuh disiplin fase |
| Satu halaman end-to-end | Belajar pola awal | Lama sampai nav/layout selaras |

## Prinsip migrasi

1. Ubah **partials & layout** dulu — dampak luas, satu kali.
2. Setiap halaman: ganti kartu → `design_surface()`; CTA → semantic primary; empty → `ui_empty_state()`.
3. Pertahankan `Enums/*::badgeClass()`; sesuaikan hanya jika kontras gagal dengan palet hijau.
4. Legacy `bg-admin` di nav boleh fase akhir (§3.4) setelah primary hijau terbukti di auth/dashboard.

## Definisi selesai per fase

- Tidak ada `border-white/80` / `shadow-soft` baru di file yang disentuh.
- File yang disentuh memakai `text-foreground` / `text-muted-foreground` bukan `gray-*` / `content-secondary` (kecuali alias yang sudah map ke semantic).
- Halaman dinamis punya empty/loading/error sesuai §11.
- Checklist §15 lulus untuk scope fase (review manual 360px + desktop).

## Out of scope

- Migrasi ke Tailwind build / v4 npm.
- React/shadcn.
- Redesign informasi produk di `Petshop Dashboard UI Guideline.md` (referensi historis saja).

Implementasi detail: `docs/superpowers/plans/2026-09-24-ui-redesign-pencatatan-standards.md`.
