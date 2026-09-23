<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kosongkan semua isi variabel $_SESSION
session_unset();

// 2. Hancurkan data session di server
session_destroy();

// 3. Mulai session baru lagi untuk menampung pesan flash
session_start();
$_SESSION['flash'] = [
    'type' => 'success', 
    'pesan' => 'Seluruh data session berhasil dikosongkan.'
];

// 4. Redirect kembali ke halaman asal (atau ke index.php jika HTTP_REFERER kosong)
$referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header("Location: " . $referer);
exit;