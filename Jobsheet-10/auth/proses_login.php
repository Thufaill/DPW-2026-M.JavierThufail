<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, nama, password, role FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Validasi hash menggunakan password_verify
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];
        
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Selamat datang, ' . $user['nama']];
        header('Location: ../index.php');
        exit;
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah.'];
        header('Location: login.php');
        exit;
    }
}