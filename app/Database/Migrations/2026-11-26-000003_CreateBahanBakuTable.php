<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBahanBakuTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE UNIQUE INDEX uq_bahan_baku_nama_lower
    ON bahan_baku (LOWER(nama));
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE bahan_baku IS
    'Master bahan baku pembuat menu (misalnya biji kopi, susu, sirup, es, cup). Disarankan memakai satuan kecil seperti gram, ml, atau pcs.';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN bahan_baku.stok_terkini IS
    'Denormalisasi terkontrol untuk mendukung penguncian baris (SELECT FOR UPDATE) saat transaksi atomik dan pembatasan stok >= 0. Setiap perubahannya wajib dicatat bersamaan ke tabel riwayat_stok dalam satu transaksi database.';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS bahan_baku CASCADE;');
    }
}
