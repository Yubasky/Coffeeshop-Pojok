<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePesananTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_pesanan_status_antrean
    ON pesanan (status_pembayaran, status_pesanan, waktu_dibuat);
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_pesanan_token_sesi
    ON pesanan (token_sesi_pelanggan)
    WHERE token_sesi_pelanggan IS NOT NULL;
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_pesanan_shift_status
    ON pesanan (shift_id, status_pembayaran, status_pesanan)
    WHERE shift_id IS NOT NULL;
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_pesanan_waktu_dibuat
    ON pesanan (waktu_dibuat);
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE pesanan IS
    'Header transaksi pesanan. Hanya pesanan berstatus pembayaran LUNAS yang masuk antrean Barista. Pesanan yang dibuat mandiri maupun kasir tidak pernah dihapus permanen.';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN pesanan.nomor_pesanan IS
    'Kode unik publik pesanan yang tampil di struk dan Bar Display, misalnya format ORD-YYYYMMDD-NNNN.';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN pesanan.token_sesi_pelanggan IS
    'Token acak opsional minimal 128-bit (minimal 32 karakter heksadesimal) untuk isolasi sesi pelacakan pesanan di browser.';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN pesanan.dibuat_oleh_id IS
    'ID pengguna pembuat pesanan (ID Pelanggan untuk dipesan_oleh = MANDIRI setelah login, atau ID Kasir untuk dipesan_oleh = KASIR).';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN pesanan.shift_id IS
    'Kosong saat pesanan baru dibuat (BELUM_DIBAYAR). Diisi ID shift aktif saat pembayaran dikonfirmasi (LUNAS) atau saat Kasir membatalkan pesanan pada shift aktif agar seluruh aktivitas shift dapat dianalisis.';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS pesanan CASCADE;');
    }
}
