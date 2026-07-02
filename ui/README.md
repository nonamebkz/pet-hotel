# UI Guidelines – Petshop

Dokumentasi UX/UI untuk implementasi per-halaman. Setiap file bisa dieksekusi terpisah oleh agent.

## Mulai Di Sini

1. Baca **[MASTER_UI_GUIDELINE.md](./MASTER_UI_GUIDELINE.md)** — design tokens, pola global, panduan eksekusi
2. Pilih file guideline halaman target di bawah
3. Jalankan prompt eksekusi

### Contoh Prompt

```
Implementasikan UI guideline @ui/booking_grooming_ui_guideline.md
sesuai @ui/MASTER_UI_GUIDELINE.md — fokus item CRITICAL di Acceptance Criteria.
```

> Dokumen historis design system pelanggan: [`Petshop Dashboard UI Guideline.md`](../Petshop%20Dashboard%20UI%20Guideline.md)  
> Sumber eksekusi aktif: **MASTER_UI_GUIDELINE.md** di folder ini.

---

## Daftar Guideline per Modul

### Auth
| File | Halaman | Route |
|---|---|---|
| [login_admin_ui_guideline.md](./login_admin_ui_guideline.md) | Login admin | `/admin/login` |
| [ubah_password_ui_guideline.md](./ubah_password_ui_guideline.md) | Ubah password admin | `/admin/change-password` |

### Dashboard
| File | Halaman | Route |
|---|---|---|
| [dashboard_owner_ui_guideline.md](./dashboard_owner_ui_guideline.md) | Dashboard owner | `/admin/dashboard` |

### Grooming Admin
| File | Halaman | Route |
|---|---|---|
| [jenis_grooming_ui_guideline.md](./jenis_grooming_ui_guideline.md) | Jenis layanan grooming | `/admin/grooming/layanan` |
| [kuota_grooming_ui_guideline.md](./kuota_grooming_ui_guideline.md) | Kuota grooming | `/admin/grooming/kuota` |
| [tambah_kuota_grooming_ui_guideline.md](./tambah_kuota_grooming_ui_guideline.md) | Tambah kuota grooming | `/admin/grooming/kuota/tambah` |
| [booking_grooming_ui_guideline.md](./booking_grooming_ui_guideline.md) | Booking grooming | `/admin/grooming/booking` |
| [verifikasi_bukti_transfer_ui_guideline.md](./verifikasi_bukti_transfer_ui_guideline.md) | Verifikasi bukti transfer | `/admin/grooming/pembayaran` |

### Pet Care Admin
| File | Halaman | Route |
|---|---|---|
| [layanan_petcare_ui_guideline.md](./layanan_petcare_ui_guideline.md) | Daftar layanan pet care | `/admin/pet-care/layanan` |
| [tambah_layanan_ui_guideline.md](./tambah_layanan_ui_guideline.md) | Tambah layanan pet care | `/admin/pet-care/layanan/tambah` |

### CRM & Staff
| File | Halaman | Route |
|---|---|---|
| [manajemen_pelanggan_ui_guideline.md](./manajemen_pelanggan_ui_guideline.md) | Manajemen pelanggan | `/admin/pelanggan` |
| [manajemen_staff_ui_guideline.md](./manajemen_staff_ui_guideline.md) | Manajemen staff | `/admin/staff` |
| [tambah_staff_ui_guideline.md](./tambah_staff_ui_guideline.md) | Tambah staff | `/admin/staff/tambah` |

### Operations
| File | Halaman | Route |
|---|---|---|
| [notifikasi_ui_guideline.md](./notifikasi_ui_guideline.md) | Notifikasi admin | `/admin/notifikasi` |
| [laporan_ui_guideline.md](./laporan_ui_guideline.md) | Laporan | `/admin/laporan` |
| [pengaturan_bisnis_ui_guideline.md](./pengaturan_bisnis_ui_guideline.md) | Pengaturan bisnis | `/admin/pengaturan` |

### Customer
| File | Halaman | Route |
|---|---|---|
| [grooming_ui_guideline.md](./grooming_ui_guideline.md) | Katalog grooming pelanggan | `/grooming` |
| [tambah_kucing_ui_guideline.md](./tambah_kucing_ui_guideline.md) | Tambah kucing | `/kucing/tambah` |

### Transaksi
| File | Halaman | Route |
|---|---|---|
| [riwayat_transaksi_ui_guideline.md](./riwayat_transaksi_ui_guideline.md) | Riwayat transaksi pelanggan | `/transaksi` |
| [riwayat_transaksi_advanced_ui_guideline.md](./riwayat_transaksi_advanced_ui_guideline.md) | Riwayat transaksi admin | `/admin/transaksi` |

---

## Urutan Eksekusi Disarankan

1. Shared partials (empty state, filter bar) — lihat MASTER section C
2. Auth → Dashboard → Grooming Admin cluster → sisanya
