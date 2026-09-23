<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$judul = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$tahun = trim($_POST['tahun'] ?? '');
$isbn = trim($_POST['isbn'] ?? '');
$stok = trim($_POST['stok'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');

$errors = [];
if ($judul === '') {
    $errors[] = "Judul wajib diisi.";
}
if ($pengarang === '') {
    $errors[] = "Pengarang wajib diisi.";
}

// Validasi ISBN menggunakan preg_match() jika ISBN diisi (tidak kosong)
if ($isbn !== '' && !preg_match('/^[0-9\-]+$/', $isbn)) {
    $errors[] = "ISBN hanya boleh berisi angka dan tanda hubung (-).";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori, penerbit) 
    VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori, :penerbit)"
);

$_SESSION['buku'][] = [
    'judul' => $judul,
    'pengarang' => $pengarang,
    'tahun' => $tahun,
    'isbn' => $isbn,
    'stok' => $stok,
    'kategori' => $kategori,
];

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Buku berhasil ditambahkan.'];
header('Location: list.php');
exit;