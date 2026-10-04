<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: batal.php');
    exit;
}

csrf_verify();
$id = $_POST['id'] ?? null;

if (!$id) {
    header('Location: batal.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Lock transaksi penjualan
    $stmt = $pdo->prepare("SELECT id, status FROM penjualan WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $id]);
    $trx = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trx || $trx['status'] !== 'selesai') {
        throw new Exception('Transaksi tidak ditemukan atau sudah dibatalkan.');
    }

    // Ambil detail obat yang perlu dikembalikan stoknya
    $stmtDetail = $pdo->prepare("SELECT obat_id, jumlah FROM detail_penjualan WHERE penjualan_id = :id");
    $stmtDetail->execute(['id' => $id]);
    $details = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

    // Kembalikan stok obat
    foreach ($details as $item) {
        $updateBuku = $pdo->prepare("UPDATE obat SET stok = stok + :jumlah WHERE id = :obat_id");
        $updateBuku->execute([
            'jumlah' => $item['jumlah'],
            'obat_id' => $item['obat_id']
        ]);
    }

    // Ubah status transaksi jadi dibatalkan
    $updateTrx = $pdo->prepare("UPDATE penjualan SET status = 'dibatalkan' WHERE id = :id");
    $updateTrx->execute(['id' => $id]);

    $pdo->commit();
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Transaksi berhasil dibatalkan dan stok obat dikembalikan.'];

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal membatalkan transaksi: ' . $e->getMessage()];
}

header('Location: batal.php');
exit;