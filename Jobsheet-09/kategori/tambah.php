<?php

$page_title = "Tambah Kategori";

include __DIR__ . '/../includes/header.php';

?>

<div class="page-header">

    <div>

        <h2>
            Tambah Kategori
        </h2>

        <p>
            Tambahkan kategori obat baru.
        </p>

    </div>

</div>


<div class="form-card">

    <form
        action="proses_tambah.php"
        method="POST"
        id="form-kategori"
    >

        <div class="form-section">

            <h3>
                Informasi Kategori
            </h3>

            <p>
                Masukkan informasi kategori obat.
            </p>

        </div>


        <div class="form-group">

            <label for="nama_kategori">
                Nama Kategori
                <span>*</span>
            </label>

            <input
                type="text"
                id="nama_kategori"
                name="nama_kategori"
                placeholder="Contoh: Analgesik"
                maxlength="100"
                required
            >

        </div>


        <div class="form-group">

            <label for="deskripsi">
                Deskripsi
            </label>

            <textarea
                id="deskripsi"
                name="deskripsi"
                rows="4"
                placeholder="Masukkan deskripsi kategori..."
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
                Simpan Kategori
            </button>

        </div>

    </form>

</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>