<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'pembayaran_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pesanan_id',
        'kasir_id',
        'metode',
        'jumlah_dibayar',
        'uang_diterima',
        'waktu_bayar',
    ];

    protected $useTimestamps = false;
}
