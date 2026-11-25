<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Workspace Owner — Pojok UKM Tanjung Gading') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<header class="staff-topbar">
    <div>
        <div class="brand-title">POJOK UKM TANJUNG GADING</div>
        <div style="font-size:12.5px; color:var(--text-muted);">Workspace Owner · 19 Nov 2026 · 09.41 WIB</div>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <span class="badge badge-green">Toko buka</span>
        <span class="badge badge-gray">Owner · <?= esc(session()->get('user')['nama'] ?? 'Maya') ?></span>
        <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-danger-soft">Keluar</a>
    </div>
</header>

<div class="owner-shell">
    <aside class="owner-sidebar">
        <div>
            <div style="font-size:18px; font-weight:800;">Kelola usaha</div>
            <div style="font-size:12.5px; color:var(--text-muted);">Akses khusus Owner</div>

            <?php $activeNav = $activeNav ?? 'dashboard'; ?>
            <nav class="owner-nav">
                <a href="<?= site_url('owner/dashboard') ?>" class="owner-nav-link <?= $activeNav === 'dashboard' ? 'active' : '' ?>">Dashboard Pemasukan</a>
                <a href="<?= site_url('owner/menu') ?>" class="owner-nav-link <?= $activeNav === 'menu' ? 'active' : '' ?>">Katalog Menu</a>
                <a href="<?= site_url('stok') ?>" class="owner-nav-link <?= $activeNav === 'stok' ? 'active' : '' ?>">Stok Bahan Baku</a>
                <a href="<?= site_url('owner/pengguna') ?>" class="owner-nav-link <?= $activeNav === 'pengguna' ? 'active' : '' ?>">Staf &amp; Riwayat</a>
            </nav>
        </div>

        <div style="margin-top:24px;">
            <?php $shift = \App\Services\MockDataService::getShift(); ?>
            <div class="card" style="background:var(--surface-muted); padding:12px 14px; margin-bottom:12px;">
                <div style="font-weight:700; font-size:13px;">Shift <?= esc(ucfirst(strtolower($shift['jenis'] ?? 'Pagi'))) ?></div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                    <?= esc($shift['jam'] ?? '08.00–15.00 WIB') ?><br>
                    Kasir: <?= esc($shift['kasir'] ?? 'Rina') ?><br>
                    Barista: <?= esc($shift['barista'] ?? 'Adi') ?>
                </div>
            </div>
            <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-danger-soft btn-block">Keluar akun</a>
        </div>
    </aside>

    <main class="owner-main">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>
</div>
</body>
</html>

