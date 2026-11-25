<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<h1 style="font-size:24px; font-weight:800; margin:0 0 14px;">Pesan &amp; bayar</h1>

<?php if (empty($cart)): ?>
    <div class="card" style="text-align:center; padding:28px;">
        <p style="color:var(--text-muted); margin:0 0 14px;">Keranjang Anda masih kosong.</p>
        <a href="<?= site_url('pelanggan/katalog') ?>" class="btn btn-primary">Pilih Menu</a>
    </div>
<?php else: ?>
    <form action="<?= site_url('pelanggan/checkout') ?>" method="post">
        <?= csrf_field() ?>

        <!-- Cara menikmati: Hanya Minum di Tempat & Take Away (Tanpa Antar Driver/Ongkir/Alamat) -->
        <div style="font-weight:700; font-size:14px; margin-bottom:8px;">Cara menikmati</div>
        <input type="hidden" name="layanan" id="input-layanan" value="Minum di Tempat">
        <div class="pill-group" style="margin-bottom:14px;">
            <button type="button" class="pill layanan-pill active" data-val="Minum di Tempat" onclick="setLayanan(this)">Minum di Tempat</button>
            <button type="button" class="pill layanan-pill" data-val="Take Away" onclick="setLayanan(this)">Take Away</button>
        </div>

        <!-- Daftar Item Keranjang -->
        <?php foreach ($cart as $item): ?>
            <div class="card" style="margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px;">
                    <div>
                        <div style="font-weight:700; font-size:15px;"><?= esc($item['nama']) ?></div>
                        <div style="color:var(--text-muted); font-size:13px;">Rp <?= number_format($item['harga'], 0, ',', '.') ?> / cup</div>
                        <div style="color:var(--text-muted); font-size:12px; margin-top:4px;">
                            Jumlah <?= esc($item['qty']) ?> · Subtotal Rp <?= number_format($item['qty'] * $item['harga'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px; background:var(--surface-muted); padding:4px 8px; border-radius:var(--radius-md);">
                        <button type="submit" formaction="<?= site_url('pelanggan/keranjang/update') ?>" name="delta" value="-1"
                                onclick="document.getElementById('upd-menu-id').value='<?= esc($item['menu_id']) ?>'"
                                style="border:none; background:none; font-weight:700; cursor:pointer; padding:4px 6px;">−</button>
                        <span style="font-weight:700; min-width:16px; text-align:center;"><?= esc($item['qty']) ?></span>
                        <button type="submit" formaction="<?= site_url('pelanggan/keranjang/update') ?>" name="delta" value="1"
                                onclick="document.getElementById('upd-menu-id').value='<?= esc($item['menu_id']) ?>'"
                                style="border:none; background:none; font-weight:700; cursor:pointer; padding:4px 6px;">+</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <input type="hidden" name="menu_id" id="upd-menu-id" value="">

        <div class="form-group">
            <input type="text" name="catatan" class="input" placeholder="Catatan racikan · Less sugar, tanpa es" value="<?= esc($cart[0]['catatan'] ?? '') ?>">
        </div>

        <!-- Pilih Pembayaran -->
        <div style="font-weight:700; font-size:14px; margin:14px 0 8px;">Pilih pembayaran</div>
        <input type="hidden" name="metode" id="input-metode" value="QRIS">
        <div class="pill-group" style="margin-bottom:12px;">
            <button type="button" class="pill metode-pill active" data-val="QRIS" onclick="setMetode(this)">● QRIS statis</button>
            <button type="button" class="pill metode-pill" data-val="Tunai" onclick="setMetode(this)">○ Tunai di kasir</button>
        </div>

        <div class="card" style="display:flex; gap:12px; align-items:center; margin-bottom:14px;">
            <div style="width:58px; height:58px; background:var(--surface-muted); border-radius:10px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; color:var(--text-muted); flex-shrink:0;">
                QRIS
            </div>
            <div style="font-size:12.5px; color:var(--text-muted);">
                <div style="font-weight:700; color:var(--text); margin-bottom:2px;">Scan QRIS toko</div>
                Bayar sesuai total. Diverifikasi kasir.<br>
                Tunai: bayar saat tiba di kasir.
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:13px; color:var(--text-muted); margin-bottom:6px;">
            <span>Subtotal · <?= esc($totalQty) ?> cup</span>
            <span>Rp <?= number_format($totalRp, 0, ',', '.') ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:17px; font-weight:800; margin-bottom:16px;">
            <span>Total bayar</span>
            <span>Rp <?= number_format($totalRp, 0, ',', '.') ?></span>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Bayar Rp <?= number_format($totalRp, 0, ',', '.') ?></button>
    </form>

    <script>
    function setLayanan(btn) {
        document.querySelectorAll('.layanan-pill').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('input-layanan').value = btn.dataset.val;
    }
    function setMetode(btn) {
        document.querySelectorAll('.metode-pill').forEach(el => {
            el.classList.remove('active');
            el.textContent = el.textContent.replace('●', '○');
        });
        btn.classList.add('active');
        btn.textContent = btn.textContent.replace('○', '●');
        document.getElementById('input-metode').value = btn.dataset.val;
    }
    </script>
<?php endif; ?>
<?= $this->endSection() ?>

