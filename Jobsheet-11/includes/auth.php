<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Menghitung path relatif agar redirect tidak error di subfolder
    $__root = dirname(__DIR__);
    $__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
    $__relativePath = str_replace('\\', '/', substr($__scriptDir, strlen($__root)));
    $__relativePath = trim($__relativePath, '/');
    $base = ($__relativePath === '') ? '' : str_repeat('../', substr_count($__relativePath, '/') + 1);
    
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Silakan login terlebih dahulu.'];
    header('Location: ' . $base . 'auth/login.php');
    exit;
}