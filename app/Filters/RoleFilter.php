<?php

namespace App\Filters;

use App\Services\MockDataService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * RoleFilter: Proteksi RBAC lintas peran. Menolak akses tidak sah dengan HTTP 403
 * dan mencatat kejadian ke log aktivitas (mock audit).
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session()->get('user');
        if (! $user) {
            return redirect()->to('/login');
        }

        $allowedRoles = $arguments ?? [];
        if (! empty($allowedRoles) && ! in_array($user['role'], $allowedRoles, true)) {
            MockDataService::addAudit(
                $user['nama'] . ' / ' . $user['role'],
                'Akses Ditolak (HTTP 403)',
                '/' . ltrim($request->getPath(), '/')
            );

            return service('response')
                ->setStatusCode(403)
                ->setBody(view('errors/html/error_403', [
                    'role' => $user['role'],
                    'path' => $request->getPath(),
                ]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No-op
    }
}

