<?php

namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Services\DbHelper;
use App\Services\MockDataService;

class KatalogController extends BaseController
{
    public function index()
    {
        $search   = trim((string) $this->request->getGet('q'));
        $kategori = strtoupper((string) ($this->request->getGet('kategori') ?? 'SEMUA'));

        if (DbHelper::isReady()) {
            $rows  = (new MenuModel())->filterMenus(true, $kategori, $search);
            $menus = array_map(static function (array $m): array {
                return [
                    'id'       => (int) $m['menu_id'],
                    'nama'     => $m['nama'],
                    'kategori' => $m['kategori'],
                    'harga'    => (int) $m['harga'],
                    'aktif'    => filter_var($m['aktif'], FILTER_VALIDATE_BOOLEAN),
                ];
            }, $rows);
        } else {
            MockDataService::initSession();
            $menus = array_values(array_filter(MockDataService::getMenus(), static function ($m) use ($search, $kategori) {
                if (! $m['aktif']) {
                    return false;
                }
                if ($kategori !== 'SEMUA' && $m['kategori'] !== $kategori) {
                    return false;
                }
                if ($search !== '' && stripos($m['nama'], $search) === false) {
                    return false;
                }
                return true;
            }));
        }

        $cart     = session()->get('mock_cart') ?? [];
        $totalQty = 0;
        $totalRp  = 0;
        foreach ($cart as $item) {
            $totalQty += $item['qty'];
            $totalRp  += $item['qty'] * $item['harga'];
        }

        return view('pelanggan/katalog', [
            'title'       => 'Katalog Menu — Pojok UKM Tanjung Gading',
            'topbarRight' => 'Tanjung Gading',
            'activeTab'   => 'beranda',
            'menus'       => $menus,
            'search'      => $search,
            'kategori'    => $kategori,
            'cart'        => $cart,
            'totalQty'    => $totalQty,
            'totalRp'     => $totalRp,
        ]);
    }

    public function addToCart()
    {
        $menuId    = (int) $this->request->getPost('menu_id');
        $foundMenu = null;

        if (DbHelper::isReady()) {
            $row = (new MenuModel())->find($menuId);
            if ($row && filter_var($row['aktif'], FILTER_VALIDATE_BOOLEAN)) {
                $foundMenu = [
                    'id'    => (int) $row['menu_id'],
                    'nama'  => $row['nama'],
                    'harga' => (int) $row['harga'],
                ];
            }
        } else {
            MockDataService::initSession();
            foreach (MockDataService::getMenus() as $m) {
                if ($m['id'] === $menuId && $m['aktif']) {
                    $foundMenu = $m;
                    break;
                }
            }
        }

        if (! $foundMenu) {
            return redirect()->back()->with('error', 'Menu tidak ditemukan atau sedang nonaktif.');
        }

        $cart    = session()->get('mock_cart') ?? [];
        $updated = false;
        foreach ($cart as &$item) {
            if ($item['menu_id'] === $menuId) {
                $item['qty']++;
                $updated = true;
                break;
            }
        }
        unset($item);

        if (! $updated) {
            $cart[] = [
                'menu_id' => $foundMenu['id'],
                'nama'    => $foundMenu['nama'],
                'harga'   => $foundMenu['harga'],
                'qty'     => 1,
                'catatan' => '',
            ];
        }

        session()->set('mock_cart', $cart);

        return redirect()->back()->with('success', $foundMenu['nama'] . ' ditambahkan ke keranjang.');
    }

    public function updateCart()
    {
        if (! DbHelper::isReady()) {
            MockDataService::initSession();
        }

        $menuId = (int) $this->request->getPost('menu_id');
        $delta  = (int) $this->request->getPost('delta');

        $cart    = session()->get('mock_cart') ?? [];
        $newCart = [];
        foreach ($cart as $item) {
            if ($item['menu_id'] === $menuId) {
                $item['qty'] += $delta;
            }
            if ($item['qty'] > 0) {
                $newCart[] = $item;
            }
        }

        session()->set('mock_cart', $newCart);
        return redirect()->to('/pelanggan/checkout');
    }
}
