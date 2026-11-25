<?= $this->extend($isOwner ? 'layouts/owner' : 'layouts/staff') ?>

<?php if (! $isOwner): ?>
    <?php $this->setData(['staffSubtitle' => 'Barista · Stok Bahan Baku']); ?>
    <?= $this->section('topbarActions') ?>
        <a href="<?= site_url('barista/antrean') ?>" class="btn btn-sm">← Antrean Barista</a>
    <?= $this->endSection() ?>
<?php endif; ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
    <h1 style="font-size:26px; font-weight:800; margin:0;">Stok bahan baku</h1>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('form-tambah-bahan').style.display='block'">+ Tambah stok</button>
</div>

<!-- Form Tambah Bahan Baku (FR-16) -->
<div id="form-tambah-bahan" class="card" style="display:none; margin-bottom:16px;">
    <h3 style="margin:0 0 12px; font-size:16px; font-weight:800;">Tambah Bahan Baku Baru (STOK_AWAL)</h3>
    <form action="<?= site_url('stok/tambah') ?>" method="post" style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:10px; align-items:end;">
        <?= csrf_field() ?>
        <div>
            <label class="form-label">Nama Bahan</label>
            <input type="text" name="nama" class="input" placeholder="Contoh: Gula Aren" required>
        </div>
        <div>
            <label class="form-label">Stok Awal</label>
            <input type="number" step="0.1" name="stok" class="input" placeholder="1000" required>
        </div>
        <div>
            <label class="form-label">Minimum</label>
            <input type="number" step="0.1" name="minimum" class="input" placeholder="500" required>
        </div>
        <div>
            <label class="form-label">Satuan</label>
            <input type="text" name="satuan" class="input" placeholder="g / liter / pcs" required>
        </div>
        <div style="display:flex; gap:6px;">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn" onclick="document.getElementById('form-tambah-bahan').style.display='none'">Batal</button>
        </div>
    </form>
</div>

<div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-bottom:14px;">
    <div class="pill-group">
        <a href="<?= site_url('stok?tab=daftar') ?>" class="pill <?= $tab === 'daftar' ? 'active' : '' ?>">Daftar stok</a>
        <a href="<?= site_url('stok?tab=mutasi') ?>" class="pill <?= $tab === 'mutasi' ? 'active' : '' ?>">Riwayat Mutasi</a>
    </div>
    <form action="<?= site_url('stok') ?>" method="get" style="flex:1; min-width:220px;">
        <input type="text" name="q" class="input" placeholder="Cari bahan baku…" value="<?= esc($search) ?>">
    </form>
</div>

<div class="alert alert-amber">
    2 bahan menipis · 1 kritis. Segera tambah kopi, cup, dan susu.
</div>

<?php if ($tab === 'daftar'): ?>
    <div class="table-wrap" style="margin-bottom:16px;">
        <table>
            <thead>
                <tr>
                    <th>Bahan baku</th>
                    <th>Stok saat ini</th>
                    <th>Minimum</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bahan as $b): ?>
                    <tr>
                        <td style="font-weight:700;"><?= esc($b['nama']) ?></td>
                        <td><?= esc($b['stok']) ?> <?= esc($b['satuan']) ?></td>
                        <td><?= esc($b['minimum']) ?> <?= esc($b['satuan']) ?></td>
                        <td>
                            <?php
                            $badgeClass = $b['status'] === 'Aman' ? 'badge-green' : ($b['status'] === 'Kritis' ? 'badge-red' : 'badge-amber');
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= esc($b['status']) ?></span>
                        </td>
                        <td>
                            <form action="<?= site_url('stok/koreksi/' . $b['id']) ?>" method="post" style="display:flex; gap:6px; align-items:center;">
                                <?= csrf_field() ?>
                                <input type="number" step="0.5" name="delta" class="input" style="width:90px; padding:5px 8px;" placeholder="+10" value="10">
                                <button type="submit" class="btn btn-sm">Koreksi →</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h3 style="font-size:15px; font-weight:800; margin:0;">Mutasi terbaru</h3>
        <a href="<?= site_url('stok?tab=mutasi') ?>" style="font-size:13px; font-weight:600;">Lihat Riwayat Mutasi →</a>
    </div>
    <?php foreach ($mutasi as $m): ?>
        <div style="font-size:13px; color:var(--text-muted); padding:4px 0;">
            <?= esc($m['waktu']) ?> / <strong style="color:var(--text);"><?= esc($m['keterangan']) ?></strong> / <?= esc($m['sumber']) ?> / <?= esc($m['pelaku']) ?>
        </div>
    <?php endforeach; ?>
</div>

<p style="font-size:12px; color:var(--text-muted); margin-top:12px;">
    Stok berkurang saat lunas. Koreksi &amp; pembatalan masuk ke Riwayat Mutasi.
</p>
<?= $this->endSection() ?>

