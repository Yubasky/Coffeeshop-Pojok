<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenggunaTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE UNIQUE INDEX uq_pengguna_username_lower
    ON pengguna (LOWER(username));
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE pengguna IS
    'Akun pengguna sistem (Kasir, Barista, Owner, dan Pelanggan yang mendaftar akun).';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN pengguna.password_hash IS
    'Hash password satu arah dengan salt (bcrypt atau Argon2), tidak pernah menyimpan password teks biasa.';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN pengguna.peran IS
    'Satu akun memiliki tepat satu peran: KASIR, BARISTA, OWNER, atau PELANGGAN.';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS pengguna CASCADE;');
    }
}
