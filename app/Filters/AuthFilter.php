<?php

namespace App\Filters;

use App\Models\PenggunaModel;
use App\Services\DbHelper;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter: Memastikan pengguna sudah login dan mengecek batas sesi idle 30 menit.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->has('user')) {
            return redirect()->to('/login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        // Idle timeout 30 menit (1800 detik) sesuai SRS
        $lastActivity = $session->get('last_activity') ?? time();
        if (time() - $lastActivity > 1800) {
            $session->remove('user');
            $session->remove('last_activity');
            return redirect()->to('/login')->with('error', 'Sesi berakhir karena tidak ada aktivitas selama 30 menit.');
        }

        if (DbHelper::isReady()) {
            $user   = $session->get('user');
            $userId = isset($user['id']) ? (int) $user['id'] : 0;

            if ($userId <= 0 || (new PenggunaModel())->find($userId) === null) {
                $session->remove('user');
                $session->remove('last_activity');
                return redirect()->to('/login')->with('error', 'Sesi tidak valid atau akun sudah tidak ditemukan. Silakan masuk kembali.');
            }
        }

        $session->set('last_activity', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No-op
    }
}

