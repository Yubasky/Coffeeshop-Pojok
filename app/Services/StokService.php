<?php

namespace App\Services;

use App\Models\BahanBakuModel;
use App\Models\LogAktivitasModel;
use App\Models\RiwayatStokModel;
use Config\Database;
use RuntimeException;

/**
 * Layanan bisnis untuk pengelolaan bahan baku dan mutasi stok atomik.
 */
class StokService
{
    /**
     * Menambahkan bahan baku baru beserta pencatatan STOK_AWAL ke riwayat_stok
     * dan audit log dalam satu transaksi database.
     */
    public function tambahBahanBakuDenganStokAwal(
        string $nama,
        string $satuan,
        float $stokAwal,
        int $penggunaId
    ): int {
        $nama   = trim($nama);
        $satuan = trim($satuan);

        if ($nama === '' || $satuan === '' || $stokAwal <= 0) {
            throw new RuntimeException('Nama bahan, satuan, dan stok awal (> 0) wajib diisi.');
        }

        $db           = Database::connect();
        $bahanModel   = new BahanBakuModel($db);
        $riwayatModel = new RiwayatStokModel($db);
        $logModel     = new LogAktivitasModel($db);

        if ($bahanModel->findByNamaInsensitive($nama) !== null) {
            throw new RuntimeException('Bahan baku dengan nama "' . $nama . '" sudah terdaftar.');
        }

        $now = date('Y-m-d H:i:sP');
        $db->transException(true)->transStart();

        $bahanId = (int) $bahanModel->insert([
            'nama'             => $nama,
            'satuan'           => $satuan,
            'stok_terkini'     => $stokAwal,
            'waktu_dibuat'     => $now,
            'waktu_diperbarui' => $now,
        ], true);

        $riwayatModel->insert([
            'bahan_baku_id'    => $bahanId,
            'pesanan_id'       => null,
            'pengguna_id'      => $penggunaId,
            'jenis_perubahan'  => 'STOK_AWAL',
            'jumlah_perubahan' => $stokAwal,
            'keterangan'       => 'Stok awal bahan baku ' . $nama,
            'waktu_dibuat'     => $now,
        ]);

        $logModel->catat(
            $penggunaId,
            'STOK_AWAL_BAHAN',
            'bahan_baku',
            $bahanId,
            ['nama' => $nama, 'stok_awal' => $stokAwal, 'satuan' => $satuan],
            'Menambah bahan baku baru "' . $nama . '" dengan stok awal ' . $stokAwal . ' ' . $satuan
        );

        $db->transComplete();

        return $bahanId;
    }

    /**
     * Melakukan mutasi stok (MASUK atau PENYESUAIAN) menggunakan SELECT ... FOR UPDATE.
     */
    public function sesuaikanStok(
        int $bahanBakuId,
        float $delta,
        int $penggunaId,
        string $jenisPerubahan = 'PENYESUAIAN',
        ?string $keterangan = null
    ): array {
        if ($delta == 0.0) {
            throw new RuntimeException('Nilai perubahan stok tidak boleh 0.');
        }

        $jenisUpper = strtoupper($jenisPerubahan);
        if (! in_array($jenisUpper, ['MASUK', 'PENYESUAIAN'], true)) {
            $jenisUpper = 'PENYESUAIAN';
        }
        if ($jenisUpper === 'MASUK' && $delta < 0) {
            $jenisUpper = 'PENYESUAIAN';
        }

        $db           = Database::connect();
        $riwayatModel = new RiwayatStokModel($db);
        $logModel     = new LogAktivitasModel($db);
        $now          = date('Y-m-d H:i:sP');

        $db->transException(true)->transStart();

        $row = $db->query(
            'SELECT bahan_baku_id, nama, satuan, stok_terkini FROM bahan_baku WHERE bahan_baku_id = ? FOR UPDATE',
            [$bahanBakuId]
        )->getRowArray();

        if (! $row) {
            throw new RuntimeException('Bahan baku tidak ditemukan.');
        }

        $stokBaru = round((float) $row['stok_terkini'] + $delta, 2);
        if ($stokBaru < 0) {
            throw new RuntimeException('Stok akhir tidak boleh negatif.');
        }

        $db->table('bahan_baku')
            ->where('bahan_baku_id', $bahanBakuId)
            ->update([
                'stok_terkini'     => $stokBaru,
                'waktu_diperbarui' => $now,
            ]);

        $riwayatModel->insert([
            'bahan_baku_id'    => $bahanBakuId,
            'pesanan_id'       => null,
            'pengguna_id'      => $penggunaId,
            'jenis_perubahan'  => $jenisUpper,
            'jumlah_perubahan' => $delta,
            'keterangan'       => $keterangan ?? ($delta > 0 ? 'Penambahan / koreksi stok masuk' : 'Penyesuaian stok berkurang'),
            'waktu_dibuat'     => $now,
        ]);

        $logModel->catat(
            $penggunaId,
            'PENYESUAIAN_STOK',
            'bahan_baku',
            $bahanBakuId,
            [
                'nama'       => $row['nama'],
                'stok_lama'  => (float) $row['stok_terkini'],
                'perubahan'  => $delta,
                'stok_baru'  => $stokBaru,
                'satuan'     => $row['satuan'],
            ],
            'Mutasi stok ' . $row['nama'] . ' (' . ($delta > 0 ? '+' : '') . $delta . ' ' . $row['satuan'] . ')'
        );

        $db->transComplete();

        return [
            'nama'      => $row['nama'],
            'satuan'    => $row['satuan'],
            'stok_baru' => $stokBaru,
        ];
    }
}
