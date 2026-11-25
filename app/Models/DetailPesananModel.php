<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPesananModel extends Model
{
    protected $table            = 'detail_pesanan';
    protected $primaryKey       = 'detail_pesanan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pesanan_id',
        'menu_id',
        'jumlah',
        'harga_satuan',
        'catatan',
        'waktu_dibuat',
        'waktu_diperbarui',
    ];

    protected $useTimestamps = false;
}
