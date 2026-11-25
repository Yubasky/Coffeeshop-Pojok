# Sistem Informasi Operasional & Pemesanan — Pojok UKM Tanjung Gading

Proyek Akhir Rekayasa Perangkat Lunak (RPL) — **Kelompok 2** (Politeknik Negeri Medan).  
Aplikasi web berbasis **CodeIgniter 4** dan **PostgreSQL** untuk mengelola operasional kedai kopi Pojok UKM Tanjung Gading mulai dari pemesanan mandiri pelanggan, kasir (POS & shift), antrean barista (*Bar Display*), stok bahan baku, hingga laporan & audit owner.

> **Demo Online (Live):** [https://pojok.vulnera.web.id](https://pojok.vulnera.web.id)

---

## 1. Rangkuman Proyek

### Tech Stack & Arsitektur
* **Backend & Framework:** PHP `^8.2`, CodeIgniter `^4.7`
* **Database:** PostgreSQL 13+ (driver `Postgre` / `pdo_pgsql`)
* **Pola Arsitektur:** *Layered MVC + Service*
  1. **Routing & Security Layer:** `app/Config/Routes.php`, `AuthFilter` (cek login & *idle timeout* 30 menit), dan `RoleFilter` (proteksi RBAC lintas peran dengan HTTP `403`).
  2. **Application & Presentation Layer:** `app/Controllers/` & `app/Views/` (tampilan responsif *mobile-first* untuk pelanggan serta *workspace* kasir, barista, dan owner).
  3. **Service Layer:** `app/Services/` (saat ini menggunakan `MockDataService` berbasis session untuk demo UI/alur).
  4. **Database Layer:** `app/Database/Migrations/` & `app/Database/Seeds/` (skema PostgreSQL lengkap siap pakai).

### Modul & Fitur Utama
| Peran | Fitur Utama | Controller Terkait |
| :--- | :--- | :--- |
| **Autentikasi** | Login 4 peran, registrasi pelanggan (tanpa WhatsApp), logout, sesi *idle* 30 menit | `AuthController` |
| **Pelanggan** | Katalog menu aktif + cari & filter (`KOPI`/`NON_KOPI`), keranjang, checkout, status pesanan | `Pelanggan\KatalogController`, `Pelanggan\PesananController` |
| **Kasir** | Input pesanan offline (POS), konfirmasi bayar (`TUNAI`/`QRIS`), kirim ke barista, batalkan pesanan, buka/tutup shift | `Kasir\PosController`, `Kasir\PembayaranController`, `Kasir\ShiftController` |
| **Barista** | Antrean pesanan masuk (*Bar Display*), mulai racik, selesaikan pesanan | `Barista\AntreanController` |
| **Stok (Owner & Barista)** | Monitoring daftar bahan baku, tambah bahan baku, koreksi stok | `StokController` |
| **Owner** | Dashboard laporan pemasukan, CRUD katalog menu, kelola akun staf, reset password, riwayat & audit | `Owner\LaporanController`, `Owner\MenuController`, `Owner\PenggunaController` |

> **Catatan Status Pengembangan:**  
> * Controller dan View saat ini masih terhubung ke `App\Services\MockDataService` (data disimpan sementara di session browser untuk demo alur/UI).  
> * Skema database PostgreSQL (**10 tabel 3NF + 1 view `v_ringkasan_shift`**) dan **Seeder** sudah tersedia di `app/Database/Migrations/` dan `app/Database/Seeds/` untuk tahap integrasi database nyata.

### Struktur Direktori Penting
```text
Coffeeshop-Pojok/
├── app/
│   ├── Config/         # Konfigurasi App, Database, Routes, Migrations
│   ├── Controllers/    # Controller per peran (Owner, Kasir, Barista, Pelanggan, Stok, Auth)
│   ├── Database/
│   │   ├── Migrations/ # 10 tabel PostgreSQL + 1 view v_ringkasan_shift
│   │   └── Seeds/      # 10 seeder data uji + DatabaseSeeder
│   ├── Filters/        # AuthFilter & RoleFilter (RBAC)
│   ├── Models/         # Folder model CI4 (untuk integrasi query PostgreSQL)
│   ├── Services/       # Service bisnis (saat ini MockDataService)
│   └── Views/          # Tampilan HTML/PHP per peran & layouts
├── public/             # Entry point (index.php) dan aset statis (assets/css/app.css)
├── Document/           # Dokumen SRS Final & Wireframe
├── .env.contoh         # Contoh konfigurasi environment PostgreSQL & baseURL
└── composer.json       # Dependensi PHP & CodeIgniter 4
```

### Struktur Database PostgreSQL (Migrations)
1. `pengguna` — Akun pengguna (`OWNER`, `KASIR`, `BARISTA`, `PELANGGAN`)
2. `menu` — Katalog menu (`KOPI`, `NON_KOPI`)
3. `bahan_baku` — Master bahan baku & `stok_terkini`
4. `shift` — Shift operasional (`PAGI`/`SORE`), dibatasi hanya 1 shift `AKTIF`
5. `pesanan` — Header pesanan (`MANDIRI`/`KASIR`)
6. `detail_pesanan` — Rincian item pesanan + snapshot `harga_satuan`
7. `pembayaran` — Konfirmasi pelunasan (`TUNAI`/`QRIS`)
8. `riwayat_stok` — Mutasi stok (`STOK_AWAL`, `MASUK`, `PENYESUAIAN`, `PEMAKAIAN`, `PENGEMBALIAN`)
9. `riwayat_pesanan` — Jejak transisi status pesanan
10. `log_aktivitas` — Audit log sistem & keamanan (`JSONB`)
11. `v_ringkasan_shift` *(View)* — Rekap otomatis omzet Tunai, QRIS, total penjualan, & pembatalan per shift

---

## 2. Cara Setup Proyek & Load Database di PostgreSQL Lokal

### Prasyarat
* **PHP >= 8.2** dengan ekstensi aktif: `intl`, `mbstring`, `json`, `curl`, **`pgsql`**, **`pdo_pgsql`**
* **Composer**
* **PostgreSQL 13+** (`psql` / pgAdmin / DBeaver)

Cek apakah ekstensi PostgreSQL di PHP sudah aktif:
```bash
php -m | grep -i pgsql
```

### Langkah 1 — Clone & Install Dependensi
```bash
git clone https://github.com/Yubasky/Coffeeshop-Pojok.git
cd Coffeeshop-Pojok
composer install
```

### Langkah 2 — Buat User & Database di PostgreSQL Lokal
Masuk ke `psql` sebagai superuser `postgres`:

* **Linux (Ubuntu/Debian):**
  ```bash
  sudo -u postgres psql
  ```
* **Windows / macOS:**
  ```bash
  psql -U postgres
  ```

Lalu jalankan perintah SQL berikut di dalam prompt `psql` (sesuai konfigurasi default di `.env.contoh`):
```sql
CREATE USER pojok_user WITH PASSWORD 'pojok123';
CREATE DATABASE pojok_db OWNER pojok_user;
GRANT ALL PRIVILEGES ON DATABASE pojok_db TO pojok_user;
\q
```

> Jika PostgreSQL kamu memakai user/database lain (misalnya `postgres`), silakan sesuaikan pada langkah `.env` di bawah.

### Langkah 3 — Konfigurasi `.env`
Salin file `.env.contoh` menjadi `.env`:
```bash
cp .env.contoh .env
```

Pastikan isi bagian `APP` dan `DATABASE` di `.env` sesuai environment lokal kamu:
```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8084/'

database.default.hostname = localhost
database.default.database = pojok_db
database.default.username = pojok_user
database.default.password = pojok123
database.default.DBDriver = Postgre
database.default.DBPrefix =
database.default.charset  = utf8
database.default.schema   = public
database.default.port     = 5432
```

> **Penting:** Port di `app.baseURL` harus sama dengan port server lokal yang kamu jalankan agar CSS (`public/assets/css/app.css`) dan link aplikasi termuat dengan benar.

### Langkah 4 — Jalankan Migration & Seeder
Pastikan PostgreSQL sedang berjalan, lalu jalankan:
```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

**Catatan jika ingin mengulang/reset database dari awal:**  
Seeder menggunakan ID relasional tetap (mulai dari `1`) dan tidak bersifat *idempotent*. Jika ingin mengisi ulang seeder, reset tabel terlebih dahulu:
```bash
php spark migrate:refresh
php spark db:seed DatabaseSeeder
```

### Langkah 5 — Cek Hasil Load Database di `psql`
Masuk ke database menggunakan `psql`:
```bash
psql -h localhost -U pojok_user -d pojok_db
```
Di dalam `psql`, jalankan perintah berikut untuk memastikan seluruh tabel, view, dan data sudah masuk:
```sql
-- Daftar 10 tabel (+ tabel migrations)
\dt

-- Daftar view (v_ringkasan_shift)
\dv

-- Cek data akun, menu, dan ringkasan shift
SELECT pengguna_id, nama, username, peran FROM pengguna;
SELECT menu_id, nama, kategori, harga, aktif FROM menu;
SELECT shift_id, jenis_shift, tanggal, status, total_penjualan FROM v_ringkasan_shift;
\q
```

#### Akun Uji dari Seeder Database (`PenggunaSeeder`)
Semua akun pada tabel `pengguna` hasil seeder memakai password **`Pojok123!`**:
* **Owner:** `owner_budi`
* **Kasir:** `kasir_siti`, `kasir_rina`, `kasir_dedi`
* **Barista:** `barista_andi`, `barista_reza`
* **Pelanggan:** `dewi_l`, `fajar_n`, `putri_ayu`, `hendra_w`

*(Untuk login demo UI tanpa DB melalui `MockDataService`, gunakan tombol pilihan peran di halaman login: `ayu.pelanggan`, `rina.kasir`, `adi.barista`, atau `maya.owner`.)*

### Langkah 6 — Menjalankan Aplikasi Lokal
Jalankan server CodeIgniter pada port yang sama dengan `app.baseURL` di `.env`:
```bash
php spark serve --port 8084
```
Lalu buka di browser: `http://localhost:8084/`

---

## 3. Cheat Sheet Dasar Git (Panduan Tim)

### A. Setup Awal (Sekali Saja)
```bash
# Set identitas commit
git config --global user.name "Nama Kamu"
git config --global user.email "email@kamu.com"

# Clone repository
git clone https://github.com/Yubasky/Coffeeshop-Pojok.git
cd Coffeeshop-Pojok
```

### B. Alur Kerja Harian (Sebelum & Sesudah Ngoding)
```bash
# 1. Cek posisi branch dan perubahan lokal
git status

# 2. Ambil update terbaru dari branch main sebelum mulai kerja
git switch main
git pull origin main

# 3. Buat branch baru untuk fitur/tugas kamu (hindari kerja langsung di main)
git switch -c fitur/nama-fitur

# 4. Setelah selesai edit file, cek file apa saja yang berubah
git status
git diff

# 5. Tandai file untuk di-commit
git add .                  # semua perubahan
# atau spesifik per file:
git add app/Controllers/NamaController.php

# 6. Simpan commit dengan pesan yang jelas
git commit -m "feat: deskripsi singkat perubahan"

# 7. Upload branch ke GitHub
git push -u origin fitur/nama-fitur
```

### C. Sinkronisasi & Gabung Branch (`merge`)
```bash
# Pindah ke main dan pastikan paling baru
git switch main
git pull origin main

# Gabungkan branch fitur ke main
git merge fitur/nama-fitur
git push origin main

# Hapus branch fitur lokal jika sudah selesai di-merge
git branch -d fitur/nama-fitur
```

### D. Perintah Penyelamat / Cek Riwayat
```bash
# Lihat daftar commit ringkas
git log --oneline -n 10

# Lihat semua branch (lokal & remote)
git branch -a

# Batalkan perubahan pada 1 file yang belum di-commit
git restore path/ke/file.php

# Simpan sementara perubahan yang belum siap di-commit (misalnya mau pindah branch)
git stash
git stash pop   # kembalikan perubahan yang di-stash
```

### E. Aturan Penting Git di Proyek Ini
1. **Jangan pernah commit file `.env` atau folder `vendor/`** (sudah diblokir otomatis oleh `.gitignore`). Untuk konfigurasi contoh, gunakan `.env.contoh`.
2. **Selalu `git pull origin main`** sebelum membuat branch atau mulai mengerjakan fitur baru agar minim konflik.
3. **Commit per tugas kecil yang jelas** (contoh: `feat: tambah filter kategori menu`, `fix: perbaiki validasi shift aktif`).
