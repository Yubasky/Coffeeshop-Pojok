<?php

namespace App\Models;

use CodeIgniter\Model;

class RingkasanShiftModel extends Model
{
    /**
     * Database view v_ringkasan_shift (read-only).
     */
    protected $table            = 'v_ringkasan_shift';
    protected $primaryKey       = 'shift_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    protected $useTimestamps = false;
}
