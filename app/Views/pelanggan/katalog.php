<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
    <div>
        <h1 style="font-size:24px; font-weight:800; margin:0 0 2px;">Mau ngopi apa?</h1>
        <div style="font-size:12.5px; color:var(--text-muted);">Kamis, 19 Nov 2026 · 08.00–22.00 WIB</div>
    </div>
    <span class="badge badge-green">Buka</span>
</div>

<form action="<?= site_url('pelanggan/katalog') ?>" method="get" style="margin:14px 0 12px;">
    <input type="hidden" name="kategori" value="<?= esc($kategori) ?>">
    <input type="text" name="q" class="input" placeholder="Cari kopi atau minuman favorit…" value="<?= esc($search) ?>">
</form>

<div class="pill-group" style="margin-bottom:16px;">
    <a href="<?= site_url('pelanggan/katalog?kategori=SEMUA&q=' . urlencode($search)) ?>" class="pill <?= $kategori === 'SEMUA' ? 'active' : '' ?>">Semua</a>
    <a href="<?= site_url('pelanggan/katalog?kategori=KOPI&q=' . urlencode($search)) ?>" class="pill <?= $kategori === 'KOPI' ? 'active' : '' ?>">Kopi</a>
    <a href="<?= site_url('pelanggan/katalog?kategori=NON_KOPI&q=' . urlencode($search)) ?>" class="pill <?= $kategori === 'NON_KOPI' ? 'active' : '' ?>">Non-Kopi</a>
</div>

<?php if (empty($menus)): ?>
    <div class="card" style="text-align:center; color:var(--text-muted); padding:28px;">
        Menu tidak ditemukan untuk pencarian "<strong><?= esc($search) ?></strong>".
    </div>
<?php else: ?>
    <div class="menu-grid">
        <?php foreach ($menus as $menu): ?>
            <div class="menu-card">
                <div class="menu-thumb">
                    <?= $menu['kategori'] === 'KOPI' ? '☕ Foto kopi' : '🧋 Foto minuman' ?>
                </div>
                <div style="font-weight:700; font-size:14px; line-height:1.3;"><?= esc($menu['nama']) ?></div>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:12.5px;">
                    <span style="font-weight:700;">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></span>
                    <span style="color:var(--text-muted);">Aktif</span>
                </div>
                <form action="<?= site_url('pelanggan/keranjang/tambah') ?>" method="post" style="margin-top:auto;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="menu_id" value="<?= esc($menu['id']) ?>">
                    <button type="submit" class="btn btn-sm btn-block">+ Pesan</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<p style="font-size:12px; color:var(--text-muted); margin-top:14px;">
    Diseduh segar setelah pembayaran terkonfirmasi.
</p>
<?= $this->endSection() ?>

<?= $this->section('stickyBar') ?>
<?php if (($totalQty ?? 0) > 0): ?>
    <a href="<?= site_url('pelanggan/checkout') ?>" class="sticky-cart-bar">
        <div>
            <div style="font-weight:700; font-size:14px;"><?= esc($totalQty) ?> item · Rp <?= number_format($totalRp, 0, ',', '.') ?></div>
            <div style="font-size:12px; opacity:.85;"><?= esc($cart[0]['nama'] ?? '') ?></div>
        </div>
        <div style="font-weight:700; font-size:13.5px;">Lihat keranjang →</div>
    </a>
<?php endif; ?>
<?= $this->endSection() ?>

