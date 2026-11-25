<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatStokModel extends Model
{
    protected $table            = 'riwayat_stok';
    protected $primaryKey       = 'riwayat_stok_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'bahan_baku_id',
        'pesanan_id',
        'pengguna_id',
        'jenis_perubahan',
        'jumlah_perubahan',
        'keterangan',
        'waktu_dibuat',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil daftar mutasi stok terbaru lengkap dengan nama bahan dan pelaku.
     */
    public function getMutasiTerbaru(int $limit = 30): array
    {
        return $this->select('riwayat_stok.*, bahan_baku.nama AS nama_bahan, bahan_baku.satuan, pengguna.nama AS nama_pengguna')
            ->join('bahan_baku', 'bahan_baku.bahan_baku_id = riwayat_stok.bahan_baku_id', 'left')
            ->join('pengguna', 'pengguna.pengguna_id = riwayat_stok.pengguna_id', 'left')
            ->orderBy('riwayat_stok.waktu_dibuat', 'DESC')
            ->orderBy('riwayat_stok.riwayat_stok_id', 'DESC')
            ->findAll($limit);
    }
}
