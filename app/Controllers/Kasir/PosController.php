<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Services\MockDataService;

class PosController extends BaseController
{
    public function index()
    {
        MockDataService::initSession();

        $kanalFilter  = (string) ($this->request->getGet('kanal') ?? 'Semua');
        $statusFilter = (string) ($this->request->getGet('status') ?? 'Semua');
        $search       = trim((string) $this->request->getGet('q'));

        $allOrders = MockDataService::getOrders();
        $onlineCount  = count(array_filter($allOrders, static fn($o) => $o['kanal'] === 'Online'));
        $offlineCount = count(array_filter($allOrders, static fn($o) => $o['kanal'] === 'Offline'));

        $orders = array_filter($allOrders, static function ($o) use ($kanalFilter, $statusFilter, $search) {
            if ($kanalFilter !== 'Semua' && $o['kanal'] !== $kanalFilter) {
                return false;
            }
            if ($statusFilter !== 'Semua' && $o['status_bayar'] !== $statusFilter) {
                return false;
            }
            if ($search !== '') {
                $haystack = $o['nomor_pendek'] . ' ' . $o['kode'] . ' ' . $o['pelanggan'];
                if (stripos($haystack, $search) === false) {
                    return false;
                }
            }
            return true;
        });

        $menus = array_filter(MockDataService::getMenus(), static fn($m) => $m['aktif']);

        return view('kasir/pos', [
            'title'        => 'POS & Antrean Kasir — Pojok UKM Tanjung Gading',
            'shift'        => MockDataService::getShift(),
            'orders'       => array_values($orders),
            'totalOrders'  => count($allOrders),
            'onlineCount'  => $onlineCount,
            'offlineCount' => $offlineCount,
            'kanalFilter'  => $kanalFilter,
            'statusFilter' => $statusFilter,
            'search'       => $search,
            'menus'        => array_slice(array_values($menus), 0, 8),
        ]);
    }

    public function storeOffline()
    {
        MockDataService::initSession();

        $pelanggan = trim((string) $this->request->getPost('pelanggan'));
        if ($pelanggan === '') {
            $pelanggan = 'Pelanggan langsung';
        }
        $layanan = (string) ($this->request->getPost('layanan') ?? 'Minum di Tempat');
        $catatan = trim((string) $this->request->getPost('catatan'));
        $itemsJson = (string) $this->request->getPost('items_json');
        $items = json_decode($itemsJson, true);

        if (! is_array($items) || empty($items)) {
            return redirect()->back()->with('error', 'Pilih minimal 1 minuman untuk membuat pesanan offline.');
        }

        $total = 0;
        foreach ($items as $it) {
            $total += ((int) $it['qty']) * ((int) $it['harga']);
        }

        $orders = MockDataService::getOrders();
        $nextId = count($orders) + 1;
        $kode   = 'ORD-20261119-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);

        $newOrder = [
            'id'              => $nextId,
            'kode'            => $kode,
            'nomor_pendek'    => '#' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT),
            'pelanggan'       => $pelanggan,
            'kanal'           => 'Offline',
            'layanan'         => $layanan,
            'status_bayar'    => 'Belum Dibayar',
            'metode'          => 'Tunai',
            'status_bar'      => 'Menunggu',
            'dikirim_barista' => false,
            'waktu'           => date('H.i') . ' WIB',
            'durasi'          => '00:00',
            'total'           => $total,
            'catatan'         => $catatan,
            'items'           => $items,
        ];

        array_unshift($orders, $newOrder);
        session()->set('mock_orders', $orders);

        return redirect()->to('/kasir/pembayaran/' . $nextId)
            ->with('success', 'Pesanan offline ' . $newOrder['nomor_pendek'] . ' dibuat. Silakan lanjutkan pembayaran.');
    }
}

