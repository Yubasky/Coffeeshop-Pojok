<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PembayaranSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1. Pesanan 1 (Total 36.000 - QRIS)
            [
                'pesanan_id'     => 1,
                'kasir_id'       => 2,
                'metode'         => 'QRIS',
                'jumlah_dibayar' => 36000,
                'uang_diterima'  => null,
                'waktu_bayar'    => '2026-11-25 09:02:00+07',
            ],
            // 2. Pesanan 2 (Total 15.000 - TUNAI)
            [
                'pesanan_id'     => 2,
                'kasir_id'       => 2,
                'metode'         => 'TUNAI',
                'jumlah_dibayar' => 15000,
                'uang_diterima'  => 20000,
                'waktu_bayar'    => '2026-11-25 10:01:00+07',
            ],
            // 3. Pesanan 3 (Total 20.000 - QRIS)
            [
                'pesanan_id'     => 3,
                'kasir_id'       => 2,
                'metode'         => 'QRIS',
                'jumlah_dibayar' => 20000,
                'uang_diterima'  => null,
                'waktu_bayar'    => '2026-11-25 11:02:00+07',
            ],
            // 4. Pesanan 4 (Total 40.000 - TUNAI)
            [
                'pesanan_id'     => 4,
                'kasir_id'       => 2,
                'metode'         => 'TUNAI',
                'jumlah_dibayar' => 40000,
                'uang_diterima'  => 50000,
                'waktu_bayar'    => '2026-11-25 12:31:00+07',
            ],
            // 5. Pesanan 5 (Total 23.000 - QRIS)
            [
                'pesanan_id'     => 5,
                'kasir_id'       => 2,
                'metode'         => 'QRIS',
                'jumlah_dibayar' => 23000,
                'uang_diterima'  => null,
                'waktu_bayar'    => '2026-11-26 08:17:00+07',
            ],
            // 6. Pesanan 6 (Total 22.000 - TUNAI)
            [
                'pesanan_id'     => 6,
                'kasir_id'       => 2,
                'metode'         => 'TUNAI',
                'jumlah_dibayar' => 22000,
                'uang_diterima'  => 25000,
                'waktu_bayar'    => '2026-11-26 08:46:00+07',
            ],
            // 7. Pesanan 7 (Total 40.000 - QRIS)
            [
                'pesanan_id'     => 7,
                'kasir_id'       => 2,
                'metode'         => 'QRIS',
                'jumlah_dibayar' => 40000,
                'uang_diterima'  => null,
                'waktu_bayar'    => '2026-11-26 09:12:00+07',
            ],
            // 8. Pesanan 8 (Total 19.000 - TUNAI)
            [
                'pesanan_id'     => 8,
                'kasir_id'       => 2,
                'metode'         => 'TUNAI',
                'jumlah_dibayar' => 19000,
                'uang_diterima'  => 20000,
                'waktu_bayar'    => '2026-11-26 09:21:00+07',
            ],
            // 9. Pesanan 9 (Total 19.000 - QRIS)
            [
                'pesanan_id'     => 9,
                'kasir_id'       => 2,
                'metode'         => 'QRIS',
                'jumlah_dibayar' => 19000,
                'uang_diterima'  => null,
                'waktu_bayar'    => '2026-11-26 09:32:00+07',
            ],
            // 10. Pesanan 10 (Total 28.000 - TUNAI)
            [
                'pesanan_id'     => 10,
                'kasir_id'       => 2,
                'metode'         => 'TUNAI',
                'jumlah_dibayar' => 28000,
                'uang_diterima'  => 30000,
                'waktu_bayar'    => '2026-11-26 09:41:00+07',
            ],
        ];

        $this->db->table('pembayaran')->insertBatch($data);
    }
}
