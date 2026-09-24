<?php

$page_title = "Data Penjualan";

require __DIR__ . '/../includes/koneksi.php';
include __DIR__ . '/../includes/header.php';


$stmt = $pdo->query("
    SELECT
        penjualan.id,
        penjualan.tanggal,
        penjualan.total,
        COUNT(detail_penjualan.id) AS jumlah_item
    FROM penjualan
    LEFT JOIN detail_penjualan
        ON detail_penjualan.penjualan_id = penjualan.id
    GROUP BY
        penjualan.id,
        penjualan.tanggal,
        penjualan.total
    ORDER BY penjualan.id DESC
");

$penjualan = $stmt->fetchAll();

?>

<div class="page-header">

    <div>

        <h2>
            Data Penjualan
        </h2>

        <p>
            Lihat riwayat transaksi penjualan obat.
        </p>

    </div>

    <a
        href="tambah.php"
        class="btn btn-primary"
    >
        + Transaksi Baru
    </a>

</div>


<div class="card">

    <div class="card-header">

        <div>

            <h3>
                Riwayat Penjualan
            </h3>

            <span class="card-description">
                <?php echo count($penjualan); ?> transaksi
            </span>

        </div>

        <div class="search-box">

            <span>🔍</span>

            <input
                type="text"
                id="search-input"
                placeholder="Cari transaksi..."
            >

        </div>

    </div>


    <div class="table-responsive">

        <table>

            <thead>

                <tr>

                    <th>No. Invoice</th>
                    <th>Tanggal</th>
                    <th>Jumlah Item</th>
                    <th>Total</th>
                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

            <?php if (empty($penjualan)): ?>

                <tr>

                    <td
                        colspan="5"
                        class="empty-data"
                    >
                        Belum ada transaksi penjualan.
                    </td>

                </tr>

            <?php else: ?>

                <?php foreach ($penjualan as $item): ?>

                    <tr>

                        <td>

                            <strong>
                                INV-<?php echo str_pad(
                                    $item['id'],
                                    3,
                                    '0',
                                    STR_PAD_LEFT
                                ); ?>
                            </strong>

                        </td>

                        <td>

                            <?php
                            echo date(
                                'd M Y H:i',
                                strtotime($item['tanggal'])
                            );
                            ?>

                        </td>

                        <td>

                            <span class="count-badge">
                                <?php
                                echo $item['jumlah_item'];
                                ?>
                                item
                            </span>

                        </td>

                        <td>

                            <strong class="price">

                                Rp
                                <?php
                                echo number_format(
                                    $item['total'],
                                    0,
                                    ',',
                                    '.'
                                );
                                ?>

                            </strong>

                        </td>

                        <td>

                            <span class="status-success">
                                Selesai
                            </span>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>