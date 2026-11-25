<?= $this->extend('layouts/owner') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
    <h1 style="font-size:26px; font-weight:800; margin:0;">Staf &amp; riwayat</h1>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('form-tambah-staf').style.display='block'">+ Tambah staf</button>
</div>

<!-- Form Tambah Staf -->
<div id="form-tambah-staf" class="card" style="display:none; margin-bottom:16px;">
    <h3 style="margin:0 0 12px; font-size:16px; font-weight:800;">Tambah Akun Staf Baru</h3>
    <form action="<?= site_url('owner/pengguna/tambah') ?>" method="post" style="display:grid; grid-template-columns:1.5fr 1.5fr 1fr auto; gap:10px; align-items:end;">
        <?= csrf_field() ?>
        <div>
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="input" placeholder="Nama staf" required>
        </div>
        <div>
            <label class="form-label">Username</label>
            <input type="text" name="username" class="input" placeholder="contoh: siti.kasir" required>
        </div>
        <div>
            <label class="form-label">Role</label>
            <select name="role" class="input">
                <option value="Kasir">Kasir</option>
                <option value="Barista">Barista</option>
            </select>
        </div>
        <div style="display:flex; gap:6px;">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn" onclick="document.getElementById('form-tambah-staf').style.display='none'">Batal</button>
        </div>
    </form>
</div>

<div class="pill-group" style="margin-bottom:14px;">
    <a href="<?= site_url('owner/pengguna?tab=staf') ?>" class="pill <?= $tab === 'staf' ? 'active' : '' ?>">Data Akun Staf</a>
    <a href="<?= site_url('owner/pengguna?tab=shift') ?>" class="pill <?= $tab === 'shift' ? 'active' : '' ?>">Riwayat Staf</a>
    <a href="<?= site_url('owner/pengguna?tab=audit') ?>" class="pill <?= $tab === 'audit' ? 'active' : '' ?>">Riwayat Akun / Audit Log</a>
</div>

<form action="<?= site_url('owner/pengguna') ?>" method="get" style="display:flex; gap:10px; margin-bottom:14px;">
    <input type="hidden" name="tab" value="<?= esc($tab) ?>">
    <input type="text" name="q" class="input" placeholder="Cari nama / username…" value="<?= esc($search) ?>">
    <select name="role" class="input" style="max-width:160px;" onchange="this.form.submit()">
        <option value="Semua" <?= $role === 'Semua' ? 'selected' : '' ?>>Semua role ▾</option>
        <option value="Kasir" <?= $role === 'Kasir' ? 'selected' : '' ?>>Kasir</option>
        <option value="Barista" <?= $role === 'Barista' ? 'selected' : '' ?>>Barista</option>
    </select>
</form>

<!-- Tabel Data Akun Staf -->
<div class="table-wrap" style="margin-bottom:20px;">
    <table>
        <thead>
            <tr>
                <th>Nama / Username</th>
                <th>Role</th>
                <th>Status akun</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staff as $s): ?>
                <tr>
                    <td style="font-weight:700;"><?= esc($s['nama']) ?> / <span style="font-weight:500; color:var(--text-muted);"><?= esc($s['username']) ?></span></td>
                    <td><?= esc($s['role']) ?></td>
                    <td>
                        <span class="badge <?= $s['aktif'] ? 'badge-green' : 'badge-red' ?>">
                            <?= $s['aktif'] ? 'Aktif · ' . esc($s['shift']) : 'Nonaktif' ?>
                        </span>
                    </td>
                    <td style="display:flex; gap:6px; flex-wrap:wrap;">
                        <form action="<?= site_url('owner/pengguna/' . $s['id'] . '/reset-password') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm">Reset Password</button>
                        </form>
                        <form action="<?= site_url('owner/pengguna/' . $s['id'] . '/toggle') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm"><?= $s['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Riwayat Staf -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h2 style="font-size:17px; font-weight:800; margin:0;">Riwayat Staf</h2>
    <span style="font-size:13px; color:var(--text-muted);">Semua riwayat →</span>
</div>
<div class="table-wrap" style="margin-bottom:20px;">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Staf / Shift</th>
                <th>Masuk–Keluar WIB</th>
                <th>Toko Buka–Tutup WIB</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($riwayatShift as $rs): ?>
                <tr>
                    <td><?= esc($rs['tanggal']) ?></td>
                    <td><?= esc($rs['staf']) ?></td>
                    <td><?= esc($rs['masuk_keluar']) ?></td>
                    <td><?= esc($rs['jam_toko']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Riwayat Akun / Audit Log -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h2 style="font-size:17px; font-weight:800; margin:0;">Riwayat Akun / Audit Log</h2>
    <span style="font-size:13px; color:var(--text-muted);">Terbaru</span>
</div>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Waktu WIB</th>
                <th>Pelaku</th>
                <th>Aktivitas</th>
                <th>Objek</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($audit as $a): ?>
                <tr>
                    <td><?= esc($a['waktu']) ?></td>
                    <td><?= esc($a['pelaku']) ?></td>
                    <td><?= esc($a['aktivitas']) ?></td>
                    <td><?= esc($a['objek']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<p style="font-size:12px; color:var(--text-muted); margin-top:12px;">
    Perubahan akun, akses, jam toko, dan transaksi tercatat untuk penelusuran Owner.
</p>
<?= $this->endSection() ?>

