<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftModel extends Model
{
    protected $table            = 'shift';
    protected $primaryKey       = 'shift_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jenis_shift',
        'tanggal',
        'waktu_buka',
        'waktu_tutup',
        'dibuka_oleh_id',
        'ditutup_oleh_id',
        'status',
        'waktu_dibuat',
        'waktu_diperbarui',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil satu shift yang sedang berstatus AKTIF beserta nama kasir pembuka.
     */
    public function getActiveShift(): ?array
    {
        $row = $this->select('shift.*, pengguna.nama AS nama_kasir_pembuka')
            ->join('pengguna', 'pengguna.pengguna_id = shift.dibuka_oleh_id', 'left')
            ->where('shift.status', 'AKTIF')
            ->first();

        return is_array($row) ? $row : null;
    }
}
