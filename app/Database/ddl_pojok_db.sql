-- ============================================================================
-- SKRIP SQL DDL BASIS DATA (POSTGRESQL)
-- Sistem Informasi Operasional Pojok UKM Tanjung Gading (Web-App / CI4)
--
-- Cakupan  : 10 Tabel Inti (3NF) + Indeks Kinerja + 1 Database View
-- Catatan  : Murni struktur DDL (tanpa data awal / dummy)
-- Target   : PostgreSQL 13+
-- ============================================================================

-- Pastikan seluruh zona waktu transaksi konsisten menggunakan WIB
-- (Aplikasi CodeIgniter 4 juga disarankan mengatur appTimezone = 'Asia/Jakarta')


-- ============================================================================
-- BAGIAN 1: TABEL MASTER & AUTENTIKASI
-- ============================================================================

-- 1. Tabel pengguna (akun staf & pelanggan: Kasir, Barista, Owner, Pelanggan)
CREATE TABLE pengguna (
    pengguna_id      BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nama             VARCHAR(100) NOT NULL,
    username         VARCHAR(50)  NOT NULL,
    password_hash    VARCHAR(255) NOT NULL,
    peran            VARCHAR(20)  NOT NULL,
    waktu_dibuat     TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    waktu_diperbarui TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_pengguna_username
        UNIQUE (username),
    CONSTRAINT chk_pengguna_nama_nonkosong
        CHECK (BTRIM(nama) <> ''),
    CONSTRAINT chk_pengguna_username_nonkosong
        CHECK (BTRIM(username) <> ''),
    CONSTRAINT chk_pengguna_peran
        CHECK (peran IN ('KASIR', 'BARISTA', 'OWNER', 'PELANGGAN'))
);

-- Mencegah duplikat username yang hanya berbeda huruf besar/kecil
CREATE UNIQUE INDEX uq_pengguna_username_lower
    ON pengguna (LOWER(username));

COMMENT ON TABLE pengguna IS
    'Akun pengguna sistem (Kasir, Barista, Owner, dan Pelanggan yang mendaftar akun).';
COMMENT ON COLUMN pengguna.password_hash IS
    'Hash password satu arah dengan salt (bcrypt atau Argon2), tidak pernah menyimpan password teks biasa.';
COMMENT ON COLUMN pengguna.peran IS
    'Satu akun memiliki tepat satu peran: KASIR, BARISTA, OWNER, atau PELANGGAN.';


-- 2. Tabel menu (katalog varian kopi & non-kopi)
CREATE TABLE menu (
    menu_id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nama             VARCHAR(100) NOT NULL,
    kategori         VARCHAR(20)  NOT NULL,
    harga            INTEGER      NOT NULL,
    deskripsi        TEXT         NULL,
    foto_url         VARCHAR(255) NULL,
    aktif            BOOLEAN      NOT NULL DEFAULT TRUE,
    waktu_dibuat     TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    waktu_diperbarui TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_menu_nama
        UNIQUE (nama),
    CONSTRAINT chk_menu_nama_nonkosong
        CHECK (BTRIM(nama) <> ''),
    CONSTRAINT chk_menu_kategori
        CHECK (kategori IN ('KOPI', 'NON_KOPI')),
    CONSTRAINT chk_menu_harga_positif
        CHECK (harga > 0)
);

CREATE UNIQUE INDEX uq_menu_nama_lower
    ON menu (LOWER(nama));

CREATE INDEX idx_menu_aktif_kategori
    ON menu (aktif, kategori);

COMMENT ON TABLE menu IS
    'Master katalog menu kedai (KOPI dan NON_KOPI).';
COMMENT ON COLUMN menu.kategori IS
    'Kategori menu dibatasi menjadi KOPI atau NON_KOPI.';
COMMENT ON COLUMN menu.harga IS
    'Harga jual per porsi dalam Rupiah bulat (tanpa pajak, diskon, atau biaya layanan).';
COMMENT ON COLUMN menu.aktif IS
    'Menu yang pernah dipesan tidak dihapus permanen, cukup diubah menjadi FALSE agar hilang dari katalog tetapi tetap utuh di riwayat transaksi.';


-- 3. Tabel bahan_baku (master bahan beserta saldo stok terkini)
CREATE TABLE bahan_baku (
    bahan_baku_id    BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nama             VARCHAR(100)   NOT NULL,
    satuan           VARCHAR(30)    NOT NULL,
    stok_terkini     NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
    waktu_dibuat     TIMESTAMPTZ    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    waktu_diperbarui TIMESTAMPTZ    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_bahan_baku_nama
        UNIQUE (nama),
    CONSTRAINT chk_bahan_baku_nama_nonkosong
        CHECK (BTRIM(nama) <> ''),
    CONSTRAINT chk_bahan_baku_satuan_nonkosong
        CHECK (BTRIM(satuan) <> ''),
    CONSTRAINT chk_bahan_baku_stok_nonnegatif
        CHECK (stok_terkini >= 0)
);

CREATE UNIQUE INDEX uq_bahan_baku_nama_lower
    ON bahan_baku (LOWER(nama));

COMMENT ON TABLE bahan_baku IS
    'Master bahan baku pembuat menu (misalnya biji kopi, susu, sirup, es, cup). Disarankan memakai satuan kecil seperti gram, ml, atau pcs.';
COMMENT ON COLUMN bahan_baku.stok_terkini IS
    'Denormalisasi terkontrol untuk mendukung penguncian baris (SELECT FOR UPDATE) saat transaksi atomik dan pembatasan stok >= 0. Setiap perubahannya wajib dicatat bersamaan ke tabel riwayat_stok dalam satu transaksi database.';


-- ============================================================================
-- BAGIAN 2: MANAJEMEN SHIFT & TRANSAKSI POS
-- ============================================================================

-- 4. Tabel shift (periode kerja Kasir: Pagi atau Sore)
CREATE TABLE shift (
    shift_id         BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    jenis_shift      VARCHAR(10) NOT NULL,
    tanggal          DATE        NOT NULL,
    waktu_buka       TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    waktu_tutup      TIMESTAMPTZ NULL,
    dibuka_oleh_id   BIGINT      NOT NULL,
    ditutup_oleh_id  BIGINT      NULL,
    status           VARCHAR(15) NOT NULL DEFAULT 'AKTIF',
    waktu_dibuat     TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    waktu_diperbarui TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_shift_dibuka_oleh
        FOREIGN KEY (dibuka_oleh_id)
        REFERENCES pengguna (pengguna_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT fk_shift_ditutup_oleh
        FOREIGN KEY (ditutup_oleh_id)
        REFERENCES pengguna (pengguna_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT chk_shift_jenis
        CHECK (jenis_shift IN ('PAGI', 'SORE')),
    CONSTRAINT chk_shift_status
        CHECK (status IN ('AKTIF', 'TERTUTUP')),
    CONSTRAINT chk_shift_konsistensi_tutup
        CHECK (
            (status = 'AKTIF' AND waktu_tutup IS NULL AND ditutup_oleh_id IS NULL)
            OR
            (status = 'TERTUTUP' AND waktu_tutup IS NOT NULL AND ditutup_oleh_id IS NOT NULL AND waktu_tutup >= waktu_buka)
        )
);

-- Menjamin secara mutlak di level database bahwa hanya ada maksimal 1 shift berstatus AKTIF
-- sekaligus mempercepat pencarian shift yang sedang berjalan
CREATE UNIQUE INDEX uq_satu_shift_aktif
    ON shift (status)
    WHERE status = 'AKTIF';

CREATE INDEX idx_shift_tanggal_jenis
    ON shift (tanggal, jenis_shift);

COMMENT ON TABLE shift IS
    'Periode kerja operasional (Pagi atau Sore). Indeks unik parsial uq_satu_shift_aktif menjamin tidak ada dua shift yang aktif secara bersamaan.';


-- 5. Tabel pesanan (header transaksi pesanan mandiri pelanggan & pesanan kasir)
CREATE TABLE pesanan (
    pesanan_id           BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nomor_pesanan        VARCHAR(30)  NOT NULL,
    dipesan_oleh         VARCHAR(15)  NOT NULL,
    token_sesi_pelanggan VARCHAR(64)  NULL,
    nama_pelanggan       VARCHAR(100) NULL,
    status_pembayaran    VARCHAR(20)  NOT NULL DEFAULT 'BELUM_DIBAYAR',
    status_pesanan       VARCHAR(20)  NOT NULL DEFAULT 'MENUNGGU',
    shift_id             BIGINT       NULL,
    dibuat_oleh_id       BIGINT       NULL,
    dibatalkan_oleh_id   BIGINT       NULL,
    waktu_batal          TIMESTAMPTZ  NULL,
    alasan_pembatalan    VARCHAR(255) NULL,
    waktu_dibuat         TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    waktu_diperbarui     TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_pesanan_nomor
        UNIQUE (nomor_pesanan),
    CONSTRAINT fk_pesanan_shift
        FOREIGN KEY (shift_id)
        REFERENCES shift (shift_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT fk_pesanan_dibuat_oleh
        FOREIGN KEY (dibuat_oleh_id)
        REFERENCES pengguna (pengguna_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT fk_pesanan_dibatalkan_oleh
        FOREIGN KEY (dibatalkan_oleh_id)
        REFERENCES pengguna (pengguna_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT chk_pesanan_nomor_nonkosong
        CHECK (BTRIM(nomor_pesanan) <> ''),
    CONSTRAINT chk_pesanan_sumber
        CHECK (dipesan_oleh IN ('MANDIRI', 'KASIR')),
    CONSTRAINT chk_pesanan_status_pembayaran
        CHECK (status_pembayaran IN ('BELUM_DIBAYAR', 'LUNAS')),
    CONSTRAINT chk_pesanan_status_pesanan
        CHECK (status_pesanan IN ('MENUNGGU', 'DIPROSES', 'SELESAI', 'DIBATALKAN')),
    CONSTRAINT chk_pesanan_konsistensi_status
        CHECK (
            (status_pembayaran = 'BELUM_DIBAYAR' AND status_pesanan IN ('MENUNGGU', 'DIBATALKAN'))
            OR
            (status_pembayaran = 'LUNAS' AND shift_id IS NOT NULL)
        ),
    CONSTRAINT chk_pesanan_konsistensi_batal
        CHECK (
            (
                status_pesanan = 'DIBATALKAN'
                AND dibatalkan_oleh_id IS NOT NULL
                AND waktu_batal IS NOT NULL
                AND alasan_pembatalan IS NOT NULL
                AND BTRIM(alasan_pembatalan) <> ''
            )
            OR
            (
                status_pesanan <> 'DIBATALKAN'
                AND dibatalkan_oleh_id IS NULL
                AND waktu_batal IS NULL
                AND alasan_pembatalan IS NULL
            )
        )
);

-- Indeks untuk pembaruan cepat layar Kasir (belum dibayar) dan antrean Barista
CREATE INDEX idx_pesanan_status_antrean
    ON pesanan (status_pembayaran, status_pesanan, waktu_dibuat);

-- Indeks untuk halaman pantau status pesanan pelanggan tanpa login
CREATE INDEX idx_pesanan_token_sesi
    ON pesanan (token_sesi_pelanggan)
    WHERE token_sesi_pelanggan IS NOT NULL;

-- Indeks untuk ringkasan & laporan per shift
CREATE INDEX idx_pesanan_shift_status
    ON pesanan (shift_id, status_pembayaran, status_pesanan)
    WHERE shift_id IS NOT NULL;

-- Indeks untuk daftar pesanan hari berjalan di layar Kasir
CREATE INDEX idx_pesanan_waktu_dibuat
    ON pesanan (waktu_dibuat);

COMMENT ON TABLE pesanan IS
    'Header transaksi pesanan. Hanya pesanan berstatus pembayaran LUNAS yang masuk antrean Barista. Pesanan yang dibuat mandiri maupun kasir tidak pernah dihapus permanen.';
COMMENT ON COLUMN pesanan.nomor_pesanan IS
    'Kode unik publik pesanan yang tampil di struk dan Bar Display, misalnya format ORD-YYYYMMDD-NNNN.';
COMMENT ON COLUMN pesanan.token_sesi_pelanggan IS
    'Token acak opsional minimal 128-bit (minimal 32 karakter heksadesimal) untuk isolasi sesi pelacakan pesanan di browser.';
COMMENT ON COLUMN pesanan.dibuat_oleh_id IS
    'ID pengguna pembuat pesanan (ID Pelanggan untuk dipesan_oleh = MANDIRI setelah login, atau ID Kasir untuk dipesan_oleh = KASIR).';
COMMENT ON COLUMN pesanan.shift_id IS
    'Kosong saat pesanan baru dibuat (BELUM_DIBAYAR). Diisi ID shift aktif saat pembayaran dikonfirmasi (LUNAS) atau saat Kasir membatalkan pesanan pada shift aktif agar seluruh aktivitas shift dapat dianalisis.';


-- 6. Tabel detail_pesanan (daftar item menu per pesanan)
CREATE TABLE detail_pesanan (
    detail_pesanan_id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    pesanan_id        BIGINT       NOT NULL,
    menu_id           BIGINT       NOT NULL,
    jumlah            SMALLINT     NOT NULL,
    harga_satuan      INTEGER      NOT NULL,
    catatan           VARCHAR(100) NULL,
    waktu_dibuat      TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    waktu_diperbarui  TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_detail_pesanan_pesanan
        FOREIGN KEY (pesanan_id)
        REFERENCES pesanan (pesanan_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT fk_detail_pesanan_menu
        FOREIGN KEY (menu_id)
        REFERENCES menu (menu_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT chk_detail_pesanan_jumlah
        CHECK (jumlah BETWEEN 1 AND 99),
    CONSTRAINT chk_detail_pesanan_harga_positif
        CHECK (harga_satuan > 0)
);

CREATE INDEX idx_detail_pesanan_pesanan_id
    ON detail_pesanan (pesanan_id);

CREATE INDEX idx_detail_pesanan_menu_id
    ON detail_pesanan (menu_id);

COMMENT ON TABLE detail_pesanan IS
    'Rincian item dalam satu pesanan. Tidak diberi batasan unik (pesanan_id, menu_id) agar satu pesanan dapat memuat menu yang sama dengan catatan racikan berbeda (misalnya less sugar dan normal sugar).';
COMMENT ON COLUMN detail_pesanan.harga_satuan IS
    'Salinan harga menu saat pesanan dibuat (historical snapshot) sehingga perubahan harga katalog di kemudian hari tidak mengubah nilai transaksi lama. Subtotal (jumlah x harga_satuan) dihitung saat query.';


-- 7. Tabel pembayaran (pelunasan transaksi, relasi 1:0..1 dengan pesanan)
CREATE TABLE pembayaran (
    pembayaran_id  BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    pesanan_id     BIGINT      NOT NULL,
    kasir_id       BIGINT      NOT NULL,
    metode         VARCHAR(10) NOT NULL,
    jumlah_dibayar INTEGER     NOT NULL,
    uang_diterima  INTEGER     NULL,
    waktu_bayar    TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_pembayaran_pesanan
        UNIQUE (pesanan_id),
    CONSTRAINT fk_pembayaran_pesanan
        FOREIGN KEY (pesanan_id)
        REFERENCES pesanan (pesanan_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT fk_pembayaran_kasir
        FOREIGN KEY (kasir_id)
        REFERENCES pengguna (pengguna_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT chk_pembayaran_metode
        CHECK (metode IN ('TUNAI', 'QRIS')),
    CONSTRAINT chk_pembayaran_jumlah_positif
        CHECK (jumlah_dibayar > 0),
    CONSTRAINT chk_pembayaran_konsistensi_metode
        CHECK (
            (metode = 'TUNAI' AND uang_diterima IS NOT NULL AND uang_diterima >= jumlah_dibayar)
            OR
            (metode = 'QRIS' AND uang_diterima IS NULL)
        )
);

-- Indeks untuk urutan antrean Barista (berdasarkan waktu konfirmasi bayar) dan laporan harian/bulanan
CREATE INDEX idx_pembayaran_waktu_metode
    ON pembayaran (waktu_bayar, metode);

CREATE INDEX idx_pembayaran_kasir_id
    ON pembayaran (kasir_id);

COMMENT ON TABLE pembayaran IS
    'Catatan konfirmasi pelunasan pesanan (Tunai atau QRIS statis manual). Satu pesanan hanya boleh memiliki maksimal satu baris pembayaran. Relasi shift merujuk pada pesanan.shift_id agar bebas duplikasi.';
COMMENT ON COLUMN pembayaran.jumlah_dibayar IS
    'Total final Rupiah bulat yang dikunci saat pembayaran dikonfirmasi, sekaligus mempercepat agregasi laporan harian/bulanan tanpa harus menjumlah ulang detail_pesanan.';
COMMENT ON COLUMN pembayaran.uang_diterima IS
    'Wajib diisi (>= jumlah_dibayar) untuk metode TUNAI dan wajib kosong untuk QRIS. Kembalian dihitung dinamis (uang_diterima - jumlah_dibayar).';


-- ============================================================================
-- BAGIAN 3: RIWAYAT MUTASI & AUDIT LOG (APPEND-ONLY)
-- ============================================================================

-- 8. Tabel riwayat_stok (buku besar mutasi keluar-masuk bahan baku)
CREATE TABLE riwayat_stok (
    riwayat_stok_id  BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    bahan_baku_id    BIGINT         NOT NULL,
    pesanan_id       BIGINT         NULL,
    pengguna_id      BIGINT         NOT NULL,
    jenis_perubahan  VARCHAR(20)    NOT NULL,
    jumlah_perubahan NUMERIC(12, 2) NOT NULL,
    keterangan       VARCHAR(255)   NULL,
    waktu_dibuat     TIMESTAMPTZ    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_riwayat_stok_bahan_baku
        FOREIGN KEY (bahan_baku_id)
        REFERENCES bahan_baku (bahan_baku_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT fk_riwayat_stok_pesanan
        FOREIGN KEY (pesanan_id)
        REFERENCES pesanan (pesanan_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT fk_riwayat_stok_pengguna
        FOREIGN KEY (pengguna_id)
        REFERENCES pengguna (pengguna_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT chk_riwayat_stok_jenis
        CHECK (jenis_perubahan IN ('STOK_AWAL', 'MASUK', 'PENYESUAIAN', 'PEMAKAIAN', 'PENGEMBALIAN')),
    CONSTRAINT chk_riwayat_stok_konsistensi
        CHECK (
            (jenis_perubahan IN ('STOK_AWAL', 'MASUK') AND jumlah_perubahan > 0 AND pesanan_id IS NULL)
            OR (jenis_perubahan = 'PENYESUAIAN' AND jumlah_perubahan <> 0 AND pesanan_id IS NULL)
            OR (jenis_perubahan = 'PEMAKAIAN' AND jumlah_perubahan < 0 AND pesanan_id IS NOT NULL)
            OR (jenis_perubahan = 'PENGEMBALIAN' AND jumlah_perubahan > 0 AND pesanan_id IS NOT NULL)
        )
);

-- Mencegah pengurangan stok ganda atau pengembalian stok ganda untuk pesanan yang sama
CREATE UNIQUE INDEX uq_riwayat_stok_transaksi_unik
    ON riwayat_stok (pesanan_id, bahan_baku_id, jenis_perubahan)
    WHERE pesanan_id IS NOT NULL;

CREATE INDEX idx_riwayat_stok_bahan_waktu
    ON riwayat_stok (bahan_baku_id, waktu_dibuat DESC);

CREATE INDEX idx_riwayat_stok_pesanan
    ON riwayat_stok (pesanan_id)
    WHERE pesanan_id IS NOT NULL;

COMMENT ON TABLE riwayat_stok IS
    'Catatan permanen setiap perubahan stok bahan baku. Saat pesanan lunas dibatalkan sebelum diproses, sistem mengembalikan stok persis sebesar nilai PEMAKAIAN pesanan tersebut di tabel ini.';
COMMENT ON COLUMN riwayat_stok.jumlah_perubahan IS
    'Selisih bertanda: bernilai positif (+) untuk STOK_AWAL, MASUK, PENGEMBALIAN, atau penyesuaian tambah; bernilai negatif (-) untuk PEMAKAIAN atau penyesuaian kurang.';


-- 9. Tabel riwayat_pesanan (jejak transisi pengerjaan searah)
CREATE TABLE riwayat_pesanan (
    riwayat_pesanan_id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    pesanan_id         BIGINT       NOT NULL,
    status_lama        VARCHAR(20)  NULL,
    status_baru        VARCHAR(20)  NOT NULL,
    diubah_oleh_id     BIGINT       NULL,
    catatan            VARCHAR(255) NULL,
    waktu_dibuat       TIMESTAMPTZ  NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_riwayat_status_pesanan
        FOREIGN KEY (pesanan_id)
        REFERENCES pesanan (pesanan_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT fk_riwayat_status_pengguna
        FOREIGN KEY (diubah_oleh_id)
        REFERENCES pengguna (pengguna_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT chk_riwayat_status_lama
        CHECK (status_lama IS NULL OR status_lama IN ('MENUNGGU', 'DIPROSES', 'SELESAI', 'DIBATALKAN')),
    CONSTRAINT chk_riwayat_status_baru
        CHECK (status_baru IN ('MENUNGGU', 'DIPROSES', 'SELESAI', 'DIBATALKAN')),
    CONSTRAINT chk_riwayat_status_transisi
        CHECK (
            (status_lama IS NULL AND status_baru = 'MENUNGGU')
            OR (status_lama = 'MENUNGGU' AND status_baru IN ('DIPROSES', 'DIBATALKAN'))
            OR (status_lama = 'DIPROSES' AND status_baru = 'SELESAI')
        ),
    CONSTRAINT chk_riwayat_status_pelaku
        CHECK (status_lama IS NULL OR diubah_oleh_id IS NOT NULL)
);

CREATE INDEX idx_riwayat_status_pesanan_waktu
    ON riwayat_pesanan (pesanan_id, waktu_dibuat);

COMMENT ON TABLE riwayat_pesanan IS
    'Jejak audit setiap perubahan status pengerjaan pesanan. Transisi dikunci searah di level database: NULL -> MENUNGGU -> DIPROSES -> SELESAI, atau MENUNGGU -> DIBATALKAN.';


-- 10. Tabel log_aktivitas (catatan audit keamanan & aksi penting sistem)
CREATE TABLE log_aktivitas (
    log_aktivitas_id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    pengguna_id      BIGINT      NULL,
    jenis_aksi       VARCHAR(50) NOT NULL,
    entitas          VARCHAR(50) NULL,
    entitas_id       BIGINT      NULL,
    detail_json      JSONB       NULL,
    keterangan       TEXT        NULL,
    waktu_dibuat     TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_log_aktivitas_pengguna
        FOREIGN KEY (pengguna_id)
        REFERENCES pengguna (pengguna_id)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT chk_log_aktivitas_jenis_nonkosong
        CHECK (BTRIM(jenis_aksi) <> '')
);

CREATE INDEX idx_log_aktivitas_waktu
    ON log_aktivitas (waktu_dibuat DESC);

CREATE INDEX idx_log_aktivitas_jenis_waktu
    ON log_aktivitas (jenis_aksi, waktu_dibuat DESC);

CREATE INDEX idx_log_aktivitas_pengguna
    ON log_aktivitas (pengguna_id)
    WHERE pengguna_id IS NOT NULL;

COMMENT ON TABLE log_aktivitas IS
    'Audit trail kejadian penting dan keamanan sistem (misalnya AKSES_DITOLAK, UBAH_HARGA_MENU, PEMBATALAN_PESANAN, TRANSISI_STATUS_DITOLAK, PENYESUAIAN_STOK, LOGIN_GAGAL, AKUN_DIKUNCI).';
COMMENT ON COLUMN log_aktivitas.detail_json IS
    'Rincian terstruktur opsional dalam format JSONB, misalnya nilai lama vs nilai baru atau alamat URL yang ditolak.';


-- ============================================================================
-- BAGIAN 4: DATABASE VIEW (TABEL VIRTUAL REAL-TIME — BUKAN ENTITAS FISIK ERD)
-- ============================================================================

-- View: Ringkasan seluruh transaksi & pembatalan per shift
CREATE OR REPLACE VIEW v_ringkasan_shift AS
SELECT
    s.shift_id,
    s.jenis_shift,
    s.tanggal,
    s.status,
    s.waktu_buka,
    s.waktu_tutup,
    s.dibuka_oleh_id,
    u_buka.nama AS nama_kasir_pembuka,
    s.ditutup_oleh_id,
    u_tutup.nama AS nama_kasir_penutup,

    -- Transaksi sah (Lunas dan tidak dibatalkan) -> dasar perhitungan omzet
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
    ) AS jumlah_transaksi_lunas,
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
          AND pb.metode = 'TUNAI'
    ) AS jumlah_transaksi_tunai,
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
          AND pb.metode = 'QRIS'
    ) AS jumlah_transaksi_qris,
    COALESCE(SUM(pb.jumlah_dibayar) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
          AND pb.metode = 'TUNAI'
    ), 0)::BIGINT AS total_tunai,
    COALESCE(SUM(pb.jumlah_dibayar) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
          AND pb.metode = 'QRIS'
    ), 0)::BIGINT AS total_qris,
    COALESCE(SUM(pb.jumlah_dibayar) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
    ), 0)::BIGINT AS total_penjualan,

    -- Rincian pembatalan selama shift untuk analisis & audit pengembalian uang manual
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan = 'DIBATALKAN'
    ) AS jumlah_dibatalkan_sudah_bayar,
    COALESCE(SUM(pb.jumlah_dibayar) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan = 'DIBATALKAN'
    ), 0)::BIGINT AS nominal_dibatalkan_sudah_bayar,
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'BELUM_DIBAYAR'
          AND p.status_pesanan = 'DIBATALKAN'
    ) AS jumlah_dibatalkan_belum_bayar,
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pesanan = 'DIBATALKAN'
    ) AS total_pesanan_dibatalkan,

    -- Total seluruh pesanan yang tercatat pada shift ini
    COUNT(p.pesanan_id) AS total_semua_pesanan
FROM shift s
INNER JOIN pengguna u_buka
    ON u_buka.pengguna_id = s.dibuka_oleh_id
LEFT JOIN pengguna u_tutup
    ON u_tutup.pengguna_id = s.ditutup_oleh_id
LEFT JOIN pesanan p
    ON p.shift_id = s.shift_id
LEFT JOIN pembayaran pb
    ON pb.pesanan_id = p.pesanan_id
GROUP BY
    s.shift_id,
    s.jenis_shift,
    s.tanggal,
    s.status,
    s.waktu_buka,
    s.waktu_tutup,
    s.dibuka_oleh_id,
    u_buka.nama,
    s.ditutup_oleh_id,
    u_tutup.nama;

COMMENT ON VIEW v_ringkasan_shift IS
    'Rekapitulasi otomatis per shift untuk layar closing Kasir dan laporan Owner: jumlah transaksi, omzet Tunai, omzet QRIS, total penjualan (tidak menghitung pesanan dibatalkan), serta rincian pembatalan (sudah bayar vs belum bayar).';
