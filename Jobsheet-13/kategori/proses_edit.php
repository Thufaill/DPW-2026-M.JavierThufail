<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

$id = $_POST['id'] ?? '';
$namaKategori = trim($_POST['nama_kategori'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');

if ($id === '' || $namaKategori === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Data kategori belum lengkap.'];
    header('Location: list.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE kategori
        SET nama_kategori = :nama_kategori,
            deskripsi = :deskripsi
        WHERE id = :id
    ");
    $stmt->execute([
        'id' => (int)$id,
        'nama_kategori' => $namaKategori,
        'deskripsi' => $deskripsi !== '' ? $deskripsi : null
    ]);

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Kategori berhasil diperbarui.'];
    header('Location: list.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal memperbarui kategori.'];
    header('Location: list.php');
    exit;
}