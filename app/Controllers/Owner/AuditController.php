<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\LogAktivitasModel;
use App\Services\DbHelper;
use App\Services\MockDataService;

class AuditController extends BaseController
{
    public function index()
    {
        $jenisAksi = strtoupper(trim((string) ($this->request->getGet('aksi') ?? 'SEMUA')));
        $logs      = [];

        if (DbHelper::isReady()) {
            $logModel = new LogAktivitasModel();
            $rows     = $logModel->getTerbaru(100);

            foreach ($rows as $r) {
                if ($jenisAksi !== 'SEMUA' && strtoupper((string) $r['jenis_aksi']) !== $jenisAksi) {
                    continue;
                }
                $pelaku = $r['nama_pengguna']
                    ? ($r['nama_pengguna'] . ' / ' . ($r['peran_pengguna'] ?? '-'))
                    : 'Sistem / Guest';

                $logs[] = [
                    'waktu'      => date('d M Y · H.i', strtotime((string) $r['waktu_dibuat'])) . ' WIB',
                    'pelaku'     => $pelaku,
                    'jenis_aksi' => $r['jenis_aksi'],
                    'entitas'    => trim(($r['entitas'] ?? '-') . ($r['entitas_id'] ? ' #' . $r['entitas_id'] : '')),
                    'keterangan' => $r['keterangan'] ?? ($r['detail_json'] ?? '-'),
                ];
            }
        } else {
            MockDataService::initSession();
            foreach (MockDataService::getAudit() as $a) {
                $logs[] = [
                    'waktu'      => $a['waktu'],
                    'pelaku'     => $a['pelaku'],
                    'jenis_aksi' => $a['aktivitas'],
                    'entitas'    => '-',
                    'keterangan' => $a['objek'],
                ];
            }
        }

        return view('owner/audit', [
            'title'     => 'Audit Log Sistem — Owner Pojok UKM',
            'activeNav' => 'audit',
            'logs'      => $logs,
            'jenisAksi' => $jenisAksi,
            'usingDb'   => DbHelper::isReady(),
        ]);
    }
}
