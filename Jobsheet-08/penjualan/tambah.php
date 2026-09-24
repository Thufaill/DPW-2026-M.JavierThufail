<?php

$page_title = "Transaksi Penjualan";

require __DIR__ . '/../includes/koneksi.php';


$stmt = $pdo->query("
    SELECT
        id,
        kode_obat,
        nama_obat,
        harga_jual,
        stok,
        satuan
    FROM obat
    WHERE stok > 0
    ORDER BY nama_obat ASC
");

$obat = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';

?>

<div class="page-header">

    <div>

        <h2>
            Transaksi Penjualan
        </h2>

        <p>
            Buat transaksi penjualan obat baru.
        </p>

    </div>

</div>


<div class="form-card">

    <form
        action="proses_tambah.php"
        method="POST"
        id="form-penjualan"
    >

        <div class="form-section">

            <h3>
                Detail Penjualan
            </h3>

            <p>
                Pilih obat dan masukkan jumlah yang dijual.
            </p>

        </div>


        <div class="form-group">

            <label for="obat_id">
                Obat
                <span>*</span>
            </label>

            <select
                id="obat_id"
                name="obat_id"
                required
            >

                <option value="">
                    -- Pilih Obat --
                </option>

                <?php foreach ($obat as $item): ?>

                    <option
                        value="<?php echo $item['id']; ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $item['nama_obat']
                        );
                        ?>

                        -
                        Rp
                        <?php
                        echo number_format(
                            $item['harga_jual'],
                            0,
                            ',',
                            '.'
                        );
                        ?>

                        -
                        Stok:
                        <?php echo $item['stok']; ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <div class="form-grid">

            <div class="form-group">

                <label for="jumlah">
                    Jumlah
                    <span>*</span>
                </label>

                <input
                    type="number"
                    id="jumlah"
                    name="jumlah"
                    min="1"
                    placeholder="1"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Keterangan
                </label>

                <div class="form-info-box">

                    Stok akan otomatis berkurang
                    setelah transaksi berhasil.

                </div>

            </div>

        </div>


        <div class="form-actions">

            <a
                href="list.php"
                class="btn btn-secondary"
            >
                Batal
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Simpan Transaksi
            </button>

        </div>

    </form>

</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>