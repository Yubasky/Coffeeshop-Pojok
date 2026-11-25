<?php

namespace App\Controllers\Owner;

use App\Controllers\BaseController;
use App\Models\LogAktivitasModel;
use App\Models\PenggunaModel;
use App\Models\RingkasanShiftModel;
use App\Services\DbHelper;
use App\Services\MockDataService;

class PenggunaController extends BaseController
{
    public function index()
    {
        $search = trim((string) $this->request->getGet('q'));
        $role   = (string) ($this->request->getGet('role') ?? 'Semua');
        $tab    = (string) ($this->request->getGet('tab') ?? 'staf');

        if (DbHelper::isReady()) {
            $staffRows = (new PenggunaModel())->getStaff($search, $role);
            $staff     = array_map(static function (array $s): array {
                return [
                    'id'       => (int) $s['pengguna_id'],
                    'nama'     => $s['nama'],
                    'username' => $s['username'],
                    'role'     => ucfirst(strtolower($s['peran'])),
                    'shift'    => $s['peran'],
                    'aktif'    => true,
                ];
            }, $staffRows);

            $shiftRows    = (new RingkasanShiftModel())->orderBy('shift_id', 'DESC')->findAll(20);
            $riwayatShift = array_map(static function (array $rs): array {
                $jamBuka  = $rs['waktu_buka'] ? date('H.i', strtotime((string) $rs['waktu_buka'])) : '-';
                $jamTutup = $rs['waktu_tutup'] ? date('H.i', strtotime((string) $rs['waktu_tutup'])) : 'Aktif';
                return [
                    'tanggal'      => date('d M Y', strtotime((string) $rs['tanggal'])),
                    'staf'         => ($rs['nama_kasir_pembuka'] ?? '-') . ' / ' . ucfirst(strtolower((string) $rs['jenis_shift'])),
                    'masuk_keluar' => $jamBuka . '–' . $jamTutup,
                    'jam_toko'     => ($rs['status'] ?? '') === 'AKTIF' ? 'Sedang berjalan' : 'Rp ' . number_format((int) ($rs['total_penjualan'] ?? 0), 0, ',', '.'),
                ];
            }, $shiftRows);

            $auditRows = (new LogAktivitasModel())->getTerbaru(20);
            $audit     = array_map(static function (array $a): array {
                $pelaku = $a['nama_pengguna']
                    ? ($a['nama_pengguna'] . ' / ' . ($a['peran_pengguna'] ?? '-'))
                    : 'Sistem / Guest';
                return [
                    'waktu'     => date('d M · H.i', strtotime((string) $a['waktu_dibuat'])),
                    'pelaku'    => $pelaku,
                    'aktivitas' => $a['jenis_aksi'],
                    'objek'     => $a['keterangan'] ?? ($a['entitas'] ?? '-'),
                ];
            }, $auditRows);

            return view('owner/pengguna', [
                'title'        => 'Staf & Riwayat — Owner Pojok UKM',
                'activeNav'    => 'pengguna',
                'staff'        => $staff,
                'riwayatShift' => $riwayatShift,
                'audit'        => $audit,
                'search'       => $search,
                'role'         => $role,
                'tab'          => $tab,
            ]);
        }

        MockDataService::initSession();

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
        $nama     = trim((string) $this->request->getPost('nama'));
        $username = trim((string) $this->request->getPost('username'));
        $role     = strtoupper((string) ($this->request->getPost('role') ?? 'KASIR'));
        $peran    = in_array($role, ['KASIR', 'BARISTA', 'OWNER'], true) ? $role : 'KASIR';

        if ($nama === '' || $username === '') {
            return redirect()->back()->with('error', 'Nama dan username staf wajib diisi.');
        }

        if (DbHelper::isReady()) {
            $penggunaModel = new PenggunaModel();
            if ($penggunaModel->findByUsernameInsensitive($username) !== null) {
                return redirect()->back()->with('error', 'Username "' . $username . '" sudah digunakan.');
            }

            $now    = date('Y-m-d H:i:sP');
            $stafId = (int) $penggunaModel->insert([
                'nama'             => $nama,
                'username'         => strtolower($username),
                'password_hash'    => password_hash('Pojok123!', PASSWORD_BCRYPT),
                'peran'            => $peran,
                'waktu_dibuat'     => $now,
                'waktu_diperbarui' => $now,
            ], true);

            $user = session()->get('user');
            (new LogAktivitasModel())->catat(
                isset($user['id']) ? (int) $user['id'] : null,
                'TAMBAH_STAF',
                'pengguna',
                $stafId,
                ['username' => strtolower($username), 'peran' => $peran],
                'Menambah akun staf ' . $nama . ' (' . $peran . ')'
            );

            return redirect()->to('/owner/pengguna')->with('success', 'Akun staf "' . $nama . '" berhasil dibuat (password awal: Pojok123!).');
        }

        MockDataService::initSession();
        $staff   = MockDataService::getStaff();
        $staff[] = [
            'id'       => count($staff) + 1,
            'nama'     => $nama,
            'username' => strtolower($username),
            'role'     => ucfirst(strtolower($peran)),
            'shift'    => 'Shift Pagi',
            'aktif'    => true,
        ];
        session()->set('mock_staff', $staff);

        $user = session()->get('user');
        MockDataService::addAudit($user['nama'] . ' / Owner', 'Tambah Akun Staf', $username . ' / Role ' . $peran);

        return redirect()->to('/owner/pengguna')->with('success', 'Akun staf "' . $nama . '" berhasil dibuat.');
    }

    public function toggleStatus(int $id)
    {
        if (DbHelper::isReady()) {
            $pengguna = (new PenggunaModel())->find($id);
            if (! $pengguna) {
                return redirect()->back()->with('error', 'Akun staf tidak ditemukan.');
            }

            $user = session()->get('user');
            (new LogAktivitasModel())->catat(
                isset($user['id']) ? (int) $user['id'] : null,
                'CEK_STATUS_STAF',
                'pengguna',
                $id,
                ['username' => $pengguna['username']],
                'Aksi ubah status dicatat untuk ' . $pengguna['username'] . ' (tabel pengguna pada skema 3NF belum memiliki kolom aktif)'
            );

            return redirect()->to('/owner/pengguna')
                ->with('success', 'Permintaan status akun ' . $pengguna['username'] . ' tercatat di Audit Log.');
        }

        MockDataService::initSession();
        $staff      = MockDataService::getStaff();
        $uname      = '';
        $statusText = '';

        foreach ($staff as &$s) {
            if ($s['id'] === $id) {
                $s['aktif'] = ! $s['aktif'];
                $uname      = $s['username'];
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
        if (DbHelper::isReady()) {
            $penggunaModel = new PenggunaModel();
            $staf          = $penggunaModel->find($id);
            if (! $staf) {
                return redirect()->back()->with('error', 'Akun staf tidak ditemukan.');
            }

            $penggunaModel->update($id, [
                'password_hash'    => password_hash('Pojok123!', PASSWORD_BCRYPT),
                'waktu_diperbarui' => date('Y-m-d H:i:sP'),
            ]);

            $user = session()->get('user');
            (new LogAktivitasModel())->catat(
                isset($user['id']) ? (int) $user['id'] : null,
                'RESET_PASSWORD_STAF',
                'pengguna',
                $id,
                ['username' => $staf['username']],
                'Reset password staf ' . $staf['username'] . ' ke default'
            );

            return redirect()->to('/owner/pengguna')
                ->with('success', 'Password untuk ' . $staf['username'] . ' berhasil direset ke "Pojok123!".');
        }

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
