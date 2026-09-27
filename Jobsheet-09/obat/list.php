<?php
$page_title = "Daftar Obat";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Logika penangkapan pencarian dan halaman persis seperti SISALON[cite: 4]
$q = trim($_GET['q'] ?? '');
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$perHalaman = 5;

// Hitung total data berdasarkan pencarian ILIKE[cite: 4]
$hitung = $pdo->prepare("SELECT COUNT(*) FROM obat WHERE nama_obat ILIKE :kw");
$hitung->execute(['kw' => "%$q%"]);
$totalData = (int) $hitung->fetchColumn();

// Kalkulasi total halaman dan offset[cite: 4]
$totalHalaman = max(1, (int) ceil($totalData / $perHalaman));
$halaman = min($halaman, $totalHalaman);
$offset = ($halaman - 1) * $perHalaman;

// Query SIAFARMA dengan penambahan ILIKE, LIMIT, dan OFFSET[cite: 4]
$stmt = $pdo->prepare("
    SELECT 
        obat.*, 
        kategori.nama_kategori, 
        supplier.nama_supplier
    FROM obat
    LEFT JOIN kategori ON obat.kategori_id = kategori.id
    LEFT JOIN supplier ON obat.supplier_id = supplier.id
    WHERE obat.nama_obat ILIKE :kw 
    ORDER BY obat.id DESC 
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':kw', "%$q%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $perHalaman, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftarObat = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
    <div class="page-header">
        <div>
            <h2>Data Obat</h2>
            <p>Kelola seluruh obat yang tersedia di apotek.</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">+ Tambah Obat</a>
    </div>

    <?php if ($flash): ?>
        <p class="flash-message <?php echo htmlspecialchars($flash['type']); ?>">
            <span><?php echo htmlspecialchars($flash['pesan']); ?></span>
        </p>
    <?php endif; ?>

    <!-- Form pencarian mengikuti struktur kelas SISALON[cite: 4] -->
    <div class="search-box">
        <form class="search-form" method="get">
            <div class="search-field">
                <label for="search-input">Cari Nama Obat</label>
                <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Ketik nama obat...">
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Obat</th>
                    <th>Kategori</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarObat)): ?>
                    <tr>
                        <td colspan="6" class="empty-data">Belum ada data obat atau pencarian tidak ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarObat as $obat): ?>
                        <tr>
                            <td><span class="code-badge"><?php echo htmlspecialchars($obat['kode_obat']); ?></span></td>
                            <td><strong><?php echo htmlspecialchars($obat['nama_obat']); ?></strong></td>
                            <td><?php echo htmlspecialchars($obat['nama_kategori'] ?? '-'); ?></td>
                            <td>Rp <?php echo number_format($obat['harga_jual'], 0, ',', '.'); ?></td>
                            <td><?php echo $obat['stok']; ?> <?php echo htmlspecialchars($obat['satuan']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?php echo $obat['id']; ?>" class="btn-action edit">Edit</a>
                                    <!-- Tombol hapus menggunakan form POST[cite: 4] -->
                                    <form class="form-hapus" method="post" action="hapus.php">
                                        <input type="hidden" name="id" value="<?php echo $obat['id']; ?>">
                                        <button type="submit" class="btn-action delete">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Navigasi pagination dari SISALON yang mempertahankan parameter pencarian (q)[cite: 4] -->
    <?php if ($totalHalaman > 1): ?>
        <nav class="pagination" aria-label="Navigasi halaman">
            <?php for ($nomor = 1; $nomor <= $totalHalaman; $nomor++): ?>
                <a class="<?php echo $nomor === $halaman ? 'active' : ''; ?>" 
                   href="?q=<?php echo urlencode($q); ?>&amp;halaman=<?php echo $nomor; ?>">
                   <?php echo $nomor; ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>