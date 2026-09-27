<?php

session_start();

require __DIR__ . '/../includes/koneksi.php';

$id = $_POST['id'] ?? '';

$kodeObat = trim(
    $_POST['kode_obat'] ?? ''
);

$namaObat = trim(
    $_POST['nama_obat'] ?? ''
);

$kategoriId = $_POST['kategori_id'] ?? '';
$supplierId = $_POST['supplier_id'] ?? '';

$hargaBeli = $_POST['harga_beli'] ?? '';
$hargaJual = $_POST['harga_jual'] ?? '';
$stok = $_POST['stok'] ?? '';

$satuan = trim(
    $_POST['satuan'] ?? ''
);


if (
    $id === '' ||
    $kodeObat === '' ||
    $namaObat === '' ||
    $hargaBeli === '' ||
    $hargaJual === '' ||
    $stok === '' ||
    $satuan === ''
) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Data obat belum lengkap.'
    ];

    header('Location: list.php');
    exit;
}


if (
    !is_numeric($hargaBeli) ||
    !is_numeric($hargaJual) ||
    !is_numeric($stok)
) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Harga dan stok harus berupa angka.'
    ];

    header('Location: list.php');
    exit;
}


try {

    $stmt = $pdo->prepare("
        UPDATE obat
        SET
            kode_obat = :kode_obat,
            nama_obat = :nama_obat,
            kategori_id = :kategori_id,
            supplier_id = :supplier_id,
            harga_beli = :harga_beli,
            harga_jual = :harga_jual,
            stok = :stok,
            satuan = :satuan
        WHERE id = :id
    ");

    $stmt->execute([

        'id' => $id,

        'kode_obat' => $kodeObat,

        'nama_obat' => $namaObat,

        'kategori_id' =>
            $kategoriId !== ''
                ? $kategoriId
                : null,

        'supplier_id' =>
            $supplierId !== ''
                ? $supplierId
                : null,

        'harga_beli' => $hargaBeli,

        'harga_jual' => $hargaJual,

        'stok' => $stok,

        'satuan' => $satuan
    ]);


    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Obat berhasil diperbarui.'
    ];

    header('Location: list.php');
    exit;

} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Gagal memperbarui obat.'
    ];

    header('Location: list.php');
    exit;
}