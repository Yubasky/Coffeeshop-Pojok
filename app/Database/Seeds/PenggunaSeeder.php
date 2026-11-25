<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    public function run()
    {
        $passwordHash = password_hash('Pojok123!', PASSWORD_BCRYPT);
        $now          = '2026-11-26 07:00:00+07';

        $data = [
            // 1. OWNER
            [
                'nama'             => 'Budi Santoso',
                'username'         => 'owner_budi',
                'password_hash'    => $passwordHash,
                'peran'            => 'OWNER',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 2. KASIR 1
            [
                'nama'             => 'Siti Aminah',
                'username'         => 'kasir_siti',
                'password_hash'    => $passwordHash,
                'peran'            => 'KASIR',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 3. KASIR 2
            [
                'nama'             => 'Rina Wati',
                'username'         => 'kasir_rina',
                'password_hash'    => $passwordHash,
                'peran'            => 'KASIR',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 4. KASIR 3
            [
                'nama'             => 'Dedi Kurniawan',
                'username'         => 'kasir_dedi',
                'password_hash'    => $passwordHash,
                'peran'            => 'KASIR',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 5. BARISTA 1
            [
                'nama'             => 'Andi Pratama',
                'username'         => 'barista_andi',
                'password_hash'    => $passwordHash,
                'peran'            => 'BARISTA',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 6. BARISTA 2
            [
                'nama'             => 'Reza Rahadian',
                'username'         => 'barista_reza',
                'password_hash'    => $passwordHash,
                'peran'            => 'BARISTA',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 7. PELANGGAN 1
            [
                'nama'             => 'Dewi Lestari',
                'username'         => 'dewi_l',
                'password_hash'    => $passwordHash,
                'peran'            => 'PELANGGAN',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 8. PELANGGAN 2
            [
                'nama'             => 'Fajar Nugraha',
                'username'         => 'fajar_n',
                'password_hash'    => $passwordHash,
                'peran'            => 'PELANGGAN',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 9. PELANGGAN 3
            [
                'nama'             => 'Putri Ayu',
                'username'         => 'putri_ayu',
                'password_hash'    => $passwordHash,
                'peran'            => 'PELANGGAN',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
            // 10. PELANGGAN 4
            [
                'nama'             => 'Hendra Wijaya',
                'username'         => 'hendra_w',
                'password_hash'    => $passwordHash,
                'peran'            => 'PELANGGAN',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ],
        ];

        $this->db->table('pengguna')->insertBatch($data);
    }
}
