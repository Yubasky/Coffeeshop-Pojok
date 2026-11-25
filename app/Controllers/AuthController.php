<?php

namespace App\Controllers;

use App\Services\MockDataService;

class AuthController extends BaseController
{
    public function login()
    {
        MockDataService::initSession();

        if (session()->has('user')) {
            return $this->redirectByRole(session()->get('user')['role']);
        }

        return view('auth/login', [
            'title'       => 'Masuk — Pojok UKM Tanjung Gading',
            'topbarRight' => 'Akun pelanggan',
            'activeTab'   => 'akun',
            'mode'        => 'login',
        ]);
    }

    public function register()
    {
        MockDataService::initSession();

        return view('auth/login', [
            'title'       => 'Daftar Pelanggan — Pojok UKM Tanjung Gading',
            'topbarRight' => 'Pelanggan baru',
            'activeTab'   => 'akun',
            'mode'        => 'register',
        ]);
    }

    public function attemptLogin()
    {
        MockDataService::initSession();

        $roleInput = strtolower((string) $this->request->getPost('role'));
        $username  = trim((string) $this->request->getPost('username'));

        $users = MockDataService::getUsers();
        $selectedUser = $users[$roleInput] ?? $users['pelanggan'];

        if ($username !== '') {
            // Gunakan username yang diketik jika cocok, atau tetap gunakan nama demo
            foreach ($users as $u) {
                if (strcasecmp($u['username'], $username) === 0) {
                    $selectedUser = $u;
                    break;
                }
            }
        }

        session()->set('user', $selectedUser);
        session()->set('last_activity', time());

        MockDataService::addAudit(
            $selectedUser['nama'] . ' / ' . $selectedUser['role'],
            'Login Berhasil',
            $selectedUser['username']
        );

        return $this->redirectByRole($selectedUser['role'])
            ->with('success', 'Selamat datang, ' . $selectedUser['nama'] . ' (' . $selectedUser['role'] . ').');
    }

    public function attemptRegister()
    {
        MockDataService::initSession();

        $nama     = trim((string) $this->request->getPost('nama_lengkap'));
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if ($nama === '' || $username === '' || strlen($password) < 8) {
            return redirect()->back()->withInput()
                ->with('error', 'Nama lengkap, username, dan password (minimal 8 karakter) wajib diisi.');
        }

        $newUser = [
            'id'       => rand(100, 999),
            'nama'     => $nama,
            'username' => strtolower($username),
            'role'     => 'PELANGGAN',
        ];

        session()->set('user', $newUser);
        session()->set('last_activity', time());

        MockDataService::addAudit(
            $newUser['nama'] . ' / PELANGGAN',
            'Registrasi Pelanggan Baru',
            $newUser['username']
        );

        return redirect()->to('/pelanggan/katalog')
            ->with('success', 'Pendaftaran berhasil! Selamat datang, ' . $newUser['nama'] . '.');
    }

    public function logout()
    {
        $user = session()->get('user');
        if ($user) {
            MockDataService::addAudit(
                $user['nama'] . ' / ' . $user['role'],
                'Logout',
                $user['username']
            );
        }

        session()->remove('user');
        session()->remove('last_activity');

        return redirect()->to('/login')->with('success', 'Anda telah keluar dari akun.');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'OWNER'     => redirect()->to('/owner/dashboard'),
            'KASIR'     => redirect()->to('/kasir/pos'),
            'BARISTA'   => redirect()->to('/barista/antrean'),
            default     => redirect()->to('/pelanggan/katalog'),
        };
    }
}

