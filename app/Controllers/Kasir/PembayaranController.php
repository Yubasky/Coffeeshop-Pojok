<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Services\MockDataService;

class PembayaranController extends BaseController
{
    public function detail(int $id)
    {
        MockDataService::initSession();
        $order = $this->findOrder($id);

        if (! $order) {
            return redirect()->to('/kasir/pos')->with('error', 'Pesanan tidak ditemukan.');
        }

        return view('kasir/pembayaran', [
            'title' => 'Bayar & Koordinasi — Pojok UKM Tanjung Gading',
            'shift' => MockDataService::getShift(),
            'order' => $order,
        ]);
    }

    public function bayar(int $id)
    {
        MockDataService::initSession();
        $orders = MockDataService::getOrders();
        $metode = (string) ($this->request->getPost('metode') ?? 'QRIS');

        foreach ($orders as &$o) {
            if ($o['id'] === $id) {
                $o['status_bayar'] = 'Lunas';
                $o['metode']       = $metode;
                break;
            }
        }
        unset($o);

        session()->set('mock_orders', $orders);

        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / KASIR', 'Verifikasi Pembayaran (' . $metode . ')', 'Order #' . $id);

        return redirect()->to('/kasir/pembayaran/' . $id)
            ->with('success', 'Pembayaran berhasil dikonfirmasi (Lunas). Stok bahan otomatis dipotong.');
    }

    public function kirimBarista(int $id)
    {
        MockDataService::initSession();
        $orders = MockDataService::getOrders();

        foreach ($orders as &$o) {
            if ($o['id'] === $id) {
                if ($o['status_bayar'] !== 'Lunas') {
                    return redirect()->back()->with('error', 'Pesanan harus Lunas sebelum dikirim ke Barista.');
                }
                $o['dikirim_barista'] = true;
                break;
            }
        }
        unset($o);

        session()->set('mock_orders', $orders);

        return redirect()->to('/kasir/pembayaran/' . $id)
            ->with('success', 'Info pesanan telah dikirim ke Antrean Barista.');
    }

    public function updatePelanggan(int $id)
    {
        $status = (string) $this->request->getPost('status_bar');
        $orders = MockDataService::getOrders();

        foreach ($orders as &$o) {
            if ($o['id'] === $id) {
                $o['status_bar'] = $status;
                break;
            }
        }
        unset($o);
        session()->set('mock_orders', $orders);

        return redirect()->to('/kasir/pembayaran/' . $id)
            ->with('success', 'Status pelanggan diperbarui menjadi: ' . $status . '.');
    }

    public function batal(int $id)
    {
        $alasan = trim((string) $this->request->getPost('alasan'));
        if ($alasan === '') {
            return redirect()->back()->with('error', 'Alasan pembatalan wajib diisi.');
        }

        $orders = array_values(array_filter(MockDataService::getOrders(), static fn($o) => $o['id'] !== $id));
        session()->set('mock_orders', $orders);

        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / KASIR', 'Batalkan Pesanan', 'Order #' . $id . ' (' . $alasan . ')');

        return redirect()->to('/kasir/pos')
            ->with('success', 'Pesanan #' . $id . ' dibatalkan dan dicatat ke Audit Log.');
    }

    private function findOrder(int $id): ?array
    {
        foreach (MockDataService::getOrders() as $o) {
            if ($o['id'] === $id) {
                return $o;
            }
        }
        return null;
    }
}

