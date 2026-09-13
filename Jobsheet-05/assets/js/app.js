// ===== Fungsi Pembantu: Update Counter Tabel =====
function updateTableCounter() {
    const table = document.querySelector(".table-responsive table");
    const counterEl = document.getElementById("table-counter");
    if (!table || !counterEl) return;

    const allRows = table.querySelectorAll("tbody tr");
    const totalBuku = allRows.length;
    
    // Hitung baris yang tampil (tidak di-hide oleh filter)
    let visibleCount = 0;
    allRows.forEach(function (row) {
        if (row.style.display !== "none") {
            visibleCount++;
        }
    });

    counterEl.textContent = `Menampilkan ${visibleCount} dari ${totalBuku} buku`;
}

// ===== Hamburger menu (JS-driven, menggantikan checkbox hack) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

// ===== Konfirmasi hapus (front-end only, belum ke server) =====
function initHapusConfirm() {
    document.querySelectorAll(".btn-hapus").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");
            const nama = row ? row.querySelector("td")?.textContent : "data ini";
            const yakin = confirm("Yakin ingin menghapus \"" + nama + "\"?");
            if (yakin && row) {
                row.remove();
            }
        });
    });
}

// ===== Filter/pencarian tabel real-time (Hanya Kolom Judul) =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");
        
        rows.forEach(function (row) {
            // Ambil elemen td pertama (kolom Judul)
            const kolomJudul = row.querySelector("td");
            const teksJudul = kolomJudul ? kolomJudul.textContent.toLowerCase() : "";
            
            // Tampilkan baris jika teks judul mengandung keyword
            row.style.display = teksJudul.includes(keyword) ? "" : "none";
        });

        // Update counter setelah filter diterapkan
        updateTableCounter();
    });
}

// ===== Validasi form (client-side) - Refactored =====
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    // Daftar aturan validasi untuk tiap field
    const aturanValidasi = [
        {
            selector: "[name='judul'], [name='nama']",
            validate: (val) => val.trim() !== "",
            pesan: "Field ini wajib diisi."
        },
        {
            selector: "[name='pengarang']",
            validate: (val) => val.trim() !== "",
            pesan: "Pengarang wajib diisi."
        },
        {
            selector: "[name='tahun']",
            validate: (val) => {
                const nilai = parseInt(val, 10);
                return !isNaN(nilai) && nilai >= 1900 && nilai <= 2026;
            },
            pesan: "Tahun harus di antara 1900-2026."
        },
        {
            selector: "[name='stok']",
            validate: (val) => {
                const nilai = parseInt(val, 10);
                return !isNaN(nilai) && nilai >= 0;
            },
            pesan: "Stok tidak boleh negatif."
        },
        {
            selector: "[name='isbn']",
            validate: (val) => val.trim() === "" || /^[0-9-]+$/.test(val.trim()),
            pesan: "ISBN hanya boleh berisi angka dan tanda hubung (-)."
        }
    ];

    form.addEventListener("submit", function (e) {
        let valid = true;

        // Perulangan untuk mengeksekusi aturan validasi
        aturanValidasi.forEach(({ selector, validate, pesan }) => {
            const field = form.querySelector(selector);
            if (!field) return;

            if (!validate(field.value)) {
                tampilkanError(field, pesan);
                valid = false;
            } else {
                hapusError(field);
            }
        });

        if (!valid) {
            e.preventDefault();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();

    // Hitung jumlah awal saat halaman selesai dimuat
    updateTableCounter();
});