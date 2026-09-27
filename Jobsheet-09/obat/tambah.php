<?php

$page_title = "Tambah Obat";

require __DIR__ . '/../includes/koneksi.php';

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
            Tambah Obat
        </h2>

        <p>
            Tambahkan obat baru ke dalam data apotek.
        </p>

    </div>

</div>


<div class="form-card">

    <form
        action="proses_tambah.php"
        method="POST"
        id="form-obat"
    >

        <div class="form-section">

            <h3>
                Informasi Obat
            </h3>

            <p>
                Masukkan informasi obat secara lengkap.
            </p>

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
                    placeholder="Contoh: OBT001"
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
                    placeholder="Contoh: Paracetamol"
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

                        <option value="<?php echo $item['id']; ?>">

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

                        <option value="<?php echo $item['id']; ?>">

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
                    min="0"
                    step="0.01"
                    placeholder="3500"
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
                    min="0"
                    step="0.01"
                    placeholder="5000"
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
                    min="0"
                    placeholder="100"
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

                    <option value="">
                        -- Pilih Satuan --
                    </option>

                    <option value="Tablet">
                        Tablet
                    </option>

                    <option value="Kapsul">
                        Kapsul
                    </option>

                    <option value="Botol">
                        Botol
                    </option>

                    <option value="Strip">
                        Strip
                    </option>

                    <option value="Tube">
                        Tube
                    </option>

                    <option value="Box">
                        Box
                    </option>

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
                Simpan Obat
            </button>

        </div>

    </form>

</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>