<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Services\MockDataService;

class LaporanController extends BaseController
{
    public function index()
    {
        MockDataService::initSession();

        $periode = (string) ($this->request->getGet('periode') ?? 'Harian');

        return view('owner/dashboard', [
            'title'     => 'Dashboard Pemasukan — Owner Pojok UKM',
            'activeNav' => 'dashboard',
            'periode'   => $periode,
        ]);
    }
}

