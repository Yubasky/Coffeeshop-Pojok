<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogAktivitasTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_log_aktivitas_waktu
    ON log_aktivitas (waktu_dibuat DESC);
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_log_aktivitas_jenis_waktu
    ON log_aktivitas (jenis_aksi, waktu_dibuat DESC);
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_log_aktivitas_pengguna
    ON log_aktivitas (pengguna_id)
    WHERE pengguna_id IS NOT NULL;
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE log_aktivitas IS
    'Audit trail kejadian penting dan keamanan sistem (misalnya AKSES_DITOLAK, UBAH_HARGA_MENU, PEMBATALAN_PESANAN, TRANSISI_STATUS_DITOLAK, PENYESUAIAN_STOK, LOGIN_GAGAL, AKUN_DIKUNCI).';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN log_aktivitas.detail_json IS
    'Rincian terstruktur opsional dalam format JSONB, misalnya nilai lama vs nilai baru atau alamat URL yang ditolak.';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS log_aktivitas CASCADE;');
    }
}
