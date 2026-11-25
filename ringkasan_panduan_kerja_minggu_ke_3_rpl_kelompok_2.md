# Ringkasan, Cara Pengerjaan, & Pembagian Tugas Minggu ke-3 (Sprint 3)

**Proyek Akhir Rekayasa Perangkat Lunak (RPL)**  
* **Dosen Pengampu:** Cut Try Utari, S.T., M.Kom (Politeknik Negeri Medan)  
* **Kelompok:** Kelompok 2  
* **Studi Kasus:** Sistem Informasi Operasional & Pemesanan — **Pojok UKM Tanjung Gading**  
* **Tema Minggu ke-3:** Perancangan Arsitektur dan Implementasi Awal  
* **Agenda di Kelas:** Presentasi Arsitektur, Implementasi Database, dan Demo Fitur Awal  
* **Tech Stack:** CodeIgniter 4 (PHP) & PostgreSQL 13+  
* **Acuan Utama:** `Project RPL- Pertemuan 11-15.pdf` & `SRS_Final_Sistem_Informasi_Pojok_UKM_Tanjung_Gading_Rapi.docx`

---

## 1. Aturan Wajib Sebelum Mulai Mengerjakan (Penyelarasan SRS Final)

Agar implementasi kode dan tampilan sinkron dengan **ERD 10 tabel** serta tidak membuang waktu pada fitur yang tidak diminta, seluruh anggota **wajib** mematuhi batasan ruang lingkup (Bab 1.2, Bab 6.2 Tabel 6.2, dan BS-04 SRS Final):

* ❌ **Jangan Dibuat / Wajib Dihapus dari UI & Kode:**
  * Fitur **Pesan-Antar (*Delivery*) & Ongkir** (opsi *Antar Driver*, ongkir Rp 8.000, input alamat, dan patokan).
  * Input **Nomor Meja** pada alur pemesanan.
  * Fitur **Multi-Cabang** (sistem hanya untuk operasional internal satu kedai Pojok UKM Tanjung Gading).
  * Input **Nomor WhatsApp** pada form Registrasi Pelanggan.
  * Tombol **Ganti / Batalkan Pesanan** pada halaman status pesanan sisi Pelanggan.
  * Kartu **Pengeluaran & Laba Bersih** pada Dashboard Owner.
* ✅ **Wajib Ditambahkan:**
  * Menu navigasi **Katalog Menu** di sidebar Dashboard Owner (untuk akses `FR-14` & `FR-15`).

---

## 2. Apa yang Harus Dibuat di Minggu ke-3? (4 Output Wajib)

Berdasarkan panduan perkuliahan Minggu ke-3, kelompok wajib menghasilkan **4 luaran utama**:

| No | Output Wajib | Bentuk Hasil Akhir |
| :--- | :--- | :--- |
| **1** | **Diagram Arsitektur Perangkat Lunak** | Gambar diagram 4 lapisan (*Layered MVC*) + penjelasan singkat pemilihan *architectural pattern* |
| **2** | **Struktur Modul dan Komponen** | Tabel/diagram pemetaan 6 modul SRS ke folder CodeIgniter 4 + *screenshot* struktur folder proyek |
| **3** | **Database Terimplementasi** | 10 tabel 3NF + 1 *view* (`v_ringkasan_shift`) aktif di PostgreSQL, terisi data uji (*seeder*), dan terkoneksi ke CI4 |
| **4** | **Implementasi Awal Aplikasi** | Aplikasi web berjalan di browser dengan fitur Autentikasi/RBAC 4 peran, Katalog Menu, serta fondasi operasional awal |

---

## 3. Panduan Detail Cara Pengerjaan per Output

### Output 1: Diagram Arsitektur Perangkat Lunak & *Architectural Pattern*

* **Pattern yang Ditetapkan:** **Layered Architecture** dengan pola **MVC (*Model–View–Controller*)** berbasis *Web Client-Server*.
* **Komponen 4 Lapisan (*Layer*) yang Harus Digambar:**
  1. **Presentation / Client Layer (Browser Pengguna):**
     * Perangkat: Ponsel Pelanggan (*mobile-first*), PC/tablet Kasir & Owner, serta monitor/tablet *Bar Display* Barista.
     * Komunikasi: Protokol **HTTPS** dan *polling* berkala (**AJAX/Fetch**) untuk pembaruan antrean pesanan.
  2. **Routing & Security Layer (CI4 Routes & Filters):**
     * Mengatur rute URL berdasarkan peran pengguna.
     * Menjalankan filter keamanan **RBAC** (`AuthFilter`, `RoleFilter`) dan pengecekan batas sesi *idle* **30 menit** sebelum request masuk ke Controller.
  3. **Application & Business Logic Layer (Controllers, Services, & Views CI4):**
     * Memisahkan tampilan (**View**), pengendali request (**Controller**), dan logika bisnis kritis (**Service**)—seperti transaksi atomik pembayaran (`SELECT ... FOR UPDATE`), mutasi stok bahan baku, dan pencatatan audit.
  4. **Data Access & Database Layer (Models CI4 & PostgreSQL 13+):**
     * Menangani *query* ke 10 tabel fisik 3NF dan 1 *database view* (`v_ringkasan_shift`).
* **Cara Pengerjaan:**
  1. Buka **Draw.io (diagrams.net)** atau **Figma**.
  2. Susun 4 kotak besar berurutan (dari atas ke bawah atau kiri ke kanan) mewakili tiap *layer* di atas.
  3. Tambahkan panah dua arah (*Request* & *Response*) antar-lapisan lengkap dengan label protokol/mekanismenya (contoh: `HTTPS / AJAX Polling`, `Filter Check / HTTP 403`, `Query SQL / PDO Postgre`).
  4. Ekspor ke format `.png` / `.pdf` resolusi tinggi dan siapkan 1 paragraf alasan mengapa pola **Layered MVC + Service** dipilih (memudahkan pembagian kerja tim, menjaga keamanan akses per peran, dan memisahkan logika transaksi kritis dari tampilan).

---

### Output 2: Struktur Modul dan Komponen

* **Pemetaan 6 Modul Fungsional (Tabel 2.2 SRS Final) ke Folder CodeIgniter 4:**

| No | Modul Sistem | Kode FR | Controller (`app/Controllers`) | Model / Service / Filter (`app/Models`, `app/Services`, `app/Filters`) | View (`app/Views`) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **1** | **Autentikasi & Akun** | FR-01 – FR-04 | `AuthController`, `Owner/PenggunaController` | `PenggunaModel`, `LogAktivitasModel`, `AuthFilter`, `RoleFilter` | `auth/login`, `auth/register`, `owner/pengguna` |
| **2** | **Katalog Menu** | FR-05, FR-14, FR-15 | `Pelanggan/KatalogController`, `Owner/MenuController` | `MenuModel`, `LogAktivitasModel` | `pelanggan/katalog`, `owner/menu` |
| **3** | **Pemesanan & POS** | FR-06 – FR-10 | `Pelanggan/PesananController`, `Kasir/PosController`, `Kasir/PembayaranController` | `PesananModel`, `DetailPesananModel`, `PembayaranModel`, `RiwayatPesananModel`, `PembayaranService` | `pelanggan/checkout`, `pelanggan/status`, `kasir/pos`, `kasir/struk` |
| **4** | **Antrean Bar Display** | FR-11 – FR-13 | `Barista/AntreanController` | `PesananModel`, `RiwayatPesananModel` | `barista/antrean` |
| **5** | **Bahan Baku & Stok** | FR-16 – FR-18 | `StokController` (Owner & Barista) | `BahanBakuModel`, `RiwayatStokModel`, `StokService` | `stok/index`, `stok/riwayat` |
| **6** | **Shift, Laporan & Audit** | FR-19 – FR-22 | `Kasir/ShiftController`, `Owner/LaporanController`, `Owner/AuditController` | `ShiftModel`, `RingkasanShiftModel` (`v_ringkasan_shift`), `LogAktivitasModel` | `kasir/shift`, `owner/dashboard`, `owner/audit` |

* **Cara Pengerjaan:**
  1. Buat kerangka folder langsung di repositori proyek CodeIgniter 4 (`app/Controllers/Owner`, `app/Controllers/Kasir`, `app/Controllers/Barista`, `app/Controllers/Pelanggan`, `app/Services`, `app/Filters`, serta sub-folder `app/Views`).
  2. Susun tabel pemetaan di atas ke dalam dokumen spesifikasi modul (atau buat versi diagram blok komponen untuk ditampilkan di slide).
  3. Ambil *screenshot* struktur direktori proyek CI4 di VS Code sebagai bukti bahwa struktur kode sudah rapi mengikuti rancangan modul.

---

### Output 3: Implementasi Database Nyata (PostgreSQL 13+)

* **Target:** Membuktikan database sudah aktif berjalan (bukan sekadar gambar ERD), terisi data awal, dan berhasil terkoneksi ke aplikasi CI4.
* **Cara Pengerjaan:**
  1. **Instalasi & Pembuatan Database:** Buka **pgAdmin / DBeaver / psql**, lalu buat database baru (misalnya `pojok_db`) pada PostgreSQL 13+.
  2. **Eksekusi DDL:** Jalankan file skrip `ddl_pojok_db.sql` dari Minggu ke-2. Pastikan terbentuk lengkap:
     * **10 Tabel Fisik:** `pengguna`, `menu`, `bahan_baku`, `shift`, `pesanan`, `detail_pesanan`, `pembayaran`, `riwayat_stok`, `riwayat_pesanan`, `log_aktivitas`.
     * **1 Database View:** `v_ringkasan_shift`.
     * **Seluruh Constraint:** `PRIMARY KEY`, `FOREIGN KEY` (`ON DELETE RESTRICT`), `UNIQUE`, dan `CHECK`.
  3. **Pembuatan & Eksekusi Data Uji (*Seeder*):** Buat file SQL seeder (atau CI4 Database Seeder) untuk mengisi data awal agar aplikasi siap didemokan:
     * Minimal **4 akun uji** di tabel `pengguna` (1 `OWNER`, 1 `KASIR`, 1 `BARISTA`, 1 `PELANGGAN`) dengan *password* ter-*hash* (**bcrypt / Argon2**, bukan teks biasa).
     * **22 varian menu** (13 `KOPI`, 9 `NON_KOPI`) di tabel `menu`.
     * Daftar **bahan baku awal** di tabel `bahan_baku` beserta pencatatan `STOK_AWAL` di tabel `riwayat_stok`.
  4. **Koneksi ke CodeIgniter 4:**
     * Pastikan ekstensi PHP `pgsql` dan `pdo_pgsql` aktif di `php.ini`.
     * Atur file `.env` CI4 pada bagian `database.default.*` dengan `DBDriver = Postgre`, nama database, username, password, dan port (`5432`).
     * Uji koneksi dengan menampilkan data tabel `menu` atau melakukan proses login dari aplikasi.

---

### Output 4: Implementasi Awal Aplikasi (Target Demo Minggu ke-3)

Di Minggu ke-3 aplikasi **belum wajib selesai 100%** (integrasi penuh seluruh fitur & *Black Box Testing* adalah target Minggu ke-4). Fokuskan pengerjaan berdasarkan skala prioritas berikut:

* **Prioritas 1 (Wajib Selesai) — Autentikasi & Hak Akses RBAC (`FR-01` – `FR-04`):**
  1. **Registrasi Pelanggan:** Form hanya berisi *Nama Lengkap*, *Username* (unik *case-insensitive*), dan *Password* (tanpa input WhatsApp).
  2. **Login & Logout 4 Peran:** Setelah login berhasil, sistem otomatis mengarahkan ke halaman utama sesuai peran (`Owner`, `Kasir`, `Barista`, `Pelanggan`).
  3. **Proteksi RBAC & Audit:** Buat `AuthFilter` (cek login + *idle timeout* 30 menit) dan `RoleFilter`. Uji paksa akses URL lintas peran (contoh: login sebagai Pelanggan lalu mengetik `/owner/dashboard` di browser) — sistem wajib menolak dengan **HTTP 403 Forbidden** dan otomatis mencatat kejadian tersebut ke tabel `log_aktivitas`.
  4. **Kelola Akun Staf (Owner):** Halaman bagi Owner untuk melihat daftar staf, menambah akun staf, mengubah data, dan *reset password*.
* **Prioritas 2 (Wajib Selesai) — Fitur Utama Awal: Katalog Menu (`FR-05`, `FR-14`, `FR-15`):**
  1. **Sisi Owner:** CRUD menu — tambah menu baru, ubah harga (perubahan harga wajib tercatat ke `log_aktivitas`), dan ubah status ketersediaan aktif (`TRUE`/`FALSE`).
  2. **Sisi Pelanggan:** Melihat daftar katalog menu yang berstatus aktif, mencari nama menu (*search*), dan memfilter kategori (`KOPI` / `NON_KOPI`).
* **Prioritas 3 (Nilai Tambah untuk Demo Minggu ke-3):**
  1. **Manajemen Shift (`FR-19`):** Kasir dapat membuka shift (`PAGI`/`SORE`) dengan validasi sistem bahwa maksimal hanya boleh ada **1 shift berstatus `AKTIF`** pada satu waktu, serta menutup shift.
  2. **Bahan Baku Dasar (`FR-16`):** Owner dapat melihat daftar bahan baku dan menambahkan item bahan baku baru beserta pencatatan `STOK_AWAL`.
* **Cara Pengerjaan:**
  1. Inisialisasi repositori **Git** (GitHub/GitLab) berisi proyek dasar CodeIgniter 4 + konfigurasi `.env.example` + struktur folder modul.
  2. Terapkan **revisi UI Figma** sesuai Tabel 6.2 terlebih dahulu, lalu *slicing* HTML/CSS ke template dasar (`layout`) di `app/Views`.
  3. Kerjakan **Database + Seeder** dan **Auth + RBAC Filter** sebagai pondasi utama.
  4. Setelah pondasi berjalan dan di-*push* ke Git, seluruh developer paralel mengerjakan fitur **Katalog Menu**, **Kelola Akun Staf**, **Shift**, dan **Bahan Baku** pada *branch* masing-masing, lalu lakukan *merge* dan uji coba bersama sebelum presentasi.

---

## 4. Pembagian Tugas Anggota Kelompok 2 & Bukti Trello

Sesuai aturan Bu Cut, **setiap anggota wajib aktif di Trello** dengan mencentang *checklist* pada kartu tugas masing-masing serta mengunggah **bukti lampiran (*attachment*)**.

| Anggota | Peran Minggu ke-3 | Rincian Tugas Spesifik | Bukti Lampiran Wajib di Trello |
| :--- | :--- | :--- | :--- |
| **Bayu Prayoga** | *Lead Backend & Security* | • Setup repositori Git & proyek awal CodeIgniter 4<br>• Konfigurasi koneksi PostgreSQL (`.env`)<br>• Implementasi Login, Register Pelanggan, Logout 4 peran (`FR-01`–`FR-03`)<br>• Implementasi `AuthFilter`, `RoleFilter` (HTTP 403 + *idle* 30 mnt), pencatatan `log_aktivitas`, & kelola akun staf (`FR-04`) | • Link repositori Git<br>• *Screenshot* login 4 peran<br>• *Screenshot* uji blokir HTTP 403 & baris tercatat di `log_aktivitas` |
| **Satria Darzkine RKT** | *Database & Modul Architect* | • Menjalankan `ddl_pojok_db.sql` di PostgreSQL hingga 10 tabel & `v_ringkasan_shift` aktif<br>• Membuat & menguji **Seeder** (4 akun uji *hashed*, 22 menu, bahan baku + `STOK_AWAL`)<br>• Menyusun dokumen **Struktur Modul & Komponen** (pemetaan 6 modul SRS ke folder CI4) | • File SQL Seeder<br>• *Screenshot* daftar 10 tabel, isi data, & `v_ringkasan_shift` di PostgreSQL<br>• Dokumen/tabel Struktur Modul & Komponen |
| **Brian Decon Christoffel Silaban** | *UI/UX & Frontend Slicing* | • Revisi desain Figma sesuai Tabel 6.2 (hapus *delivery*/ongkir/meja/WA/batal pesanan/laba bersih; tambah menu *Katalog Menu* Owner)<br>• *Slicing* HTML/CSS View CI4: `auth/login`, `auth/register`, `pelanggan/katalog`, dan *base layout* Dashboard | • Link Figma revisi terbaru<br>• *Screenshot* tampilan halaman View CI4 yang sudah responsif di browser |
| **Yuga Fitrah Perdana** | *System Architect & Backend Menu* | • Menggambar **Diagram Arsitektur Perangkat Lunak** (4 layer MVC CI4 + PostgreSQL) + narasi alasan pemilihan *pattern*<br>• Implementasi CRUD fitur awal **Katalog Menu** (`FR-05`, `FR-14`, `FR-15`: Owner kelola menu & Pelanggan cari/filter menu) serta membantu **Bahan Baku** (`FR-16`) | • File gambar Diagram Arsitektur (`.png`/`.pdf`)<br>• Link *commit* Git & *screenshot* fitur Katalog Menu berjalan |
| **Tafazzul Haqqi Lubis** | *Ketua Tim (PM), Fitur Shift & Presentasi* | • Mengelola board & kartu Sprint Minggu ke-3 di Trello (memastikan semua anggota & dosen terundang, *deadline* jelas, & bukti lengkap)<br>• Membantu implementasi fitur **Buka/Tutup Shift** (`FR-19`)<br>• Menyusun **Slide Presentasi Minggu ke-3** (Arsitektur, Modul, DB, & Demo) serta mengatur alur presentasi tim | • Link *commit* fitur Shift (jika selesai)<br>• File Slide Presentasi Minggu ke-3<br>• Rangkuman kelengkapan kartu Sprint Minggu ke-3 di Trello |

---

## 5. Alur Urutan Pengerjaan Tim (Agar Tidak Saling Menunggu)

Untuk menghindari *bottleneck*, ikuti urutan kerja bertahap berikut selama 1 minggu:

1. **Tahap 1 — Fondasi (Hari 1–2):**
   * **Satria** mengeksekusi DDL di PostgreSQL dan membagikan file `seeder.sql` ke grup tim.
   * **Bayu** membuat repositori Git berisi CI4, struktur folder modul, dan koneksi `.env` PostgreSQL.
   * **Brian** menyelesaikan revisi Figma (Tabel 6.2) dan mulai *slicing* halaman Login, Register, & Layout utama.
   * **Yuga** menyelesaikan Diagram Arsitektur 4 Layer.
   * **Tafazzul** membuat seluruh kartu tugas Minggu ke-3 di Trello dan memetakan *checklist* per anggota.
2. **Tahap 2 — Implementasi Inti (Hari 3–5):**
   * **Bayu** menyelesaikan Autentikasi 4 peran, RBAC Filter (HTTP 403), `log_aktivitas`, dan kelola akun staf, lalu *push* ke *branch* utama.
   * **Brian** menyelesaikan *slicing* View Katalog Pelanggan dan Owner Menu lalu menyambungkannya dengan variabel dari Controller.
   * **Yuga** mengimplementasikan CRUD Katalog Menu (Owner) & pencarian/filter Katalog Menu (Pelanggan) menggunakan koneksi & filter dari Bayu.
   * **Tafazzul** mengimplementasikan fitur Buka/Tutup Shift (`FR-19`) dan mulai mengisi kerangka slide presentasi.
   * **Satria** memfinalisasi dokumen Struktur Modul & Komponen (dilengkapi *screenshot* struktur folder CI4 terbaru).
3. **Tahap 3 — Integrasi, Uji Coba, & Persiapan Demo (Hari 6–7):**
   * Seluruh kode di-*merge* ke satu *branch* utama dan dijalankan pada 1 laptop utama yang akan dipakai presentasi di kelas.
   * Lakukan simulasi demo penuh (skenario di Bagian 6).
   * Seluruh anggota mengunggah bukti lampiran ke kartu Trello masing-masing dan memastikan kartu sudah berada di kolom *Done*.

---

## 6. Checklist Kesiapan Presentasi & Demo di Kelas

Sebelum maju presentasi di hadapan Bu Cut, pastikan seluruh poin berikut sudah dicek:

- [ ] **Slide Presentasi siap** memuat: (1) Diagram Arsitektur 4 Layer & alasan *pattern* MVC berlapis, (2) Tabel/Diagram Struktur Modul & Komponen CI4, (3) Bukti Database PostgreSQL, dan (4) Penjelasan ruang lingkup sesuai SRS Final.
- [ ] **Demo PostgreSQL siap** di pgAdmin/DBeaver: bisa langsung menunjukkan 10 tabel, `v_ringkasan_shift`, serta isi data awal (`pengguna`, 22 `menu`, `bahan_baku`).
- [ ] **Skenario Live Demo Aplikasi berjalan mulus tanpa error:**
  - [ ] Registrasi akun Pelanggan baru (tanpa field WhatsApp) → sukses masuk database.
  - [ ] Login sebagai **Pelanggan** → tampil Katalog Menu aktif → uji fitur cari & filter Kopi/Non-Kopi.
  - [ ] Uji keamanan **RBAC**: saat masih login Pelanggan, akses paksa URL `/owner/dashboard` → muncul **HTTP 403** dan bukti tercatat di `log_aktivitas`.
  - [ ] Login sebagai **Owner** → buka sidebar **Katalog Menu** → tambah/edit harga menu & ubah status aktif → cek perubahan langsung muncul di sisi Pelanggan dan tercatat di `log_aktivitas`.
  - [ ] *(Nilai Tambah)* Login sebagai **Kasir** → uji Buka Shift (`PAGI`/`SORE`) & validasi tolak buka shift ganda.
- [ ] **Kesiapan Tanya Jawab (Ketentuan Proyek No. 12 & 13):** Setiap anggota memahami alur keseluruhan sistem dan mampu menjelaskan bagian yang dikerjakannya sendiri (termasuk kode atau diagram yang dibantu AI).
- [ ] **Trello 100% Terbarui:** Semua kartu Minggu ke-3 sudah dicentang, memiliki *attachment* bukti kerja per anggota, dan dapat diakses oleh Bu Cut.