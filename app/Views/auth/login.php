<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<h1 style="font-size:24px; font-weight:800; margin:0 0 4px;">Selamat datang</h1>
<p style="color:var(--text-muted); margin:0 0 16px;">Pojok UKM Tanjung Gading</p>

<div class="pill-group" style="margin-bottom:16px;">
    <button type="button" id="tab-masuk" class="pill <?= ($mode ?? 'login') === 'login' ? 'active' : '' ?>" onclick="switchAuthTab('masuk')">Masuk</button>
    <button type="button" id="tab-daftar" class="pill <?= ($mode ?? 'login') === 'register' ? 'active' : '' ?>" onclick="switchAuthTab('daftar')">Daftar Pelanggan</button>
</div>

<!-- Form Masuk -->
<div id="panel-masuk" style="display:<?= ($mode ?? 'login') === 'login' ? 'block' : 'none' ?>;">
    <form action="<?= site_url('login') ?>" method="post" class="card" style="margin-bottom:18px;">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="login-username">Username</label>
            <input type="text" id="login-username" name="username" class="input" placeholder="Masukkan username" value="ayu.pelanggan">
        </div>
        <div class="form-group">
            <label class="form-label" for="login-password">Password</label>
            <input type="password" id="login-password" name="password" class="input" placeholder="••••••••" value="password123">
        </div>

        <div class="form-group">
            <label class="form-label">Pilih Peran (Demo Tanpa DB)</label>
            <input type="hidden" name="role" id="selected-role" value="pelanggan">
            <div class="pill-group">
                <button type="button" class="pill role-pill active" data-role="pelanggan" data-user="ayu.pelanggan" onclick="selectRole(this)">Pelanggan</button>
                <button type="button" class="pill role-pill" data-role="kasir" data-user="rina.kasir" onclick="selectRole(this)">Kasir</button>
                <button type="button" class="pill role-pill" data-role="barista" data-user="adi.barista" onclick="selectRole(this)">Barista</button>
                <button type="button" class="pill role-pill" data-role="owner" data-user="maya.owner" onclick="selectRole(this)">Owner</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top:6px;">Masuk</button>
    </form>
</div>

<!-- Form Daftar Pelanggan (Tanpa Input WhatsApp sesuai SRS Final) -->
<div id="panel-daftar" style="display:<?= ($mode ?? 'login') === 'register' ? 'block' : 'none' ?>;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <h2 style="font-size:16px; font-weight:700; margin:0;">Daftar Pelanggan</h2>
        <span style="font-size:12.5px; color:var(--text-muted);">Pelanggan baru</span>
    </div>
    <form action="<?= site_url('register') ?>" method="post" class="card" style="margin-bottom:14px;">
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="form-label" for="reg-nama">Nama lengkap</label>
            <input type="text" id="reg-nama" name="nama_lengkap" class="input" placeholder="Nama lengkap" required value="<?= old('nama_lengkap') ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="reg-user">Username baru</label>
            <input type="text" id="reg-user" name="username" class="input" placeholder="Username baru (unik)" required value="<?= old('username') ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="reg-pass">Password baru</label>
            <input type="password" id="reg-pass" name="password" class="input" placeholder="min. 8 karakter" minlength="8" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Daftar sebagai pelanggan</button>
    </form>
</div>

<p style="font-size:12.5px; color:var(--text-muted); margin-top:8px;">
    Akun staf dibuat dan dikelola oleh Owner.
</p>

<script>
function switchAuthTab(tab) {
    document.getElementById('panel-masuk').style.display = tab === 'masuk' ? 'block' : 'none';
    document.getElementById('panel-daftar').style.display = tab === 'daftar' ? 'block' : 'none';
    document.getElementById('tab-masuk').classList.toggle('active', tab === 'masuk');
    document.getElementById('tab-daftar').classList.toggle('active', tab === 'daftar');
}

function selectRole(btn) {
    document.querySelectorAll('.role-pill').forEach(el => el.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('selected-role').value = btn.dataset.role;
    document.getElementById('login-username').value = btn.dataset.user;
}
</script>
<?= $this->endSection() ?>

