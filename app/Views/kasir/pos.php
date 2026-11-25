<?= $this->extend('layouts/staff') ?>

<?php $this->setData(['staffSubtitle' => 'Kasir · ' . ($shift['kasir'] ?? 'Rina') . ' / 19 Nov 2026 · 09.41 WIB']); ?>

<?= $this->section('topbarActions') ?>
<form action="<?= site_url('kasir/shift/toggle') ?>" method="post" style="display:flex; align-items:center; gap:8px;">
    <?= csrf_field() ?>
    <button type="submit" name="jenis" value="PAGI" class="pill <?= ($shift['jenis'] ?? 'PAGI') === 'PAGI' ? 'active' : '' ?>">Pagi</button>
    <button type="submit" name="jenis" value="SORE" class="pill <?= ($shift['jenis'] ?? 'PAGI') === 'SORE' ? 'active' : '' ?>">Sore</button>

    <?php if (! empty($shift['aktif'])): ?>
        <span class="badge badge-green">Toko buka</span>
        <button type="submit" name="action" value="tutup" class="btn btn-sm">Tutup shift</button>
    <?php else: ?>
        <span class="badge badge-red">Shift tutup</span>
        <button type="submit" name="action" value="buka" class="btn btn-sm btn-primary">Buka shift</button>
    <?php endif; ?>
</form>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="split-grid">
    <!-- Kolom Kiri: Antrean Masuk -->
    <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <h1 style="font-size:24px; font-weight:800; margin:0;">Antrean masuk</h1>
            <span class="badge badge-gray"><?= esc($totalOrders) ?> pesanan</span>
        </div>
        <p style="color:var(--text-muted); margin:0 0 14px;">Pesanan online &amp; offline dalam satu antrean.</p>

        <div class="pill-group" style="margin-bottom:8px;">
            <a href="<?= site_url('kasir/pos?kanal=Semua&status=' . urlencode($statusFilter) . '&q=' . urlencode($search)) ?>" class="pill <?= $kanalFilter === 'Semua' ? 'active' : '' ?>">Semua</a>
            <a href="<?= site_url('kasir/pos?kanal=Online&status=' . urlencode($statusFilter) . '&q=' . urlencode($search)) ?>" class="pill <?= $kanalFilter === 'Online' ? 'active' : '' ?>">Online · <?= esc($onlineCount) ?></a>
            <a href="<?= site_url('kasir/pos?kanal=Offline&status=' . urlencode($statusFilter) . '&q=' . urlencode($search)) ?>" class="pill <?= $kanalFilter === 'Offline' ? 'active' : '' ?>">Offline · <?= esc($offlineCount) ?></a>
        </div>

        <div class="pill-group" style="margin-bottom:12px;">
            <a href="<?= site_url('kasir/pos?kanal=' . urlencode($kanalFilter) . '&status=Semua&q=' . urlencode($search)) ?>" class="pill <?= $statusFilter === 'Semua' ? 'active' : '' ?>">Semua status</a>
            <a href="<?= site_url('kasir/pos?kanal=' . urlencode($kanalFilter) . '&status=Belum+Dibayar&q=' . urlencode($search)) ?>" class="pill <?= $statusFilter === 'Belum Dibayar' ? 'active' : '' ?>">Belum Dibayar</a>
            <a href="<?= site_url('kasir/pos?kanal=' . urlencode($kanalFilter) . '&status=Lunas&q=' . urlencode($search)) ?>" class="pill <?= $statusFilter === 'Lunas' ? 'active' : '' ?>">Lunas</a>
        </div>

        <form action="<?= site_url('kasir/pos') ?>" method="get" style="margin-bottom:14px;">
            <input type="hidden" name="kanal" value="<?= esc($kanalFilter) ?>">
            <input type="hidden" name="status" value="<?= esc($statusFilter) ?>">
            <input type="text" name="q" class="input" placeholder="Cari nomor pesanan / nama pelanggan" value="<?= esc($search) ?>">
        </form>

        <div style="display:flex; flex-direction:column; gap:10px;">
            <?php foreach ($orders as $o): ?>
                <a href="<?= site_url('kasir/pembayaran/' . $o['id']) ?>" class="card" style="display:block; transition:border-color .15s;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <div style="font-weight:700;">
                            <?= esc($o['nomor_pendek']) ?> · <?= esc($o['pelanggan']) ?> · <?= esc($o['kanal']) ?> · <?= esc($o['layanan']) ?>
                        </div>
                        <span class="badge <?= $o['status_bayar'] === 'Lunas' ? 'badge-green' : 'badge-amber' ?>">
                            <?= esc($o['status_bayar']) ?>
                        </span>
                    </div>
                    <div style="font-size:13px; color:var(--text-muted); margin-bottom:6px;">
                        <?php
                        $summary = array_map(static fn($i) => $i['qty'] . ' ' . $i['nama'], $o['items']);
                        echo esc(implode(' + ', $summary));
                        if ($o['status_bar'] === 'Diproses') echo ' · Diproses';
                        ?>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12.5px; color:var(--text-muted);"><?= esc($o['waktu']) ?></span>
                        <span style="font-weight:800;">Rp <?= number_format($o['total'], 0, ',', '.') ?> →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Kolom Kanan: POS Pesanan Offline (Tanpa Input Nomor Meja sesuai SRS Final) -->
    <div class="card" style="padding:20px;">
        <h2 style="font-size:20px; font-weight:800; margin:0 0 14px;">POS · Pesanan offline</h2>

        <form action="<?= site_url('kasir/pos/offline') ?>" method="post" id="pos-form">
            <?= csrf_field() ?>
            <input type="hidden" name="items_json" id="pos-items-json" value="">
            <input type="hidden" name="layanan" id="pos-layanan" value="Minum di Tempat">

            <div class="form-group">
                <input type="text" name="pelanggan" class="input" placeholder="Nama pelanggan · Pelanggan langsung">
            </div>

            <div class="pill-group" style="margin-bottom:14px;">
                <button type="button" class="pill pos-layanan active" onclick="setPosLayanan(this, 'Minum di Tempat')">Minum di Tempat</button>
                <button type="button" class="pill pos-layanan" onclick="setPosLayanan(this, 'Take Away')">Take Away</button>
            </div>

            <div class="menu-grid" style="margin-bottom:16px;">
                <?php foreach ($menus as $m): ?>
                    <button type="button" class="card" style="padding:10px 12px; text-align:left; cursor:pointer; background:var(--surface-muted);"
                            onclick="addPosItem(<?= (int) $m['id'] ?>, '<?= esc($m['nama'], 'js') ?>', <?= (int) $m['harga'] ?>)">
                        <div style="font-weight:700; font-size:13.5px;"><?= esc($m['nama']) ?></div>
                        <div style="display:flex; justify-content:space-between; font-size:12.5px; color:var(--text-muted); margin-top:4px;">
                            <span>Rp <?= number_format($m['harga'], 0, ',', '.') ?></span>
                            <span style="font-weight:800; color:var(--text);">+</span>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>

            <div style="font-weight:700; font-size:13.5px; margin-bottom:8px;">Pesanan baru</div>
            <div id="pos-cart-list" style="margin-bottom:10px;">
                <div style="font-size:13px; color:var(--text-muted);">Belum ada item dipilih. Klik menu di atas.</div>
            </div>

            <div class="form-group">
                <input type="text" name="catatan" class="input" placeholder="Catatan · Gula normal, es sedikit">
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin:12px 0 14px;">
                <span style="color:var(--text-muted); font-weight:600;" id="pos-total-qty">Total · 0 cup</span>
                <span style="font-size:18px; font-weight:800;" id="pos-total-rp">Rp 0</span>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Buat Pesanan Offline</button>
            <div style="font-size:12px; color:var(--text-muted); margin-top:8px;">
                Lanjutkan ke pembayaran setelah pesanan dibuat.
            </div>
        </form>
    </div>
</div>

<script>
let posCart = [
    { id: 1, nama: 'Kopi Susu Gading', harga: 18000, qty: 2 }
];

function setPosLayanan(btn, val) {
    document.querySelectorAll('.pos-layanan').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('pos-layanan').value = val;
}

function addPosItem(id, nama, harga) {
    const found = posCart.find(i => i.id === id);
    if (found) found.qty++;
    else posCart.push({ id, nama, harga, qty: 1 });
    renderPosCart();
}

function changePosQty(id, delta) {
    const found = posCart.find(i => i.id === id);
    if (!found) return;
    found.qty += delta;
    posCart = posCart.filter(i => i.qty > 0);
    renderPosCart();
}

function renderPosCart() {
    const container = document.getElementById('pos-cart-list');
    let totalQty = 0;
    let totalRp = 0;

    if (posCart.length === 0) {
        container.innerHTML = '<div style="font-size:13px; color:var(--text-muted);">Belum ada item dipilih. Klik menu di atas.</div>';
    } else {
        container.innerHTML = posCart.map(i => {
            totalQty += i.qty;
            totalRp += i.qty * i.harga;
            return `<div style="display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid var(--border);">
                <div>
                    <div style="font-weight:700; font-size:13.5px;">${i.nama}</div>
                    <div style="font-size:12px; color:var(--text-muted);">Rp ${i.harga.toLocaleString('id-ID')} / cup</div>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <button type="button" class="btn btn-sm" onclick="changePosQty(${i.id}, -1)">−</button>
                    <span style="font-weight:700;">${i.qty}</span>
                    <button type="button" class="btn btn-sm" onclick="changePosQty(${i.id}, 1)">+</button>
                </div>
            </div>`;
        }).join('');
    }

    document.getElementById('pos-total-qty').textContent = `Total · ${totalQty} cup`;
    document.getElementById('pos-total-rp').textContent = `Rp ${totalRp.toLocaleString('id-ID')}`;
    document.getElementById('pos-items-json').value = JSON.stringify(posCart);
}

renderPosCart();
</script>
<?= $this->endSection() ?>

