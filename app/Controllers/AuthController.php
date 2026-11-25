<?php

namespace App\Controllers;

use App\Models\LogAktivitasModel;
use App\Models\PenggunaModel;
use App\Services\DbHelper;
use App\Services\MockDataService;

class AuthController extends BaseController
{
    public function login()
    {
        if (! DbHelper::isReady()) {
            MockDataService::initSession();
        }

        if (session()->has('user')) {
            return $this->redirectByRole(session()->get('user')['role']);
        }

        return view('auth/login', [
            'title'       => 'Masuk — Pojok UKM Tanjung Gading',
            'topbarRight' => 'Akun pelanggan',
            'activeTab'   => 'akun',
            'mode'        => 'login',
            'usingDb'     => DbHelper::isReady(),
        ]);
    }

    public function register()
    {
        if (! DbHelper::isReady()) {
            MockDataService::initSession();
        }

        return view('auth/register', [
            'title'       => 'Daftar Pelanggan — Pojok UKM Tanjung Gading',
            'topbarRight' => 'Pelanggan baru',
            'activeTab'   => 'akun',
            'mode'        => 'register',
            'usingDb'     => DbHelper::isReady(),
        ]);
    }

    public function attemptLogin()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if (DbHelper::isReady()) {
            if ($username === '' || $password === '') {
                return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
            }

            $penggunaModel = new PenggunaModel();
            $logModel      = new LogAktivitasModel();
            $userRow       = $penggunaModel->findByUsernameInsensitive($username);

            if (! $userRow || ! password_verify($password, (string) $userRow['password_hash'])) {
                $logModel->catat(
                    $userRow ? (int) $userRow['pengguna_id'] : null,
                    'LOGIN_GAGAL',
                    'pengguna',
                    $userRow ? (int) $userRow['pengguna_id'] : null,
                    ['username_dicoba' => $username, 'ip' => $this->request->getIPAddress()],
                    'Percobaan login gagal untuk username: ' . $username
                );

                return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
            }

            $sessionUser = [
                'id'       => (int) $userRow['pengguna_id'],
                'nama'     => $userRow['nama'],
                'username' => $userRow['username'],
                'role'     => $userRow['peran'],
            ];

            session()->set('user', $sessionUser);
            session()->set('last_activity', time());

            $logModel->catat(
                $sessionUser['id'],
                'LOGIN_BERHASIL',
                'pengguna',
                $sessionUser['id'],
                ['ip' => $this->request->getIPAddress(), 'peran' => $sessionUser['role']],
                'Login berhasil sebagai ' . $sessionUser['role']
            );

            return $this->redirectByRole($sessionUser['role'])
                ->with('success', 'Selamat datang, ' . $sessionUser['nama'] . ' (' . $sessionUser['role'] . ').');
        }

        // Fallback Mode Mock Session (jika DB PostgreSQL belum dikonfigurasi)
        MockDataService::initSession();
        $roleInput    = strtolower((string) $this->request->getPost('role'));
        $users        = MockDataService::getUsers();
        $selectedUser = $users[$roleInput] ?? $users['pelanggan'];

        if ($username !== '') {
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
        $nama     = trim((string) $this->request->getPost('nama_lengkap'));
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if ($nama === '' || $username === '' || strlen($password) < 8) {
            return redirect()->back()->withInput()
                ->with('error', 'Nama lengkap, username, dan password (minimal 8 karakter) wajib diisi.');
        }

        if (DbHelper::isReady()) {
            $penggunaModel = new PenggunaModel();
            $logModel      = new LogAktivitasModel();

            if ($penggunaModel->findByUsernameInsensitive($username) !== null) {
                return redirect()->back()->withInput()
                    ->with('error', 'Username "' . $username . '" sudah digunakan. Silakan pilih username lain.');
            }

            $now        = date('Y-m-d H:i:sP');
            $penggunaId = (int) $penggunaModel->insert([
                'nama'             => $nama,
                'username'         => strtolower($username),
                'password_hash'    => password_hash($password, PASSWORD_BCRYPT),
                'peran'            => 'PELANGGAN',
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ], true);

            $newUser = [
                'id'       => $penggunaId,
                'nama'     => $nama,
                'username' => strtolower($username),
                'role'     => 'PELANGGAN',
            ];

            session()->set('user', $newUser);
            session()->set('last_activity', time());

            $logModel->catat(
                $penggunaId,
                'REGISTRASI_PELANGGAN',
                'pengguna',
                $penggunaId,
                ['username' => $newUser['username']],
                'Registrasi akun pelanggan baru: ' . $nama
            );

            return redirect()->to('/pelanggan/katalog')
                ->with('success', 'Pendaftaran berhasil! Selamat datang, ' . $newUser['nama'] . '.');
        }

        // Fallback Mode Mock Session
        MockDataService::initSession();
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

        session()->remove('user');
        session()->remove('last_activity');

        if ($user) {
            if (DbHelper::isReady()) {
                try {
                    (new LogAktivitasModel())->catat(
                        isset($user['id']) ? (int) $user['id'] : null,
                        'LOGOUT',
                        'pengguna',
                        isset($user['id']) ? (int) $user['id'] : null,
                        ['username' => $user['username'] ?? null],
                        'Logout pengguna ' . ($user['username'] ?? '')
                    );
                } catch (\Throwable $e) {
                    log_message('warning', 'Gagal mencatat log logout: ' . $e->getMessage());
                }
            } else {
                MockDataService::addAudit(
                    $user['nama'] . ' / ' . $user['role'],
                    'Logout',
                    $user['username']
                );
            }
        }

        return redirect()->to('/login')->with('success', 'Anda telah keluar dari akun.');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'OWNER'   => redirect()->to('/owner/dashboard'),
            'KASIR'   => redirect()->to('/kasir/pos'),
            'BARISTA' => redirect()->to('/barista/antrean'),
            default   => redirect()->to('/pelanggan/katalog'),
        };
    }
}
