<?php

namespace App\Controllers\Barista;

use App\Controllers\BaseController;
use App\Services\MockDataService;

class AntreanController extends BaseController
{
    public function index()
    {
        MockDataService::initSession();

        $orders = array_filter(MockDataService::getOrders(), static function ($o) {
            return $o['status_bayar'] === 'Lunas' && ! empty($o['dikirim_barista']);
        });

        $menunggu = array_values(array_filter($orders, static fn($o) => $o['status_bar'] === 'Menunggu'));
        $diproses = array_values(array_filter($orders, static fn($o) => $o['status_bar'] === 'Diproses'));

        return view('barista/antrean', [
            'title'    => 'Antrean Barista — Pojok UKM Tanjung Gading',
            'shift'    => MockDataService::getShift(),
            'menunggu' => $menunggu,
            'diproses' => $diproses,
        ]);
    }

    public function mulai(int $id)
    {
        MockDataService::initSession();
        $orders = MockDataService::getOrders();

        foreach ($orders as &$o) {
            if ($o['id'] === $id) {
                $o['status_bar'] = 'Diproses';
                $o['durasi']     = '00:01';
                break;
            }
        }
        unset($o);
        session()->set('mock_orders', $orders);

        return redirect()->to('/barista/antrean')->with('success', 'Pesanan #' . $id . ' mulai diracik.');
    }

    public function selesai(int $id)
    {
        MockDataService::initSession();
        $orders = MockDataService::getOrders();

        foreach ($orders as &$o) {
            if ($o['id'] === $id) {
                $o['status_bar'] = 'Selesai';
                break;
            }
        }
        unset($o);
        session()->set('mock_orders', $orders);

        return redirect()->to('/barista/antrean')->with('success', 'Pesanan #' . $id . ' selesai! Info telah dikirim ke Kasir.');
    }
}

