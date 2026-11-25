<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRiwayatStokTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE UNIQUE INDEX uq_riwayat_stok_transaksi_unik
    ON riwayat_stok (pesanan_id, bahan_baku_id, jenis_perubahan)
    WHERE pesanan_id IS NOT NULL;
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_riwayat_stok_bahan_waktu
    ON riwayat_stok (bahan_baku_id, waktu_dibuat DESC);
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_riwayat_stok_pesanan
    ON riwayat_stok (pesanan_id)
    WHERE pesanan_id IS NOT NULL;
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE riwayat_stok IS
    'Catatan permanen setiap perubahan stok bahan baku. Saat pesanan lunas dibatalkan sebelum diproses, sistem mengembalikan stok persis sebesar nilai PEMAKAIAN pesanan tersebut di tabel ini.';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN riwayat_stok.jumlah_perubahan IS
    'Selisih bertanda: bernilai positif (+) untuk STOK_AWAL, MASUK, PENGEMBALIAN, atau penyesuaian tambah; bernilai negatif (-) untuk PEMAKAIAN atau penyesuaian kurang.';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS riwayat_stok CASCADE;');
    }
}
