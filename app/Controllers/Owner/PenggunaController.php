<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Services\MockDataService;

class PenggunaController extends BaseController
{
    public function index()
    {
        MockDataService::initSession();

        $search = trim((string) $this->request->getGet('q'));
        $role   = (string) ($this->request->getGet('role') ?? 'Semua');
        $tab    = (string) ($this->request->getGet('tab') ?? 'staf');

        $staff = array_filter(MockDataService::getStaff(), static function ($s) use ($search, $role) {
            if ($role !== 'Semua' && strcasecmp($s['role'], $role) !== 0) {
                return false;
            }
            if ($search !== '' && stripos($s['nama'] . ' ' . $s['username'], $search) === false) {
                return false;
            }
            return true;
        });

        return view('owner/pengguna', [
            'title'        => 'Staf & Riwayat — Owner Pojok UKM',
            'activeNav'    => 'pengguna',
            'staff'        => array_values($staff),
            'riwayatShift' => MockDataService::getRiwayatShift(),
            'audit'        => MockDataService::getAudit(),
            'search'       => $search,
            'role'         => $role,
            'tab'          => $tab,
        ]);
    }

    public function store()
    {
        MockDataService::initSession();

        $nama     = trim((string) $this->request->getPost('nama'));
        $username = trim((string) $this->request->getPost('username'));
        $role     = (string) ($this->request->getPost('role') ?? 'Kasir');

        if ($nama === '' || $username === '') {
            return redirect()->back()->with('error', 'Nama dan username staf wajib diisi.');
        }

        $staff = MockDataService::getStaff();
        $staff[] = [
            'id'       => count($staff) + 1,
            'nama'     => $nama,
            'username' => strtolower($username),
            'role'     => $role,
            'shift'    => 'Shift Pagi',
            'aktif'    => true,
        ];
        session()->set('mock_staff', $staff);

        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / Owner', 'Tambah Akun Staf', $username . ' / Role ' . $role);

        return redirect()->to('/owner/pengguna')->with('success', 'Akun staf "' . $nama . '" berhasil dibuat.');
    }

    public function toggleStatus(int $id)
    {
        MockDataService::initSession();
        $staff = MockDataService::getStaff();
        $uname = '';
        $statusText = '';

        foreach ($staff as &$s) {
            if ($s['id'] === $id) {
                $s['aktif'] = ! $s['aktif'];
                $uname = $s['username'];
                $statusText = $s['aktif'] ? 'Aktif' : 'Nonaktif';
                break;
            }
        }
        unset($s);

        session()->set('mock_staff', $staff);
        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / Owner', 'Ubah Status Staf', $uname . ' → ' . $statusText);

        return redirect()->to('/owner/pengguna')->with('success', 'Status akun ' . $uname . ' diubah menjadi ' . $statusText . '.');
    }

    public function resetPassword(int $id)
    {
        MockDataService::initSession();
        $uname = 'staf#' . $id;
        foreach (MockDataService::getStaff() as $s) {
            if ($s['id'] === $id) {
                $uname = $s['username'];
                break;
            }
        }

        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / Owner', 'Reset Password Staf', $uname);

        return redirect()->to('/owner/pengguna')->with('success', 'Password untuk ' . $uname . ' berhasil direset ke default.');
    }
}

