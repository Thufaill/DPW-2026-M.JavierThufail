const kolomAnggota = ["no_anggota", "nama", "alamat", "umur", "no_hp"];

function muatDaftarAnggota() {
    muatDataTabel("anggota.json", kolomAnggota);
}

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarAnggota();

    const btnReload = document.getElementById("btn-reload");
    if (btnReload) {
        btnReload.addEventListener("click", muatDaftarAnggota);
    }
});