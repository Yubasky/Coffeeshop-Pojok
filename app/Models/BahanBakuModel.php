<?php

namespace App\Models;

use CodeIgniter\Model;

class BahanBakuModel extends Model
{
    protected $table            = 'bahan_baku';
    protected $primaryKey       = 'bahan_baku_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama',
        'satuan',
        'stok_terkini',
        'waktu_dibuat',
        'waktu_diperbarui',
    ];

    protected $useTimestamps = false;

    /**
     * Cari bahan baku berdasarkan nama (case-insensitive) untuk mencegah duplikat.
     */
    public function findByNamaInsensitive(string $nama): ?array
    {
        $row = $this->where('LOWER(nama) =', strtolower(trim($nama)))->first();

        return is_array($row) ? $row : null;
    }
}
