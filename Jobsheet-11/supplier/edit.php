<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Edit Supplier";
require __DIR__ . '/../includes/koneksi.php';

$id = $_GET['id'] ?? '';
if ($id === '' || !is_numeric($id)) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM supplier WHERE id = :id");
$stmt->execute(['id' => (int)$id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    header('Location: list.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h2>Edit Supplier</h2>
        <p>Ubah informasi supplier.</p>
    </div>
</div>

<div class="form-card">
    <form action="proses_edit.php" method="POST" id="form-supplier">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$supplier['id']; ?>">
        <div class="form-section">
            <h3>Informasi Supplier</h3>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="nama_supplier">Nama Supplier <span>*</span></label>
                <input type="text" id="nama_supplier" name="nama_supplier" value="<?php echo e($supplier['nama_supplier']); ?>" maxlength="100" required>
            </div>
            <div class="form-group">
                <label for="no_hp">Nomor HP</label>
                <input type="text" id="no_hp" name="no_hp" value="<?php echo e($supplier['no_hp']); ?>" maxlength="20">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo e($supplier['email']); ?>" maxlength="100">
            </div>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" rows="4"><?php echo e($supplier['alamat']); ?></textarea>
        </div>
        <div class="form-actions">
            <a href="list.php" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>