<?php

session_start();

require __DIR__ . '/../includes/koneksi.php';

$namaSupplier = trim(
    $_POST['nama_supplier'] ?? ''
);

$alamat = trim(
    $_POST['alamat'] ?? ''
);

$noHp = trim(
    $_POST['no_hp'] ?? ''
);

$email = trim(
    $_POST['email'] ?? ''
);

if ($namaSupplier === '') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Nama supplier wajib diisi.'
    ];

    header('Location: tambah.php');
    exit;
}

if (
    $email !== '' &&
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Format email tidak valid.'
    ];

    header('Location: tambah.php');
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO supplier (
            nama_supplier,
            alamat,
            no_hp,
            email
        )
        VALUES (
            :nama_supplier,
            :alamat,
            :no_hp,
            :email
        )
    ");

    $stmt->execute([
        'nama_supplier' => $namaSupplier,
        'alamat' => $alamat !== '' ? $alamat : null,
        'no_hp' => $noHp !== '' ? $noHp : null,
        'email' => $email !== '' ? $email : null
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Supplier berhasil ditambahkan.'
    ];

    header('Location: list.php');
    exit;

} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Gagal menambahkan supplier.'
    ];

    header('Location: tambah.php');
    exit;
}