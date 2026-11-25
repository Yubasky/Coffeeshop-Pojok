<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table            = 'pengguna';
    protected $primaryKey       = 'pengguna_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama',
        'username',
        'password_hash',
        'peran',
        'waktu_dibuat',
        'waktu_diperbarui',
    ];

    protected $useTimestamps = false;

    /**
     * Cari akun pengguna berdasarkan username secara case-insensitive.
     */
    public function findByUsernameInsensitive(string $username): ?array
    {
        $row = $this->where('LOWER(username) =', strtolower(trim($username)))->first();

        return is_array($row) ? $row : null;
    }

    /**
     * Ambil daftar akun staf internal (KASIR, BARISTA, OWNER).
     */
    public function getStaff(?string $search = null, ?string $role = null): array
    {
        $builder = $this->whereIn('peran', ['KASIR', 'BARISTA', 'OWNER']);

        if ($role !== null && $role !== '' && strtoupper($role) !== 'SEMUA') {
            $builder->where('peran', strtoupper($role));
        }

        if ($search !== null && trim($search) !== '') {
            $keyword = '%' . trim($search) . '%';
            $builder->groupStart()
                ->where('nama ILIKE', $keyword)
                ->orWhere('username ILIKE', $keyword)
                ->groupEnd();
        }

        return $builder->orderBy('pengguna_id', 'ASC')->findAll();
    }
}
