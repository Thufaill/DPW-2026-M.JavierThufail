<?php

$page_title = "Data Obat";

require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT
        obat.id,
        obat.kode_obat,
        obat.nama_obat,
        obat.harga_beli,
        obat.harga_jual,
        obat.stok,
        obat.satuan,
        kategori.nama_kategori,
        supplier.nama_supplier
    FROM obat
    LEFT JOIN kategori
        ON obat.kategori_id = kategori.id
    LEFT JOIN supplier
        ON obat.supplier_id = supplier.id
    ORDER BY obat.id DESC
");

$obat = $stmt->fetchAll();

?>

<div class="page-header">

    <div>

        <h2>
            Data Obat
        </h2>

        <p>
            Kelola seluruh obat yang tersedia di apotek.
        </p>

    </div>

    <a
        href="tambah.php"
        class="btn btn-primary"
    >
        + Tambah Obat
    </a>

</div>


<div class="card">

    <div class="card-header">

        <div>

            <h3>
                Daftar Obat
            </h3>

            <span class="card-description">
                <?php echo count($obat); ?> obat terdaftar
            </span>

        </div>

        <div class="search-box">

            <span>🔍</span>

            <input
                type="text"
                id="search-input"
                placeholder="Cari obat..."
            >

        </div>

    </div>


    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Obat</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Harga Jual</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if (empty($obat)): ?>

                <tr>

                    <td
                        colspan="7"
                        class="empty-data"
                    >
                        Belum ada data obat.
                    </td>

                </tr>

            <?php else: ?>

                <?php foreach ($obat as $index => $item): ?>

                    <?php

                    if ($item['stok'] <= 0) {
                        $stokClass = 'stock-empty';
                        $stokLabel = 'Habis';
                    } elseif ($item['stok'] <= 10) {
                        $stokClass = 'stock-low';
                        $stokLabel = 'Stok Rendah';
                    } else {
                        $stokClass = 'stock-good';
                        $stokLabel = 'Tersedia';
                    }

                    ?>

                    <tr>

                        <td>
                            <?php echo $index + 1; ?>
                        </td>

                        <td>

                            <span class="code-badge">
                                <?php
                                echo htmlspecialchars(
                                    $item['kode_obat']
                                );
                                ?>
                            </span>

                        </td>

                        <td>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $item['nama_obat']
                                );
                                ?>
                            </strong>

                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $item['nama_kategori'] ?? '-'
                            );
                            ?>
                        </td>

                        <td>

                            <div class="stock-display">

                                <strong>
                                    <?php echo $item['stok']; ?>
                                </strong>

                                <small>
                                    <?php
                                    echo htmlspecialchars(
                                        $item['satuan']
                                    );
                                    ?>
                                </small>

                                <span class="<?php echo $stokClass; ?>">
                                    <?php echo $stokLabel; ?>
                                </span>

                            </div>

                        </td>

                        <td>

                            <strong>
                                Rp
                                <?php
                                echo number_format(
                                    $item['harga_jual'],
                                    0,
                                    ',',
                                    '.'
                                );
                                ?>
                            </strong>

                        </td>

                        <td>

                            <div class="action-buttons">

                                <a
                                    href="edit.php?id=<?php echo $item['id']; ?>"
                                    class="btn-action edit"
                                >
                                    Edit
                                </a>

                                <a
                                    href="hapus.php?id=<?php echo $item['id']; ?>"
                                    class="btn-action delete btn-hapus"
                                >
                                    Hapus
                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>