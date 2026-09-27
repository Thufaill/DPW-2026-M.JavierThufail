<?php

$page_title = "Edit Obat";

require __DIR__ . '/../includes/koneksi.php';

$id = $_GET['id'] ?? '';

if ($id === '') {

    header('Location: list.php');
    exit;
}


$stmt = $pdo->prepare("
    SELECT *
    FROM obat
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$obat = $stmt->fetch();

if (!$obat) {

    header('Location: list.php');
    exit;
}


$kategori = $pdo
    ->query("
        SELECT id, nama_kategori
        FROM kategori
        ORDER BY nama_kategori ASC
    ")
    ->fetchAll();


$supplier = $pdo
    ->query("
        SELECT id, nama_supplier
        FROM supplier
        ORDER BY nama_supplier ASC
    ")
    ->fetchAll();


include __DIR__ . '/../includes/header.php';

?>

<div class="page-header">

    <div>

        <h2>
            Edit Obat
        </h2>

        <p>
            Ubah informasi obat.
        </p>

    </div>

</div>


<div class="form-card">

    <form
        action="proses_edit.php"
        method="POST"
        id="form-obat"
    >

        <input
            type="hidden"
            name="id"
            value="<?php echo $obat['id']; ?>"
        >


        <div class="form-section">

            <h3>
                Informasi Obat
            </h3>

        </div>


        <div class="form-grid">

            <div class="form-group">

                <label for="kode_obat">
                    Kode Obat
                    <span>*</span>
                </label>

                <input
                    type="text"
                    id="kode_obat"
                    name="kode_obat"
                    value="<?php echo htmlspecialchars($obat['kode_obat']); ?>"
                    maxlength="30"
                    required
                >

            </div>


            <div class="form-group">

                <label for="nama_obat">
                    Nama Obat
                    <span>*</span>
                </label>

                <input
                    type="text"
                    id="nama_obat"
                    name="nama_obat"
                    value="<?php echo htmlspecialchars($obat['nama_obat']); ?>"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label for="kategori_id">
                    Kategori
                </label>

                <select
                    id="kategori_id"
                    name="kategori_id"
                >

                    <option value="">
                        -- Pilih Kategori --
                    </option>

                    <?php foreach ($kategori as $item): ?>

                        <option
                            value="<?php echo $item['id']; ?>"
                            <?php
                            echo $obat['kategori_id'] == $item['id']
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $item['nama_kategori']
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label for="supplier_id">
                    Supplier
                </label>

                <select
                    id="supplier_id"
                    name="supplier_id"
                >

                    <option value="">
                        -- Pilih Supplier --
                    </option>

                    <?php foreach ($supplier as $item): ?>

                        <option
                            value="<?php echo $item['id']; ?>"
                            <?php
                            echo $obat['supplier_id'] == $item['id']
                                ? 'selected'
                                : '';
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $item['nama_supplier']
                            );
                            ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="form-group">

                <label for="harga_beli">
                    Harga Beli
                    <span>*</span>
                </label>

                <input
                    type="number"
                    id="harga_beli"
                    name="harga_beli"
                    value="<?php echo $obat['harga_beli']; ?>"
                    min="0"
                    step="0.01"
                    required
                >

            </div>


            <div class="form-group">

                <label for="harga_jual">
                    Harga Jual
                    <span>*</span>
                </label>

                <input
                    type="number"
                    id="harga_jual"
                    name="harga_jual"
                    value="<?php echo $obat['harga_jual']; ?>"
                    min="0"
                    step="0.01"
                    required
                >

            </div>


            <div class="form-group">

                <label for="stok">
                    Stok
                    <span>*</span>
                </label>

                <input
                    type="number"
                    id="stok"
                    name="stok"
                    value="<?php echo $obat['stok']; ?>"
                    min="0"
                    required
                >

            </div>


            <div class="form-group">

                <label for="satuan">
                    Satuan
                    <span>*</span>
                </label>

                <select
                    id="satuan"
                    name="satuan"
                    required
                >

                    <?php
                    $satuanList = [
                        'Tablet',
                        'Kapsul',
                        'Botol',
                        'Strip',
                        'Tube',
                        'Box'
                    ];
                    ?>

                    <option value="">
                        -- Pilih Satuan --
                    </option>

                    <?php foreach ($satuanList as $satuan): ?>

                        <option
                            value="<?php echo $satuan; ?>"
                            <?php
                            echo $obat['satuan'] === $satuan
                                ? 'selected'
                                : '';
                            ?>
                        >
                            <?php echo $satuan; ?>
                        </option>

                    <?php endforeach; ?>

                </select>

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
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>