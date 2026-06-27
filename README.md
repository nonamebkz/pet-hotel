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
├── docker-compose.yml      # Deployment MariaDB + PHP Apache
├── Dockerfile
├── composer.json
├── .env                    # Konfigurasi lokal (jangan commit)
├── .env.example
├── .env.docker.example     # Template env untuk Docker Compose
├── scripts/
│   ├── db-init.sh          # Import schema & seed (Docker / lokal)
│   └── docker-up.sh        # Build, up, init DB
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
| **Owner** | `staff` (`role=OWNER`) | Dashboard internal + manajemen staff & pengaturan bisnis | `/admin/*` | `auth:staff`, `role:owner` (route khusus) |

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

# Pengaturan bisnis (default instalasi — owner dapat ubah lewat /admin/pengaturan)
PETSHOP_LAT=-6.2088
PETSHOP_LNG=106.8456
PICKUP_FREE_RADIUS_KM=3
PICKUP_EXTRA_FEE_PER_KM=5000
PAYMENT_DEADLINE_HOURS=24
PETSHOP_BANK_NAME=BCA
PETSHOP_BANK_ACCOUNT=1234567890
PETSHOP_BANK_ACCOUNT_NAME=Petshop Sejahtera
PROMO_MIN_DAYS=7
PROMO_DISCOUNT_PERCENT=10
MIN_VACCINATION_COUNT=1
PETSHOP_WHATSAPP=6281234567890
```

Nilai bisnis di atas dipakai sebagai **default bootstrap**. Setelah owner menyimpan pengaturan lewat dashboard, database (`pengaturan_petshop`) menjadi sumber kebenaran runtime.

---

## Setup Lokal

### Opsi A — Docker (disarankan untuk server)

```bash
cp .env.docker.example .env
# Sesuaikan APP_URL, DB_PASSWORD, APP_DEBUG=false untuk production

chmod +x scripts/*.sh
./scripts/docker-up.sh
```

Perintah lain:

```bash
./scripts/db-init.sh --docker schema      # hanya schema
./scripts/db-init.sh --docker seed-dev    # seed development
./scripts/db-init.sh --docker --wait all  # schema + seed (DB sudah jalan)

docker compose up -d --build                # manual tanpa init
docker compose down                         # stop
docker compose down -v                      # stop + hapus volume DB
./scripts/docker-up.sh --fresh              # reset DB dari nol
```

### Opsi B — PHP native + MariaDB lokal/Docker

```bash
# 1. Install dependencies
composer install

# 2. Salin env
cp .env.example .env

# 3. Init database (auto-detect container petshop-mariadb atau CLI lokal)
chmod +x scripts/db-init.sh
./scripts/db-init.sh --wait all

# Atau manual per file:
# mysql -u root -p petshop < database/schema-mariadb-auth.sql
# ...

# 4. Jalankan dev server
php -S localhost:8080 -t public
```

Buka http://localhost:8080

**Akun owner default (dev):**
- Email: `owner@petshop.local`
- Password: `password123`

---

## Deployment Server (Docker Compose)

Stack: **MariaDB 10.11** + **PHP 8.4 Apache** (docroot `public/`).

1. Clone repo ke server, masuk folder proyek.
2. `cp .env.docker.example .env` — set `APP_URL` ke domain/IP production, `APP_DEBUG=false`, ganti `DB_PASSWORD`.
3. `chmod +x scripts/*.sh && ./scripts/docker-up.sh`
4. (Opsional) Cron notifikasi pembayaran — jalankan tiap 15 menit di host:

```bash
*/15 * * * * docker exec petshop-app php /var/www/html/scripts/send-payment-reminders.php
*/15 * * * * docker exec petshop-app php /var/www/html/scripts/expire-pending-payments.php
```

- `send-payment-reminders.php` — pengingat 6 jam sebelum `batas_waktu_bayar` (1x per transaksi)
- `expire-pending-payments.php` — batalkan transaksi lewat batas waktu & kirim notifikasi jatuh tempo

5. Reverse proxy (Nginx/Caddy) arahkan ke `localhost:${APP_PORT}` jika perlu HTTPS.

Volume persisten: `petshop_mariadb_data`, `public/uploads`, `storage/logs`.

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
| GET | `/pet-care` | Daftar layanan pet care (auth) |
| GET | `/pet-care/booking` | Form booking pet care (auth) |
| POST | `/pet-care/booking` | Submit booking (auth) |
| GET | `/pet-care/riwayat` | Riwayat booking pet care (auth) |
| POST | `/pet-care/booking/batalkan` | Batalkan booking (auth) |
| GET | `/grooming` | Daftar layanan grooming (auth) |
| GET/POST | `/grooming/booking` | Form & submit booking grooming (auth) |
| GET | `/grooming/estimasi-pickup` | Estimasi jarak & biaya antar-jemput JSON (auth) |
| GET | `/grooming/riwayat` | Riwayat booking grooming (auth) |
| GET | `/grooming/detail` | Detail booking — `?id=` (auth) |
| POST | `/grooming/booking/batalkan` | Batalkan booking (auth) |
| GET/POST | `/grooming/pembayaran` | Upload bukti transfer (auth) |
| GET | `/grooming/invoice` | Invoice setelah lunas — `?id=` (auth) |
| GET | `/penitipan` | Daftar paket penitipan (auth) |
| GET/POST | `/penitipan/booking` | Form & submit booking penitipan (auth) |
| GET | `/penitipan/estimasi-biaya` | Estimasi biaya penitipan JSON (auth) |
| GET | `/penitipan/estimasi-pickup` | Estimasi jarak & biaya antar-jemput JSON (auth) |
| GET | `/penitipan/riwayat` | Riwayat booking penitipan (auth) |
| GET | `/penitipan/detail` | Detail booking — `?id=` (auth) |
| POST | `/penitipan/booking/batalkan` | Batalkan booking (auth) |
| GET/POST | `/penitipan/pembayaran` | Upload bukti transfer (auth) |
| GET | `/penitipan/invoice` | Invoice setelah lunas — `?id=` (auth) |
| GET | `/penitipan/perpanjangan/estimasi` | Estimasi perpanjangan JSON (auth) |
| POST | `/penitipan/perpanjangan` | Ajukan perpanjangan (auth) |
| GET/POST | `/penitipan/perpanjangan/pembayaran` | Upload bukti perpanjangan (auth) |

### Staff / Owner (Internal)

| Method | Path | Keterangan |
|--------|------|------------|
| GET/POST | `/admin/login` | Login internal |
| POST | `/admin/logout` | Logout |
| GET/POST | `/admin/change-password` | Ubah password (auth) |
| GET | `/admin/dashboard` | Home internal (auth) |
| GET | `/admin/staff` | Daftar akun staff (owner only) |
| GET/POST | `/admin/staff/tambah` | Tambah akun staff (owner only) |
| GET/POST | `/admin/staff/edit` | Edit data staff — `?id=` (owner only) |
| GET/POST | `/admin/staff/reset-password` | Reset password staff — `?id=` (owner only) |
| POST | `/admin/staff/status` | Aktifkan / nonaktifkan staff (owner only) |
| GET | `/admin/laporan` | Ringkasan laporan booking per layanan (auth) |
| GET | `/admin/laporan/grooming` | Laporan data grooming — filter periode & status (auth) |
| GET | `/admin/laporan/penitipan` | Laporan data pet hotel — filter periode & status (auth) |
| GET | `/admin/laporan/pet-care` | Laporan data pet care — filter periode, status & layanan (auth) |
| GET/POST | `/admin/pengaturan` | Pengaturan bisnis petshop (owner only) |
| GET/POST | `/admin/pet-care/layanan/*` | CRUD layanan pet care (auth) |
| GET/POST | `/admin/pet-care/slot/*` | Kelola slot dokter (auth) |
| GET/POST | `/admin/pet-care/booking/*` | Kelola booking pet care (auth) |
| GET/POST | `/admin/grooming/layanan/*` | CRUD jenis grooming (auth) |
| GET/POST | `/admin/grooming/kuota/*` | Kelola kuota grooming harian (auth) |
| GET/POST | `/admin/grooming/booking/*` | Konfirmasi/tolak booking, update status (auth) |
| GET/POST | `/admin/grooming/pembayaran/*` | Verifikasi bukti transfer (auth) |
| GET/POST | `/admin/penitipan/paket/*` | CRUD paket penitipan (auth) |
| GET/POST | `/admin/penitipan/kamar/*` | CRUD kamar penitipan (auth) |
| GET/POST | `/admin/penitipan/kuota/*` | Kelola kuota penitipan (auth) |
| GET/POST | `/admin/penitipan/booking/*` | Konfirmasi/tolak, check-in/out (auth) |
| GET/POST | `/admin/penitipan/monitoring/*` | Input monitoring harian (auth) |
| GET/POST | `/admin/penitipan/perpanjangan/*` | Konfirmasi/tolak perpanjangan (auth) |
| GET/POST | `/admin/penitipan/pembayaran/*` | Verifikasi bukti penitipan (auth) |

---

## Database

| File | DBMS | Keterangan |
|------|------|------------|
| `database/schema.sql` | PostgreSQL | Referensi domain lengkap (diagram/ERD) |
| `database/schema-mariadb-auth.sql` | MariaDB | Implementasi aktual — tabel auth |
| `database/schema-mariadb-kucing.sql` | MariaDB | Tabel kucing & riwayat vaksin |
| `database/schema-mariadb-pet-care.sql` | MariaDB | Tabel layanan, slot, & booking pet care |
| `database/schema-mariadb-grooming.sql` | MariaDB | Tabel jenis grooming, kuota, & booking grooming |
| `database/schema-mariadb-penitipan.sql` | MariaDB | Tabel paket, kamar, kuota, booking penitipan, monitoring, perpanjangan |
| `database/schema-mariadb-transaksi.sql` | MariaDB | Tabel transaksi, bukti transfer, & invoice |
| `database/schema-mariadb-pengaturan.sql` | MariaDB | Tabel pengaturan bisnis petshop |
| `database/seeds/seed-owner.sql` | MariaDB | Seed akun owner dev |
| `database/seeds/seed-pengaturan-dev.sql` | MariaDB | Seed pengaturan bisnis default dev |
| `database/seeds/seed-pet-care-dev.sql` | MariaDB | Seed layanan & slot pet care dev |
| `database/seeds/seed-grooming-dev.sql` | MariaDB | Seed jenis grooming & kuota dev |
| `database/seeds/seed-penitipan-dev.sql` | MariaDB | Seed paket, kamar, & kuota penitipan dev |

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
