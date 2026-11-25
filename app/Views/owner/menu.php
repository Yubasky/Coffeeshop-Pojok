<?= $this->extend('layouts/owner') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
    <div>
        <h1 style="font-size:26px; font-weight:800; margin:0;">Katalog Menu</h1>
        <p style="font-size:13px; color:var(--text-muted); margin:2px 0 0;">Kelola daftar menu, harga, dan ketersediaan aktif (FR-14 &amp; FR-15).</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('form-tambah-menu').style.display='block'">+ Tambah Menu</button>
</div>

<!-- Form Tambah Menu -->
<div id="form-tambah-menu" class="card" style="display:none; margin-bottom:16px;">
    <h3 style="margin:0 0 12px; font-size:16px; font-weight:800;">Tambah Menu Baru</h3>
    <form action="<?= site_url('owner/menu/tambah') ?>" method="post" style="display:grid; grid-template-columns:2fr 1fr 1fr auto; gap:10px; align-items:end;">
        <?= csrf_field() ?>
        <div>
            <label class="form-label">Nama Menu</label>
            <input type="text" name="nama" class="input" placeholder="Contoh: Kopi Susu Gading" required>
        </div>
        <div>
            <label class="form-label">Kategori</label>
            <select name="kategori" class="input">
                <option value="KOPI">Kopi</option>
                <option value="NON_KOPI">Non-Kopi</option>
            </select>
        </div>
        <div>
            <label class="form-label">Harga (Rp)</label>
            <input type="number" name="harga" class="input" placeholder="18000" min="1000" required>
        </div>
        <div style="display:flex; gap:6px;">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn" onclick="document.getElementById('form-tambah-menu').style.display='none'">Batal</button>
        </div>
    </form>
</div>

<!-- Filter & Search -->
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
    <div class="pill-group">
        <a href="<?= site_url('owner/menu?kategori=SEMUA&q=' . urlencode($search)) ?>" class="pill <?= $kategori === 'SEMUA' ? 'active' : '' ?>">Semua (<?= esc($totalAllMenus ?? count($menus)) ?>)</a>
        <a href="<?= site_url('owner/menu?kategori=KOPI&q=' . urlencode($search)) ?>" class="pill <?= $kategori === 'KOPI' ? 'active' : '' ?>">Kopi</a>
        <a href="<?= site_url('owner/menu?kategori=NON_KOPI&q=' . urlencode($search)) ?>" class="pill <?= $kategori === 'NON_KOPI' ? 'active' : '' ?>">Non-Kopi</a>
    </div>
    <form action="<?= site_url('owner/menu') ?>" method="get" style="min-width:260px;">
        <input type="hidden" name="kategori" value="<?= esc($kategori) ?>">
        <input type="text" name="q" class="input" placeholder="Cari nama menu…" value="<?= esc($search) ?>">
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menus as $m): ?>
                <tr>
                    <td style="font-weight:700;"><?= esc($m['nama']) ?></td>
                    <td><span class="badge badge-gray"><?= esc($m['kategori']) ?></span></td>
                    <td>
                        <form action="<?= site_url('owner/menu/' . $m['id'] . '/update') ?>" method="post" style="display:flex; gap:6px; align-items:center;">
                            <?= csrf_field() ?>
                            <input type="number" name="harga" value="<?= esc($m['harga']) ?>" class="input" style="width:110px; padding:6px 10px;">
                            <button type="submit" class="btn btn-sm">Ubah</button>
                        </form>
                    </td>
                    <td>
                        <span class="badge <?= $m['aktif'] ? 'badge-green' : 'badge-red' ?>">
                            <?= $m['aktif'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </td>
                    <td>
                        <form action="<?= site_url('owner/menu/' . $m['id'] . '/toggle') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm">
                                <?= $m['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

