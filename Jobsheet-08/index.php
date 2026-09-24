<?php
$page_title = "Dashboard";
require_once __DIR__ . '/includes/koneksi.php';
include __DIR__ . '/includes/header.php';

// Mengambil data ringkasan dari tabel yang benar
$totalObat     = $pdo->query("SELECT COUNT(*) FROM obat")->fetchColumn();
$totalKategori = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$totalSupplier = $pdo->query("SELECT COUNT(*) FROM supplier")->fetchColumn();
$totalPenjualan = $pdo->query("SELECT COUNT(*) FROM penjualan")->fetchColumn();

// Mengambil 5 obat dengan stok terendah
$stokRendah = $pdo->query("SELECT nama_obat, stok, satuan FROM obat ORDER BY stok ASC LIMIT 5")->fetchAll();
?>

<div class="welcome-banner">
    <div>
        <h2>Selamat Datang di SIAFARMA</h2>
        <p>Sistem Informasi Pengelolaan Data dan Transaksi Apotek.</p>
    </div>
</div>

<div class="stats-grid">
    <article class="stat-card">
        <div class="stat-info">
            <h3>TOTAL OBAT</h3>
            <p><?= $totalObat; ?></p>
            <small>Item Terdaftar</small>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-info">
            <h3>KATEGORI</h3>
            <p><?= $totalKategori; ?></p>
            <small>Kategori Obat</small>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-info">
            <h3>SUPPLIER</h3>
            <p><?= $totalSupplier; ?></p>
            <small>Pemasok Terdaftar</small>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-info">
            <h3>PENJUALAN</h3>
            <p><?= $totalPenjualan; ?></p>
            <small>Total Transaksi</small>
        </div>
    </article>
</div>

<div class="dashboard-grid">
    <div class="card">
        <div class="card-header">
            <div>
                <h3>Stok Obat Terendah</h3>
                <span class="card-description">Perlu perhatian untuk pemesanan ulang</span>
            </div>
            <a href="obat/list.php" class="card-link">Lihat Semua Obat &rarr;</a>
        </div>
        <div class="card-body">
            <?php if (empty($stokRendah)): ?>
                <p style="padding: 20px; text-align: center; color: #8b98a9;">Belum ada data obat.</p>
            <?php else: ?>
                <?php foreach ($stokRendah as $item): ?>
                    <div class="stock-item">
                        <div class="stock-item-info">
                            <strong><?= htmlspecialchars($item['nama_obat']); ?></strong>
                            <small>Satuan: <?= htmlspecialchars($item['satuan']); ?></small>
                        </div>
                        <div class="stock-number">
                            <strong><?= $item['stok']; ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>