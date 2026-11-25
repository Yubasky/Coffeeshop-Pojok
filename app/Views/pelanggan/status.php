<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<h1 style="font-size:24px; font-weight:800; margin:0 0 6px;">Status pesanan</h1>

<?php if (! $order): ?>
    <div class="card" style="text-align:center; padding:28px;">
        <p style="color:var(--text-muted); margin:0 0 12px;">Belum ada pesanan aktif.</p>
        <a href="<?= site_url('pelanggan/katalog') ?>" class="btn btn-primary">Pesan Sekarang</a>
    </div>
<?php else: ?>
    <div style="font-size:18px; font-weight:800; margin-bottom:8px;"><?= esc($order['kode']) ?></div>
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:16px;">
        <span class="badge <?= $order['status_bayar'] === 'Lunas' ? 'badge-green' : 'badge-amber' ?>">
            <?= esc($order['status_bayar']) ?>
        </span>
        <span style="font-size:12.5px; color:var(--text-muted);"><?= esc($order['metode']) ?> · <?= esc($order['waktu']) ?></span>
    </div>

    <!-- Tracking Pesanan -->
    <div class="card" style="margin-bottom:14px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <span style="font-weight:700;">Tracking pesanan</span>
            <span style="font-size:12px; color:var(--text-muted);">Live · <?= esc($order['waktu']) ?></span>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:8px; margin-bottom:10px;">
            <?php
            $step = $order['status_bar'];
            $isStep1 = $step === 'Menunggu';
            $isStep2 = $step === 'Diproses';
            $isStep3 = $step === 'Selesai';
            ?>
            <div style="padding:8px; border-radius:8px; text-align:center; font-size:12px; font-weight:700;
                        background:<?= $isStep1 ? 'var(--amber-bg)' : 'var(--surface-muted)' ?>;
                        color:<?= $isStep1 ? 'var(--amber-text)' : 'var(--text-muted)' ?>;">
                1 Menunggu
            </div>
            <div style="padding:8px; border-radius:8px; text-align:center; font-size:12px; font-weight:700;
                        background:<?= $isStep2 ? 'var(--amber-bg)' : 'var(--surface-muted)' ?>;
                        color:<?= $isStep2 ? 'var(--amber-text)' : 'var(--text-muted)' ?>;">
                2 Diproses
            </div>
            <div style="padding:8px; border-radius:8px; text-align:center; font-size:12px; font-weight:700;
                        background:<?= $isStep3 ? 'var(--green-bg)' : 'var(--surface-muted)' ?>;
                        color:<?= $isStep3 ? 'var(--green-text)' : 'var(--text-muted)' ?>;">
                3 Selesai
            </div>
        </div>

        <div style="font-size:12.5px; color:var(--text-muted);">
            <?php if ($order['status_bayar'] !== 'Lunas'): ?>
                Menunggu verifikasi pembayaran oleh kasir.
            <?php elseif ($step === 'Menunggu'): ?>
                Pembayaran lunas. Pesanan menunggu giliran diseduh barista.
            <?php elseif ($step === 'Diproses'): ?>
                Pesanan Anda sedang diracik oleh barista.
            <?php else: ?>
                Pesanan sudah selesai! Silakan ambil di meja pengambilan.
            <?php endif; ?>
        </div>
    </div>

    <!-- Ringkasan Pesanan (Tanpa Ongkir / Alamat / Tombol Ganti-Batalkan sesuai SRS Final) -->
    <div class="card">
        <div style="font-weight:700; margin-bottom:10px;">Ringkasan · <?= esc($order['layanan']) ?></div>
        <?php foreach ($order['items'] as $item): ?>
            <div style="display:flex; justify-content:space-between; font-size:13.5px; margin-bottom:6px;">
                <span><?= esc($item['qty']) ?> × <?= esc($item['nama']) ?></span>
                <span>Rp <?= number_format($item['qty'] * $item['harga'], 0, ',', '.') ?></span>
            </div>
        <?php endforeach; ?>
        <div style="display:flex; justify-content:space-between; font-weight:800; font-size:14.5px; border-top:1px solid var(--border); padding-top:8px; margin-top:8px;">
            <span>Total · <?= esc($order['metode']) ?></span>
            <span>Rp <?= number_format($order['total'], 0, ',', '.') ?></span>
        </div>

        <?php if (! empty($order['catatan'])): ?>
            <div style="font-size:12.5px; color:var(--text-muted); margin-top:10px;">
                Racikan: <?= esc($order['catatan']) ?>
            </div>
        <?php endif; ?>

        <div style="font-size:12px; color:var(--text-muted); margin-top:8px;">
            <?= $order['status_bayar'] === 'Lunas' ? 'Pembayaran lunas · Struk tersedia di kasir.' : 'Struk tersedia setelah pembayaran lunas.' ?>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

