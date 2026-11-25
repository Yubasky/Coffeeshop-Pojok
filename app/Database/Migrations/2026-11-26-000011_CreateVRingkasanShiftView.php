<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVRingkasanShiftView extends Migration
{
    public function up()
    {
        $this->db->query(<<<'SQL'
CREATE OR REPLACE VIEW v_ringkasan_shift AS
SELECT
    s.shift_id,
    s.jenis_shift,
    s.tanggal,
    s.status,
    s.waktu_buka,
    s.waktu_tutup,
    s.dibuka_oleh_id,
    u_buka.nama AS nama_kasir_pembuka,
    s.ditutup_oleh_id,
    u_tutup.nama AS nama_kasir_penutup,

    -- Transaksi sah (Lunas dan tidak dibatalkan) -> dasar perhitungan omzet
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
    ) AS jumlah_transaksi_lunas,
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
          AND pb.metode = 'TUNAI'
    ) AS jumlah_transaksi_tunai,
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
          AND pb.metode = 'QRIS'
    ) AS jumlah_transaksi_qris,
    COALESCE(SUM(pb.jumlah_dibayar) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
          AND pb.metode = 'TUNAI'
    ), 0)::BIGINT AS total_tunai,
    COALESCE(SUM(pb.jumlah_dibayar) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
          AND pb.metode = 'QRIS'
    ), 0)::BIGINT AS total_qris,
    COALESCE(SUM(pb.jumlah_dibayar) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan <> 'DIBATALKAN'
    ), 0)::BIGINT AS total_penjualan,

    -- Rincian pembatalan selama shift untuk analisis & audit pengembalian uang manual
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan = 'DIBATALKAN'
    ) AS jumlah_dibatalkan_sudah_bayar,
    COALESCE(SUM(pb.jumlah_dibayar) FILTER (
        WHERE p.status_pembayaran = 'LUNAS'
          AND p.status_pesanan = 'DIBATALKAN'
    ), 0)::BIGINT AS nominal_dibatalkan_sudah_bayar,
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pembayaran = 'BELUM_DIBAYAR'
          AND p.status_pesanan = 'DIBATALKAN'
    ) AS jumlah_dibatalkan_belum_bayar,
    COUNT(p.pesanan_id) FILTER (
        WHERE p.status_pesanan = 'DIBATALKAN'
    ) AS total_pesanan_dibatalkan,

    -- Total seluruh pesanan yang tercatat pada shift ini
    COUNT(p.pesanan_id) AS total_semua_pesanan
FROM shift s
INNER JOIN pengguna u_buka
    ON u_buka.pengguna_id = s.dibuka_oleh_id
LEFT JOIN pengguna u_tutup
    ON u_tutup.pengguna_id = s.ditutup_oleh_id
LEFT JOIN pesanan p
    ON p.shift_id = s.shift_id
LEFT JOIN pembayaran pb
    ON pb.pesanan_id = p.pesanan_id
GROUP BY
    s.shift_id,
    s.jenis_shift,
    s.tanggal,
    s.status,
    s.waktu_buka,
    s.waktu_tutup,
    s.dibuka_oleh_id,
    u_buka.nama,
    s.ditutup_oleh_id,
    u_tutup.nama;
SQL);

        $this->db->query(<<<'SQL'
COMMENT ON VIEW v_ringkasan_shift IS
    'Rekapitulasi otomatis per shift untuk layar closing Kasir dan laporan Owner: jumlah transaksi, omzet Tunai, omzet QRIS, total penjualan (tidak menghitung pesanan dibatalkan), serta rincian pembatalan (sudah bayar vs belum bayar).';
SQL);
    }

    public function down()
    {
        $this->db->query('DROP VIEW IF EXISTS v_ringkasan_shift;');
    }
}
