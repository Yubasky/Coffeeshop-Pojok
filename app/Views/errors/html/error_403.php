<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Akses Ditolak — Pojok UKM Tanjung Gading</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<div class="mobile-wrapper">
    <div class="mobile-shell" style="justify-content:center; padding: 24px;">
        <div class="card" style="text-align:center; padding: 28px 20px;">
            <span class="badge badge-red" style="margin-bottom: 12px;">HTTP 403 Forbidden</span>
            <h2 style="margin: 0 0 8px;">Akses Ditolak</h2>
            <p style="color: var(--text-muted); margin: 0 0 18px;">
                Peran Anda saat ini (<strong><?= esc($role ?? 'GUEST') ?></strong>) tidak memiliki izin untuk mengakses halaman
                <code>/<?= esc($path ?? '') ?></code>. Kejadian ini telah dicatat ke Audit Log.
            </p>
            <a href="<?= site_url('login') ?>" class="btn btn-primary btn-block">Kembali ke Halaman Masuk</a>
        </div>
    </div>
</div>
</body>
</html>

