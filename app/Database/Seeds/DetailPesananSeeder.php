<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DetailPesananSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1. Pesanan 1: 2x Kopi Susu Gading (@18.000 = 36.000)
            [
                'pesanan_id'       => 1,
                'menu_id'          => 1,
                'jumlah'           => 2,
                'harga_satuan'     => 18000,
                'catatan'          => 'Less sugar',
                'waktu_dibuat'     => '2026-11-25 09:00:00+07',
                'waktu_diperbarui' => '2026-11-25 09:00:00+07',
            ],
            // 2. Pesanan 2: 1x Americano (@15.000 = 15.000)
            [
                'pesanan_id'       => 2,
                'menu_id'          => 2,
                'jumlah'           => 1,
                'harga_satuan'     => 15000,
                'catatan'          => 'Panas, tanpa gula',
                'waktu_dibuat'     => '2026-11-25 10:00:00+07',
                'waktu_diperbarui' => '2026-11-25 10:00:00+07',
            ],
            // 3. Pesanan 3: 1x Cappuccino (@22.000 = 22.000)
            [
                'pesanan_id'       => 3,
                'menu_id'          => 3,
                'jumlah'           => 1,
                'harga_satuan'     => 22000,
                'catatan'          => 'Normal ice',
                'waktu_dibuat'     => '2026-11-25 11:00:00+07',
                'waktu_diperbarui' => '2026-11-25 11:00:00+07',
            ],
            // 4. Pesanan 4: 2x Kopi Susu Aren (@20.000 = 40.000)
            [
                'pesanan_id'       => 4,
                'menu_id'          => 4,
                'jumlah'           => 2,
                'harga_satuan'     => 20000,
                'catatan'          => 'Extra hot',
                'waktu_dibuat'     => '2026-11-25 12:30:00+07',
                'waktu_diperbarui' => '2026-11-25 12:30:00+07',
            ],
            // 5. Pesanan 5: 1x Caramel Macchiato (@24.000 = 24.000)
            [
                'pesanan_id'       => 5,
                'menu_id'          => 5,
                'jumlah'           => 1,
                'harga_satuan'     => 24000,
                'catatan'          => 'Less ice',
                'waktu_dibuat'     => '2026-11-26 08:15:00+07',
                'waktu_diperbarui' => '2026-11-26 08:15:00+07',
            ],
            // 6. Pesanan 6: 1x Caffe Latte (@22.000 = 22.000)
            [
                'pesanan_id'       => 6,
                'menu_id'          => 6,
                'jumlah'           => 1,
                'harga_satuan'     => 22000,
                'catatan'          => 'Disajikan hangat',
                'waktu_dibuat'     => '2026-11-26 08:45:00+07',
                'waktu_diperbarui' => '2026-11-26 08:45:00+07',
            ],
            // 7. Pesanan 7: 2x Matcha Latte (@22.000 = 44.000)
            [
                'pesanan_id'       => 7,
                'menu_id'          => 15,
                'jumlah'           => 2,
                'harga_satuan'     => 22000,
                'catatan'          => 'Less sweet',
                'waktu_dibuat'     => '2026-11-26 09:10:00+07',
                'waktu_diperbarui' => '2026-11-26 09:10:00+07',
            ],
            // 8. Pesanan 8: 1x Cokelat Susu (@20.000 = 20.000)
            [
                'pesanan_id'       => 8,
                'menu_id'          => 14,
                'jumlah'           => 1,
                'harga_satuan'     => 20000,
                'catatan'          => 'Normal ice',
                'waktu_dibuat'     => '2026-11-26 09:20:00+07',
                'waktu_diperbarui' => '2026-11-26 09:20:00+07',
            ],
            // 9. Pesanan 9: 1x Red Velvet Latte (@22.000 = 22.000)
            [
                'pesanan_id'       => 9,
                'menu_id'          => 16,
                'jumlah'           => 1,
                'harga_satuan'     => 22000,
                'catatan'          => null,
                'waktu_dibuat'     => '2026-11-26 09:30:00+07',
                'waktu_diperbarui' => '2026-11-26 09:30:00+07',
            ],
            // 10. Pesanan 10: 2x Lemon Tea Segar (@15.000 = 30.000)
            [
                'pesanan_id'       => 10,
                'menu_id'          => 19,
                'jumlah'           => 2,
                'harga_satuan'     => 15000,
                'catatan'          => 'Less ice',
                'waktu_dibuat'     => '2026-11-26 09:40:00+07',
                'waktu_diperbarui' => '2026-11-26 09:40:00+07',
            ],
        ];

        $this->db->table('detail_pesanan')->insertBatch($data);
    }
}
