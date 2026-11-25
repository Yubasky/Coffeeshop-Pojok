<?= $this->extend('layouts/staff') ?>

<?php $this->setData(['staffSubtitle' => 'Kasir · Manajemen Shift (FR-19)']); ?>

<?= $this->section('topbarActions') ?>
<a href="<?= site_url('kasir/pos') ?>" class="btn btn-sm">← POS Kasir</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
    <h1 style="font-size:24px; font-weight:800; margin:0;">Manajemen &amp; Rekap Shift</h1>
    <form action="<?= site_url('kasir/shift/toggle') ?>" method="post" style="display:flex; gap:8px; align-items:center;">
        <?= csrf_field() ?>
        <?php if (! empty($shift['aktif'])): ?>
            <span class="badge badge-green">Shift <?= esc($shift['jenis']) ?> AKTIF</span>
            <button type="submit" name="action" value="tutup" class="btn btn-danger-soft btn-sm">Tutup Shift Aktif</button>
        <?php else: ?>
            <button type="submit" name="jenis" value="PAGI" class="btn btn-primary btn-sm">Buka Shift PAGI</button>
            <button type="submit" name="jenis" value="SORE" class="btn btn-sm">Buka Shift SORE</button>
        <?php endif; ?>
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Status</th>
                <th>Kasir Pembuka</th>
                <th>Transaksi Lunas</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ringkasanShift)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; color:var(--text-muted); padding:24px;">
                        Belum ada data shift tercatat.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($ringkasanShift as $s): ?>
                    <tr>
                        <td><?= esc($s['tanggal'] ?? '-') ?></td>
                        <td><?= esc($s['jenis_shift'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= ($s['status'] ?? '') === 'AKTIF' ? 'badge-green' : 'badge-gray' ?>">
                                <?= esc($s['status'] ?? '-') ?>
                            </span>
                        </td>
                        <td><?= esc($s['nama_kasir_pembuka'] ?? '-') ?></td>
                        <td><?= esc($s['jumlah_transaksi_lunas'] ?? 0) ?></td>
                        <td style="font-weight:700;">Rp <?= number_format((int) ($s['total_penjualan'] ?? 0), 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
