<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('PenggunaSeeder');
        $this->call('MenuSeeder');
        $this->call('BahanBakuSeeder');
        $this->call('ShiftSeeder');
        $this->call('PesananSeeder');
        $this->call('DetailPesananSeeder');
        $this->call('PembayaranSeeder');
        $this->call('RiwayatStokSeeder');
        $this->call('RiwayatPesananSeeder');
        $this->call('LogAktivitasSeeder');
    }
}
