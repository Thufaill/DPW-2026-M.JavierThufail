<?php
$page_title = "Data Supplier";
require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

$q = trim($_GET['q'] ?? '');
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$perHalaman = 10;

$hitung = $pdo->prepare("
    SELECT COUNT(*) FROM supplier 
    WHERE nama_supplier ILIKE :kw OR no_hp ILIKE :kw OR email ILIKE :kw
");
$hitung->execute(['kw' => "%$q%"]);
$totalData = (int) $hitung->fetchColumn();

$totalHalaman = max(1, (int) ceil($totalData / $perHalaman));
$halaman = min($halaman, $totalHalaman);
$offset = ($halaman - 1) * $perHalaman;

$stmt = $pdo->prepare("
    SELECT
        supplier.id,
        supplier.nama_supplier,
        supplier.alamat,
        supplier.no_hp,
        supplier.email,
        COUNT(obat.id) AS jumlah_obat
    FROM supplier
    LEFT JOIN obat ON obat.supplier_id = supplier.id
    WHERE supplier.nama_supplier ILIKE :kw OR supplier.no_hp ILIKE :kw OR supplier.email ILIKE :kw
    GROUP BY supplier.id, supplier.nama_supplier, supplier.alamat, supplier.no_hp, supplier.email
    ORDER BY supplier.id DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':kw', "%$q%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $perHalaman, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$supplier = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <h2>Data Supplier</h2>
        <p>Kelola data pemasok obat apotek.</p>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="tambah.php" class="btn btn-primary">+ Tambah Supplier</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h3>Daftar Supplier</h3>
            <span class="card-description"><?php echo $totalData; ?> supplier terdaftar</span>
        </div>
        
        <form method="get" action="list.php" style="margin: 0; padding: 0;">
            <div class="search-box">
                <span style="font-size: 13px;">🔍</span>
                <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Cari supplier..." autocomplete="off">
            </div>
        </form>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA SUPPLIER</th>
                    <th>NO. HP</th>
                    <th>EMAIL</th>
                    <th>OBAT</th>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <th>AKSI</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($supplier)): ?>
                <tr>
                    <td colspan="<?php echo isset($_SESSION['user_id']) ? '6' : '5'; ?>" class="empty-data">Data supplier tidak ditemukan.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($supplier as $index => $item): ?>
                    <tr>
                        <td><?php echo $offset + $index + 1; ?></td>
                        <td><strong><?php echo e($item['nama_supplier']); ?></strong></td>
                        <td><?php echo e($item['no_hp'] ?: '-'); ?></td>
                        <td><?php echo e($item['email'] ?: '-'); ?></td>
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