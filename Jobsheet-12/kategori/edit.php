<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Edit Kategori";
require __DIR__ . '/../includes/koneksi.php';

$id = $_GET['id'] ?? '';
if ($id === '' || !is_numeric($id)) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = :id");
$stmt->execute(['id' => (int)$id]);
$kategori = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kategori) {
    header('Location: list.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h2>Edit Kategori</h2>
        <p>Ubah informasi kategori obat.</p>
    </div>
</div>

<div class="form-card">
    <form action="proses_edit.php" method="POST" id="form-kategori">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$kategori['id']; ?>">
        <div class="form-section">
            <h3>Informasi Kategori</h3>
        </div>
        <div class="form-group">
            <label for="nama_kategori">Nama Kategori <span>*</span></label>
            <input type="text" id="nama_kategori" name="nama_kategori" value="<?php echo e($kategori['nama_kategori']); ?>" maxlength="100" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4"><?php echo e($kategori['deskripsi']); ?></textarea>
        </div>
        <div class="form-actions">
            <a href="list.php" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>