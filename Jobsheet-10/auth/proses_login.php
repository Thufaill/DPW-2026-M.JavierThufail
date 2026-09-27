<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

// 1. Periksa apakah akun sedang dikunci sementara karena terlalu banyak gagal login
if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
    $sisa_waktu = $_SESSION['lockout_time'] - time();
    $_SESSION['flash'] = [
        'type' => 'error', 
        'pesan' => "Terlalu banyak percobaan gagal. Silakan tunggu $sisa_waktu detik lagi."
    ];
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username dan password wajib diisi.'];
        header('Location: login.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, nama, password, role FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // 2. Cek kecocokan password
    if ($user && password_verify($password, $user['password'])) {
        // Jika BERHASIL: Reset hitungan gagal login dan lockout
        unset($_SESSION['login_attempts']);
        unset($_SESSION['lockout_time']);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];
        
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Selamat datang, ' . $user['nama']];
        header('Location: ../index.php');
        exit;
    } else {
        // 3. Jika GAGAL: Tambah jumlah percobaan gagal di session
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        
        // Jika sudah gagal 3 kali, kunci selama 60 detik
        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['lockout_time'] = time() + 60; 
            $_SESSION['flash'] = [
                'type' => 'error', 
                'pesan' => 'Akun dikunci sementara karena 3x salah password. Coba lagi dalam 60 detik.'
            ];
        } else {
            $sisa_coba = 3 - $_SESSION['login_attempts'];
            $_SESSION['flash'] = [
                'type' => 'error', 
                'pesan' => "Username atau password salah. (Sisa percobaan: $sisa_coba kali)"
            ];
        }
        
        header('Location: login.php');
        exit;
    }
}