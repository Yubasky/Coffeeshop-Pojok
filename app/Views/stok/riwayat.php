<?= $this->extend(! empty($isOwner) ? 'layouts/owner' : 'layouts/staff') ?>

<?php if (empty($isOwner)): ?>
    <?php $this->setData(['staffSubtitle' => 'Barista · Riwayat Mutasi Stok']); ?>
    <?= $this->section('topbarActions') ?>
        <a href="<?= site_url('stok') ?>" class="btn btn-sm">← Daftar Stok</a>
    <?= $this->endSection() ?>
<?php endif; ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
    <h1 style="font-size:24px; font-weight:800; margin:0;">Riwayat Mutasi Stok</h1>
    <a href="<?= site_url('stok') ?>" class="btn btn-sm">← Kembali ke Daftar Stok</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Waktu WIB</th>
                <th>Mutasi Bahan</th>
                <th>Jenis / Sumber</th>
                <th>Pelaku</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($mutasi)): ?>
                <tr>
                    <td colspan="4" style="text-align:center; color:var(--text-muted); padding:24px;">
                        Belum ada mutasi stok.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($mutasi as $m): ?>
                    <tr>
                        <td><?= esc($m['waktu']) ?></td>
                        <td style="font-weight:700;"><?= esc($m['keterangan']) ?></td>
                        <td><?= esc($m['sumber']) ?></td>
                        <td><?= esc($m['pelaku']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
