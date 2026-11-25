<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BahanBakuSeeder extends Seeder
{
    public function run()
    {
        $now = '2026-11-26 07:10:00+07';

        $data = [
            // 1
            [
                'nama'             => 'Biji Kopi Arabika Gayo',
                'satuan'           => 'gram',
                'stok_terkini'     => 2500.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 2
            [
                'nama'             => 'Biji Kopi House Blend Pojok',
                'satuan'           => 'gram',
                'stok_terkini'     => 5000.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 3
            [
                'nama'             => 'Susu UHT Full Cream',
                'satuan'           => 'ml',
                'stok_terkini'     => 15000.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 4
            [
                'nama'             => 'Gula Aren Cair',
                'satuan'           => 'ml',
                'stok_terkini'     => 4000.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 5
            [
                'nama'             => 'Sirup Caramel',
                'satuan'           => 'ml',
                'stok_terkini'     => 2250.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 6
            [
                'nama'             => 'Bubuk Matcha Uji',
                'satuan'           => 'gram',
                'stok_terkini'     => 1200.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 7
            [
                'nama'             => 'Bubuk Dark Chocolate',
                'satuan'           => 'gram',
                'stok_terkini'     => 1800.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 8
            [
                'nama'             => 'Bubuk Red Velvet',
                'satuan'           => 'gram',
                'stok_terkini'     => 1000.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 9
            [
                'nama'             => 'Teh Hitam Daun & Ekstrak Lemon',
                'satuan'           => 'gram',
                'stok_terkini'     => 1500.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 10
            [
                'nama'             => 'Cup Plastik 16oz & Tutup',
                'satuan'           => 'pcs',
                'stok_terkini'     => 350.00,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
        ];

        $this->db->table('bahan_baku')->insertBatch($data);
    }
}
