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

// Fungsi generik untuk memuat data tabel dari JSON
async function muatDataTabel(namaFile, kunciKolom) {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    loading.style.display = "block";
    tbody.innerHTML = ""; // Mengosongkan tbody terlebih dahulu

    try {
        // Simulasi delay jaringan agar loading indicator terlihat
        await new Promise((resolve) => setTimeout(resolve, 300));

        const res = await fetch(`../data/${namaFile}`);
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }
        const dataList = await res.json();

        dataList.forEach(function (item) {
            const tr = document.createElement("tr");
            
            // Generate sel td berdasarkan urutan kunciKolom yang diminta
            let tdHtml = kunciKolom
                .map((kunci) => `<td>${item[kunci] ?? "-"}</td>`)
                .join("");

            // Tambahkan kolom tombol Aksi di akhir
            tdHtml += `
                <td>
                    <button type="button">Edit</button> 
                    <button type="button" class="btn-hapus">Hapus</button>
                </td>
            `;

            tr.innerHTML = tdHtml;
            tbody.appendChild(tr);
        });

        // Re-bind konfirmasi hapus untuk baris baru
        if (typeof initHapusConfirm === "function") {
            initHapusConfirm();
        }
    } catch (err) {
        const totalKolom = kunciKolom.length + 1;
        tbody.innerHTML = `<tr><td colspan="${totalKolom}">Gagal memuat data: ${err.message}</td></tr>`;
    } finally {
        loading.style.display = "none";
        
        // Update counter tabel jika fungsi tersedia
        if (typeof updateTableCounter === "function") {
            updateTableCounter();
        }
    }
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

// ===== Konfirmasi hapus (Event Delegation di tingkat document) =====
function initHapusConfirm() {
    document.addEventListener("click", function (e) {
        // 1. Cetak elemen persis yang diklik oleh user
        console.log("Elemen yang diklik (e.target):", e.target);

        // 2. Saring hanya jika yang diklik (atau elemen di dalamnya) adalah tombol .btn-hapus
        const btnHapus = e.target.closest(".btn-hapus");

        if (btnHapus) {
            console.log("--> Event tersaring! Tombol Hapus terdeteksi.");
            
            const row = btnHapus.closest("tr");
            const nama = row ? row.querySelector("td")?.textContent : "data ini";
            const yakin = confirm('Yakin ingin menghapus "' + nama + '"?');
            
            if (yakin && row) {
                row.remove();
                // Update counter setelah baris dihapus
                if (typeof updateTableCounter === "function") {
                    updateTableCounter();
                }
            }
        }
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