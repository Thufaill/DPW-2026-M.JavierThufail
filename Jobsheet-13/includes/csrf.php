<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Menghasilkan atau mengambil token CSRF aktif dalam sesi
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Menghasilkan input hidden untuk ditaruh di dalam form POST
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

// Memverifikasi token CSRF saat form dikirim via POST
function csrf_verify() {
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa.');
    }
}