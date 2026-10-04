<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';

$__root = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__relativePath = str_replace('\\', '/', substr($__scriptDir, strlen($__root)));
$__relativePath = trim($__relativePath, '/');

$base = ($__relativePath === '') ? '' : str_repeat('../', substr_count($__relativePath, '/') + 1);

$page_title = $page_title ?? 'Dashboard';
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_nama'] ?? 'Tamu';
$userInitial = $isLoggedIn ? strtoupper(substr($userName, 0, 1)) : '?';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAFARMA <?php echo $page_title ? ' | ' . e($page_title) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo e($base); ?>assets/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="navbar-container">
        <a href="<?php echo e($base); ?>index.php" class="brand">
            <div class="brand-icon">💊</div>
            <div class="brand-text">
                <h1>SIA<span>FARMA</span></h1>
                <small>Sistem Informasi Apotek</small>
            </div>
        </a>
        
        <nav class="main-nav">
            <a href="<?php echo e($base); ?>index.php" class="<?php echo $page_title === 'Dashboard' ? 'active' : ''; ?>">Dashboard</a>
            <a href="<?php echo e($base); ?>obat/list.php" class="<?php echo $page_title === 'Data Obat' ? 'active' : ''; ?>">Katalog Obat</a>
            
            <?php if ($isLoggedIn): ?>
                <a href="<?php echo e($base); ?>kategori/list.php" class="<?php echo $page_title === 'Data Kategori' ? 'active' : ''; ?>">Kategori</a>
                <a href="<?php echo e($base); ?>supplier/list.php" class="<?php echo $page_title === 'Data Supplier' ? 'active' : ''; ?>">Supplier</a>
                <a href="<?php echo e($base); ?>penjualan/tambah.php" class="<?php echo $page_title === 'Transaksi Penjualan Baru' ? 'active' : ''; ?>">Transaksi Baru</a>
                <a href="<?php echo e($base); ?>penjualan/batal.php" class="<?php echo $page_title === 'Pembatalan Transaksi' ? 'active' : ''; ?>">Retur/Batal</a>
                <a href="<?php echo e($base); ?>penjualan/riwayat.php" class="<?php echo $page_title === 'Riwayat Penjualan' ? 'active' : ''; ?>">Riwayat</a>
            <?php endif; ?>
        </nav>

        <div class="navbar-right">
            <div class="admin-profile">
                <div class="admin-avatar"><?php echo e($userInitial); ?></div>
                <div class="admin-info">
                    <strong><?php echo e($userName); ?></strong>
                    <small><?php echo $isLoggedIn ? e($_SESSION['user_role'] ?? 'Administrator') : 'Pengunjung Publik'; ?></small>
                </div>
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo e($base); ?>auth/logout.php" style="margin-left: 10px; font-size: 12px; color: #d24b4b; text-decoration: none; font-weight: bold;">Logout</a>
                <?php else: ?>
                    <a href="<?php echo e($base); ?>auth/login.php" style="margin-left: 10px; font-size: 12px; color: #18a77a; text-decoration: none; font-weight: bold;">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<main class="main-container">
<?php if ($flash): ?>
    <div class="flash-message <?php echo e($flash['type']); ?>">
        <span><?php echo e($flash['pesan']); ?></span>
        <button type="button" class="flash-close" onclick="this.parentElement.remove()">✖</button>
    </div>
<?php endif; ?>