<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table            = 'menu';
    protected $primaryKey       = 'menu_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama',
        'kategori',
        'harga',
        'deskripsi',
        'foto_url',
        'aktif',
        'waktu_dibuat',
        'waktu_diperbarui',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil daftar menu dengan filter opsional aktif, kategori, dan pencarian nama.
     */
    public function filterMenus(?bool $onlyActive = null, string $kategori = 'SEMUA', string $search = ''): array
    {
        $builder = $this->builder();

        if ($onlyActive !== null) {
            $builder->where('aktif', $onlyActive ? 'TRUE' : 'FALSE', false);
        }

        $kategoriUpper = strtoupper($kategori);
        if ($kategoriUpper !== 'SEMUA' && in_array($kategoriUpper, ['KOPI', 'NON_KOPI'], true)) {
            $builder->where('kategori', $kategoriUpper);
        }

        if (trim($search) !== '') {
            $builder->where('nama ILIKE', '%' . trim($search) . '%');
        }

        return $builder->orderBy('menu_id', 'ASC')->get()->getResultArray();
    }
}
