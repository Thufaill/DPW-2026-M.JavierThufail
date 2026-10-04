<?php
$page_title = "Data Kategori";
require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

$q = trim($_GET['q'] ?? '');
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$perHalaman = 10;

$hitung = $pdo->prepare("
    SELECT COUNT(*) FROM kategori 
    WHERE nama_kategori ILIKE :kw OR deskripsi ILIKE :kw
");
$hitung->execute(['kw' => "%$q%"]);
$totalData = (int) $hitung->fetchColumn();

$totalHalaman = max(1, (int) ceil($totalData / $perHalaman));
$halaman = min($halaman, $totalHalaman);
$offset = ($halaman - 1) * $perHalaman;

$stmt = $pdo->prepare("
    SELECT 
        kategori.id, 
        kategori.nama_kategori, 
        kategori.deskripsi, 
        COUNT(obat.id) AS jumlah_obat
    FROM kategori
    LEFT JOIN obat ON obat.kategori_id = kategori.id
    WHERE kategori.nama_kategori ILIKE :kw OR kategori.deskripsi ILIKE :kw
    GROUP BY kategori.id, kategori.nama_kategori, kategori.deskripsi
    ORDER BY kategori.id DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':kw', "%$q%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $perHalaman, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$kategori = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <h2>Data Kategori</h2>
        <p>Kelola kategori obat yang tersedia di apotek.</p>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="tambah.php" class="btn btn-primary">+ Tambah Kategori</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Kategori</h3>
            <span class="card-description"><?php echo $totalData; ?> kategori terdaftar</span>
        </div>
        
        <form method="get" action="list.php" style="margin: 0; padding: 0;">
            <div class="search-box">
                <span style="font-size: 13px;">🔍</span>
                <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari nama atau deskripsi..." autocomplete="off">
            </div>
        </form>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA KATEGORI</th>
                    <th>DESKRIPSI</th>
                    <th>JUMLAH OBAT</th>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <th>AKSI</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($kategori)): ?>
                <tr>
                    <td colspan="<?php echo isset($_SESSION['user_id']) ? '5' : '4'; ?>" class="empty-data">Data kategori tidak ditemukan.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($kategori as $index => $item): ?>
                    <tr>
                        <td><?php echo $offset + $index + 1; ?></td>
                        <td><strong><?php echo e($item['nama_kategori']); ?></strong></td>
                        <td><?php echo e($item['deskripsi'] ?: '-'); ?></td>
                        <td><span class="count-badge"><?php echo (int)$item['jumlah_obat']; ?> obat</span></td>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <td>
                            <div class="action-buttons">
                                <a href="edit.php?id=<?php echo (int)$item['id']; ?>" class="btn-action edit">Edit</a>
                                
                                <form class="form-hapus-inline form-hapus" method="post" action="hapus.php" style="display: inline-block; margin: 0; padding: 0;">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">
                                    <button type="submit" class="btn-action delete btn-hapus" style="border: none; outline: none; cursor: pointer; font-family: inherit;">Hapus</button>
                                </form>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalHalaman > 1): ?>
    <div style="padding: 18px 22px; display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid #edf2f1;">
        <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
            <a href="?q=<?php echo urlencode($q); ?>&halaman=<?php echo $i; ?>" 
                style="padding: 6px 14px; border-radius: 7px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid <?php echo $i === $halaman ? '#18a77a' : '#dfe8e5'; ?>; <?php echo $i === $halaman ? 'background: #18a77a; color: white;' : 'background: #fbfdfc; color: #68778d;'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>