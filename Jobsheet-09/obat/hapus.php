<?php

session_start();

require __DIR__ . '/../includes/koneksi.php';

$id = $_GET['id'] ?? '';

if ($id === '') {

    header('Location: list.php');
    exit;
}


try {

    $stmt = $pdo->prepare("
        DELETE FROM obat
        WHERE id = :id
    ");

    $stmt->execute([
        'id' => $id
    ]);


    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Obat berhasil dihapus.'
    ];

} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Obat tidak dapat dihapus karena sudah digunakan dalam transaksi.'
    ];
}


header('Location: list.php');
exit;