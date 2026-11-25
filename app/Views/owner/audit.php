<?= $this->extend('layouts/owner') ?>

<?= $this->section('content') ?>
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
    <div>
        <h1 style="font-size:26px; font-weight:800; margin:0;">Audit Log Sistem</h1>
        <p style="font-size:13px; color:var(--text-muted); margin:2px 0 0;">
            Jejak audit keamanan, perubahan harga menu, pembatalan, dan mutasi stok (FR-22).
        </p>
    </div>
    <span class="badge <?= ! empty($usingDb) ? 'badge-green' : 'badge-amber' ?>">
        <?= ! empty($usingDb) ? 'PostgreSQL Aktif' : 'Mode Mock Session' ?>
    </span>
</div>

<div class="pill-group" style="margin-bottom:16px;">
    <a href="<?= site_url('owner/audit?aksi=SEMUA') ?>" class="pill <?= ($jenisAksi ?? 'SEMUA') === 'SEMUA' ? 'active' : '' ?>">Semua</a>
    <a href="<?= site_url('owner/audit?aksi=AKSES_DITOLAK') ?>" class="pill <?= ($jenisAksi ?? '') === 'AKSES_DITOLAK' ? 'active' : '' ?>">Akses Ditolak (403)</a>
    <a href="<?= site_url('owner/audit?aksi=UBAH_HARGA_MENU') ?>" class="pill <?= ($jenisAksi ?? '') === 'UBAH_HARGA_MENU' ? 'active' : '' ?>">Ubah Harga Menu</a>
    <a href="<?= site_url('owner/audit?aksi=LOGIN_BERHASIL') ?>" class="pill <?= ($jenisAksi ?? '') === 'LOGIN_BERHASIL' ? 'active' : '' ?>">Login</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Waktu WIB</th>
                <th>Pelaku</th>
                <th>Jenis Aksi</th>
                <th>Entitas</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="5" style="text-align:center; color:var(--text-muted); padding:24px;">
                        Belum ada catatan log aktivitas.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= esc($log['waktu']) ?></td>
                        <td style="font-weight:600;"><?= esc($log['pelaku']) ?></td>
                        <td><span class="badge badge-gray"><?= esc($log['jenis_aksi']) ?></span></td>
                        <td><?= esc($log['entitas']) ?></td>
                        <td><?= esc($log['keterangan']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
