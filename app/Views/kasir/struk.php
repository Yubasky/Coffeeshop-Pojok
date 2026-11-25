<?= $this->extend('layouts/staff') ?>

<?php $this->setData(['staffSubtitle' => 'Kasir · Struk Pembayaran']); ?>

<?= $this->section('topbarActions') ?>
<button type="button" class="btn btn-sm" onclick="window.print()">Cetak Struk</button>
<a href="<?= site_url('kasir/pos') ?>" class="btn btn-sm">← Kembali ke POS</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card" style="max-width:420px; margin:0 auto; padding:24px;">
    <div style="text-align:center; border-bottom:1px dashed var(--border); padding-bottom:12px; margin-bottom:12px;">
        <div style="font-weight:800; font-size:16px;">POJOK UKM TANJUNG GADING</div>
        <div style="font-size:12px; color:var(--text-muted);">Struk Pembayaran Resmi</div>
    </div>

    <div style="font-size:13px; margin-bottom:12px;">
        <div><strong>No. Pesanan:</strong> <?= esc($order['kode'] ?? '-') ?></div>
        <div><strong>Pelanggan:</strong> <?= esc($order['pelanggan'] ?? '-') ?></div>
        <div><strong>Waktu:</strong> <?= esc($order['waktu'] ?? date('H.i') . ' WIB') ?></div>
        <div><strong>Metode:</strong> <?= esc($order['metode'] ?? 'TUNAI') ?></div>
    </div>

    <div style="border-top:1px dashed var(--border); border-bottom:1px dashed var(--border); padding:10px 0; margin-bottom:12px;">
        <?php foreach (($order['items'] ?? []) as $it): ?>
            <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:4px;">
                <span><?= esc($it['qty']) ?> × <?= esc($it['nama']) ?></span>
                <span>Rp <?= number_format((int) $it['qty'] * (int) $it['harga'], 0, ',', '.') ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="display:flex; justify-content:space-between; font-weight:800; font-size:16px;">
        <span>TOTAL</span>
        <span>Rp <?= number_format((int) ($order['total'] ?? 0), 0, ',', '.') ?></span>
    </div>
</div>
<?= $this->endSection() ?>
