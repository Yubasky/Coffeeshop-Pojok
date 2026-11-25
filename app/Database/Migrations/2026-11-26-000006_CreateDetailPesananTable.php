<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailPesananTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_detail_pesanan_pesanan_id
    ON detail_pesanan (pesanan_id);
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_detail_pesanan_menu_id
    ON detail_pesanan (menu_id);
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE detail_pesanan IS
    'Rincian item dalam satu pesanan. Tidak diberi batasan unik (pesanan_id, menu_id) agar satu pesanan dapat memuat menu yang sama dengan catatan racikan berbeda (misalnya less sugar dan normal sugar).';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN detail_pesanan.harga_satuan IS
    'Salinan harga menu saat pesanan dibuat (historical snapshot) sehingga perubahan harga katalog di kemudian hari tidak mengubah nilai transaksi lama. Subtotal (jumlah x harga_satuan) dihitung saat query.';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS detail_pesanan CASCADE;');
    }
}
