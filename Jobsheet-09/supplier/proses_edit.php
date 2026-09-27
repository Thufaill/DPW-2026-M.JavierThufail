<?php

session_start();

require __DIR__ . '/../includes/koneksi.php';

$id = $_POST['id'] ?? '';

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

if ($id === '' || $namaSupplier === '') {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Data supplier belum lengkap.'
    ];

    header('Location: list.php');
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

    header('Location: list.php');
    exit;
}

try {

    $stmt = $pdo->prepare("
        UPDATE supplier
        SET
            nama_supplier = :nama_supplier,
            alamat = :alamat,
            no_hp = :no_hp,
            email = :email
        WHERE id = :id
    ");

    $stmt->execute([
        'id' => $id,
        'nama_supplier' => $namaSupplier,
        'alamat' => $alamat !== '' ? $alamat : null,
        'no_hp' => $noHp !== '' ? $noHp : null,
        'email' => $email !== '' ? $email : null
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Supplier berhasil diperbarui.'
    ];

    header('Location: list.php');
    exit;

} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Gagal memperbarui supplier.'
    ];

    header('Location: list.php');
    exit;
}