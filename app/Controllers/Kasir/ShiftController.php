<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Services\MockDataService;

class ShiftController extends BaseController
{
    public function toggle()
    {
        MockDataService::initSession();
        $shift = MockDataService::getShift();
        $action = (string) $this->request->getPost('action');
        $jenis  = strtoupper((string) ($this->request->getPost('jenis') ?? $shift['jenis'] ?? 'PAGI'));

        if ($action === 'tutup') {
            $shift['aktif'] = false;
            $msg = 'Shift ' . $shift['jenis'] . ' berhasil ditutup.';
        } else {
            if (! empty($shift['aktif']) && $shift['jenis'] !== $jenis) {
                return redirect()->back()->with('error', 'Tolak: Masih ada Shift ' . $shift['jenis'] . ' yang AKTIF. Tutup shift aktif terlebih dahulu.');
            }
            $shift['aktif'] = true;
            $shift['jenis'] = $jenis;
            $shift['jam']   = $jenis === 'PAGI' ? '08.00–15.00 WIB' : '15.00–22.00 WIB';
            $msg = 'Shift ' . $jenis . ' dibuka dan berstatus AKTIF.';
        }

        session()->set('mock_shift', $shift);
        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / KASIR', $action === 'tutup' ? 'Tutup Shift' : 'Buka Shift', 'Shift ' . $jenis);

        return redirect()->to('/kasir/pos')->with('success', $msg);
    }
}

