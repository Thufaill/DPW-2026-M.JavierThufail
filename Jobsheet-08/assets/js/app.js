// =========================================================
// SIAFARMA JAVASCRIPT
// =========================================================


// =========================================================
// KONFIRMASI HAPUS
// =========================================================

function initHapusConfirm() {

    document.addEventListener("click", function (e) {

        const button = e.target.closest(".btn-hapus");

        if (!button) {
            return;
        }

        const yakin = confirm(
            "Yakin ingin menghapus data ini?"
        );

        if (!yakin) {
            e.preventDefault();
        }

    });

}


// =========================================================
// PENCARIAN TABEL
// =========================================================

function initTableFilter() {

    const input =
        document.getElementById("search-input");

    const table =
        document.querySelector(
            ".table-responsive table"
        );


    if (!input || !table) {
        return;
    }


    input.addEventListener(
        "input",
        function () {

            const keyword =
                input.value
                    .toLowerCase()
                    .trim();


            const rows =
                table.querySelectorAll(
                    "tbody tr"
                );


            rows.forEach(function (row) {

                const text =
                    row.textContent
                        .toLowerCase();


                if (
                    text.includes(keyword)
                ) {

                    row.style.display = "";

                } else {

                    row.style.display = "none";

                }

            });

        }
    );

}


// =========================================================
// VALIDASI FORM KATEGORI
// =========================================================

function initValidasiKategori() {

    const form =
        document.getElementById(
            "form-kategori"
        );


    if (!form) {
        return;
    }


    form.addEventListener(
        "submit",
        function (e) {

            const nama =
                form.querySelector(
                    "[name='nama_kategori']"
                );


            if (
                nama &&
                nama.value.trim() === ""
            ) {

                alert(
                    "Nama kategori wajib diisi."
                );

                e.preventDefault();

                nama.focus();

            }

        }
    );

}


// =========================================================
// VALIDASI FORM SUPPLIER
// =========================================================

function initValidasiSupplier() {

    const form =
        document.getElementById(
            "form-supplier"
        );


    if (!form) {
        return;
    }


    form.addEventListener(
        "submit",
        function (e) {

            const nama =
                form.querySelector(
                    "[name='nama_supplier']"
                );


            if (
                nama &&
                nama.value.trim() === ""
            ) {

                alert(
                    "Nama supplier wajib diisi."
                );

                e.preventDefault();

                nama.focus();

            }

        }
    );

}


// =========================================================
// VALIDASI FORM OBAT
// =========================================================

function initValidasiObat() {

    const form =
        document.getElementById(
            "form-obat"
        );


    if (!form) {
        return;
    }


    form.addEventListener(
        "submit",
        function (e) {

            const kode =
                form.querySelector(
                    "[name='kode_obat']"
                );

            const nama =
                form.querySelector(
                    "[name='nama_obat']"
                );

            const hargaBeli =
                form.querySelector(
                    "[name='harga_beli']"
                );

            const hargaJual =
                form.querySelector(
                    "[name='harga_jual']"
                );

            const stok =
                form.querySelector(
                    "[name='stok']"
                );


            if (
                !kode.value.trim() ||
                !nama.value.trim()
            ) {

                alert(
                    "Kode dan nama obat wajib diisi."
                );

                e.preventDefault();

                return;

            }


            if (
                Number(hargaBeli.value) < 0 ||
                Number(hargaJual.value) < 0 ||
                Number(stok.value) < 0
            ) {

                alert(
                    "Harga dan stok tidak boleh negatif."
                );

                e.preventDefault();

            }

        }
    );

}


// =========================================================
// VALIDASI FORM PENJUALAN
// =========================================================

function initValidasiPenjualan() {

    const form =
        document.getElementById(
            "form-penjualan"
        );


    if (!form) {
        return;
    }


    form.addEventListener(
        "submit",
        function (e) {

            const obat =
                form.querySelector(
                    "[name='obat_id']"
                );

            const jumlah =
                form.querySelector(
                    "[name='jumlah']"
                );


            if (!obat.value) {

                alert(
                    "Silakan pilih obat."
                );

                e.preventDefault();

                obat.focus();

                return;

            }


            if (
                !jumlah.value ||
                Number(jumlah.value) <= 0
            ) {

                alert(
                    "Jumlah penjualan harus lebih dari 0."
                );

                e.preventDefault();

                jumlah.focus();

            }

        }
    );

}


// =========================================================
// AUTO HIDE FLASH MESSAGE
// =========================================================

function initFlashMessage() {

    const flash =
        document.querySelector(
            ".flash-message"
        );


    if (!flash) {
        return;
    }


    setTimeout(
        function () {

            flash.style.opacity = "0";

            flash.style.transform =
                "translateY(-10px)";


            setTimeout(
                function () {

                    flash.remove();

                },
                300
            );

        },
        4000
    );

}


// =========================================================
// INISIALISASI
// =========================================================

document.addEventListener(
    "DOMContentLoaded",
    function () {

        initHapusConfirm();

        initTableFilter();

        initValidasiKategori();

        initValidasiSupplier();

        initValidasiObat();

        initValidasiPenjualan();

        initFlashMessage();

    }
);