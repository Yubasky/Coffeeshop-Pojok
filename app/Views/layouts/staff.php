<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Workspace Staf — Pojok UKM Tanjung Gading') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<header class="staff-topbar">
    <div class="brand-lockup">
        <img class="brand-logo" src="<?= base_url('assets/logo-pojok-ukm.png') ?>" alt="Logo Pojok UKM">
        <div>
            <div class="brand-title">POJOK UKM TANJUNG GADING</div>
            <div style="font-size:12.5px; color:var(--text-muted);">
                <?= esc($staffSubtitle ?? 'Operasional Kedai · 19 Nov 2026') ?>
            </div>
        </div>
    </div>
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <?= $this->renderSection('topbarActions') ?>
        <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-danger-soft">Keluar</a>
    </div>
</header>

<main class="staff-container">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>
</body>
</html>

