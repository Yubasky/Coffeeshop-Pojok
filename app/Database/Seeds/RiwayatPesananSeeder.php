<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RiwayatPesananSeeder extends Seeder
{
    public function run()
    {
        // Mematuhi constraint chk_riwayat_status_transisi:
        // (status_lama IS NULL AND status_baru = 'MENUNGGU')
        // OR (status_lama = 'MENUNGGU' AND status_baru IN ('DIPROSES', 'DIBATALKAN'))
        // OR (status_lama = 'DIPROSES' AND status_baru = 'SELESAI')
        $data = [
            // 1. Pesanan 1: NULL -> MENUNGGU
            [
                'pesanan_id'     => 1,
                'status_lama'    => null,
                'status_baru'    => 'MENUNGGU',
                'diubah_oleh_id' => 7,
                'catatan'        => 'Pesanan mandiri dibuat oleh pelanggan',
                'waktu_dibuat'   => '2026-11-25 09:00:00+07',
            ],
            // 2. Pesanan 1: MENUNGGU -> DIPROSES
            [
                'pesanan_id'     => 1,
                'status_lama'    => 'MENUNGGU',
                'status_baru'    => 'DIPROSES',
                'diubah_oleh_id' => 5,
                'catatan'        => 'Barista mulai meracik pesanan',
                'waktu_dibuat'   => '2026-11-25 09:05:00+07',
            ],
            // 3. Pesanan 1: DIPROSES -> SELESAI
            [
                'pesanan_id'     => 1,
                'status_lama'    => 'DIPROSES',
                'status_baru'    => 'SELESAI',
                'diubah_oleh_id' => 5,
                'catatan'        => 'Pesanan selesai dan siap diambil',
                'waktu_dibuat'   => '2026-11-25 09:15:00+07',
            ],
            // 4. Pesanan 5: NULL -> MENUNGGU
            [
                'pesanan_id'     => 5,
                'status_lama'    => null,
                'status_baru'    => 'MENUNGGU',
                'diubah_oleh_id' => 9,
                'catatan'        => 'Pesanan mandiri dibuat oleh pelanggan',
                'waktu_dibuat'   => '2026-11-26 08:15:00+07',
            ],
            // 5. Pesanan 5: MENUNGGU -> DIPROSES
            [
                'pesanan_id'     => 5,
                'status_lama'    => 'MENUNGGU',
                'status_baru'    => 'DIPROSES',
                'diubah_oleh_id' => 5,
                'catatan'        => 'Barista mulai meracik pesanan',
                'waktu_dibuat'   => '2026-11-26 08:20:00+07',
            ],
            // 6. Pesanan 5: DIPROSES -> SELESAI
            [
                'pesanan_id'     => 5,
                'status_lama'    => 'DIPROSES',
                'status_baru'    => 'SELESAI',
                'diubah_oleh_id' => 5,
                'catatan'        => 'Pesanan selesai disajikan',
                'waktu_dibuat'   => '2026-11-26 08:30:00+07',
            ],
            // 7. Pesanan 7: NULL -> MENUNGGU
            [
                'pesanan_id'     => 7,
                'status_lama'    => null,
                'status_baru'    => 'MENUNGGU',
                'diubah_oleh_id' => 10,
                'catatan'        => 'Pesanan mandiri dibuat',
                'waktu_dibuat'   => '2026-11-26 09:10:00+07',
            ],
            // 8. Pesanan 7: MENUNGGU -> DIPROSES
            [
                'pesanan_id'     => 7,
                'status_lama'    => 'MENUNGGU',
                'status_baru'    => 'DIPROSES',
                'diubah_oleh_id' => 6,
                'catatan'        => 'Sedang dibuat oleh barista',
                'waktu_dibuat'   => '2026-11-26 09:15:00+07',
            ],
            // 9. Pesanan 10: NULL -> MENUNGGU
            [
                'pesanan_id'     => 10,
                'status_lama'    => null,
                'status_baru'    => 'MENUNGGU',
                'diubah_oleh_id' => 2,
                'catatan'        => 'Pesanan kasir dibuat',
                'waktu_dibuat'   => '2026-11-26 09:40:00+07',
            ],
            // 10. Pesanan 10: MENUNGGU -> DIBATALKAN
            [
                'pesanan_id'     => 10,
                'status_lama'    => 'MENUNGGU',
                'status_baru'    => 'DIBATALKAN',
                'diubah_oleh_id' => 2,
                'catatan'        => 'Dibatalkan oleh kasir atas permintaan pelanggan sebelum diproses',
                'waktu_dibuat'   => '2026-11-26 09:45:00+07',
            ],
        ];

        $this->db->table('riwayat_pesanan')->insertBatch($data);
    }
}
