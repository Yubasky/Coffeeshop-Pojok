<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRiwayatPesananTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_riwayat_status_pesanan_waktu
    ON riwayat_pesanan (pesanan_id, waktu_dibuat);
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE riwayat_pesanan IS
    'Jejak audit setiap perubahan status pengerjaan pesanan. Transisi dikunci searah di level database: NULL -> MENUNGGU -> DIPROSES -> SELESAI, atau MENUNGGU -> DIBATALKAN.';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS riwayat_pesanan CASCADE;');
    }
}
