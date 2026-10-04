<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/koneksi.php';

csrf_verify();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$nama || !$username || !$password) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Semua kolom wajib diisi.'];
        header('Location: register.php');
        exit;
    }

    try {
        $cek = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $cek->execute(['username' => $username]);
        if ($cek->fetch()) {
            $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username sudah digunakan.'];
            header('Location: register.php');
            exit;
        }

        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (nama, username, password) VALUES (:nama, :username, :password)");
        $stmt->execute([
            'nama' => $nama,
            'username' => $username,
            'password' => $hashPassword
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Registrasi berhasil. Silakan login.'];
        header('Location: login.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan sistem.'];
        header('Location: register.php');
        exit;
    }
}