<?php

namespace App\Models;

use CodeIgniter\Model;

class LogAktivitasModel extends Model
{
    protected $table            = 'log_aktivitas';
    protected $primaryKey       = 'log_aktivitas_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pengguna_id',
        'jenis_aksi',
        'entitas',
        'entitas_id',
        'detail_json',
        'keterangan',
        'waktu_dibuat',
    ];

    protected $useTimestamps = false;

    /**
     * Catat kejadian audit/keamanan ke tabel log_aktivitas.
     */
    public function catat(
        ?int $penggunaId,
        string $jenisAksi,
        ?string $entitas = null,
        ?int $entitasId = null,
        ?array $detail = null,
        ?string $keterangan = null
    ): void {
        $this->insert([
            'pengguna_id'  => $penggunaId,
            'jenis_aksi'   => $jenisAksi,
            'entitas'      => $entitas,
            'entitas_id'   => $entitasId,
            'detail_json'  => $detail !== null ? json_encode($detail, JSON_UNESCAPED_UNICODE) : null,
            'keterangan'   => $keterangan,
            'waktu_dibuat' => date('Y-m-d H:i:sP'),
        ]);
    }

    /**
     * Ambil daftar audit log terbaru beserta nama dan peran pelaku.
     */
    public function getTerbaru(int $limit = 50): array
    {
        return $this->select('log_aktivitas.*, pengguna.nama AS nama_pengguna, pengguna.peran AS peran_pengguna')
            ->join('pengguna', 'pengguna.pengguna_id = log_aktivitas.pengguna_id', 'left')
            ->orderBy('log_aktivitas.waktu_dibuat', 'DESC')
            ->orderBy('log_aktivitas.log_aktivitas_id', 'DESC')
            ->findAll($limit);
    }
}
