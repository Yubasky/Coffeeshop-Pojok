<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LogAktivitasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1
            [
                'pengguna_id'  => 1,
                'jenis_aksi'   => 'LOGIN_BERHASIL',
                'entitas'      => 'pengguna',
                'entitas_id'   => 1,
                'detail_json'  => json_encode(['ip' => '127.0.0.1', 'peran' => 'OWNER']),
                'keterangan'   => 'Owner berhasil masuk ke sistem',
                'waktu_dibuat' => '2026-11-25 07:25:00+07',
            ],
            // 2
            [
                'pengguna_id'  => 1,
                'jenis_aksi'   => 'STOK_AWAL_BAHAN',
                'entitas'      => 'bahan_baku',
                'entitas_id'   => 1,
                'detail_json'  => json_encode(['stok_awal' => 2500.00, 'satuan' => 'gram']),
                'keterangan'   => 'Input saldo awal Biji Kopi Arabika Gayo',
                'waktu_dibuat' => '2026-11-25 07:30:00+07',
            ],
            // 3
            [
                'pengguna_id'  => 1,
                'jenis_aksi'   => 'PENYESUAIAN_STOK',
                'entitas'      => 'bahan_baku',
                'entitas_id'   => 5,
                'detail_json'  => json_encode(['selisih' => -50.00, 'alasan' => 'Koreksi stok opname']),
                'keterangan'   => 'Penyesuaian stok Sirup Caramel',
                'waktu_dibuat' => '2026-11-25 08:00:00+07',
            ],
            // 4
            [
                'pengguna_id'  => 2,
                'jenis_aksi'   => 'BUKA_SHIFT',
                'entitas'      => 'shift',
                'entitas_id'   => 9,
                'detail_json'  => json_encode(['jenis_shift' => 'PAGI', 'tanggal' => '2026-11-25']),
                'keterangan'   => 'Kasir membuka shift pagi 2026-11-25',
                'waktu_dibuat' => '2026-11-25 08:00:00+07',
            ],
            // 5
            [
                'pengguna_id'  => 2,
                'jenis_aksi'   => 'TUTUP_SHIFT',
                'entitas'      => 'shift',
                'entitas_id'   => 9,
                'detail_json'  => json_encode(['total_penjualan' => 111000]),
                'keterangan'   => 'Kasir menutup shift pagi 2026-11-25',
                'waktu_dibuat' => '2026-11-25 15:00:00+07',
            ],
            // 6
            [
                'pengguna_id'  => 1,
                'jenis_aksi'   => 'UBAH_HARGA_MENU',
                'entitas'      => 'menu',
                'entitas_id'   => 1,
                'detail_json'  => json_encode(['harga_lama' => 17000, 'harga_baru' => 18000]),
                'keterangan'   => 'Pembaruan harga Kopi Susu Pojok Gula Aren',
                'waktu_dibuat' => '2026-11-25 16:00:00+07',
            ],
            // 7
            [
                'pengguna_id'  => null,
                'jenis_aksi'   => 'LOGIN_GAGAL',
                'entitas'      => 'pengguna',
                'entitas_id'   => null,
                'detail_json'  => json_encode(['username_dicoba' => 'admin_test', 'ip' => '192.168.1.25']),
                'keterangan'   => 'Percobaan login gagal: username tidak ditemukan',
                'waktu_dibuat' => '2026-11-26 07:50:00+07',
            ],
            // 8
            [
                'pengguna_id'  => 2,
                'jenis_aksi'   => 'BUKA_SHIFT',
                'entitas'      => 'shift',
                'entitas_id'   => 10,
                'detail_json'  => json_encode(['jenis_shift' => 'PAGI', 'tanggal' => '2026-11-26']),
                'keterangan'   => 'Kasir membuka shift pagi 2026-11-26',
                'waktu_dibuat' => '2026-11-26 08:00:00+07',
            ],
            // 9
            [
                'pengguna_id'  => 5,
                'jenis_aksi'   => 'AKSES_DITOLAK',
                'entitas'      => 'halaman',
                'entitas_id'   => null,
                'detail_json'  => json_encode(['url' => '/owner/laporan-penjualan', 'peran' => 'BARISTA']),
                'keterangan'   => 'Barista mencoba mengakses halaman laporan Owner',
                'waktu_dibuat' => '2026-11-26 09:05:00+07',
            ],
            // 10
            [
                'pengguna_id'  => 2,
                'jenis_aksi'   => 'PEMBATALAN_PESANAN',
                'entitas'      => 'pesanan',
                'entitas_id'   => 10,
                'detail_json'  => json_encode([
                    'nomor_pesanan' => 'ORD-20261126-0006',
                    'alasan'        => 'Pelanggan meminta batal karena buru-buru sebelum pesanan diproses barista',
                ]),
                'keterangan'   => 'Kasir membatalkan pesanan ORD-20261126-0006 dan mengembalikan stok',
                'waktu_dibuat' => '2026-11-26 09:45:00+07',
            ],
        ];

        $this->db->table('log_aktivitas')->insertBatch($data);
    }
}
