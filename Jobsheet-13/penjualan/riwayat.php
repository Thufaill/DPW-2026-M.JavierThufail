<?php
$page_title = "Riwayat Penjualan";
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

// Query JOIN 4 Tabel
$stmt = $pdo->query("
    SELECT 
        p.id AS invoice_id,
        p.tanggal,
        p.total,
        p.status,
        u.nama AS nama_kasir,
        o.nama_obat,
        dp.jumlah,
        dp.harga,
        dp.subtotal
    FROM penjualan p
    JOIN detail_penjualan dp ON dp.penjualan_id = p.id
    JOIN obat o ON dp.obat_id = o.id
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY p.id DESC
");
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <h2>Riwayat Penjualan Lanjutan</h2>
        <p>Rincian histori transaksi penjualan obat dan petugas yang melayani.</p>
    </div>
    <a href="tambah.php" class="btn btn-primary">+ Transaksi Baru</a>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Detail Transaksi</h3>
            <span class="card-description">Total <?php echo count($riwayat); ?> item transaksi</span>
        </div>
        <div class="search-box">
            <input type="text" id="search-input" placeholder="Cari transaksi..." autocomplete="off">
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>INVOICE</th>
                    <th>TANGGAL</th>
                    <th>NAMA OBAT</th>
                    <th>QTY</th>
                    <th>HARGA</th>
                    <th>SUBTOTAL</th>
                    <th>STATUS</th>
                    <th>KASIR</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($riwayat)): ?>
                <tr>
                    <td colspan="8" class="empty-data">Belum ada riwayat transaksi.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($riwayat as $item): ?>
                    <tr>
                        <td><strong>INV-<?php echo str_pad($item['invoice_id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><?php echo date('d M Y H:i', strtotime($item['tanggal'])); ?></td>
                        <td><strong><?php echo e($item['nama_obat']); ?></strong></td>
                        <td><span class="count-badge"><?php echo (int)$item['jumlah']; ?></span></td>
                        <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                        <td><strong class="price">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <span class="<?php echo $item['status'] === 'selesai' ? 'status-success' : 'stock-empty'; ?>">
                                <?php echo e(ucfirst($item['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo e($item['nama_kasir'] ?? 'Sistem'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>