<?php

namespace App\Models;

use CodeIgniter\Model;

class RiwayatPesananModel extends Model
{
    protected $table            = 'riwayat_pesanan';
    protected $primaryKey       = 'riwayat_pesanan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pesanan_id',
        'status_lama',
        'status_baru',
        'diubah_oleh_id',
        'catatan',
        'waktu_dibuat',
    ];

    protected $useTimestamps = false;
}
