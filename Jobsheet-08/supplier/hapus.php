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
        DELETE FROM supplier
        WHERE id = :id
    ");

    $stmt->execute([
        'id' => $id
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Supplier berhasil dihapus.'
    ];

} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Supplier tidak dapat dihapus.'
    ];
}

header('Location: list.php');
exit;