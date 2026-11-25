<?= $this->extend('layouts/staff') ?>

<?php $this->setData(['staffSubtitle' => 'Kasir · ' . ($shift['kasir'] ?? 'Rina') . ' / Shift ' . ucfirst(strtolower($shift['jenis'] ?? 'Pagi')) . ' · 19 Nov 2026']); ?>

<?= $this->section('topbarActions') ?>
<span class="badge badge-green">Toko buka</span>
<a href="<?= site_url('kasir/pos') ?>" class="btn btn-sm">← Antrean</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
    <h1 style="font-size:24px; font-weight:800; margin:0;">Bayar &amp; koordinasi</h1>
    <div style="display:flex; gap:8px; align-items:center;">
        <span class="badge badge-gray"><?= esc($order['kode']) ?></span>
        <span class="badge <?= $order['status_bayar'] === 'Lunas' ? 'badge-green' : 'badge-amber' ?>">
            <?= esc($order['status_bayar']) ?>
        </span>
    </div>
</div>

<div class="two-col-grid">
    <!-- Kiri: Pembayaran (Tanpa Alamat/Ongkir) -->
    <div class="card" style="padding:20px;">
        <h2 style="font-size:18px; font-weight:800; margin:0 0 12px;">
            <?= esc($order['pelanggan']) ?> · <?= esc($order['layanan']) ?>
        </h2>

        <?php foreach ($order['items'] as $it): ?>
            <div style="display:flex; justify-content:space-between; font-size:13.5px; margin-bottom:6px;">
                <span><?= esc($it['qty']) ?> × <?= esc($it['nama']) ?></span>
                <span>Rp <?= number_format($it['qty'] * $it['harga'], 0, ',', '.') ?></span>
            </div>
        <?php endforeach; ?>

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:10px; margin:10px 0 16px;">
            <span style="font-weight:700;">Total tagihan</span>
            <span style="font-size:20px; font-weight:800;">Rp <?= number_format($order['total'], 0, ',', '.') ?></span>
        </div>

        <form action="<?= site_url('kasir/pembayaran/' . $order['id'] . '/bayar') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="metode" id="pay-metode" value="<?= esc($order['metode']) ?>">

            <div class="pill-group" style="margin-bottom:14px;">
                <button type="button" class="pill pay-pill <?= $order['metode'] === 'QRIS' ? 'active' : '' ?>" onclick="setPayMetode(this, 'QRIS')">QRIS · verifikasi</button>
                <button type="button" class="pill pay-pill <?= $order['metode'] === 'Tunai' ? 'active' : '' ?>" onclick="setPayMetode(this, 'Tunai')">Tunai</button>
            </div>

            <div class="card" style="background:var(--surface-muted); margin-bottom:12px;">
                <div style="font-weight:700; margin-bottom:4px;">Terima QRIS</div>
                <div style="font-size:12.5px; color:var(--text-muted); margin-bottom:10px;">Cocokkan nominal, waktu, dan referensi mutasi.</div>
                <div class="form-group">
                    <input type="text" name="referensi_qris" class="input" placeholder="Referensi transaksi · QR-191126-0091">
                </div>
                <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:600;">
                    <input type="checkbox" checked> Rp <?= number_format($order['total'], 0, ',', '.') ?> sudah masuk ke rekening toko
                </label>
            </div>

            <div style="font-size:12.5px; color:var(--text-muted); margin-bottom:6px;">Jika metode diganti ke Tunai</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px;">
                <input type="number" id="tunai-diterima" class="input" placeholder="Diterima · Rp 50.000" value="50000" oninput="hitungKembalian(<?= (int) $order['total'] ?>)">
                <input type="text" id="tunai-kembalian" class="input" readonly value="Kembalian · Rp <?= number_format(max(0, 50000 - $order['total']), 0, ',', '.') ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit Pembayaran ke Kasir</button>
            <div style="display:flex; align-items:center; gap:10px; margin-top:10px;">
                <span class="badge <?= $order['status_bayar'] === 'Lunas' ? 'badge-green' : 'badge-gray' ?>"><?= esc($order['status_bayar']) ?></span>
                <span style="font-size:12px; color:var(--text-muted);">Setelah berhasil: stok bahan otomatis dipotong.</span>
            </div>
        </form>
    </div>

    <!-- Kanan: Koordinasi Barista, Update Pelanggan, Batalkan -->
    <div style="display:flex; flex-direction:column; gap:14px;">
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <h3 style="font-size:16px; font-weight:800; margin:0;">1. Info ke Barista</h3>
                <span class="badge badge-gray"><?= ! empty($order['dikirim_barista']) ? 'Terkirim' : 'Setelah lunas' ?></span>
            </div>
            <div style="font-size:13.5px; font-weight:600; margin-bottom:8px;">
                <?= esc($order['nomor_pendek']) ?> ·
                <?php $summary = array_map(static fn($i) => $i['qty'] . ' ' . $i['nama'], $order['items']); echo esc(implode(' + ', $summary)); ?>
                · <?= esc($order['layanan']) ?>
            </div>
            <?php if (! empty($order['catatan'])): ?>
                <div class="alert alert-amber" style="margin-bottom:10px;">Racikan: <?= esc($order['catatan']) ?></div>
            <?php endif; ?>
            <form action="<?= site_url('kasir/pembayaran/' . $order['id'] . '/kirim-barista') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-block">Kirim Info ke Barista</button>
            </form>
        </div>

        <div class="card">
            <h3 style="font-size:16px; font-weight:800; margin:0 0 10px;">2. Update ke Pelanggan</h3>
            <form action="<?= site_url('kasir/pembayaran/' . $order['id'] . '/update-pelanggan') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="status_bar" id="upd-status-bar" value="Diproses">
                <div class="pill-group" style="margin-bottom:10px;">
                    <button type="button" class="pill upd-pill active" onclick="setUpdStatus(this, 'Diproses')">Sedang Dibuat</button>
                    <button type="button" class="pill upd-pill" onclick="setUpdStatus(this, 'Selesai')">Selesai</button>
                </div>
                <button type="submit" class="btn btn-block">Kirim update pelanggan</button>
                <div style="font-size:12px; color:var(--text-muted); margin-top:8px;">Dikirim mengikuti status pengerjaan barista.</div>
            </form>
        </div>

        <div class="card">
            <h3 style="font-size:16px; font-weight:800; margin:0 0 10px;">Batalkan pesanan</h3>
            <form action="<?= site_url('kasir/pembayaran/' . $order['id'] . '/batal') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <input type="text" name="alasan" class="input" placeholder="Alasan wajib * · Masukkan alasan" required>
                </div>
                <button type="submit" class="btn btn-danger-soft btn-block">Batal &amp; beri tahu pelanggan</button>
                <div style="font-size:12px; color:var(--text-muted); margin-top:8px;">Jika lunas, proses refund dan koreksi stok dicatat.</div>
            </form>
        </div>
    </div>
</div>

<script>
function setPayMetode(btn, val) {
    document.querySelectorAll('.pay-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('pay-metode').value = val;
}
function setUpdStatus(btn, val) {
    document.querySelectorAll('.upd-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('upd-status-bar').value = val;
}
function hitungKembalian(total) {
    const diterima = parseInt(document.getElementById('tunai-diterima').value || '0', 10);
    const kembali = Math.max(0, diterima - total);
    document.getElementById('tunai-kembalian').value = 'Kembalian · Rp ' + kembali.toLocaleString('id-ID');
}
</script>
<?= $this->endSection() ?>

