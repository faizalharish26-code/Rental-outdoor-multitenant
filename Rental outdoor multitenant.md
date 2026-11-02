# Rental Outdoor Multi-Tenant

Aplikasi sistem informasi rental peralatan *outdoor* berbasis multi-tenant (modular monolith) yang dibangun secara kumulatif.

Stack: **Laravel 13** · **Livewire 4** (starter kit) · **Reverb** · **MariaDB** · **Redis**.

> Basis desain: Artefak SRS, ERD, dan modul praktikum (lihat `references/`).  
> Status & ketertelusuran: `docs/`.

---

## Requirements

| Tool | Versi minimum | Diuji pada |
|---|---|---|
| PHP | 8.3+ (ext: pdo_mysql, redis, mbstring, openssl, ctype, curl, fileinfo, xml, tokenizer, bcmath) | 8.3.20 |
| Composer | 2.x | 2.7.1 |
| Node / npm | 22 / 10 | 24.x / 11.x |
| MariaDB | 10.6+ | 11.4.5 |
| Redis | 7.x | 7.2.6 |
| Git | 2.x | 2.50.1 |

---

## Setup (Instalasi Bersih)

```bash
git clone git@github.com:USERNAME-ANDA/rental_outdoor_multitenant.git
cd rental_outdoor_multitenant

# Salin file environment
cp .env.example .env
```

> **Edit `.env`:** Pastikan variabel `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai dengan konfigurasi MariaDB lokal Anda. Isikan juga variabel `REVERB_APP_*` untuk kebutuhan realtime.  
> **Default Konfigurasi:**  
> `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=rental_outdoor_multitenant`, `SESSION_DRIVER=redis`, `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`, `APP_TIMEZONE=UTC`, `APP_LOCALE=id`.

Jalankan perintah berikut untuk menginisialisasi proyek:

```bash
composer install
php artisan key:generate
npm install
npm run build
php artisan migrate --seed           # Membuat tabel pada database dev
```

### Database Pengujian Terpisah

Pengujian otomatis (*testing*) berjalan pada MariaDB menggunakan database terpisah bernama **`kantin_test`** (lihat `phpunit.xml`).

Buat database pengujian sekali saja melalui MariaDB CLI:

```sql
CREATE DATABASE IF NOT EXISTS kantin_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON kantin_test.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

---

## Menjalankan Server (Dev)

Perintah ini akan menjalankan server HTTP Laravel, *queue worker*, dan Vite secara bersamaan dalam satu terminal:

```bash
composer run dev     # HTTP server + queue worker + Vite
```

Untuk fitur *realtime* (WebSocket/Reverb), jalankan perintah ini di terminal terpisah:

```bash
php artisan reverb:start
```

Buka **`http://localhost:8000`** atau **`http://127.0.0.1:8000`** di browser Anda untuk mengakses aplikasi.

---

## Test & Quality Gate

```bash
php artisan test                                   # Jalankan pengujian (feature + unit) -> kantin_test
vendor\bin\pint --test                           # Pemeriksaan format kode PHP (Laravel Pint)
npm run build                                      # Kompilasi aset produksi (Vite)
composer validate --strict && composer audit       # Audit dependensi composer
```

---

## Troubleshooting Cepat

| Gejala | Penyebab | Perbaikan |
|---|---|---|
| `could not find driver` | Ekstensi `pdo_mysql` belum aktif | Aktifkan ekstensi di `php.ini` lalu restart PHP |
| `Access denied ... kantin_test` | User DB belum diberikan hak akses | Jalankan perintah `GRANT ALL PRIVILEGES` untuk database `kantin_test` |
| `Failed to create broadcaster "reverb"` | Variabel `REVERB_APP_*` belum diisi | Lengkapi variabel `REVERB_APP_ID/KEY/SECRET` pada file `.env` |
| `Vite manifest not found` | Aset belum dikompilasi | Jalankan `npm install && npm run build` |

---

## Struktur Direktori

```text
app/             # Logika aplikasi Laravel
bootstrap/       # Inisialisasi dan konfigurasi framework
config/          # File konfigurasi aplikasi
database/        # Migration, seeder, dan factory
public/          # Entry point aplikasi (index.php) dan aset publik
resources/       # Tampilan Blade, komponen Livewire, dan skrip frontend (JS/CSS)
routes/          # Rute HTTP dan Channels broadcasting
tests/           # File pengujian otomatis (Feature & Unit test)
references/      # Artefak sumber (SRS, ERD, Mockup, Modul) — jangan diedit
docs/            # Dokumentasi arsitektur dan matriks ketertelusuran
storage/logs/    # Catatan log aplikasi (laravel.log)
```