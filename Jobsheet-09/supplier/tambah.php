<?php

$page_title = "Tambah Supplier";

include __DIR__ . '/../includes/header.php';

?>

<div class="page-header">

    <div>

        <h2>
            Tambah Supplier
        </h2>

        <p>
            Tambahkan data supplier baru.
        </p>

    </div>

</div>


<div class="form-card">

    <form
        action="proses_tambah.php"
        method="POST"
        id="form-supplier"
    >

        <div class="form-section">

            <h3>
                Informasi Supplier
            </h3>

            <p>
                Masukkan informasi pemasok obat.
            </p>

        </div>


        <div class="form-grid">

            <div class="form-group">

                <label for="nama_supplier">
                    Nama Supplier
                    <span>*</span>
                </label>

                <input
                    type="text"
                    id="nama_supplier"
                    name="nama_supplier"
                    placeholder="Contoh: PT Sehat Farma"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label for="no_hp">
                    Nomor HP
                </label>

                <input
                    type="text"
                    id="no_hp"
                    name="no_hp"
                    placeholder="08xxxxxxxxxx"
                    maxlength="20"
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="supplier@email.com"
                    maxlength="100"
                >

            </div>

        </div>


        <div class="form-group">

            <label for="alamat">
                Alamat
            </label>

            <textarea
                id="alamat"
                name="alamat"
                rows="4"
                placeholder="Masukkan alamat supplier..."
            ></textarea>

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
                Simpan Supplier
            </button>

        </div>

    </form>

</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>