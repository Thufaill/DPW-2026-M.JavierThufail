<?php

$page_title = "Data Supplier";

require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT
        supplier.id,
        supplier.nama_supplier,
        supplier.alamat,
        supplier.no_hp,
        supplier.email,
        COUNT(obat.id) AS jumlah_obat
    FROM supplier
    LEFT JOIN obat
        ON obat.supplier_id = supplier.id
    GROUP BY
        supplier.id,
        supplier.nama_supplier,
        supplier.alamat,
        supplier.no_hp,
        supplier.email
    ORDER BY supplier.id DESC
");

$supplier = $stmt->fetchAll();

?>

<div class="page-header">

    <div>

        <h2>
            Data Supplier
        </h2>

        <p>
            Kelola data pemasok obat apotek.
        </p>

    </div>

    <a
        href="tambah.php"
        class="btn btn-primary"
    >
        + Tambah Supplier
    </a>

</div>


<div class="card">

    <div class="card-header">

        <div>

            <h3>
                Daftar Supplier
            </h3>

            <span class="card-description">
                <?php echo count($supplier); ?> supplier terdaftar
            </span>

        </div>

        <div class="search-box">

            <span>🔍</span>

            <input
                type="text"
                id="search-input"
                placeholder="Cari supplier..."
            >

        </div>

    </div>


    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>No</th>
                    <th>Nama Supplier</th>
                    <th>No. HP</th>
                    <th>Email</th>
                    <th>Obat</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if (empty($supplier)): ?>

                <tr>

                    <td
                        colspan="6"
                        class="empty-data"
                    >
                        Belum ada data supplier.
                    </td>

                </tr>

            <?php else: ?>

                <?php foreach ($supplier as $index => $item): ?>

                    <tr>

                        <td>
                            <?php echo $index + 1; ?>
                        </td>

                        <td>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $item['nama_supplier']
                                );
                                ?>
                            </strong>

                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $item['no_hp'] ?: '-'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $item['email'] ?: '-'
                            );
                            ?>
                        </td>

                        <td>

                            <span class="count-badge">
                                <?php echo $item['jumlah_obat']; ?>
                                obat
                            </span>

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