<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Masuk — Pojok UKM Tanjung Gading') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="auth-body">
    <header class="auth-topbar">
        <div class="auth-topbar-inner">
            <div class="brand-title">POJOK UKM</div>
            <div style="font-size:13px; color:var(--text-muted); font-weight:500;">
                <?= esc($topbarRight ?? 'Tanjung Gading') ?>
            </div>
        </div>
    </header>

    <main class="auth-container">
        <div class="auth-grid">
            <!-- Panel Info Kedai (tampil elegan di tablet/desktop, ringkas di HP) -->
            <section class="auth-brand-panel">
                <span class="badge badge-green" style="margin-bottom:14px;">Buka · 08.00–22.00 WIB</span>
                <h1 class="auth-hero-title">Pojok UKM<br>Tanjung Gading</h1>
                <p class="auth-hero-desc">
                    Sistem operasional dan pemesanan kedai kopi. Pelanggan dapat memesan langsung dari HP atau tablet, sementara Kasir, Barista, dan Owner terhubung dalam satu sistem.
                </p>
                <div class="auth-feature-list">
                    <div class="auth-feature-item">
                        <strong>Pelanggan</strong>
                        <span>Lihat katalog menu, pesan Minum di Tempat atau Take Away, dan pantau status.</span>
                    </div>
                    <div class="auth-feature-item">
                        <strong>Staf &amp; Owner</strong>
                        <span>Kelola POS kasir, antrean barista, stok bahan baku, dan laporan pemasukan.</span>
                    </div>
                </div>
            </section>

            <!-- Panel Form Masuk / Daftar -->
            <section class="auth-form-panel">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </section>
        </div>
    </main>
</body>
</html>
