const kolomBuku = ["judul", "pengarang", "tahun", "stok", "kategori"];

function muatDaftarBuku() {
    muatDataTabel("buku.json", kolomBuku);
}

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarBuku();

    const btnReload = document.getElementById("btn-reload");
    if (btnReload) {
        btnReload.addEventListener("click", muatDaftarBuku);
    }
});