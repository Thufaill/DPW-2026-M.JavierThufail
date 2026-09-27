<?php

session_start();

require __DIR__ . '/../includes/koneksi.php';

$namaKategori = trim(
    $_POST['nama_kategori'] ?? ''
);

$deskripsi = trim(
    $_POST['deskripsi'] ?? ''
);

if ($namaKategori === '') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Nama kategori wajib diisi.'
    ];

    header('Location: tambah.php');
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO kategori (
            nama_kategori,
            deskripsi
        )
        VALUES (
            :nama_kategori,
            :deskripsi
        )
    ");

    $stmt->execute([
        'nama_kategori' => $namaKategori,
        'deskripsi' => $deskripsi !== ''
            ? $deskripsi
            : null
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Kategori berhasil ditambahkan.'
    ];

    header('Location: list.php');
    exit;

} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Gagal menambahkan kategori.'
    ];

    header('Location: tambah.php');
    exit;
}