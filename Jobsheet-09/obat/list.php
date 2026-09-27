<?php
$page_title = "Data Obat";
require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

// Menangkap parameter dari URL untuk pencarian dan pagination
$q = trim($_GET['q'] ?? '');
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$perHalaman = 5; // Batas data per halaman

// Menghitung total data dengan filter pencarian
$hitung = $pdo->prepare("
    SELECT COUNT(*) FROM obat 
    WHERE nama_obat ILIKE :kw OR kode_obat ILIKE :kw
");
$hitung->execute(['kw' => "%$q%"]);
$totalData = (int) $hitung->fetchColumn();

// Menghitung jumlah halaman dan offset
$totalHalaman = max(1, (int) ceil($totalData / $perHalaman));
$halaman = min($halaman, $totalHalaman);
$offset = ($halaman - 1) * $perHalaman;

// Mengambil data dengan limit dan offset
$stmt = $pdo->prepare("
    SELECT
        obat.id,
        obat.kode_obat,
        obat.nama_obat,
        obat.harga_beli,
        obat.harga_jual,
        obat.stok,
        obat.satuan,
        kategori.nama_kategori,
        supplier.nama_supplier
    FROM obat
    LEFT JOIN kategori ON obat.kategori_id = kategori.id
    LEFT JOIN supplier ON obat.supplier_id = supplier.id
    WHERE obat.nama_obat ILIKE :kw OR obat.kode_obat ILIKE :kw
    ORDER BY obat.id DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':kw', "%$q%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $perHalaman, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$obat = $stmt->fetchAll();
?>

<div class="page-header">
    <div>
        <h2>Data Obat</h2>
        <p>Kelola seluruh obat yang tersedia di apotek.</p>
    </div>
    <a href="tambah.php" class="btn btn-primary">+ Tambah Obat</a>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Obat</h3>
            <span class="card-description">
                Total <?php echo $totalData; ?> obat terdaftar (Halaman <?php echo $halaman; ?> dari <?php echo $totalHalaman; ?>)
            </span>
        </div>
        <!-- Search diubah menjadi form GET, mempertahankan tampilan asli -->
        <div class="search-box">
            <form method="get" action="list.php" style="display: flex; gap: 5px; width: 100%; align-items: center; margin: 0;">
                <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Cari obat..." style="border: none; outline: none; background: transparent; width: 100%; font-size: 12px; color: #344054;">
                <button type="submit" style="background: none; border: none; padding: 0; color: #a3acb8; font-size: 14px; cursor: pointer;">🔍</button>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Obat</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga Jual</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($obat)): ?>
                <tr>
                    <td colspan="7" class="empty-data">Data obat tidak ditemukan.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($obat as $index => $item): ?>
                    <?php
                    if ($item['stok'] <= 0) {
                        $stokClass = 'stock-empty';
                        $stokLabel = 'Habis';
                    } elseif ($item['stok'] <= 10) {
                        $stokClass = 'stock-low';
                        $stokLabel = 'Stok Rendah';
                    } else {
                        $stokClass = 'stock-good';
                        $stokLabel = 'Tersedia';
                    }
                    ?>
                    <tr>
                        <td><?php echo $offset + $index + 1; ?></td>
                        <td><span class="code-badge"><?php echo htmlspecialchars($item['kode_obat']); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($item['nama_obat']); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['nama_kategori'] ?? '-'); ?></td>
                        <td>
                            <div class="stock-display">
                                <strong><?php echo $item['stok']; ?></strong>
                                <small><?php echo htmlspecialchars($item['satuan']); ?></small>
                                <span class="<?php echo $stokClass; ?>"><?php echo $stokLabel; ?></span>
                            </div>
                        </td>
                        <td><strong>Rp <?php echo number_format($item['harga_jual'], 0, ',', '.'); ?></strong></td>
                        <td>
                            <div class="action-buttons">
                                <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn-action edit">Edit</a>
                                
                                <!-- Tombol hapus diubah menjadi form POST namun tampilannya persis sama -->
                                <form class="form-hapus-inline" method="post" action="hapus.php" style="display: inline-block; margin: 0; padding: 0;">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn-action delete btn-hapus" style="border: none; cursor: pointer; font-family: inherit;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- UI Pagination minimalis menyesuaikan card -->
    <?php if ($totalHalaman > 1): ?>
    <div style="padding: 15px 22px; display: flex; gap: 5px; border-top: 1px solid #edf2f1; justify-content: flex-end;">
        <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
            <a href="?q=<?php echo urlencode($q); ?>&halaman=<?php echo $i; ?>" 
               style="padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; 
                      <?php echo $i === $halaman ? 'background: #18a77a; color: white;' : 'background: #f0f5f4; color: #607080;'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>