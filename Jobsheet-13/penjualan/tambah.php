<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Transaksi Penjualan Baru";
require __DIR__ . '/../includes/koneksi.php';

// Ambil daftar obat yang stoknya > 0
$stmt = $pdo->query("SELECT id, kode_obat, nama_obat, harga_jual, stok, satuan FROM obat WHERE stok > 0 ORDER BY nama_obat ASC");
$daftarObat = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h2>Transaksi Penjualan Baru</h2>
        <p>Pencatatan transaksi kasir dan pengurangan stok obat otomatis.</p>
    </div>
</div>

<div class="form-card">
    <form action="proses_tambah.php" method="POST" id="form-penjualan">
        <?php echo csrf_field(); ?>
        <div class="form-section">
            <h3>Detail Transaksi</h3>
            <p>Pilih obat dan tentukan jumlah yang dibeli pelanggan.</p>
        </div>
        
        <div class="form-group">
            <label for="obat_id">Pilih Obat <span>*</span></label>
            <select id="obat_id" name="obat_id" required>
                <option value="">-- Pilih Obat (Stok Tersedia) --</option>
                <?php foreach ($daftarObat as $item): ?>
                    <option value="<?php echo (int)$item['id']; ?>">
                        <?php echo e($item['nama_obat']); ?> [<?php echo e($item['kode_obat']); ?>] — Rp <?php echo number_format($item['harga_jual'], 0, ',', '.'); ?> (Sisa Stok: <?php echo (int)$item['stok']; ?> <?php echo e($item['satuan']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="jumlah">Jumlah Beli <span>*</span></label>
                <input type="number" id="jumlah" name="jumlah" min="1" placeholder="1" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Kasir Bertanggung Jawab</label>
                <input type="text" value="<?php echo e($_SESSION['user_nama']); ?>" disabled style="background: #eef2f3;">
            </div>
        </div>

        <div class="form-actions">
            <a href="list.php" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>