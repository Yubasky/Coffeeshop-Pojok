<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Services\MockDataService;

class MenuController extends BaseController
{
    public function index()
    {
        MockDataService::initSession();

        $search   = trim((string) $this->request->getGet('q'));
        $kategori = strtoupper((string) ($this->request->getGet('kategori') ?? 'SEMUA'));

        $menus = array_filter(MockDataService::getMenus(), static function ($m) use ($search, $kategori) {
            if ($kategori !== 'SEMUA' && $m['kategori'] !== $kategori) {
                return false;
            }
            if ($search !== '' && stripos($m['nama'], $search) === false) {
                return false;
            }
            return true;
        });

        return view('owner/menu', [
            'title'     => 'Katalog Menu — Owner Pojok UKM',
            'activeNav' => 'menu',
            'menus'     => array_values($menus),
            'search'    => $search,
            'kategori'  => $kategori,
        ]);
    }

    public function store()
    {
        MockDataService::initSession();

        $nama     = trim((string) $this->request->getPost('nama'));
        $kategori = strtoupper((string) ($this->request->getPost('kategori') ?? 'KOPI'));
        $harga    = (int) $this->request->getPost('harga');

        if ($nama === '' || $harga <= 0) {
            return redirect()->back()->with('error', 'Nama menu dan harga valid wajib diisi.');
        }

        $menus = MockDataService::getMenus();
        $newId = count($menus) + 1;

        array_unshift($menus, [
            'id'       => $newId,
            'nama'     => $nama,
            'kategori' => $kategori === 'NON_KOPI' ? 'NON_KOPI' : 'KOPI',
            'harga'    => $harga,
            'aktif'    => true,
        ]);

        session()->set('mock_menus', $menus);
        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / Owner', 'Tambah Menu Baru', $nama . ' (Rp ' . number_format($harga, 0, ',', '.') . ')');

        return redirect()->to('/owner/menu')->with('success', 'Menu "' . $nama . '" berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        MockDataService::initSession();
        $harga = (int) $this->request->getPost('harga');

        if ($harga <= 0) {
            return redirect()->back()->with('error', 'Harga harus lebih dari 0.');
        }

        $menus = MockDataService::getMenus();
        $namaMenu = '';
        foreach ($menus as &$m) {
            if ($m['id'] === $id) {
                $namaMenu   = $m['nama'];
                $m['harga'] = $harga;
                break;
            }
        }
        unset($m);

        session()->set('mock_menus', $menus);
        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / Owner', 'Ubah Harga Menu', $namaMenu . ' → Rp ' . number_format($harga, 0, ',', '.'));

        return redirect()->to('/owner/menu')->with('success', 'Harga menu "' . $namaMenu . '" diperbarui dan dicatat ke Audit Log.');
    }

    public function toggleStatus(int $id)
    {
        MockDataService::initSession();
        $menus = MockDataService::getMenus();
        $namaMenu = '';
        $statusText = '';

        foreach ($menus as &$m) {
            if ($m['id'] === $id) {
                $m['aktif'] = ! $m['aktif'];
                $namaMenu   = $m['nama'];
                $statusText = $m['aktif'] ? 'Aktif' : 'Nonaktif';
                break;
            }
        }
        unset($m);

        session()->set('mock_menus', $menus);
        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / Owner', 'Ubah Status Menu', $namaMenu . ' → ' . $statusText);

        return redirect()->to('/owner/menu')->with('success', 'Status "' . $namaMenu . '" diubah menjadi ' . $statusText . '.');
    }
}

