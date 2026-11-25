<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run()
    {
        // Catatan: Indeks unik parsial uq_satu_shift_aktif menjamin hanya boleh ada 1 baris dengan status = 'AKTIF'.
        // Oleh karena itu, 9 shift pertama berstatus 'TERTUTUP' dan shift ke-10 berstatus 'AKTIF'.
        $data = [
            // 1
            [
                'jenis_shift'      => 'PAGI',
                'tanggal'          => '2026-11-21',
                'waktu_buka'       => '2026-11-21 08:00:00+07',
                'waktu_tutup'      => '2026-11-21 15:00:00+07',
                'dibuka_oleh_id'   => 2,
                'ditutup_oleh_id'  => 2,
                'status'           => 'TERTUTUP',
                'waktu_dibuat'     => '2026-11-21 08:00:00+07',
                'waktu_diperbarui' => '2026-11-21 15:00:00+07',
            ],
            // 2
            [
                'jenis_shift'      => 'SORE',
                'tanggal'          => '2026-11-21',
                'waktu_buka'       => '2026-11-21 15:05:00+07',
                'waktu_tutup'      => '2026-11-21 22:00:00+07',
                'dibuka_oleh_id'   => 3,
                'ditutup_oleh_id'  => 3,
                'status'           => 'TERTUTUP',
                'waktu_dibuat'     => '2026-11-21 15:05:00+07',
                'waktu_diperbarui' => '2026-11-21 22:00:00+07',
            ],
            // 3
            [
                'jenis_shift'      => 'PAGI',
                'tanggal'          => '2026-11-22',
                'waktu_buka'       => '2026-11-22 08:00:00+07',
                'waktu_tutup'      => '2026-11-22 15:00:00+07',
                'dibuka_oleh_id'   => 2,
                'ditutup_oleh_id'  => 2,
                'status'           => 'TERTUTUP',
                'waktu_dibuat'     => '2026-11-22 08:00:00+07',
                'waktu_diperbarui' => '2026-11-22 15:00:00+07',
            ],
            // 4
            [
                'jenis_shift'      => 'SORE',
                'tanggal'          => '2026-11-22',
                'waktu_buka'       => '2026-11-22 15:05:00+07',
                'waktu_tutup'      => '2026-11-22 22:00:00+07',
                'dibuka_oleh_id'   => 4,
                'ditutup_oleh_id'  => 4,
                'status'           => 'TERTUTUP',
                'waktu_dibuat'     => '2026-11-22 15:05:00+07',
                'waktu_diperbarui' => '2026-11-22 22:00:00+07',
            ],
            // 5
            [
                'jenis_shift'      => 'PAGI',
                'tanggal'          => '2026-11-23',
                'waktu_buka'       => '2026-11-23 08:00:00+07',
                'waktu_tutup'      => '2026-11-23 15:00:00+07',
                'dibuka_oleh_id'   => 3,
                'ditutup_oleh_id'  => 3,
                'status'           => 'TERTUTUP',
                'waktu_dibuat'     => '2026-11-23 08:00:00+07',
                'waktu_diperbarui' => '2026-11-23 15:00:00+07',
            ],
            // 6
            [
                'jenis_shift'      => 'SORE',
                'tanggal'          => '2026-11-23',
                'waktu_buka'       => '2026-11-23 15:05:00+07',
                'waktu_tutup'      => '2026-11-23 22:00:00+07',
                'dibuka_oleh_id'   => 2,
                'ditutup_oleh_id'  => 2,
                'status'           => 'TERTUTUP',
                'waktu_dibuat'     => '2026-11-23 15:05:00+07',
                'waktu_diperbarui' => '2026-11-23 22:00:00+07',
            ],
            // 7
            [
                'jenis_shift'      => 'PAGI',
                'tanggal'          => '2026-11-24',
                'waktu_buka'       => '2026-11-24 08:00:00+07',
                'waktu_tutup'      => '2026-11-24 15:00:00+07',
                'dibuka_oleh_id'   => 4,
                'ditutup_oleh_id'  => 4,
                'status'           => 'TERTUTUP',
                'waktu_dibuat'     => '2026-11-24 08:00:00+07',
                'waktu_diperbarui' => '2026-11-24 15:00:00+07',
            ],
            // 8
            [
                'jenis_shift'      => 'SORE',
                'tanggal'          => '2026-11-24',
                'waktu_buka'       => '2026-11-24 15:05:00+07',
                'waktu_tutup'      => '2026-11-24 22:00:00+07',
                'dibuka_oleh_id'   => 3,
                'ditutup_oleh_id'  => 3,
                'status'           => 'TERTUTUP',
                'waktu_dibuat'     => '2026-11-24 15:05:00+07',
                'waktu_diperbarui' => '2026-11-24 22:00:00+07',
            ],
            // 9
            [
                'jenis_shift'      => 'PAGI',
                'tanggal'          => '2026-11-25',
                'waktu_buka'       => '2026-11-25 08:00:00+07',
                'waktu_tutup'      => '2026-11-25 15:00:00+07',
                'dibuka_oleh_id'   => 2,
                'ditutup_oleh_id'  => 2,
                'status'           => 'TERTUTUP',
                'waktu_dibuat'     => '2026-11-25 08:00:00+07',
                'waktu_diperbarui' => '2026-11-25 15:00:00+07',
            ],
            // 10 (Shift AKTIF saat ini)
            [
                'jenis_shift'      => 'PAGI',
                'tanggal'          => '2026-11-26',
                'waktu_buka'       => '2026-11-26 08:00:00+07',
                'waktu_tutup'      => null,
                'dibuka_oleh_id'   => 2,
                'ditutup_oleh_id'  => null,
                'status'           => 'AKTIF',
                'waktu_dibuat'     => '2026-11-26 08:00:00+07',
                'waktu_diperbarui' => '2026-11-26 08:00:00+07',
            ],
        ];

        $this->db->table('shift')->insertBatch($data);
    }
}
