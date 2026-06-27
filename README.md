# Aplikasi Petshop

Sistem manajemen layanan petshop: penitipan kucing (pet hotel), grooming, dan pet care dengan booking system.

Spesifikasi fitur lengkap: [idea.md](idea.md)

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | **PHP 8.4** (native, tanpa framework) |
| Database | **MariaDB 10.6+** |
| DB Access | PDO dengan prepared statements |
| Autoload | Composer PSR-4 (`App\` → `src/`) |
| Frontend | PHP server-rendered templates + **Tailwind CSS** (CDN) |
| Web Server | Apache/Nginx (docroot: `public/`) atau `php -S` untuk dev |
| Session | PHP native session (HttpOnly, SameSite=Lax) |

---

## Struktur Folder

```
petshop/
├── README.md
├── composer.json
├── .env                    # Konfigurasi lokal (jangan commit)
├── .env.example
├── public/
│   ├── index.php           # Front controller — semua request masuk sini
│   └── .htaccess
├── config/
│   ├── app.php
│   └── database.php
├── database/
│   ├── schema.sql                  # Referensi PostgreSQL (domain penuh)
│   ├── schema-mariadb-auth.sql     # Implementasi auth MariaDB
│   └── seeds/
│       └── seed-owner.sql
├── routes/
│   ├── web.php             # Public + area pelanggan
│   └── admin.php           # Area internal staff/owner
├── storage/
│   └── logs/
└── src/
    ├── Core/               # Router, Request, Response, Session, Database, Csrf, View
    ├── Middleware/
    ├── Controllers/
    ├── Services/
    ├── Repositories/
    ├── Enums/
    └── Views/
```

---

## Role & Routing

| Role | Tabel DB | Portal | Prefix Route | Middleware |
|------|----------|--------|--------------|------------|
| **Pelanggan** | `pelanggan` | Dashboard pelanggan | `/`, `/login`, `/register`, `/dashboard`, `/change-password` | `auth:pelanggan` |
| **Staff** | `staff` (`role=STAFF`) | Dashboard internal | `/admin/*` | `auth:staff` |
| **Owner** | `staff` (`role=OWNER`) | Dashboard internal + manajemen staff (fase berikutnya) | `/admin/*` | `auth:staff`, `role:owner` (route khusus) |

**Aturan akses:**
- Pelanggan **tidak bisa** akses `/admin/*`
- Staff/Owner **tidak bisa** akses `/dashboard` pelanggan
- Staff dengan `status=NONAKTIF` ditolak login
- Owner tidak bisa dinonaktifkan oleh staff (validasi di fase manajemen staff)

**Session keys:**
- Pelanggan: `auth.pelanggan_id`, `auth.type = PELANGGAN`
- Internal: `auth.staff_id`, `auth.role`, `auth.type = STAFF`

---

## Konvensi Kode

- Namespace: `App\` (PSR-4)
- Database: `snake_case` untuk tabel & kolom
- PHP: `camelCase` methods, `PascalCase` classes
- Query: **selalu** prepared statements via PDO
- Form POST: wajib CSRF token
- Password: `password_hash()` / `password_verify()` (PASSWORD_DEFAULT)
- UUID: di-generate di PHP (`Ramsey\Uuid` tidak dipakai — native `random_bytes`)

---

## Environment Variables

Salin `.env.example` ke `.env`:

```env
APP_NAME=Petshop
APP_URL=http://localhost:8080
APP_ENV=local
APP_DEBUG=true

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=petshop
DB_USERNAME=root
DB_PASSWORD=

SESSION_LIFETIME=120
PASSWORD_RESET_EXPIRY=60
```

---

## Setup Lokal

```bash
# 1. Install dependencies
composer install

# 2. Salin env
cp .env.example .env

# 3. Buat database & import schema auth
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS petshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p petshop < database/schema-mariadb-auth.sql
mysql -u root -p petshop < database/schema-mariadb-kucing.sql
mysql -u root -p petshop < database/seeds/seed-owner.sql

# 4. Jalankan dev server
php -S localhost:8080 -t public
```

Buka http://localhost:8080

**Akun owner default (dev):**
- Email: `owner@petshop.local`
- Password: `password123`

---

## Route Map (Auth)

### Pelanggan

| Method | Path | Keterangan |
|--------|------|------------|
| GET | `/` | Landing / redirect |
| GET/POST | `/register` | Daftar akun |
| GET/POST | `/login` | Login |
| POST | `/logout` | Logout |
| GET/POST | `/forgot-password` | Lupa password |
| GET/POST | `/reset-password` | Reset password (token) |
| GET/POST | `/change-password` | Ubah password (auth) |
| GET | `/dashboard` | Home pelanggan (auth) |
| GET/POST | `/profil` | Lihat & edit profil pelanggan (auth) |
| GET | `/kucing` | Daftar kucing milik pelanggan (auth) |
| GET | `/kucing/tambah` | Form tambah kucing (auth) |
| POST | `/kucing` | Simpan kucing baru (auth) |
| GET | `/kucing/edit` | Form edit kucing — `?id=` (auth) |
| POST | `/kucing/update` | Simpan perubahan kucing (auth) |
| POST | `/kucing/hapus` | Hapus kucing (auth) |

### Staff / Owner (Internal)

| Method | Path | Keterangan |
|--------|------|------------|
| GET/POST | `/admin/login` | Login internal |
| POST | `/admin/logout` | Logout |
| GET/POST | `/admin/change-password` | Ubah password (auth) |
| GET | `/admin/dashboard` | Home internal (auth) |
| GET | `/admin/staff` | Placeholder manajemen staff (owner only) |

---

## Database

| File | DBMS | Keterangan |
|------|------|------------|
| `database/schema.sql` | PostgreSQL | Referensi domain lengkap (diagram/ERD) |
| `database/schema-mariadb-auth.sql` | MariaDB | Implementasi aktual — tabel auth |
| `database/schema-mariadb-kucing.sql` | MariaDB | Tabel kucing & riwayat vaksin |
| `database/seeds/seed-owner.sql` | MariaDB | Seed akun owner dev |

Konversi schema penuh ke MariaDB dilakukan bertahap per modul fitur.

---

## Dokumentasi Terkait

- [idea.md](idea.md) — spesifikasi fitur
- [diagrams/README.md](diagrams/README.md) — diagram UML
- [diagrams/erd/erd-diagram.md](diagrams/erd/erd-diagram.md) — ERD
- [diagrams/sequence/sequence-autentikasi-pelanggan.puml](diagrams/sequence/sequence-autentikasi-pelanggan.puml)
- [diagrams/sequence/sequence-autentikasi-staff.puml](diagrams/sequence/sequence-autentikasi-staff.puml)

---

## Panduan untuk AI Agent

Saat menambah fitur baru:

1. Tambah migration/SQL di `database/` (MariaDB syntax)
2. Buat Repository di `src/Repositories/`
3. Buat Service jika ada business logic
4. Buat Controller di `src/Controllers/Pelanggan/` atau `src/Controllers/Admin/`
5. Daftarkan route di `routes/web.php` atau `routes/admin.php` dengan middleware yang tepat
6. Buat view di `src/Views/` extend layout `pelanggan.php` atau `admin.php`
7. Ikuti pola CSRF + flash message yang sudah ada di auth controllers
