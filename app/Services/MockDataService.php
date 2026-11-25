<?php

namespace App\Services;

/**
 * In-memory & session-backed mock data service for UI/UX demo
 * prior to PostgreSQL database integration.
 */
class MockDataService
{
    public static function initSession(): void
    {
        $session = session();

        if (! $session->has('mock_initialized')) {
            $session->set('mock_initialized', true);
            $session->set('mock_menus', self::defaultMenus());
            $session->set('mock_orders', self::defaultOrders());
            $session->set('mock_cart', [
                [
                    'menu_id' => 1,
                    'nama'    => 'Kopi Susu Gading',
                    'harga'   => 18000,
                    'qty'     => 2,
                    'catatan' => 'Less sugar, tanpa es',
                ],
            ]);
            $session->set('mock_shift', [
                'aktif'       => true,
                'jenis'       => 'PAGI',
                'jam'         => '08.00–15.00 WIB',
                'kasir'       => 'Rina',
                'barista'     => 'Adi',
                'waktu_buka'  => '08.00 WIB',
            ]);
            $session->set('mock_bahan', self::defaultBahan());
            $session->set('mock_mutasi', self::defaultMutasi());
            $session->set('mock_staff', self::defaultStaff());
            $session->set('mock_riwayat_shift', self::defaultRiwayatShift());
            $session->set('mock_audit', self::defaultAudit());
        }
    }

    public static function getUsers(): array
    {
        return [
            'pelanggan' => ['id' => 1, 'nama' => 'Ayu Lestari', 'username' => 'ayu.pelanggan', 'role' => 'PELANGGAN'],
            'kasir'     => ['id' => 2, 'nama' => 'Rina', 'username' => 'rina.kasir', 'role' => 'KASIR'],
            'barista'   => ['id' => 3, 'nama' => 'Adi', 'username' => 'adi.barista', 'role' => 'BARISTA'],
            'owner'     => ['id' => 4, 'nama' => 'Maya', 'username' => 'maya.owner', 'role' => 'OWNER'],
        ];
    }

    public static function getMenus(): array
    {
        self::initSession();
        return session()->get('mock_menus') ?? [];
    }

    public static function getOrders(): array
    {
        self::initSession();
        return session()->get('mock_orders') ?? [];
    }

    public static function getCart(): array
    {
        self::initSession();
        return session()->get('mock_cart') ?? [];
    }

    public static function getShift(): array
    {
        self::initSession();
        return session()->get('mock_shift') ?? [];
    }

    public static function getBahan(): array
    {
        self::initSession();
        return session()->get('mock_bahan') ?? [];
    }

    public static function getMutasi(): array
    {
        self::initSession();
        return session()->get('mock_mutasi') ?? [];
    }

    public static function getStaff(): array
    {
        self::initSession();
        return session()->get('mock_staff') ?? [];
    }

    public static function getRiwayatShift(): array
    {
        self::initSession();
        return session()->get('mock_riwayat_shift') ?? [];
    }

    public static function getAudit(): array
    {
        self::initSession();
        return session()->get('mock_audit') ?? [];
    }

    public static function addAudit(string $pelaku, string $aktivitas, string $objek): void
    {
        self::initSession();
        $audit = self::getAudit();
        array_unshift($audit, [
            'waktu'     => date('d M · H.i'),
            'pelaku'    => $pelaku,
            'aktivitas' => $aktivitas,
            'objek'     => $objek,
        ]);
        session()->set('mock_audit', $audit);
    }

    /**
     * 22 Varian Menu (13 KOPI, 9 NON_KOPI) sesuai target Seeder SRS
     */
    private static function defaultMenus(): array
    {
        return [
            // 13 KOPI
            ['id' => 1,  'nama' => 'Kopi Susu Gading',     'kategori' => 'KOPI',     'harga' => 18000, 'aktif' => true],
            ['id' => 2,  'nama' => 'Americano',            'kategori' => 'KOPI',     'harga' => 15000, 'aktif' => true],
            ['id' => 3,  'nama' => 'Cappuccino',           'kategori' => 'KOPI',     'harga' => 22000, 'aktif' => true],
            ['id' => 4,  'nama' => 'Kopi Susu Aren',       'kategori' => 'KOPI',     'harga' => 20000, 'aktif' => true],
            ['id' => 5,  'nama' => 'Caramel Macchiato',    'kategori' => 'KOPI',     'harga' => 24000, 'aktif' => true],
            ['id' => 6,  'nama' => 'Caffe Latte',          'kategori' => 'KOPI',     'harga' => 22000, 'aktif' => true],
            ['id' => 7,  'nama' => 'Espresso Double',      'kategori' => 'KOPI',     'harga' => 14000, 'aktif' => true],
            ['id' => 8,  'nama' => 'Mochaccino',           'kategori' => 'KOPI',     'harga' => 23000, 'aktif' => true],
            ['id' => 9,  'nama' => 'Hazelnut Latte',       'kategori' => 'KOPI',     'harga' => 24000, 'aktif' => true],
            ['id' => 10, 'nama' => 'Vanilla Latte',        'kategori' => 'KOPI',     'harga' => 23000, 'aktif' => true],
            ['id' => 11, 'nama' => 'V60 Manual Brew',      'kategori' => 'KOPI',     'harga' => 20000, 'aktif' => true],
            ['id' => 12, 'nama' => 'Vietnam Drip',         'kategori' => 'KOPI',     'harga' => 17000, 'aktif' => true],
            ['id' => 13, 'nama' => 'Kopi Tubruk Gading',   'kategori' => 'KOPI',     'harga' => 12000, 'aktif' => false],
            // 9 NON_KOPI
            ['id' => 14, 'nama' => 'Cokelat Susu',         'kategori' => 'NON_KOPI', 'harga' => 20000, 'aktif' => true],
            ['id' => 15, 'nama' => 'Matcha Latte',         'kategori' => 'NON_KOPI', 'harga' => 22000, 'aktif' => true],
            ['id' => 16, 'nama' => 'Red Velvet Latte',     'kategori' => 'NON_KOPI', 'harga' => 22000, 'aktif' => true],
            ['id' => 17, 'nama' => 'Taro Milk',            'kategori' => 'NON_KOPI', 'harga' => 20000, 'aktif' => true],
            ['id' => 18, 'nama' => 'Thai Tea Original',    'kategori' => 'NON_KOPI', 'harga' => 18000, 'aktif' => true],
            ['id' => 19, 'nama' => 'Lemon Tea Segar',      'kategori' => 'NON_KOPI', 'harga' => 15000, 'aktif' => true],
            ['id' => 20, 'nama' => 'Lychee Tea',           'kategori' => 'NON_KOPI', 'harga' => 18000, 'aktif' => true],
            ['id' => 21, 'nama' => 'Klepon Latte',         'kategori' => 'NON_KOPI', 'harga' => 21000, 'aktif' => true],
            ['id' => 22, 'nama' => 'Susu Segar Pandan',    'kategori' => 'NON_KOPI', 'harga' => 16000, 'aktif' => true],
        ];
    }

    private static function defaultOrders(): array
    {
        return [
            [
                'id'               => 1,
                'kode'             => 'ORD-20261119-0001',
                'nomor_pendek'     => '#0001',
                'pelanggan'        => 'Ayu',
                'kanal'            => 'Online',
                'layanan'          => 'Take Away',
                'status_bayar'     => 'Belum Dibayar',
                'metode'           => 'QRIS',
                'status_bar'       => 'Menunggu',
                'dikirim_barista'  => false,
                'waktu'            => '09.41 WIB',
                'durasi'           => '00:00',
                'total'            => 36000,
                'catatan'          => 'Less sugar, tanpa es',
                'items'            => [
                    ['nama' => 'Kopi Susu Gading', 'qty' => 2, 'harga' => 18000],
                ],
            ],
            [
                'id'               => 2,
                'kode'             => 'ORD-20261119-0002',
                'nomor_pendek'     => '#0002',
                'pelanggan'        => 'Deni',
                'kanal'            => 'Online',
                'layanan'          => 'Minum di Tempat',
                'status_bayar'     => 'Lunas',
                'metode'           => 'QRIS',
                'status_bar'       => 'Menunggu',
                'dikirim_barista'  => true,
                'waktu'            => '09.38 WIB',
                'durasi'           => '00:00',
                'total'            => 36000,
                'catatan'          => 'Gula normal, es sedikit',
                'items'            => [
                    ['nama' => 'Kopi Susu Gading', 'qty' => 2, 'harga' => 18000],
                ],
            ],
            [
                'id'               => 3,
                'kode'             => 'ORD-20261119-0003',
                'nomor_pendek'     => '#0003',
                'pelanggan'        => 'Bima',
                'kanal'            => 'Online',
                'layanan'          => 'Take Away',
                'status_bayar'     => 'Lunas',
                'metode'           => 'Tunai',
                'status_bar'       => 'Menunggu',
                'dikirim_barista'  => true,
                'waktu'            => '09.36 WIB',
                'durasi'           => '00:00',
                'total'            => 37000,
                'catatan'          => 'Americano panas · Cappuccino tanpa gula',
                'items'            => [
                    ['nama' => 'Americano', 'qty' => 1, 'harga' => 15000],
                    ['nama' => 'Cappuccino', 'qty' => 1, 'harga' => 22000],
                ],
            ],
            [
                'id'               => 4,
                'kode'             => 'ORD-20261119-0004',
                'nomor_pendek'     => '#0004',
                'pelanggan'        => 'Sari',
                'kanal'            => 'Offline',
                'layanan'          => 'Take Away',
                'status_bayar'     => 'Lunas',
                'metode'           => 'Tunai',
                'status_bar'       => 'Diproses',
                'dikirim_barista'  => true,
                'waktu'            => '09.34 WIB',
                'durasi'           => '04:12',
                'total'            => 22000,
                'catatan'          => 'Susu hangat, tanpa gula',
                'items'            => [
                    ['nama' => 'Cappuccino', 'qty' => 1, 'harga' => 22000],
                ],
            ],
        ];
    }

    private static function defaultBahan(): array
    {
        return [
            ['id' => 1, 'nama' => 'Kopi',  'stok' => 1200, 'minimum' => 2000, 'satuan' => 'g',     'status' => 'Menipis'],
            ['id' => 2, 'nama' => 'Cup',   'stok' => 48,   'minimum' => 50,   'satuan' => 'pcs',   'status' => 'Menipis'],
            ['id' => 3, 'nama' => 'Pipet', 'stok' => 120,  'minimum' => 50,   'satuan' => 'pcs',   'status' => 'Aman'],
            ['id' => 4, 'nama' => 'Air',   'stok' => 19,   'minimum' => 10,   'satuan' => 'liter', 'status' => 'Aman'],
            ['id' => 5, 'nama' => 'Susu',  'stok' => 1,    'minimum' => 3,    'satuan' => 'liter', 'status' => 'Kritis'],
            ['id' => 6, 'nama' => 'Sirup', 'stok' => 2.4,  'minimum' => 1,    'satuan' => 'liter', 'status' => 'Aman'],
        ];
    }

    private static function defaultMutasi(): array
    {
        return [
            ['waktu' => '19 Nov · 09.34 WIB', 'keterangan' => 'Susu −150 ml', 'sumber' => '#0004 · Lunas', 'pelaku' => 'Rina'],
            ['waktu' => '19 Nov · 08.10 WIB', 'keterangan' => 'Kopi +2.000 g', 'sumber' => 'Pembelian bahan', 'pelaku' => 'Maya'],
            ['waktu' => '18 Nov · 16.20 WIB', 'keterangan' => 'Cup +100 pcs', 'sumber' => 'STOK_AWAL / Restock', 'pelaku' => 'Maya'],
        ];
    }

    private static function defaultStaff(): array
    {
        return [
            ['id' => 1, 'nama' => 'Rina', 'username' => 'rina.kasir',  'role' => 'Kasir',   'shift' => 'Shift Pagi', 'aktif' => true],
            ['id' => 2, 'nama' => 'Adi',  'username' => 'adi.barista', 'role' => 'Barista', 'shift' => 'Shift Pagi', 'aktif' => true],
            ['id' => 3, 'nama' => 'Budi', 'username' => 'budi.kasir',  'role' => 'Kasir',   'shift' => 'Shift Sore', 'aktif' => true],
        ];
    }

    private static function defaultRiwayatShift(): array
    {
        return [
            ['tanggal' => '19 Nov 2026', 'staf' => 'Rina / Pagi', 'masuk_keluar' => '08.00–Aktif', 'jam_toko' => '08.00–Belum tutup'],
            ['tanggal' => '18 Nov 2026', 'staf' => 'Budi / Sore', 'masuk_keluar' => '15.00–22.05', 'jam_toko' => '08.00–22.00'],
        ];
    }

    private static function defaultAudit(): array
    {
        return [
            ['waktu' => '19 Nov · 08.10', 'pelaku' => 'Maya / Owner', 'aktivitas' => 'Tambah stok kopi', 'objek' => '+2.000 g'],
            ['waktu' => '19 Nov · 07.55', 'pelaku' => 'Maya / Owner', 'aktivitas' => 'Ubah akun staf',   'objek' => 'rina.kasir / Role Kasir'],
        ];
    }
}

