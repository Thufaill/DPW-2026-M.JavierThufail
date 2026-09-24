<?php

session_start();

require __DIR__ . '/../includes/koneksi.php';

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
    $kodeObat === '' ||
    $namaObat === '' ||
    $hargaBeli === '' ||
    $hargaJual === '' ||
    $stok === '' ||
    $satuan === ''
) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Data obat wajib diisi dengan lengkap.'
    ];

    header('Location: tambah.php');
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

    header('Location: tambah.php');
    exit;
}


if ($hargaBeli < 0 || $hargaJual < 0 || $stok < 0) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Harga dan stok tidak boleh bernilai negatif.'
    ];

    header('Location: tambah.php');
    exit;
}


try {

    $stmt = $pdo->prepare("
        INSERT INTO obat (
            kode_obat,
            nama_obat,
            kategori_id,
            supplier_id,
            harga_beli,
            harga_jual,
            stok,
            satuan
        )
        VALUES (
            :kode_obat,
            :nama_obat,
            :kategori_id,
            :supplier_id,
            :harga_beli,
            :harga_jual,
            :stok,
            :satuan
        )
    ");

    $stmt->execute([

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
        'pesan' => 'Obat berhasil ditambahkan.'
    ];

    header('Location: list.php');
    exit;

} catch (PDOException $e) {

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Gagal menambahkan obat. Kode obat mungkin sudah digunakan.'
    ];

    header('Location: tambah.php');
    exit;
}