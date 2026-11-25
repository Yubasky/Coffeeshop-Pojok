<?= $this->extend('layouts/owner') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
    <h1 style="font-size:26px; font-weight:800; margin:0;">Dashboard Pemasukan</h1>
    <button type="button" class="btn btn-sm" onclick="window.print()">Ekspor rekap</button>
</div>

<div class="pill-group" style="margin-bottom:10px;">
    <a href="<?= site_url('owner/dashboard?periode=Shift') ?>" class="pill <?= $periode === 'Shift' ? 'active' : '' ?>">Shift</a>
    <a href="<?= site_url('owner/dashboard?periode=Harian') ?>" class="pill <?= $periode === 'Harian' ? 'active' : '' ?>">Harian</a>
    <a href="<?= site_url('owner/dashboard?periode=Bulanan') ?>" class="pill <?= $periode === 'Bulanan' ? 'active' : '' ?>">Bulanan</a>
    <span class="pill">18 Nov 2026 ▾</span>
</div>

<p style="font-size:13px; color:var(--text-muted); margin:0 0 16px;">
    Rekap <?= esc(strtolower($periode)) ?> · transaksi lunas, di luar pesanan batal.
</p>

<!-- KPI Pemasukan (Tanpa Pengeluaran & Laba Bersih sesuai SRS Final) -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px; margin-bottom:18px;">
    <div class="card">
        <div style="font-size:13px; color:var(--text-muted); margin-bottom:6px;">Total Pemasukan</div>
        <div style="font-size:26px; font-weight:800;">Rp 1.260.000</div>
        <div style="font-size:12.5px; color:var(--text-muted); margin-top:4px;">63 transaksi lunas</div>
    </div>
    <div class="card">
        <div style="font-size:13px; color:var(--text-muted); margin-bottom:6px;">Transaksi Lunas</div>
        <div style="font-size:26px; font-weight:800;">63 pesanan</div>
        <div style="font-size:12.5px; color:var(--text-muted); margin-top:4px;">Online &amp; Offline POS</div>
    </div>
    <div class="card">
        <div style="font-size:13px; color:var(--text-muted); margin-bottom:6px;">Rata-rata per Transaksi</div>
        <div style="font-size:26px; font-weight:800;">Rp 20.000</div>
        <div style="font-size:12.5px; color:var(--text-muted); margin-top:4px;">Periode <?= esc($periode) ?></div>
    </div>
</div>

<!-- Grafik Pemasukan per Jam & QRIS vs Tunai -->
<div style="display:grid; grid-template-columns:1.5fr 1fr; gap:16px; margin-bottom:18px;">
    <div class="card">
        <div style="font-weight:800; font-size:15px; margin-bottom:2px;">Pemasukan per jam</div>
        <div style="font-size:12.5px; color:var(--text-muted); margin-bottom:18px;">Dalam ribuan rupiah · 18 Nov 2026</div>

        <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; align-items:end; height:140px; padding-top:16px;">
            <div style="text-align:center;">
                <div style="font-size:12px; font-weight:700; margin-bottom:6px;">360</div>
                <div style="height:95px; background:#9a9891; border-radius:6px 6px 0 0;"></div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:6px;">08–11</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:12px; font-weight:700; margin-bottom:6px;">360</div>
                <div style="height:95px; background:#9a9891; border-radius:6px 6px 0 0;"></div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:6px;">11–14</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:12px; font-weight:700; margin-bottom:6px;">270</div>
                <div style="height:72px; background:#9a9891; border-radius:6px 6px 0 0;"></div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:6px;">14–18</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:12px; font-weight:700; margin-bottom:6px;">270</div>
                <div style="height:72px; background:#9a9891; border-radius:6px 6px 0 0;"></div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:6px;">18–22</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="font-weight:800; font-size:15px; margin-bottom:14px;">QRIS vs Tunai</div>
        <div style="display:flex; height:14px; border-radius:999px; overflow:hidden; margin-bottom:18px; background:var(--surface-muted);">
            <div style="width:60%; background:#5c5b57;"></div>
            <div style="width:40%; background:#c2c0b8;"></div>
        </div>
        <div style="margin-bottom:12px;">
            <div style="font-size:13px; color:var(--text-muted);">QRIS · 60%</div>
            <div style="font-size:18px; font-weight:800;">Rp 756.000</div>
        </div>
        <div>
            <div style="font-size:13px; color:var(--text-muted);">Tunai · 40%</div>
            <div style="font-size:18px; font-weight:800;">Rp 504.000</div>
        </div>
    </div>
</div>

<!-- Tabel Rekap per Shift (Hanya Pemasukan, tanpa Pengeluaran & Laba Bersih) -->
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Shift</th>
                <th>Transaksi Lunas</th>
                <th>Pemasukan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pagi · 08–15</td>
                <td>36 transaksi</td>
                <td>Rp 720.000</td>
            </tr>
            <tr>
                <td>Sore · 15–22</td>
                <td>27 transaksi</td>
                <td>Rp 540.000</td>
            </tr>
            <tr style="font-weight:800; background:#faf9f6;">
                <td>Total</td>
                <td>63 transaksi</td>
                <td>Rp 1.260.000</td>
            </tr>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

