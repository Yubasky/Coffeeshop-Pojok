<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembayaranTable extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
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
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_pembayaran_waktu_metode
    ON pembayaran (waktu_bayar, metode);
SQL);

        $this->db->query(<<<'SQL'
CREATE INDEX idx_pembayaran_kasir_id
    ON pembayaran (kasir_id);
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON TABLE pembayaran IS
    'Catatan konfirmasi pelunasan pesanan (Tunai atau QRIS statis manual). Satu pesanan hanya boleh memiliki maksimal satu baris pembayaran. Relasi shift merujuk pada pesanan.shift_id agar bebas duplikasi.';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN pembayaran.jumlah_dibayar IS
    'Total final Rupiah bulat yang dikunci saat pembayaran dikonfirmasi, sekaligus mempercepat agregasi laporan harian/bulanan tanpa harus menjumlah ulang detail_pesanan.';
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON COLUMN pembayaran.uang_diterima IS
    'Wajib diisi (>= jumlah_dibayar) untuk metode TUNAI dan wajib kosong untuk QRIS. Kembalian dihitung dinamis (uang_diterima - jumlah_dibayar).';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS pembayaran CASCADE;');
    }
}
