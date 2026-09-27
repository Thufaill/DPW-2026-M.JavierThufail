<?php

$page_title = "Data Kategori";

require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT
        kategori.id,
        kategori.nama_kategori,
        kategori.deskripsi,
        COUNT(obat.id) AS jumlah_obat
    FROM kategori
    LEFT JOIN obat
        ON obat.kategori_id = kategori.id
    GROUP BY
        kategori.id,
        kategori.nama_kategori,
        kategori.deskripsi
    ORDER BY kategori.id DESC
");

$kategori = $stmt->fetchAll();

?>

<div class="page-header">

    <div>

        <h2>
            Data Kategori
        </h2>

        <p>
            Kelola kategori obat yang tersedia di apotek.
        </p>

    </div>

    <a
        href="tambah.php"
        class="btn btn-primary"
    >
        + Tambah Kategori
    </a>

</div>


<div class="card">

    <div class="card-header">

        <div>

            <h3>
                Daftar Kategori
            </h3>

            <span class="card-description">
                <?php echo count($kategori); ?> kategori terdaftar
            </span>

        </div>

        <div class="search-box">

            <span>🔍</span>

            <input
                type="text"
                id="search-input"
                placeholder="Cari kategori..."
            >

        </div>

    </div>


    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Obat</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php if (empty($kategori)): ?>

                <tr>

                    <td
                        colspan="5"
                        class="empty-data"
                    >
                        Belum ada data kategori.
                    </td>

                </tr>

            <?php else: ?>

                <?php foreach ($kategori as $index => $item): ?>

                    <tr>

                        <td>
                            <?php echo $index + 1; ?>
                        </td>

                        <td>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $item['nama_kategori']
                                );
                                ?>
                            </strong>

                        </td>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $item['deskripsi'] ?: '-'
                            );
                            ?>

                        </td>

                        <td>

                            <span class="count-badge">
                                <?php
                                echo $item['jumlah_obat'];
                                ?>
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