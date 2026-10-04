<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Akses ditolak. Metode tidak diizinkan.');
}

csrf_verify();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID supplier tidak valid.'];
    header('Location: list.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM supplier WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Supplier berhasil dihapus.'];
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Supplier tidak dapat dihapus.'];
}

header('Location: list.php');
exit;