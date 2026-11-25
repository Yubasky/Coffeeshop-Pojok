<?php

namespace App\Controllers\Pelanggan;

use App\Controllers\BaseController;
use App\Services\MockDataService;

class PesananController extends BaseController
{
    public function checkout()
    {
        MockDataService::initSession();
        $cart = MockDataService::getCart();

        $totalQty = 0;
        $totalRp  = 0;
        foreach ($cart as $item) {
            $totalQty += $item['qty'];
            $totalRp  += $item['qty'] * $item['harga'];
        }

        return view('pelanggan/checkout', [
            'title'       => 'Checkout — Pojok UKM Tanjung Gading',
            'topbarRight' => 'Checkout',
            'activeTab'   => 'pesanan',
            'cart'        => $cart,
            'totalQty'    => $totalQty,
            'totalRp'     => $totalRp,
        ]);
    }

    public function store()
    {
        MockDataService::initSession();
        $cart = MockDataService::getCart();

        if (empty($cart)) {
            return redirect()->to('/pelanggan/katalog')->with('error', 'Keranjang Anda masih kosong.');
        }

        $layanan = (string) ($this->request->getPost('layanan') ?? 'Minum di Tempat');
        if (! in_array($layanan, ['Minum di Tempat', 'Take Away'], true)) {
            $layanan = 'Minum di Tempat';
        }

        $metode  = (string) ($this->request->getPost('metode') ?? 'QRIS');
        $catatan = trim((string) $this->request->getPost('catatan'));

        $totalRp = 0;
        $items   = [];
        foreach ($cart as $c) {
            $totalRp += $c['qty'] * $c['harga'];
            $items[]  = ['nama' => $c['nama'], 'qty' => $c['qty'], 'harga' => $c['harga']];
        }

        $orders = MockDataService::getOrders();
        $nextId = count($orders) + 1;
        $kode   = 'ORD-20261119-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
        $user   = session()->get('user');

        $newOrder = [
            'id'              => $nextId,
            'kode'            => $kode,
            'nomor_pendek'    => '#' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT),
            'pelanggan'       => $user['nama'] ?? 'Pelanggan',
            'kanal'           => 'Online',
            'layanan'         => $layanan,
            'status_bayar'    => 'Belum Dibayar',
            'metode'          => $metode,
            'status_bar'      => 'Menunggu',
            'dikirim_barista' => false,
            'waktu'           => date('H.i') . ' WIB',
            'durasi'          => '00:00',
            'total'           => $totalRp,
            'catatan'         => $catatan,
            'items'           => $items,
        ];

        array_unshift($orders, $newOrder);
        session()->set('mock_orders', $orders);
        session()->set('mock_cart', []);

        return redirect()->to('/pelanggan/status/' . $kode)
            ->with('success', 'Pesanan ' . $kode . ' berhasil dibuat. Silakan selesaikan pembayaran.');
    }

    public function status(?string $kodePesanan = null)
    {
        MockDataService::initSession();
        $orders = MockDataService::getOrders();

        $order = $orders[0] ?? null;
        if ($kodePesanan !== null) {
            foreach ($orders as $o) {
                if ($o['kode'] === $kodePesanan) {
                    $order = $o;
                    break;
                }
            }
        }

        return view('pelanggan/status', [
            'title'       => 'Status Pesanan — Pojok UKM Tanjung Gading',
            'topbarRight' => 'Detail pesanan',
            'activeTab'   => 'pesanan',
            'order'       => $order,
        ]);
    }
}

