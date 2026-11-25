<?php

namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Services\MockDataService;

class KatalogController extends BaseController
{
    public function index()
    {
        MockDataService::initSession();

        $search   = trim((string) $this->request->getGet('q'));
        $kategori = strtoupper((string) ($this->request->getGet('kategori') ?? 'SEMUA'));

        $menus = array_filter(MockDataService::getMenus(), static function ($m) use ($search, $kategori) {
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
        });

        $cart = MockDataService::getCart();
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
            'menus'       => array_values($menus),
            'search'      => $search,
            'kategori'    => $kategori,
            'cart'        => $cart,
            'totalQty'    => $totalQty,
            'totalRp'     => $totalRp,
        ]);
    }

    public function addToCart()
    {
        MockDataService::initSession();
        $menuId = (int) $this->request->getPost('menu_id');
        $menus  = MockDataService::getMenus();

        $foundMenu = null;
        foreach ($menus as $m) {
            if ($m['id'] === $menuId && $m['aktif']) {
                $foundMenu = $m;
                break;
            }
        }

        if (! $foundMenu) {
            return redirect()->back()->with('error', 'Menu tidak ditemukan atau sedang nonaktif.');
        }

        $cart = MockDataService::getCart();
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
        MockDataService::initSession();
        $menuId = (int) $this->request->getPost('menu_id');
        $delta  = (int) $this->request->getPost('delta');

        $cart = MockDataService::getCart();
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

