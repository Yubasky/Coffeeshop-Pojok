<?= $this->extend('layouts/staff') ?>

<?php $this->setData(['staffSubtitle' => 'Barista · ' . ($shift['barista'] ?? 'Adi') . ' / Shift ' . ucfirst(strtolower($shift['jenis'] ?? 'Pagi')) . ' · 19 Nov 2026']); ?>

<?= $this->section('topbarActions') ?>
<a href="<?= site_url('stok') ?>" class="btn btn-sm">Stok Bahan Baku</a>
<span class="badge badge-green">Sinkron real-time</span>
<span class="badge badge-green">Toko buka</span>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:4px;">
    <h1 style="font-size:24px; font-weight:800; margin:0;">Antrean kopi</h1>
    <span style="font-size:12.5px; color:var(--text-muted);">Terakhir diperbarui 09.41 WIB</span>
</div>
<p style="color:var(--text-muted); margin:0 0 18px;">Hanya pesanan lunas yang telah dikirim oleh kasir. Urutan terlama diprioritaskan.</p>

<div class="two-col-grid">
    <!-- Kolom Menunggu -->
    <div class="card" style="padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h2 style="font-size:17px; font-weight:800; margin:0;">Menunggu</h2>
            <span class="badge badge-amber"><?= count($menunggu) ?> pesanan</span>
        </div>

        <?php if (empty($menunggu)): ?>
            <div style="color:var(--text-muted); font-size:13px; padding:16px 0;">Tidak ada pesanan menunggu.</div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <?php foreach ($menunggu as $o): ?>
                    <div class="card" style="padding:14px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <span style="font-weight:800;"><?= esc($o['nomor_pendek']) ?> · <?= esc($o['pelanggan']) ?></span>
                            <span class="badge badge-gray"><?= esc($o['layanan']) ?></span>
                        </div>
                        <div style="font-weight:600; margin-bottom:8px;">
                            <?php $summary = array_map(static fn($i) => $i['qty'] . ' ' . $i['nama'], $o['items']); echo esc(implode(' + ', $summary)); ?>
                        </div>
                        <?php if (! empty($o['catatan'])): ?>
                            <div class="alert alert-amber" style="margin-bottom:10px;"><?= esc($o['catatan']) ?></div>
                        <?php endif; ?>
                        <div style="display:flex; justify-content:space-between; font-size:12.5px; color:var(--text-muted); margin-bottom:10px;">
                            <span>Diterima dari kasir</span>
                            <span><?= esc($o['waktu']) ?></span>
                        </div>
                        <form action="<?= site_url('barista/antrean/' . $o['id'] . '/mulai') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary btn-block">Buat Kopi</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Kolom Diproses -->
    <div class="card" style="padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h2 style="font-size:17px; font-weight:800; margin:0;">Diproses</h2>
            <span class="badge badge-amber"><?= count($diproses) ?> pesanan</span>
        </div>

        <?php if (empty($diproses)): ?>
            <div style="color:var(--text-muted); font-size:13px; padding:16px 0;">Belum ada pesanan sedang diracik.</div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <?php foreach ($diproses as $o): ?>
                    <div class="card" style="padding:14px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <span style="font-weight:800;"><?= esc($o['nomor_pendek']) ?> · <?= esc($o['pelanggan']) ?></span>
                            <span class="badge badge-gray"><?= esc($o['layanan']) ?></span>
                        </div>
                        <div style="font-weight:600; margin-bottom:8px;">
                            <?php $summary = array_map(static fn($i) => $i['qty'] . ' ' . $i['nama'], $o['items']); echo esc(implode(' + ', $summary)); ?>
                        </div>
                        <?php if (! empty($o['catatan'])): ?>
                            <div class="alert alert-amber" style="margin-bottom:10px;"><?= esc($o['catatan']) ?></div>
                        <?php endif; ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-size:12.5px; color:var(--text-muted);">Durasi pengerjaan</span>
                            <span style="font-size:18px; font-weight:800;"><?= esc($o['durasi']) ?></span>
                        </div>
                        <form action="<?= site_url('barista/antrean/' . $o['id'] . '/selesai') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary btn-block">Selesai &amp; Info ke Kasir</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="alert alert-success" style="margin-top:14px; margin-bottom:0;">
            <strong>Selesai → diterima kasir</strong><br>
            Kasir memberi kabar kepada pelanggan. Status pelanggan diperbarui setelah konfirmasi.
        </div>
    </div>
</div>
<?= $this->endSection() ?>

