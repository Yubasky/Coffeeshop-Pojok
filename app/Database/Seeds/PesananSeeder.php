<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PesananSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1. Pesanan Mandiri - LUNAS - SELESAI
            [
                'nomor_pesanan'        => 'ORD-20261125-0001',
                'dipesan_oleh'         => 'MANDIRI',
                'token_sesi_pelanggan' => 'a1b2c3d4e5f60718293a4b5c6d7e8f90a1b2c3d4',
                'nama_pelanggan'       => 'Dewi Lestari',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'SELESAI',
                'shift_id'             => 9,
                'dibuat_oleh_id'       => 7,
                'dibatalkan_oleh_id'   => null,
                'waktu_batal'          => null,
                'alasan_pembatalan'    => null,
                'waktu_dibuat'         => '2026-11-25 09:00:00+07',
                'waktu_diperbarui'     => '2026-11-25 09:15:00+07',
            ],
            // 2. Pesanan Kasir - LUNAS - SELESAI
            [
                'nomor_pesanan'        => 'ORD-20261125-0002',
                'dipesan_oleh'         => 'KASIR',
                'token_sesi_pelanggan' => null,
                'nama_pelanggan'       => 'Pak Rahmat',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'SELESAI',
                'shift_id'             => 9,
                'dibuat_oleh_id'       => 2,
                'dibatalkan_oleh_id'   => null,
                'waktu_batal'          => null,
                'alasan_pembatalan'    => null,
                'waktu_dibuat'         => '2026-11-25 10:00:00+07',
                'waktu_diperbarui'     => '2026-11-25 10:12:00+07',
            ],
            // 3. Pesanan Mandiri - LUNAS - SELESAI
            [
                'nomor_pesanan'        => 'ORD-20261125-0003',
                'dipesan_oleh'         => 'MANDIRI',
                'token_sesi_pelanggan' => 'b2c3d4e5f60718293a4b5c6d7e8f90a1b2c3d4e5',
                'nama_pelanggan'       => 'Fajar Nugraha',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'SELESAI',
                'shift_id'             => 9,
                'dibuat_oleh_id'       => 8,
                'dibatalkan_oleh_id'   => null,
                'waktu_batal'          => null,
                'alasan_pembatalan'    => null,
                'waktu_dibuat'         => '2026-11-25 11:00:00+07',
                'waktu_diperbarui'     => '2026-11-25 11:15:00+07',
            ],
            // 4. Pesanan Kasir - LUNAS - SELESAI
            [
                'nomor_pesanan'        => 'ORD-20261125-0004',
                'dipesan_oleh'         => 'KASIR',
                'token_sesi_pelanggan' => null,
                'nama_pelanggan'       => 'Bu Ratna',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'SELESAI',
                'shift_id'             => 9,
                'dibuat_oleh_id'       => 2,
                'dibatalkan_oleh_id'   => null,
                'waktu_batal'          => null,
                'alasan_pembatalan'    => null,
                'waktu_dibuat'         => '2026-11-25 12:30:00+07',
                'waktu_diperbarui'     => '2026-11-25 12:45:00+07',
            ],
            // 5. Pesanan Mandiri - LUNAS - SELESAI (Hari berjalan shift 10)
            [
                'nomor_pesanan'        => 'ORD-20261126-0001',
                'dipesan_oleh'         => 'MANDIRI',
                'token_sesi_pelanggan' => 'c3d4e5f60718293a4b5c6d7e8f90a1b2c3d4e5f6',
                'nama_pelanggan'       => 'Putri Ayu',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'SELESAI',
                'shift_id'             => 10,
                'dibuat_oleh_id'       => 9,
                'dibatalkan_oleh_id'   => null,
                'waktu_batal'          => null,
                'alasan_pembatalan'    => null,
                'waktu_dibuat'         => '2026-11-26 08:15:00+07',
                'waktu_diperbarui'     => '2026-11-26 08:30:00+07',
            ],
            // 6. Pesanan Kasir - LUNAS - SELESAI (Shift 10)
            [
                'nomor_pesanan'        => 'ORD-20261126-0002',
                'dipesan_oleh'         => 'KASIR',
                'token_sesi_pelanggan' => null,
                'nama_pelanggan'       => 'Dimas Anggara',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'SELESAI',
                'shift_id'             => 10,
                'dibuat_oleh_id'       => 2,
                'dibatalkan_oleh_id'   => null,
                'waktu_batal'          => null,
                'alasan_pembatalan'    => null,
                'waktu_dibuat'         => '2026-11-26 08:45:00+07',
                'waktu_diperbarui'     => '2026-11-26 09:00:00+07',
            ],
            // 7. Pesanan Mandiri - LUNAS - DIPROSES (Shift 10)
            [
                'nomor_pesanan'        => 'ORD-20261126-0003',
                'dipesan_oleh'         => 'MANDIRI',
                'token_sesi_pelanggan' => 'd4e5f60718293a4b5c6d7e8f90a1b2c3d4e5f607',
                'nama_pelanggan'       => 'Hendra Wijaya',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'DIPROSES',
                'shift_id'             => 10,
                'dibuat_oleh_id'       => 10,
                'dibatalkan_oleh_id'   => null,
                'waktu_batal'          => null,
                'alasan_pembatalan'    => null,
                'waktu_dibuat'         => '2026-11-26 09:10:00+07',
                'waktu_diperbarui'     => '2026-11-26 09:15:00+07',
            ],
            // 8. Pesanan Kasir - LUNAS - DIPROSES (Shift 10)
            [
                'nomor_pesanan'        => 'ORD-20261126-0004',
                'dipesan_oleh'         => 'KASIR',
                'token_sesi_pelanggan' => null,
                'nama_pelanggan'       => 'Surya Saputra',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'DIPROSES',
                'shift_id'             => 10,
                'dibuat_oleh_id'       => 2,
                'dibatalkan_oleh_id'   => null,
                'waktu_batal'          => null,
                'alasan_pembatalan'    => null,
                'waktu_dibuat'         => '2026-11-26 09:20:00+07',
                'waktu_diperbarui'     => '2026-11-26 09:25:00+07',
            ],
            // 9. Pesanan Mandiri - LUNAS - MENUNGGU (Shift 10)
            [
                'nomor_pesanan'        => 'ORD-20261126-0005',
                'dipesan_oleh'         => 'MANDIRI',
                'token_sesi_pelanggan' => 'e5f60718293a4b5c6d7e8f90a1b2c3d4e5f60718',
                'nama_pelanggan'       => 'Dewi Lestari',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'MENUNGGU',
                'shift_id'             => 10,
                'dibuat_oleh_id'       => 7,
                'dibatalkan_oleh_id'   => null,
                'waktu_batal'          => null,
                'alasan_pembatalan'    => null,
                'waktu_dibuat'         => '2026-11-26 09:30:00+07',
                'waktu_diperbarui'     => '2026-11-26 09:32:00+07',
            ],
            // 10. Pesanan Kasir - LUNAS - DIBATALKAN sebelum diproses (Shift 10)
            [
                'nomor_pesanan'        => 'ORD-20261126-0006',
                'dipesan_oleh'         => 'KASIR',
                'token_sesi_pelanggan' => null,
                'nama_pelanggan'       => 'Lukman Hakim',
                'status_pembayaran'    => 'LUNAS',
                'status_pesanan'       => 'DIBATALKAN',
                'shift_id'             => 10,
                'dibuat_oleh_id'       => 2,
                'dibatalkan_oleh_id'   => 2,
                'waktu_batal'          => '2026-11-26 09:45:00+07',
                'alasan_pembatalan'    => 'Pelanggan meminta batal karena buru-buru sebelum pesanan diproses barista',
                'waktu_dibuat'         => '2026-11-26 09:40:00+07',
                'waktu_diperbarui'     => '2026-11-26 09:45:00+07',
            ],
        ];

        $this->db->table('pesanan')->insertBatch($data);
    }
}
