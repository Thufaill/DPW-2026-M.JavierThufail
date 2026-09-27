<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$__root = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);

$__relativePath = str_replace(
    '\\',
    '/',
    substr($__scriptDir, strlen($__root))
);

$__relativePath = trim($__relativePath, '/');

if ($__relativePath === '') {
    $base = '';
} else {
    $base = str_repeat(
        '../',
        substr_count($__relativePath, '/') + 1
    );
}

$page_title = $page_title ?? 'Dashboard';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        SIAFARMA
        <?php
        echo $page_title
            ? ' | ' . htmlspecialchars($page_title)
            : '';
        ?>
    </title>

    <link
        rel="stylesheet"
        href="<?php echo $base; ?>assets/css/style.css"
    >

</head>

<body>

<header class="navbar">

    <div class="navbar-container">

        <a
            href="<?php echo $base; ?>index.php"
            class="brand"
        >

            <div class="brand-icon">
                💊
            </div>

            <div class="brand-text">

                <h1>
                    SIA<span>FARMA</span>
                </h1>

                <small>
                    Sistem Informasi Apotek
                </small>

            </div>

        </a>


        <nav class="main-nav">

            <a
                href="<?php echo $base; ?>index.php"
                class="<?php echo $page_title === 'Dashboard' ? 'active' : ''; ?>"
            >
                <span>⌂</span>
                Dashboard
            </a>

            <a
                href="<?php echo $base; ?>obat/list.php"
                class="<?php echo $page_title === 'Data Obat' ? 'active' : ''; ?>"
            >
                <span>💊</span>
                Obat
            </a>

            <a
                href="<?php echo $base; ?>kategori/list.php"
                class="<?php echo $page_title === 'Data Kategori' ? 'active' : ''; ?>"
            >
                <span>🏷</span>
                Kategori
            </a>

            <a
                href="<?php echo $base; ?>supplier/list.php"
                class="<?php echo $page_title === 'Data Supplier' ? 'active' : ''; ?>"
            >
                <span>🚚</span>
                Supplier
            </a>

            <a
                href="<?php echo $base; ?>penjualan/list.php"
                class="<?php echo $page_title === 'Data Penjualan' ? 'active' : ''; ?>"
            >
                <span>🛒</span>
                Penjualan
            </a>

        </nav>


        <div class="navbar-right">

            <div class="notification">
                🔔
                <span class="notification-dot"></span>
            </div>

            <div class="admin-profile">

                <div class="admin-avatar">
                    A
                </div>

                <div class="admin-info">

                    <strong>
                        Admin
                    </strong>

                    <small>
                        Administrator
                    </small>

                </div>

                <span class="profile-arrow">
                    ▾
                </span>

            </div>

        </div>

    </div>

</header>


<main class="main-container">

<?php if ($flash): ?>

    <div class="flash-message <?php echo htmlspecialchars($flash['type']); ?>">

        <span>
            <?php echo htmlspecialchars($flash['pesan']); ?>
        </span>

        <button
            type="button"
            class="flash-close"
            onclick="this.parentElement.remove()"
        >
            ×
        </button>

    </div>

<?php endif; ?>