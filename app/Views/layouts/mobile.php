<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pojok UKM Tanjung Gading') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<div class="mobile-wrapper">
    <div class="mobile-shell">
        <header class="mobile-topbar">
            <div class="brand-lockup">
                <img class="brand-logo" src="<?= base_url('assets/logo-pojok-ukm.png') ?>" alt="Logo Pojok UKM">
                <div class="brand-title">POJOK UKM</div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:13px; color:var(--text-muted); font-weight:500;">
                    <?= esc($topbarRight ?? 'Tanjung Gading') ?>
                </span>
                <?php if (session()->has('user')): ?>
                    <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-danger-soft" style="padding:4px 10px; font-size:12px;">Keluar</a>
                <?php endif; ?>
            </div>
        </header>

        <main class="mobile-content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>

        <?= $this->renderSection('stickyBar') ?>

        <nav class="mobile-bottom-nav">
            <?php $activeTab = $activeTab ?? 'beranda'; ?>
            <a href="<?= site_url('pelanggan/katalog') ?>" class="mobile-nav-item <?= $activeTab === 'beranda' ? 'active' : '' ?>">Beranda</a>
            <a href="<?= site_url('pelanggan/status') ?>" class="mobile-nav-item <?= $activeTab === 'pesanan' ? 'active' : '' ?>">Pesanan</a>
            <a href="<?= site_url(session()->has('user') ? 'logout' : 'login') ?>" class="mobile-nav-item <?= $activeTab === 'akun' ? 'active' : '' ?>">
                <?= session()->has('user') ? 'Keluar' : 'Akun' ?>
            </a>
        </nav>
    </div>
</div>
</body>
</html>

