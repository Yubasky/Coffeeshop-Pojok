<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenuTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE UNIQUE INDEX uq_menu_nama_lower
    ON menu (LOWER(nama));
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_menu_aktif_kategori
    ON menu (aktif, kategori);
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE menu IS
    'Master katalog menu kedai (KOPI dan NON_KOPI).';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN menu.kategori IS
    'Kategori menu dibatasi menjadi KOPI atau NON_KOPI.';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN menu.harga IS
    'Harga jual per porsi dalam Rupiah bulat (tanpa pajak, diskon, atau biaya layanan).';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN menu.aktif IS
    'Menu yang pernah dipesan tidak dihapus permanen, cukup diubah menjadi FALSE agar hilang dari katalog tetapi tetap utuh di riwayat transaksi.';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS menu CASCADE;');
    }
}
