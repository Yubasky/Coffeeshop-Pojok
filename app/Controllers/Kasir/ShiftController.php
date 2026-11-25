<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\LogAktivitasModel;
use App\Models\RingkasanShiftModel;
use App\Models\ShiftModel;
use App\Services\DbHelper;
use App\Services\MockDataService;
use Throwable;

class ShiftController extends BaseController
{
    public function index()
    {
        if (DbHelper::isReady()) {
            $activeRow = (new ShiftModel())->getActiveShift();
            $shift     = $activeRow ? [
                'aktif' => true,
                'jenis' => $activeRow['jenis_shift'],
                'kasir' => $activeRow['nama_kasir_pembuka'] ?? '-',
            ] : [
                'aktif' => false,
                'jenis' => 'PAGI',
                'kasir' => '-',
            ];

            $ringkasanShift = (new RingkasanShiftModel())->orderBy('shift_id', 'DESC')->findAll(20);

            return view('kasir/shift', [
                'title'          => 'Manajemen Shift — Kasir Pojok UKM',
                'shift'          => $shift,
                'ringkasanShift' => $ringkasanShift,
            ]);
        }

        MockDataService::initSession();

        return view('kasir/shift', [
            'title'          => 'Manajemen Shift — Kasir Pojok UKM',
            'shift'          => MockDataService::getShift(),
            'ringkasanShift' => [],
        ]);
    }

    public function toggle()
    {
        $action = (string) $this->request->getPost('action');
        $jenis  = strtoupper((string) ($this->request->getPost('jenis') ?? 'PAGI'));
        $jenis  = in_array($jenis, ['PAGI', 'SORE'], true) ? $jenis : 'PAGI';

        if (DbHelper::isReady()) {
            $shiftModel = new ShiftModel();
            $logModel   = new LogAktivitasModel();
            $active     = $shiftModel->getActiveShift();
            $user       = session()->get('user');
            $kasirId    = (int) ($user['id'] ?? 2);
            $now        = date('Y-m-d H:i:sP');

            if ($action === 'tutup') {
                if (! $active) {
                    return redirect()->back()->with('error', 'Tidak ada shift aktif yang dapat ditutup.');
                }

                $shiftModel->update((int) $active['shift_id'], [
                    'status'           => 'TERTUTUP',
                    'waktu_tutup'      => $now,
                    'ditutup_oleh_id'  => $kasirId,
                    'waktu_diperbarui' => $now,
                ]);

                $logModel->catat(
                    $kasirId,
                    'TUTUP_SHIFT',
                    'shift',
                    (int) $active['shift_id'],
                    ['jenis_shift' => $active['jenis_shift']],
                    'Menutup shift ' . $active['jenis_shift']
                );

                return redirect()->to('/kasir/pos')->with('success', 'Shift ' . $active['jenis_shift'] . ' berhasil ditutup.');
            }

            // Buka shift baru: tolak jika masih ada shift berstatus AKTIF (uq_satu_shift_aktif)
            if ($active !== null) {
                return redirect()->back()->with(
                    'error',
                    'Tolak: Masih ada Shift ' . $active['jenis_shift'] . ' yang AKTIF. Tutup shift aktif terlebih dahulu.'
                );
            }

            try {
                $shiftId = (int) $shiftModel->insert([
                    'jenis_shift'      => $jenis,
                    'tanggal'          => date('Y-m-d'),
                    'waktu_buka'       => $now,
                    'waktu_tutup'      => null,
                    'dibuka_oleh_id'   => $kasirId,
                    'ditutup_oleh_id'  => null,
                    'status'           => 'AKTIF',
                    'waktu_dibuat'     => $now,
                    'waktu_diperbarui' => $now,
                ], true);

                $logModel->catat(
                    $kasirId,
                    'BUKA_SHIFT',
                    'shift',
                    $shiftId,
                    ['jenis_shift' => $jenis, 'tanggal' => date('Y-m-d')],
                    'Membuka shift ' . $jenis
                );
            } catch (Throwable $e) {
                return redirect()->back()->with('error', 'Gagal membuka shift: masih terdapat shift aktif.');
            }

            return redirect()->to('/kasir/pos')->with('success', 'Shift ' . $jenis . ' dibuka dan berstatus AKTIF.');
        }

        // Fallback Mode Mock Session
        MockDataService::initSession();
        $shift = MockDataService::getShift();
        $jenis = strtoupper((string) ($this->request->getPost('jenis') ?? $shift['jenis'] ?? 'PAGI'));

        if ($action === 'tutup') {
            $shift['aktif'] = false;
            $msg            = 'Shift ' . $shift['jenis'] . ' berhasil ditutup.';
        } else {
            if (! empty($shift['aktif']) && $shift['jenis'] !== $jenis) {
                return redirect()->back()->with('error', 'Tolak: Masih ada Shift ' . $shift['jenis'] . ' yang AKTIF. Tutup shift aktif terlebih dahulu.');
            }
            $shift['aktif'] = true;
            $shift['jenis'] = $jenis;
            $shift['jam']   = $jenis === 'PAGI' ? '08.00–15.00 WIB' : '15.00–22.00 WIB';
            $msg            = 'Shift ' . $jenis . ' dibuka dan berstatus AKTIF.';
        }

        session()->set('mock_shift', $shift);
        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / KASIR', $action === 'tutup' ? 'Tutup Shift' : 'Buka Shift', 'Shift ' . $jenis);

        return redirect()->to('/kasir/pos')->with('success', $msg);
    }
}
