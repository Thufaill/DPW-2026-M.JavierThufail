<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$__jobsheetRoot = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
$base_url = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);

// Ambil path file yang sedang diakses saat ini
$current_page = $_SERVER['SCRIPT_NAME']; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title ?? 'SIMPUS-Mini'; ?></title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
</head>
<body>
    <header>
        <h1>SIMPUS-Mini</h1>
        <button type="button" id="nav-toggle-btn" class="nav-toggle-label" aria-label="Menu">&#9776;</button>
        <nav>
            <ul>
                <li>
                    <a href="<?php echo $base_url; ?>index.php" 
                        class="<?php echo (basename($current_page) == 'index.php') ? 'active' : ''; ?>">
                        Beranda
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>buku/list.php" 
                        class="<?php echo (strpos($current_page, 'buku/list.php') !== false) ? 'active' : ''; ?>">
                        Daftar Buku
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>buku/tambah.php" 
                        class="<?php echo (strpos($current_page, 'buku/tambah.php') !== false) ? 'active' : ''; ?>">
                        Tambah Buku
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>anggota/list.php" 
                        class="<?php echo (strpos($current_page, 'anggota/list.php') !== false) ? 'active' : ''; ?>">
                        Daftar Anggota
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>anggota/tambah.php" 
                        class="<?php echo (strpos($current_page, 'anggota/tambah.php') !== false) ? 'active' : ''; ?>">
                        Tambah Anggota
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <main>