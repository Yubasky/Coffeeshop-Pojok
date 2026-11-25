<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\LogAktivitasModel;
use App\Models\MenuModel;
use App\Services\DbHelper;
use App\Services\MockDataService;

class MenuController extends BaseController
{
    public function index()
    {
        $search   = trim((string) $this->request->getGet('q'));
        $kategori = strtoupper((string) ($this->request->getGet('kategori') ?? 'SEMUA'));

        if (DbHelper::isReady()) {
            $menuModel     = new MenuModel();
            $rows          = $menuModel->filterMenus(null, $kategori, $search);
            $totalAllMenus = $menuModel->countAllResults();

            $menus = array_map(static function (array $m): array {
                return [
                    'id'       => (int) $m['menu_id'],
                    'nama'     => $m['nama'],
                    'kategori' => $m['kategori'],
                    'harga'    => (int) $m['harga'],
                    'aktif'    => filter_var($m['aktif'], FILTER_VALIDATE_BOOLEAN),
                ];
            }, $rows);

            return view('owner/menu', [
                'title'         => 'Katalog Menu — Owner Pojok UKM',
                'activeNav'     => 'menu',
                'menus'         => $menus,
                'totalAllMenus' => $totalAllMenus,
                'search'        => $search,
                'kategori'      => $kategori,
            ]);
        }

        MockDataService::initSession();
        $allMenus = MockDataService::getMenus();

        $menus = array_filter($allMenus, static function ($m) use ($search, $kategori) {
            if ($kategori !== 'SEMUA' && $m['kategori'] !== $kategori) {
                return false;
            }
            if ($search !== '' && stripos($m['nama'], $search) === false) {
                return false;
            }
            return true;
        });

        return view('owner/menu', [
            'title'         => 'Katalog Menu — Owner Pojok UKM',
            'activeNav'     => 'menu',
            'menus'         => array_values($menus),
            'totalAllMenus' => count($allMenus),
            'search'        => $search,
            'kategori'      => $kategori,
        ]);
    }

    public function store()
    {
        $nama     = trim((string) $this->request->getPost('nama'));
        $kategori = strtoupper((string) ($this->request->getPost('kategori') ?? 'KOPI'));
        $harga    = (int) $this->request->getPost('harga');
        $katValid = $kategori === 'NON_KOPI' ? 'NON_KOPI' : 'KOPI';

        if ($nama === '' || $harga <= 0) {
            return redirect()->back()->with('error', 'Nama menu dan harga valid wajib diisi.');
        }

        if (DbHelper::isReady()) {
            $menuModel = new MenuModel();
            $existing  = $menuModel->where('LOWER(nama) =', strtolower($nama))->first();
            if ($existing) {
                return redirect()->back()->with('error', 'Menu "' . $nama . '" sudah terdaftar.');
            }

            $now    = date('Y-m-d H:i:sP');
            $menuId = (int) $menuModel->insert([
                'nama'             => $nama,
                'kategori'         => $katValid,
                'harga'            => $harga,
                'aktif'            => 'true',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ], true);

            $user = session()->get('user');
            (new LogAktivitasModel())->catat(
                isset($user['id']) ? (int) $user['id'] : null,
                'TAMBAH_MENU',
                'menu',
                $menuId,
                ['nama' => $nama, 'kategori' => $katValid, 'harga' => $harga],
                'Menambah menu baru "' . $nama . '" (Rp ' . number_format($harga, 0, ',', '.') . ')'
            );

            return redirect()->to('/owner/menu')->with('success', 'Menu "' . $nama . '" berhasil ditambahkan.');
        }

        MockDataService::initSession();
        $menus = MockDataService::getMenus();
        $newId = count($menus) + 1;

        array_unshift($menus, [
            'id'       => $newId,
            'nama'     => $nama,
            'kategori' => $katValid,
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
        $harga = (int) $this->request->getPost('harga');

        if ($harga <= 0) {
            return redirect()->back()->with('error', 'Harga harus lebih dari 0.');
        }

        if (DbHelper::isReady()) {
            $menuModel = new MenuModel();
            $menu      = $menuModel->find($id);
            if (! $menu) {
                return redirect()->back()->with('error', 'Menu tidak ditemukan.');
            }

            $hargaLama = (int) $menu['harga'];
            $menuModel->update($id, [
                'harga'            => $harga,
                'waktu_diperbarui' => date('Y-m-d H:i:sP'),
            ]);

            $user = session()->get('user');
            (new LogAktivitasModel())->catat(
                isset($user['id']) ? (int) $user['id'] : null,
                'UBAH_HARGA_MENU',
                'menu',
                $id,
                ['nama' => $menu['nama'], 'harga_lama' => $hargaLama, 'harga_baru' => $harga],
                'Ubah harga ' . $menu['nama'] . ' dari Rp ' . number_format($hargaLama, 0, ',', '.') . ' ke Rp ' . number_format($harga, 0, ',', '.')
            );

            return redirect()->to('/owner/menu')->with('success', 'Harga menu "' . $menu['nama'] . '" diperbarui dan dicatat ke Audit Log.');
        }

        MockDataService::initSession();
        $menus    = MockDataService::getMenus();
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
        if (DbHelper::isReady()) {
            $menuModel = new MenuModel();
            $menu      = $menuModel->find($id);
            if (! $menu) {
                return redirect()->back()->with('error', 'Menu tidak ditemukan.');
            }

            $statusBaru = ! filter_var($menu['aktif'], FILTER_VALIDATE_BOOLEAN);
            $statusText = $statusBaru ? 'Aktif' : 'Nonaktif';

            $menuModel->update($id, [
                'aktif'            => $statusBaru ? 'true' : 'false',
                'waktu_diperbarui' => date('Y-m-d H:i:sP'),
            ]);

            $user = session()->get('user');
            (new LogAktivitasModel())->catat(
                isset($user['id']) ? (int) $user['id'] : null,
                'UBAH_STATUS_MENU',
                'menu',
                $id,
                ['nama' => $menu['nama'], 'aktif' => $statusBaru],
                'Status menu ' . $menu['nama'] . ' diubah menjadi ' . $statusText
            );

            return redirect()->to('/owner/menu')->with('success', 'Status "' . $menu['nama'] . '" diubah menjadi ' . $statusText . '.');
        }

        MockDataService::initSession();
        $menus      = MockDataService::getMenus();
        $namaMenu   = '';
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
