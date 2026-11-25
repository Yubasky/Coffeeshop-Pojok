<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table            = 'pesanan';
    protected $primaryKey       = 'pesanan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_pesanan',
        'dipesan_oleh',
        'token_sesi_pelanggan',
        'nama_pelanggan',
        'status_pembayaran',
        'status_pesanan',
        'shift_id',
        'dibuat_oleh_id',
        'dibatalkan_oleh_id',
        'waktu_batal',
        'alasan_pembatalan',
        'waktu_dibuat',
        'waktu_diperbarui',
    ];

    protected $useTimestamps = false;
}
