<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShiftTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE UNIQUE INDEX uq_satu_shift_aktif
    ON shift (status)
    WHERE status = 'AKTIF';
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_shift_tanggal_jenis
    ON shift (tanggal, jenis_shift);
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE shift IS
    'Periode kerja operasional (Pagi atau Sore). Indeks unik parsial uq_satu_shift_aktif menjamin tidak ada dua shift yang aktif secara bersamaan.';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS shift CASCADE;');
    }
}
