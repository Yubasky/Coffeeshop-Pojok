<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'AuthController::login');

// Autentikasi (FR-01 - FR-03)
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::attemptRegister');
$routes->get('logout', 'AuthController::logout');

// Pelanggan (FR-05 - FR-07)
$routes->group('pelanggan', ['filter' => ['auth', 'role:PELANGGAN']], static function ($routes) {
    $routes->get('katalog', 'Pelanggan\KatalogController::index');
    $routes->post('keranjang/tambah', 'Pelanggan\KatalogController::addToCart');
    $routes->post('keranjang/update', 'Pelanggan\KatalogController::updateCart');
    $routes->get('checkout', 'Pelanggan\PesananController::checkout');
    $routes->post('checkout', 'Pelanggan\PesananController::store');
    $routes->get('status', 'Pelanggan\PesananController::status');
    $routes->get('status/(:segment)', 'Pelanggan\PesananController::status/$1');
});

// Kasir (FR-08 - FR-10, FR-19)
$routes->group('kasir', ['filter' => ['auth', 'role:KASIR']], static function ($routes) {
    $routes->get('pos', 'Kasir\PosController::index');
    $routes->post('pos/offline', 'Kasir\PosController::storeOffline');
    $routes->get('pembayaran/(:num)', 'Kasir\PembayaranController::detail/$1');
    $routes->post('pembayaran/(:num)/bayar', 'Kasir\PembayaranController::bayar/$1');
    $routes->post('pembayaran/(:num)/kirim-barista', 'Kasir\PembayaranController::kirimBarista/$1');
    $routes->post('pembayaran/(:num)/update-pelanggan', 'Kasir\PembayaranController::updatePelanggan/$1');
    $routes->post('pembayaran/(:num)/batal', 'Kasir\PembayaranController::batal/$1');
    $routes->post('shift/toggle', 'Kasir\ShiftController::toggle');
});

// Barista (FR-11 - FR-13)
$routes->group('barista', ['filter' => ['auth', 'role:BARISTA']], static function ($routes) {
    $routes->get('antrean', 'Barista\AntreanController::index');
    $routes->post('antrean/(:num)/mulai', 'Barista\AntreanController::mulai/$1');
    $routes->post('antrean/(:num)/selesai', 'Barista\AntreanController::selesai/$1');
});

// Stok Bahan Baku (Owner & Barista: FR-16 - FR-18)
$routes->group('stok', ['filter' => ['auth', 'role:OWNER,BARISTA']], static function ($routes) {
    $routes->get('/', 'StokController::index');
    $routes->post('tambah', 'StokController::store');
    $routes->post('koreksi/(:num)', 'StokController::koreksi/$1');
});

// Owner (FR-04, FR-14, FR-15, FR-20 - FR-22)
$routes->group('owner', ['filter' => ['auth', 'role:OWNER']], static function ($routes) {
    $routes->get('dashboard', 'Owner\LaporanController::index');
    $routes->get('menu', 'Owner\MenuController::index');
    $routes->post('menu/tambah', 'Owner\MenuController::store');
    $routes->post('menu/(:num)/update', 'Owner\MenuController::update/$1');
    $routes->post('menu/(:num)/toggle', 'Owner\MenuController::toggleStatus/$1');
    $routes->get('pengguna', 'Owner\PenggunaController::index');
    $routes->post('pengguna/tambah', 'Owner\PenggunaController::store');
    $routes->post('pengguna/(:num)/toggle', 'Owner\PenggunaController::toggleStatus/$1');
    $routes->post('pengguna/(:num)/reset-password', 'Owner\PenggunaController::resetPassword/$1');
});
