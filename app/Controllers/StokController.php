<?php

namespace App\Controllers;

use App\Services\MockDataService;

class StokController extends BaseController
{
    public function index()
    {
        MockDataService::initSession();

        $search = trim((string) $this->request->getGet('q'));
        $tab    = (string) ($this->request->getGet('tab') ?? 'daftar');

        $bahan = array_filter(MockDataService::getBahan(), static function ($b) use ($search) {
            if ($search !== '' && stripos($b['nama'], $search) === false) {
                return false;
            }
            return true;
        });

        $user = session()->get('user');
        $isOwner = ($user['role'] ?? '') === 'OWNER';

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

    public function store()
    {
        MockDataService::initSession();

        $nama    = trim((string) $this->request->getPost('nama'));
        $stok    = (float) $this->request->getPost('stok');
        $minimum = (float) $this->request->getPost('minimum');
        $satuan  = trim((string) ($this->request->getPost('satuan') ?? 'pcs'));

        if ($nama === '' || $stok < 0) {
            return redirect()->back()->with('error', 'Nama bahan dan jumlah stok awal wajib diisi.');
        }

        $status = $stok <= 0 ? 'Kritis' : ($stok < $minimum ? 'Menipis' : 'Aman');

        $bahan = MockDataService::getBahan();
        $bahan[] = [
            'id'      => count($bahan) + 1,
            'nama'    => $nama,
            'stok'    => $stok,
            'minimum' => $minimum,
            'satuan'  => $satuan,
            'status'  => $status,
        ];
        session()->set('mock_bahan', $bahan);

        $user = session()->get('user');
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
        MockDataService::initSession();
        $delta = (float) $this->request->getPost('delta');

        $bahan = MockDataService::getBahan();
        $namaBahan = '';
        $satuan = '';
        foreach ($bahan as &$b) {
            if ($b['id'] === $id) {
                $b['stok'] = max(0, $b['stok'] + $delta);
                $b['status'] = $b['stok'] <= ($b['minimum'] * 0.4)
                    ? 'Kritis'
                    : ($b['stok'] < $b['minimum'] ? 'Menipis' : 'Aman');
                $namaBahan = $b['nama'];
                $satuan    = $b['satuan'];
                break;
            }
        }
        unset($b);
        session()->set('mock_bahan', $bahan);

        $user = session()->get('user');
        $sign = $delta >= 0 ? '+' : '';
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
}

