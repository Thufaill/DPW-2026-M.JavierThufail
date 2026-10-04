<?php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

$obatId = $_POST['obat_id'] ?? '';
$jumlah = $_POST['jumlah'] ?? '';

if ($obatId === '' || $jumlah === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Obat dan jumlah wajib diisi.'];
    header('Location: tambah.php');
    exit;
}

if (!is_numeric($jumlah) || (int)$jumlah <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Jumlah harus berupa angka lebih dari 0.'];
    header('Location: tambah.php');
    exit;
}

$jumlah = (int) $jumlah;

try {
    // 1. Mulai Database Transaction
    $pdo->beginTransaction();

    /*
     * 2. SELECT ... FOR UPDATE (Kunci baris stok obat)
     * Mencegah Race Condition apabila 2 kasir memproses obat stok-1 bersamaan.
     */
    $stmt = $pdo->prepare("SELECT id, nama_obat, harga_jual, stok FROM obat WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => (int)$obatId]);
    $obat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$obat) {
        throw new Exception('Data obat tidak ditemukan.');
    }

    if ($jumlah > $obat['stok']) {
        throw new Exception('Stok obat tidak mencukupi. Sisa stok: ' . $obat['stok']);
    }

    $harga = $obat['harga_jual'];
    $subtotal = $harga * $jumlah;
    $userId = $_SESSION['user_id'];

    // 3. Insert Header Penjualan
    $stmt = $pdo->prepare("INSERT INTO penjualan (total, user_id, status) VALUES (:total, :user_id, 'selesai') RETURNING id");
    $stmt->execute([
        'total' => $subtotal,
        'user_id' => $userId
    ]);
    $penjualanId = $stmt->fetchColumn();

    // 4. Insert Detail Penjualan
    $stmt = $pdo->prepare("INSERT INTO detail_penjualan (penjualan_id, obat_id, jumlah, harga, subtotal) VALUES (:penjualan_id, :obat_id, :jumlah, :harga, :subtotal)");
    $stmt->execute([
        'penjualan_id' => $penjualanId,
        'obat_id' => $obatId,
        'jumlah' => $jumlah,
        'harga' => $harga,
        'subtotal' => $subtotal
    ]);

    // 5. Potong Stok Obat
    $stmt = $pdo->prepare("UPDATE obat SET stok = stok - :jumlah WHERE id = :id");
    $stmt->execute([
        'jumlah' => $jumlah,
        'id' => $obatId
    ]);

    // 6. Commit Transaction
    $pdo->commit();

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Transaksi berhasil disimpan! Invoice: INV-' . str_pad($penjualanId, 3, '0', STR_PAD_LEFT)
    ];
    header('Location: list.php');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal mencatat transaksi: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}