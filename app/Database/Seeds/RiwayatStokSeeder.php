<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RiwayatStokSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1. STOK_AWAL Biji Kopi Arabika Gayo
            [
                'bahan_baku_id'    => 1,
                'pesanan_id'       => null,
                'pengguna_id'      => 1,
                'jenis_perubahan'  => 'STOK_AWAL',
                'jumlah_perubahan' => 2500.00,
                'keterangan'       => 'Stok awal Biji Kopi Arabika Gayo',
                'waktu_dibuat'     => '2026-11-25 07:30:00+07',
            ],
            // 2. STOK_AWAL Biji Kopi House Blend Pojok
            [
                'bahan_baku_id'    => 2,
                'pesanan_id'       => null,
                'pengguna_id'      => 1,
                'jenis_perubahan'  => 'STOK_AWAL',
                'jumlah_perubahan' => 5000.00,
                'keterangan'       => 'Stok awal Biji Kopi House Blend Pojok',
                'waktu_dibuat'     => '2026-11-25 07:31:00+07',
            ],
            // 3. STOK_AWAL Susu UHT Full Cream
            [
                'bahan_baku_id'    => 3,
                'pesanan_id'       => null,
                'pengguna_id'      => 1,
                'jenis_perubahan'  => 'STOK_AWAL',
                'jumlah_perubahan' => 15000.00,
                'keterangan'       => 'Stok awal Susu UHT Full Cream',
                'waktu_dibuat'     => '2026-11-25 07:32:00+07',
            ],
            // 4. MASUK Gula Aren Cair
            [
                'bahan_baku_id'    => 4,
                'pesanan_id'       => null,
                'pengguna_id'      => 1,
                'jenis_perubahan'  => 'MASUK',
                'jumlah_perubahan' => 4000.00,
                'keterangan'       => 'Restock Gula Aren Cair dari supplier',
                'waktu_dibuat'     => '2026-11-25 07:35:00+07',
            ],
            // 5. PENYESUAIAN Sirup Caramel
            [
                'bahan_baku_id'    => 5,
                'pesanan_id'       => null,
                'pengguna_id'      => 1,
                'jenis_perubahan'  => 'PENYESUAIAN',
                'jumlah_perubahan' => -50.00,
                'keterangan'       => 'Koreksi stok opname tumpah saat penakaran',
                'waktu_dibuat'     => '2026-11-25 08:00:00+07',
            ],
            // 6. PEMAKAIAN untuk Pesanan 1 (Biji Kopi House Blend)
            [
                'bahan_baku_id'    => 2,
                'pesanan_id'       => 1,
                'pengguna_id'      => 2,
                'jenis_perubahan'  => 'PEMAKAIAN',
                'jumlah_perubahan' => -36.00,
                'keterangan'       => 'Pemakaian otomatis untuk ORD-20261125-0001',
                'waktu_dibuat'     => '2026-11-25 09:02:00+07',
            ],
            // 7. PEMAKAIAN untuk Pesanan 1 (Susu UHT Full Cream)
            [
                'bahan_baku_id'    => 3,
                'pesanan_id'       => 1,
                'pengguna_id'      => 2,
                'jenis_perubahan'  => 'PEMAKAIAN',
                'jumlah_perubahan' => -300.00,
                'keterangan'       => 'Pemakaian otomatis untuk ORD-20261125-0001',
                'waktu_dibuat'     => '2026-11-25 09:02:00+07',
            ],
            // 8. PEMAKAIAN untuk Pesanan 6 (Biji Kopi Arabika Gayo)
            [
                'bahan_baku_id'    => 1,
                'pesanan_id'       => 6,
                'pengguna_id'      => 2,
                'jenis_perubahan'  => 'PEMAKAIAN',
                'jumlah_perubahan' => -15.00,
                'keterangan'       => 'Pemakaian otomatis untuk ORD-20261126-0002',
                'waktu_dibuat'     => '2026-11-26 08:46:00+07',
            ],
            // 9. PEMAKAIAN untuk Pesanan 10 (Teh Hitam Daun & Ekstrak Lemon)
            [
                'bahan_baku_id'    => 9,
                'pesanan_id'       => 10,
                'pengguna_id'      => 2,
                'jenis_perubahan'  => 'PEMAKAIAN',
                'jumlah_perubahan' => -20.00,
                'keterangan'       => 'Pemakaian otomatis untuk ORD-20261126-0006',
                'waktu_dibuat'     => '2026-11-26 09:41:00+07',
            ],
            // 10. PENGEMBALIAN karena Pesanan 10 dibatalkan sebelum diproses
            [
                'bahan_baku_id'    => 9,
                'pesanan_id'       => 10,
                'pengguna_id'      => 2,
                'jenis_perubahan'  => 'PENGEMBALIAN',
                'jumlah_perubahan' => 20.00,
                'keterangan'       => 'Pengembalian stok otomatis pembatalan ORD-20261126-0006',
                'waktu_dibuat'     => '2026-11-26 09:45:00+07',
            ],
        ];

        $this->db->table('riwayat_stok')->insertBatch($data);
    }
}
