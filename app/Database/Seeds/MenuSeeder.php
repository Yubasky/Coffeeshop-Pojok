<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $now = '2026-11-26 07:05:00+07';

        $data = [
            // 1
            [
                'nama'             => 'Kopi Susu Pojok Gula Aren',
                'kategori'         => 'KOPI',
                'harga'            => 18000,
                'deskripsi'        => 'Es kopi susu signature Pojok UKM dengan perpaduan espresso, susu segar, dan gula aren asli.',
                'foto_url'         => '/uploads/menu/kopi-susu-gula-aren.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 2
            [
                'nama'             => 'Americano',
                'kategori'         => 'KOPI',
                'harga'            => 15000,
                'deskripsi'        => 'Double shot espresso dengan air mineral, tersedia dingin atau panas.',
                'foto_url'         => '/uploads/menu/americano.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 3
            [
                'nama'             => 'Caffe Latte',
                'kategori'         => 'KOPI',
                'harga'            => 20000,
                'deskripsi'        => 'Espresso lembut yang dipadukan dengan steamed fresh milk.',
                'foto_url'         => '/uploads/menu/caffe-latte.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 4
            [
                'nama'             => 'Cappuccino',
                'kategori'         => 'KOPI',
                'harga'            => 20000,
                'deskripsi'        => 'Keseimbangan espresso, susu segar, dan lapisan foam tebal.',
                'foto_url'         => '/uploads/menu/cappuccino.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 5
            [
                'nama'             => 'Caramel Macchiato',
                'kategori'         => 'KOPI',
                'harga'            => 23000,
                'deskripsi'        => 'Espresso dengan sirup vanila, susu segar, dan saus karamel di atasnya.',
                'foto_url'         => '/uploads/menu/caramel-macchiato.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 6
            [
                'nama'             => 'V60 Manual Brew Arabika Gayo',
                'kategori'         => 'KOPI',
                'harga'            => 22000,
                'deskripsi'        => 'Seduhan manual biji kopi single origin Arabika Gayo dengan cita rasa fruity dan floral.',
                'foto_url'         => '/uploads/menu/v60-arabika-gayo.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 7
            [
                'nama'             => 'Matcha Latte',
                'kategori'         => 'NON_KOPI',
                'harga'            => 20000,
                'deskripsi'        => 'Minuman teh hijau Uji Matcha Jepang pilihan dengan susu segar.',
                'foto_url'         => '/uploads/menu/matcha-latte.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 8
            [
                'nama'             => 'Signature Chocolate',
                'kategori'         => 'NON_KOPI',
                'harga'            => 19000,
                'deskripsi'        => 'Cokelat dark creamy kaya rasa yang disajikan dingin maupun hangat.',
                'foto_url'         => '/uploads/menu/signature-chocolate.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 9
            [
                'nama'             => 'Red Velvet Latte',
                'kategori'         => 'NON_KOPI',
                'harga'            => 19000,
                'deskripsi'        => 'Minuman non-kopi manis dan lembut dengan rasa khas Red Velvet.',
                'foto_url'         => '/uploads/menu/red-velvet-latte.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 10
            [
                'nama'             => 'Lemon Tea Segar',
                'kategori'         => 'NON_KOPI',
                'harga'            => 14000,
                'deskripsi'        => 'Seduhan teh hitam pilihan dengan perasan lemon segar.',
                'foto_url'         => '/uploads/menu/lemon-tea.jpg',
                'aktif'            => true,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
        ];

        $this->db->table('menu')->insertBatch($data);
    }
}
