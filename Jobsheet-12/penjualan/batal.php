<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Pembatalan Transaksi";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$keyword = trim($_GET['q'] ?? '');

$sql = "
    SELECT p.id, p.tanggal, p.total, u.nama AS nama_kasir
    FROM penjualan p
    LEFT JOIN users u ON u.id = p.user_id
    SELECT p.id, p.tanggal, p.total, u.nama AS nama_kasir
    FROM penjualan p
    LEFT JOIN users u ON u.id = p.user_id
";

if ($keyword !== '') {
    $stmt = $pdo->prepare($sql . " AND (u.nama ILIKE :kw OR CAST(p.id AS TEXT) ILIKE :kw) ORDER BY p.id DESC");
    $stmt->execute(['kw' => '%' . $keyword . '%']);
} else {
    $stmt = $pdo->query($sql . " ORDER BY p.id DESC");
}
$daftarAktif = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <h2>Pembatalan Transaksi / Retur</h2>
        <p>Batalkan transaksi aktif dan kembalikan stok obat ke gudang.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Transaksi Aktif</h3>
            <span class="card-description"><?php echo count($daftarAktif); ?> transaksi selesai</span>
        </div>
        <div class="search-box">
            <form method="get" action="batal.php" style="display: flex; gap: 8px;">
                <input type="text" name="q" value="<?php echo e($keyword); ?>" placeholder="Cari invoice/kasir..." autocomplete="off">
                <button type="submit" class="btn btn-secondary">Cari</button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>INVOICE</th>
                    <th>TANGGAL</th>
                    <th>TOTAL</th>
                    <th>KASIR</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarAktif)): ?>
                    <tr>
                        <td colspan="5" class="empty-data">Tidak ada transaksi aktif.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarAktif as $trx): ?>
                        <tr>
                            <td><strong>INV-<?php echo str_pad($trx['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><?php echo date('d M Y H:i', strtotime($trx['tanggal'])); ?></td>
                            <td><strong class="price">Rp <?php echo number_format($trx['total'], 0, ',', '.'); ?></strong></td>
                            <td><?php echo e($trx['nama_kasir'] ?? 'Sistem'); ?></td>
                            <td>
                                <form method="post" action="proses_batal.php" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini? Stok obat akan dikembalikan.');">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo $trx['id']; ?>">
                                    <button type="submit" class="btn-action delete">Batalkan Transaksi</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>