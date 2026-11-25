-- ============================================================================
-- SKRIP SQL SEEDER BASIS DATA (POSTGRESQL 13+)
-- Sistem Informasi Operasional Pojok UKM Tanjung Gading (Web-App / CI4)
--
-- Cakupan :
--   - 10 akun pengguna (OWNER, KASIR, BARISTA, PELANGGAN) - Password: Pojok123!
--   - 22 varian menu (13 KOPI, 9 NON_KOPI) sesuai Panduan Kerja Minggu ke-3
--   - 10 bahan baku + 10 riwayat_stok (STOK_AWAL, MASUK, PENYESUAIAN, PEMAKAIAN, PENGEMBALIAN)
--   - 10 shift (9 TERTUTUP, 1 AKTIF)
--   - 10 pesanan, 10 detail_pesanan, 10 pembayaran, 10 riwayat_pesanan, 10 log_aktivitas
-- ============================================================================

BEGIN;

-- 1. Tabel pengguna (10 data: 1 OWNER, 3 KASIR, 2 BARISTA, 4 PELANGGAN)
-- Password seluruh akun uji: Pojok123! (bcrypt)
INSERT INTO pengguna (nama, username, password_hash, peran, waktu_dibuat, waktu_diperbarui) VALUES
('Budi Santoso',   'owner_budi',   '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'OWNER',     '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07'),
('Siti Aminah',    'kasir_siti',   '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'KASIR',     '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07'),
('Rina Wati',      'kasir_rina',   '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'KASIR',     '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07'),
('Dedi Kurniawan', 'kasir_dedi',   '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'KASIR',     '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07'),
('Andi Pratama',   'barista_andi', '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'BARISTA',   '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07'),
('Reza Rahadian',  'barista_reza', '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'BARISTA',   '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07'),
('Dewi Lestari',   'dewi_l',       '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'PELANGGAN', '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07'),
('Fajar Nugraha',  'fajar_n',      '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'PELANGGAN', '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07'),
('Putri Ayu',      'putri_ayu',    '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'PELANGGAN', '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07'),
('Hendra Wijaya',  'hendra_w',     '$2y$10$Wj2EGwk92CO2ZfLbUyhVLumCMsXGfq6VF3wOcM6U6jtOgd5Fj6ShG', 'PELANGGAN', '2026-11-26 07:00:00+07', '2026-11-26 07:00:00+07');

-- 2. Tabel menu (22 varian: 13 KOPI, 9 NON_KOPI)
INSERT INTO menu (nama, kategori, harga, deskripsi, foto_url, aktif, waktu_dibuat, waktu_diperbarui) VALUES
('Kopi Susu Gading',   'KOPI',     18000, 'Es kopi susu signature Pojok UKM Tanjung Gading dengan racikan creamy.', '/uploads/menu/kopi-susu-gading.jpg',   TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Americano',          'KOPI',     15000, 'Double shot espresso dengan air mineral, tersedia dingin atau panas.',   '/uploads/menu/americano.jpg',          TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Cappuccino',         'KOPI',     22000, 'Keseimbangan espresso, susu segar, dan lapisan foam tebal.',            '/uploads/menu/cappuccino.jpg',         TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Kopi Susu Aren',     'KOPI',     20000, 'Perpaduan espresso, susu segar, dan gula aren asli Nusantara.',         '/uploads/menu/kopi-susu-aren.jpg',     TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Caramel Macchiato',  'KOPI',     24000, 'Espresso dengan sirup vanila, susu segar, dan saus karamel.',           '/uploads/menu/caramel-macchiato.jpg',  TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Caffe Latte',        'KOPI',     22000, 'Espresso lembut yang dipadukan dengan steamed fresh milk.',             '/uploads/menu/caffe-latte.jpg',        TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Espresso Double',    'KOPI',     14000, 'Ekstraksi ganda biji kopi house blend dengan crema tebal.',             '/uploads/menu/espresso-double.jpg',    TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Mochaccino',         'KOPI',     23000, 'Kombinasi espresso, dark chocolate, dan susu segar.',                   '/uploads/menu/mochaccino.jpg',         TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Hazelnut Latte',     'KOPI',     24000, 'Kopi susu dengan aroma sirup hazelnut panggang yang harum.',            '/uploads/menu/hazelnut-latte.jpg',     TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Vanilla Latte',      'KOPI',     23000, 'Espresso dan susu segar dengan sentuhan sirup vanilla lembut.',         '/uploads/menu/vanilla-latte.jpg',      TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('V60 Manual Brew',    'KOPI',     20000, 'Seduhan manual biji kopi single origin Arabika Gayo.',                  '/uploads/menu/v60-manual-brew.jpg',    TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Vietnam Drip',       'KOPI',     17000, 'Kopi tetes khas Vietnam dengan susu kental manis.',                     '/uploads/menu/vietnam-drip.jpg',       TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Kopi Tubruk Gading', 'KOPI',     12000, 'Kopi hitam seduh tradisional (nonaktif sementara).',                    '/uploads/menu/kopi-tubruk-gading.jpg', FALSE, '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Cokelat Susu',       'NON_KOPI', 20000, 'Cokelat dark creamy kaya rasa yang disajikan dingin maupun hangat.',    '/uploads/menu/cokelat-susu.jpg',       TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Matcha Latte',       'NON_KOPI', 22000, 'Minuman teh hijau Uji Matcha Jepang pilihan dengan susu segar.',        '/uploads/menu/matcha-latte.jpg',       TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Red Velvet Latte',   'NON_KOPI', 22000, 'Minuman non-kopi manis dan lembut dengan rasa khas Red Velvet.',        '/uploads/menu/red-velvet-latte.jpg',   TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Taro Milk',          'NON_KOPI', 20000, 'Minuman susu rasa talas ungu creamy yang menyegarkan.',                 '/uploads/menu/taro-milk.jpg',          TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Thai Tea Original',  'NON_KOPI', 18000, 'Teh Thailand autentik dengan susu evaporasi dan kental manis.',         '/uploads/menu/thai-tea.jpg',           TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Lemon Tea Segar',    'NON_KOPI', 15000, 'Seduhan teh hitam pilihan dengan perasan lemon segar.',                 '/uploads/menu/lemon-tea.jpg',          TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Lychee Tea',         'NON_KOPI', 18000, 'Es teh manis beraroma buah leci dengan topping buah leci.',             '/uploads/menu/lychee-tea.jpg',         TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Klepon Latte',       'NON_KOPI', 21000, 'Minuman susu pandan wangi dengan manisnya gula merah.',                 '/uploads/menu/klepon-latte.jpg',       TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07'),
('Susu Segar Pandan',  'NON_KOPI', 16000, 'Susu segar dingin dengan sirup daun pandan alami.',                     '/uploads/menu/susu-segar-pandan.jpg',  TRUE,  '2026-11-26 07:05:00+07', '2026-11-26 07:05:00+07');

-- 3. Tabel bahan_baku (10 data)
INSERT INTO bahan_baku (nama, satuan, stok_terkini, waktu_dibuat, waktu_diperbarui) VALUES
('Biji Kopi Arabika Gayo',         'gram', 2500.00,  '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07'),
('Biji Kopi House Blend Pojok',    'gram', 5000.00,  '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07'),
('Susu UHT Full Cream',            'ml',   15000.00, '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07'),
('Gula Aren Cair',                 'ml',   4000.00,  '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07'),
('Sirup Caramel',                  'ml',   2250.00,  '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07'),
('Bubuk Matcha Uji',               'gram', 1200.00,  '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07'),
('Bubuk Dark Chocolate',           'gram', 1800.00,  '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07'),
('Bubuk Red Velvet',               'gram', 1000.00,  '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07'),
('Teh Hitam Daun & Ekstrak Lemon', 'gram', 1500.00,  '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07'),
('Cup Plastik 16oz & Tutup',       'pcs',  350.00,   '2026-11-26 07:10:00+07', '2026-11-26 07:10:00+07');

-- 4. Tabel shift (9 TERTUTUP, 1 AKTIF sesuai constraint uq_satu_shift_aktif)
INSERT INTO shift (jenis_shift, tanggal, waktu_buka, waktu_tutup, dibuka_oleh_id, ditutup_oleh_id, status, waktu_dibuat, waktu_diperbarui) VALUES
('PAGI', '2026-11-21', '2026-11-21 08:00:00+07', '2026-11-21 15:00:00+07', 2, 2,    'TERTUTUP', '2026-11-21 08:00:00+07', '2026-11-21 15:00:00+07'),
('SORE', '2026-11-21', '2026-11-21 15:05:00+07', '2026-11-21 22:00:00+07', 3, 3,    'TERTUTUP', '2026-11-21 15:05:00+07', '2026-11-21 22:00:00+07'),
('PAGI', '2026-11-22', '2026-11-22 08:00:00+07', '2026-11-22 15:00:00+07', 2, 2,    'TERTUTUP', '2026-11-22 08:00:00+07', '2026-11-22 15:00:00+07'),
('SORE', '2026-11-22', '2026-11-22 15:05:00+07', '2026-11-22 22:00:00+07', 4, 4,    'TERTUTUP', '2026-11-22 15:05:00+07', '2026-11-22 22:00:00+07'),
('PAGI', '2026-11-23', '2026-11-23 08:00:00+07', '2026-11-23 15:00:00+07', 3, 3,    'TERTUTUP', '2026-11-23 08:00:00+07', '2026-11-23 15:00:00+07'),
('SORE', '2026-11-23', '2026-11-23 15:05:00+07', '2026-11-23 22:00:00+07', 2, 2,    'TERTUTUP', '2026-11-23 15:05:00+07', '2026-11-23 22:00:00+07'),
('PAGI', '2026-11-24', '2026-11-24 08:00:00+07', '2026-11-24 15:00:00+07', 4, 4,    'TERTUTUP', '2026-11-24 08:00:00+07', '2026-11-24 15:00:00+07'),
('SORE', '2026-11-24', '2026-11-24 15:05:00+07', '2026-11-24 22:00:00+07', 3, 3,    'TERTUTUP', '2026-11-24 15:05:00+07', '2026-11-24 22:00:00+07'),
('PAGI', '2026-11-25', '2026-11-25 08:00:00+07', '2026-11-25 15:00:00+07', 2, 2,    'TERTUTUP', '2026-11-25 08:00:00+07', '2026-11-25 15:00:00+07'),
('PAGI', '2026-11-26', '2026-11-26 08:00:00+07', NULL,                     2, NULL, 'AKTIF',    '2026-11-26 08:00:00+07', '2026-11-26 08:00:00+07');

-- 5. Tabel pesanan (10 data)
INSERT INTO pesanan (nomor_pesanan, dipesan_oleh, token_sesi_pelanggan, nama_pelanggan, status_pembayaran, status_pesanan, shift_id, dibuat_oleh_id, dibatalkan_oleh_id, waktu_batal, alasan_pembatalan, waktu_dibuat, waktu_diperbarui) VALUES
('ORD-20261125-0001', 'MANDIRI', 'a1b2c3d4e5f60718293a4b5c6d7e8f90a1b2c3d4', 'Dewi Lestari',  'LUNAS', 'SELESAI',    9,  7,  NULL, NULL, NULL, '2026-11-25 09:00:00+07', '2026-11-25 09:15:00+07'),
('ORD-20261125-0002', 'KASIR',   NULL,                                       'Pak Rahmat',    'LUNAS', 'SELESAI',    9,  2,  NULL, NULL, NULL, '2026-11-25 10:00:00+07', '2026-11-25 10:12:00+07'),
('ORD-20261125-0003', 'MANDIRI', 'b2c3d4e5f60718293a4b5c6d7e8f90a1b2c3d4e5', 'Fajar Nugraha', 'LUNAS', 'SELESAI',    9,  8,  NULL, NULL, NULL, '2026-11-25 11:00:00+07', '2026-11-25 11:15:00+07'),
('ORD-20261125-0004', 'KASIR',   NULL,                                       'Bu Ratna',      'LUNAS', 'SELESAI',    9,  2,  NULL, NULL, NULL, '2026-11-25 12:30:00+07', '2026-11-25 12:45:00+07'),
('ORD-20261126-0001', 'MANDIRI', 'c3d4e5f60718293a4b5c6d7e8f90a1b2c3d4e5f6', 'Putri Ayu',     'LUNAS', 'SELESAI',    10, 9,  NULL, NULL, NULL, '2026-11-26 08:15:00+07', '2026-11-26 08:30:00+07'),
('ORD-20261126-0002', 'KASIR',   NULL,                                       'Dimas Anggara', 'LUNAS', 'SELESAI',    10, 2,  NULL, NULL, NULL, '2026-11-26 08:45:00+07', '2026-11-26 09:00:00+07'),
('ORD-20261126-0003', 'MANDIRI', 'd4e5f60718293a4b5c6d7e8f90a1b2c3d4e5f607', 'Hendra Wijaya', 'LUNAS', 'DIPROSES',   10, 10, NULL, NULL, NULL, '2026-11-26 09:10:00+07', '2026-11-26 09:15:00+07'),
('ORD-20261126-0004', 'KASIR',   NULL,                                       'Surya Saputra', 'LUNAS', 'DIPROSES',   10, 2,  NULL, NULL, NULL, '2026-11-26 09:20:00+07', '2026-11-26 09:25:00+07'),
('ORD-20261126-0005', 'MANDIRI', 'e5f60718293a4b5c6d7e8f90a1b2c3d4e5f60718', 'Dewi Lestari',  'LUNAS', 'MENUNGGU',   10, 7,  NULL, NULL, NULL, '2026-11-26 09:30:00+07', '2026-11-26 09:32:00+07'),
('ORD-20261126-0006', 'KASIR',   NULL,                                       'Lukman Hakim',  'LUNAS', 'DIBATALKAN', 10, 2,  2, '2026-11-26 09:45:00+07', 'Pelanggan meminta batal karena buru-buru sebelum pesanan diproses barista', '2026-11-26 09:40:00+07', '2026-11-26 09:45:00+07');

-- 6. Tabel detail_pesanan (10 data)
INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah, harga_satuan, catatan, waktu_dibuat, waktu_diperbarui) VALUES
(1,  1,  2, 18000, 'Less sugar',       '2026-11-25 09:00:00+07', '2026-11-25 09:00:00+07'),
(2,  2,  1, 15000, 'Panas, tanpa gula','2026-11-25 10:00:00+07', '2026-11-25 10:00:00+07'),
(3,  3,  1, 22000, 'Normal ice',       '2026-11-25 11:00:00+07', '2026-11-25 11:00:00+07'),
(4,  4,  2, 20000, 'Extra hot',        '2026-11-25 12:30:00+07', '2026-11-25 12:30:00+07'),
(5,  5,  1, 24000, 'Less ice',         '2026-11-26 08:15:00+07', '2026-11-26 08:15:00+07'),
(6,  6,  1, 22000, 'Disajikan hangat', '2026-11-26 08:45:00+07', '2026-11-26 08:45:00+07'),
(7,  15, 2, 22000, 'Less sweet',       '2026-11-26 09:10:00+07', '2026-11-26 09:10:00+07'),
(8,  14, 1, 20000, 'Normal ice',       '2026-11-26 09:20:00+07', '2026-11-26 09:20:00+07'),
(9,  16, 1, 22000, NULL,               '2026-11-26 09:30:00+07', '2026-11-26 09:30:00+07'),
(10, 19, 2, 15000, 'Less ice',         '2026-11-26 09:40:00+07', '2026-11-26 09:40:00+07');

-- 7. Tabel pembayaran (10 data)
INSERT INTO pembayaran (pesanan_id, kasir_id, metode, jumlah_dibayar, uang_diterima, waktu_bayar) VALUES
(1,  2, 'QRIS',  36000, NULL,  '2026-11-25 09:02:00+07'),
(2,  2, 'TUNAI', 15000, 20000, '2026-11-25 10:01:00+07'),
(3,  2, 'QRIS',  22000, NULL,  '2026-11-25 11:02:00+07'),
(4,  2, 'TUNAI', 40000, 50000, '2026-11-25 12:31:00+07'),
(5,  2, 'QRIS',  24000, NULL,  '2026-11-26 08:17:00+07'),
(6,  2, 'TUNAI', 22000, 25000, '2026-11-26 08:46:00+07'),
(7,  2, 'QRIS',  44000, NULL,  '2026-11-26 09:12:00+07'),
(8,  2, 'TUNAI', 20000, 20000, '2026-11-26 09:21:00+07'),
(9,  2, 'QRIS',  22000, NULL,  '2026-11-26 09:32:00+07'),
(10, 2, 'TUNAI', 30000, 30000, '2026-11-26 09:41:00+07');

-- 8. Tabel riwayat_stok (10 data)
INSERT INTO riwayat_stok (bahan_baku_id, pesanan_id, pengguna_id, jenis_perubahan, jumlah_perubahan, keterangan, waktu_dibuat) VALUES
(1, NULL, 1, 'STOK_AWAL',    2500.00,  'Stok awal Biji Kopi Arabika Gayo',                               '2026-11-25 07:30:00+07'),
(2, NULL, 1, 'STOK_AWAL',    5000.00,  'Stok awal Biji Kopi House Blend Pojok',                          '2026-11-25 07:31:00+07'),
(3, NULL, 1, 'STOK_AWAL',    15000.00, 'Stok awal Susu UHT Full Cream',                                  '2026-11-25 07:32:00+07'),
(4, NULL, 1, 'MASUK',        4000.00,  'Restock Gula Aren Cair dari supplier',                           '2026-11-25 07:35:00+07'),
(5, NULL, 1, 'PENYESUAIAN',  -50.00,   'Koreksi stok opname tumpah saat penakaran',                      '2026-11-25 08:00:00+07'),
(2, 1,    2, 'PEMAKAIAN',    -36.00,   'Pemakaian otomatis untuk ORD-20261125-0001',                     '2026-11-25 09:02:00+07'),
(3, 1,    2, 'PEMAKAIAN',    -300.00,  'Pemakaian otomatis untuk ORD-20261125-0001',                     '2026-11-25 09:02:00+07'),
(1, 6,    2, 'PEMAKAIAN',    -15.00,   'Pemakaian otomatis untuk ORD-20261126-0002',                     '2026-11-26 08:46:00+07'),
(9, 10,   2, 'PEMAKAIAN',    -20.00,   'Pemakaian otomatis untuk ORD-20261126-0006',                     '2026-11-26 09:41:00+07'),
(9, 10,   2, 'PENGEMBALIAN', 20.00,    'Pengembalian stok otomatis pembatalan ORD-20261126-0006',        '2026-11-26 09:45:00+07');

-- 9. Tabel riwayat_pesanan (10 data)
INSERT INTO riwayat_pesanan (pesanan_id, status_lama, status_baru, diubah_oleh_id, catatan, waktu_dibuat) VALUES
(1,  NULL,       'MENUNGGU',   7,  'Pesanan mandiri dibuat oleh pelanggan',                         '2026-11-25 09:00:00+07'),
(1,  'MENUNGGU', 'DIPROSES',   5,  'Barista mulai meracik pesanan',                                 '2026-11-25 09:05:00+07'),
(1,  'DIPROSES', 'SELESAI',    5,  'Pesanan selesai dan siap diambil',                              '2026-11-25 09:15:00+07'),
(5,  NULL,       'MENUNGGU',   9,  'Pesanan mandiri dibuat oleh pelanggan',                         '2026-11-26 08:15:00+07'),
(5,  'MENUNGGU', 'DIPROSES',   5,  'Barista mulai meracik pesanan',                                 '2026-11-26 08:20:00+07'),
(5,  'DIPROSES', 'SELESAI',    5,  'Pesanan selesai disajikan',                                     '2026-11-26 08:30:00+07'),
(7,  NULL,       'MENUNGGU',   10, 'Pesanan mandiri dibuat',                                        '2026-11-26 09:10:00+07'),
(7,  'MENUNGGU', 'DIPROSES',   6,  'Sedang dibuat oleh barista',                                    '2026-11-26 09:15:00+07'),
(10, NULL,       'MENUNGGU',   2,  'Pesanan kasir dibuat',                                          '2026-11-26 09:40:00+07'),
(10, 'MENUNGGU', 'DIBATALKAN', 2,  'Dibatalkan oleh kasir atas permintaan pelanggan sebelum diproses', '2026-11-26 09:45:00+07');

-- 10. Tabel log_aktivitas (10 data)
INSERT INTO log_aktivitas (pengguna_id, jenis_aksi, entitas, entitas_id, detail_json, keterangan, waktu_dibuat) VALUES
(1,    'LOGIN_BERHASIL',     'pengguna',   1,    '{"ip": "127.0.0.1", "peran": "OWNER"}'::jsonb,                                                        'Owner berhasil masuk ke sistem', '2026-11-25 07:25:00+07'),
(1,    'STOK_AWAL_BAHAN',    'bahan_baku', 1,    '{"stok_awal": 2500.0, "satuan": "gram"}'::jsonb,                                                      'Input saldo awal Biji Kopi Arabika Gayo', '2026-11-25 07:30:00+07'),
(1,    'PENYESUAIAN_STOK',   'bahan_baku', 5,    '{"selisih": -50.0, "alasan": "Koreksi stok opname"}'::jsonb,                                           'Penyesuaian stok Sirup Caramel', '2026-11-25 08:00:00+07'),
(2,    'BUKA_SHIFT',         'shift',      9,    '{"jenis_shift": "PAGI", "tanggal": "2026-11-25"}'::jsonb,                                              'Kasir membuka shift pagi 2026-11-25', '2026-11-25 08:00:00+07'),
(2,    'TUTUP_SHIFT',        'shift',      9,    '{"total_penjualan": 113000}'::jsonb,                                                                   'Kasir menutup shift pagi 2026-11-25', '2026-11-25 15:00:00+07'),
(1,    'UBAH_HARGA_MENU',    'menu',       1,    '{"harga_lama": 17000, "harga_baru": 18000}'::jsonb,                                                    'Pembaruan harga Kopi Susu Gading', '2026-11-25 16:00:00+07'),
(NULL, 'LOGIN_GAGAL',        'pengguna',   NULL, '{"username_dicoba": "admin_test", "ip": "192.168.1.25"}'::jsonb,                                       'Percobaan login gagal: username tidak ditemukan', '2026-11-26 07:50:00+07'),
(2,    'BUKA_SHIFT',         'shift',      10,   '{"jenis_shift": "PAGI", "tanggal": "2026-11-26"}'::jsonb,                                              'Kasir membuka shift pagi 2026-11-26', '2026-11-26 08:00:00+07'),
(5,    'AKSES_DITOLAK',      'halaman',    NULL, '{"url": "/owner/dashboard", "peran": "BARISTA"}'::jsonb,                                               'Barista mencoba mengakses halaman dashboard Owner', '2026-11-26 09:05:00+07'),
(2,    'PEMBATALAN_PESANAN', 'pesanan',    10,   '{"nomor_pesanan": "ORD-20261126-0006", "alasan": "Pelanggan meminta batal sebelum diproses"}'::jsonb, 'Kasir membatalkan pesanan ORD-20261126-0006 dan mengembalikan stok', '2026-11-26 09:45:00+07');

COMMIT;
