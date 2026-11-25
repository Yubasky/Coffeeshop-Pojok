<?php

namespace App\Controllers;

use App\Models\BahanBakuModel;
use App\Models\RiwayatStokModel;
use App\Services\DbHelper;
use App\Services\MockDataService;
use App\Services\StokService;
use Throwable;

class StokController extends BaseController
{
    public function index()
    {
        $search  = trim((string) $this->request->getGet('q'));
        $tab     = (string) ($this->request->getGet('tab') ?? 'daftar');
        $user    = session()->get('user');
        $isOwner = ($user['role'] ?? '') === 'OWNER';

        if (DbHelper::isReady()) {
            [$bahan, $mutasi] = $this->loadFromDatabase($search);

            return view('stok/index', [
                'title'     => 'Stok Bahan Baku — Pojok UKM Tanjung Gading',
                'activeNav' => 'stok',
                'isOwner'   => $isOwner,
                'bahan'     => $bahan,
                'mutasi'    => $mutasi,
                'search'    => $search,
                'tab'       => $tab,
            ]);
        }

        MockDataService::initSession();

        $bahan = array_filter(MockDataService::getBahan(), static function ($b) use ($search) {
            if ($search !== '' && stripos($b['nama'], $search) === false) {
                return false;
            }
            return true;
        });

        return view('stok/index', [
            'title'     => 'Stok Bahan Baku — Pojok UKM Tanjung Gading',
            'activeNav' => 'stok',
            'isOwner'   => $isOwner,
            'bahan'     => array_values($bahan),
            'mutasi'    => MockDataService::getMutasi(),
            'search'    => $search,
            'tab'       => $tab,
        ]);
    }

    public function riwayat()
    {
        $user    = session()->get('user');
        $isOwner = ($user['role'] ?? '') === 'OWNER';

        if (DbHelper::isReady()) {
            [, $mutasi] = $this->loadFromDatabase('');
        } else {
            MockDataService::initSession();
            $mutasi = MockDataService::getMutasi();
        }

        return view('stok/riwayat', [
            'title'     => 'Riwayat Mutasi Stok — Pojok UKM Tanjung Gading',
            'activeNav' => 'stok',
            'isOwner'   => $isOwner,
            'mutasi'    => $mutasi,
        ]);
    }

    public function store()
    {
        $nama    = trim((string) $this->request->getPost('nama'));
        $stok    = (float) $this->request->getPost('stok');
        $minimum = (float) $this->request->getPost('minimum');
        $satuan  = trim((string) ($this->request->getPost('satuan') ?? 'pcs'));

        if ($nama === '' || $stok <= 0) {
            return redirect()->back()->with('error', 'Nama bahan dan jumlah stok awal (> 0) wajib diisi.');
        }

        if (DbHelper::isReady()) {
            $user = session()->get('user');
            try {
                (new StokService())->tambahBahanBakuDenganStokAwal(
                    $nama,
                    $satuan,
                    $stok,
                    (int) ($user['id'] ?? 1)
                );
            } catch (Throwable $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            return redirect()->to('/stok')->with('success', 'Bahan baku "' . $nama . '" ditambahkan beserta pencatatan STOK_AWAL.');
        }

        MockDataService::initSession();
        $status = $stok <= 0 ? 'Kritis' : ($stok < $minimum ? 'Menipis' : 'Aman');

        $bahan   = MockDataService::getBahan();
        $bahan[] = [
            'id'      => count($bahan) + 1,
            'nama'    => $nama,
            'stok'    => $stok,
            'minimum' => $minimum,
            'satuan'  => $satuan,
            'status'  => $status,
        ];
        session()->set('mock_bahan', $bahan);

        $user   = session()->get('user');
        $mutasi = MockDataService::getMutasi();
        array_unshift($mutasi, [
            'waktu'      => date('d M · H.i') . ' WIB',
            'keterangan' => $nama . ' +' . $stok . ' ' . $satuan,
            'sumber'     => 'STOK_AWAL',
            'pelaku'     => $user['nama'] ?? 'Maya',
        ]);
        session()->set('mock_mutasi', $mutasi);

        MockDataService::addAudit($user['nama'] . ' / ' . $user['role'], 'Tambah Bahan Baku', $nama . ' (+' . $stok . ' ' . $satuan . ')');

        return redirect()->to('/stok')->with('success', 'Bahan baku "' . $nama . '" ditambahkan beserta pencatatan STOK_AWAL.');
    }

    public function koreksi(int $id)
    {
        $delta = (float) $this->request->getPost('delta');

        if (DbHelper::isReady()) {
            $user = session()->get('user');
            try {
                $hasil = (new StokService())->sesuaikanStok(
                    $id,
                    $delta,
                    (int) ($user['id'] ?? 1)
                );
            } catch (Throwable $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }

            return redirect()->to('/stok')->with('success', 'Stok "' . $hasil['nama'] . '" berhasil dikoreksi.');
        }

        MockDataService::initSession();
        $bahan     = MockDataService::getBahan();
        $namaBahan = '';
        $satuan    = '';
        foreach ($bahan as &$b) {
            if ($b['id'] === $id) {
                $b['stok']   = max(0, $b['stok'] + $delta);
                $b['status'] = $b['stok'] <= ($b['minimum'] * 0.4)
                    ? 'Kritis'
                    : ($b['stok'] < $b['minimum'] ? 'Menipis' : 'Aman');
                $namaBahan   = $b['nama'];
                $satuan      = $b['satuan'];
                break;
            }
        }
        unset($b);
        session()->set('mock_bahan', $bahan);

        $user   = session()->get('user');
        $sign   = $delta >= 0 ? '+' : '';
        $mutasi = MockDataService::getMutasi();
        array_unshift($mutasi, [
            'waktu'      => date('d M · H.i') . ' WIB',
            'keterangan' => $namaBahan . ' ' . $sign . $delta . ' ' . $satuan,
            'sumber'     => 'Koreksi / Pembelian',
            'pelaku'     => $user['nama'] ?? 'Maya',
        ]);
        session()->set('mock_mutasi', $mutasi);

        return redirect()->to('/stok')->with('success', 'Stok "' . $namaBahan . '" berhasil dikoreksi.');
    }

    private function loadFromDatabase(string $search): array
    {
        $bahanModel = new BahanBakuModel();
        if ($search !== '') {
            $bahanModel->where('nama ILIKE', '%' . $search . '%');
        }
        $bahanRows = $bahanModel->orderBy('bahan_baku_id', 'ASC')->findAll();

        $bahan = array_map(static function (array $b): array {
            $stok    = (float) $b['stok_terkini'];
            $minimum = $b['satuan'] === 'pcs' ? 100.0 : 500.0;
            $status  = $stok <= ($minimum * 0.4) ? 'Kritis' : ($stok < $minimum ? 'Menipis' : 'Aman');

            return [
                'id'      => (int) $b['bahan_baku_id'],
                'nama'    => $b['nama'],
                'stok'    => $stok,
                'minimum' => $minimum,
                'satuan'  => $b['satuan'],
                'status'  => $status,
            ];
        }, $bahanRows);

        $mutasiRows = (new RiwayatStokModel())->getMutasiTerbaru(25);
        $mutasi     = array_map(static function (array $m): array {
            $delta = (float) $m['jumlah_perubahan'];
            $sign  = $delta > 0 ? '+' : '';
            return [
                'waktu'      => date('d M · H.i', strtotime((string) $m['waktu_dibuat'])) . ' WIB',
                'keterangan' => ($m['nama_bahan'] ?? '-') . ' ' . $sign . $delta . ' ' . ($m['satuan'] ?? ''),
                'sumber'     => $m['jenis_perubahan'] . ($m['keterangan'] ? ' · ' . $m['keterangan'] : ''),
                'pelaku'     => $m['nama_pengguna'] ?? 'Sistem',
            ];
        }, $mutasiRows);

        return [$bahan, $mutasi];
    }
}
