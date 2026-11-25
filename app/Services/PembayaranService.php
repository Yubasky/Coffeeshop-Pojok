<?php

namespace App\Services;

use App\Models\LogAktivitasModel;
use App\Models\PembayaranModel;
use App\Models\RiwayatPesananModel;
use Config\Database;
use RuntimeException;

/**
 * Layanan transaksi pembayaran atomik (SELECT ... FOR UPDATE) dan pembatalan pesanan.
 */
class PembayaranService
{
    /**
     * Konfirmasi pembayaran pesanan dalam transaksi atomik.
     */
    public function konfirmasiPembayaran(
        int $pesananId,
        int $kasirId,
        int $shiftId,
        string $metode,
        int $jumlahDibayar,
        ?int $uangDiterima = null
    ): int {
        $metodeUpper = strtoupper($metode);
        if (! in_array($metodeUpper, ['TUNAI', 'QRIS'], true)) {
            throw new RuntimeException('Metode pembayaran harus TUNAI atau QRIS.');
        }

        if ($metodeUpper === 'TUNAI' && ($uangDiterima === null || $uangDiterima < $jumlahDibayar)) {
            throw new RuntimeException('Uang diterima tunai harus >= total tagihan.');
        }

        $db           = Database::connect();
        $bayarModel   = new PembayaranModel($db);
        $logModel     = new LogAktivitasModel($db);
        $now          = date('Y-m-d H:i:sP');

        $db->transException(true)->transStart();

        $pesanan = $db->query(
            'SELECT pesanan_id, nomor_pesanan, status_pembayaran, status_pesanan FROM pesanan WHERE pesanan_id = ? FOR UPDATE',
            [$pesananId]
        )->getRowArray();

        if (! $pesanan) {
            throw new RuntimeException('Pesanan tidak ditemukan.');
        }

        if ($pesanan['status_pembayaran'] === 'LUNAS') {
            throw new RuntimeException('Pesanan sudah berstatus LUNAS.');
        }

        if ($pesanan['status_pesanan'] === 'DIBATALKAN') {
            throw new RuntimeException('Pesanan yang sudah dibatalkan tidak dapat dibayar.');
        }

        $db->table('pesanan')
            ->where('pesanan_id', $pesananId)
            ->update([
                'status_pembayaran' => 'LUNAS',
                'shift_id'          => $shiftId,
                'waktu_diperbarui'  => $now,
            ]);

        $pembayaranId = (int) $bayarModel->insert([
            'pesanan_id'     => $pesananId,
            'kasir_id'       => $kasirId,
            'metode'         => $metodeUpper,
            'jumlah_dibayar' => $jumlahDibayar,
            'uang_diterima'  => $metodeUpper === 'TUNAI' ? $uangDiterima : null,
            'waktu_bayar'    => $now,
        ], true);

        $logModel->catat(
            $kasirId,
            'KONFIRMASI_PEMBAYARAN',
            'pesanan',
            $pesananId,
            [
                'nomor_pesanan'  => $pesanan['nomor_pesanan'],
                'metode'         => $metodeUpper,
                'jumlah_dibayar' => $jumlahDibayar,
            ],
            'Konfirmasi pelunasan ' . $pesanan['nomor_pesanan'] . ' via ' . $metodeUpper
        );

        $db->transComplete();

        return $pembayaranId;
    }

    /**
     * Membatalkan pesanan yang masih berstatus MENUNGGU secara atomik.
     */
    public function batalkanPesanan(
        int $pesananId,
        int $kasirId,
        string $alasan,
        ?int $shiftId = null
    ): void {
        $alasan = trim($alasan);
        if ($alasan === '') {
            throw new RuntimeException('Alasan pembatalan wajib diisi.');
        }

        $db             = Database::connect();
        $riwayatModel   = new RiwayatPesananModel($db);
        $logModel       = new LogAktivitasModel($db);
        $now            = date('Y-m-d H:i:sP');

        $db->transException(true)->transStart();

        $pesanan = $db->query(
            'SELECT pesanan_id, nomor_pesanan, status_pesanan, shift_id FROM pesanan WHERE pesanan_id = ? FOR UPDATE',
            [$pesananId]
        )->getRowArray();

        if (! $pesanan) {
            throw new RuntimeException('Pesanan tidak ditemukan.');
        }

        if ($pesanan['status_pesanan'] !== 'MENUNGGU') {
            throw new RuntimeException('Hanya pesanan berstatus MENUNGGU yang dapat dibatalkan.');
        }

        $db->table('pesanan')
            ->where('pesanan_id', $pesananId)
            ->update([
                'status_pesanan'     => 'DIBATALKAN',
                'shift_id'           => $pesanan['shift_id'] ?? $shiftId,
                'dibatalkan_oleh_id' => $kasirId,
                'waktu_batal'        => $now,
                'alasan_pembatalan'  => $alasan,
                'waktu_diperbarui'   => $now,
            ]);

        $riwayatModel->insert([
            'pesanan_id'     => $pesananId,
            'status_lama'    => 'MENUNGGU',
            'status_baru'    => 'DIBATALKAN',
            'diubah_oleh_id' => $kasirId,
            'catatan'        => $alasan,
            'waktu_dibuat'   => $now,
        ]);

        $logModel->catat(
            $kasirId,
            'PEMBATALAN_PESANAN',
            'pesanan',
            $pesananId,
            ['nomor_pesanan' => $pesanan['nomor_pesanan'], 'alasan' => $alasan],
            'Pembatalan pesanan ' . $pesanan['nomor_pesanan']
        );

        $db->transComplete();
    }
}
